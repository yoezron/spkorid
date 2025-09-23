<?php
// app/Libraries/MemberCardGenerator.php
namespace App\Libraries;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class MemberCardGenerator
{
    protected $memberModel;

    public function __construct()
    {
        $this->memberModel = new \App\Models\MemberModel();
    }

    /**
     * Generate digital member card
     */
    public function generateCard($memberId)
    {
        $member = $this->memberModel->getMemberWithDetails($memberId);

        if (!$member) {
            return false;
        }

        // Create card image
        $width = 856;
        $height = 540;
        $card = imagecreatetruecolor($width, $height);

        // Colors
        $white = imagecolorallocate($card, 255, 255, 255);
        $black = imagecolorallocate($card, 0, 0, 0);
        $blue = imagecolorallocate($card, 0, 123, 255);
        $gray = imagecolorallocate($card, 128, 128, 128);

        // Background
        imagefilledrectangle($card, 0, 0, $width, $height, $white);

        // Header background
        imagefilledrectangle($card, 0, 0, $width, 100, $blue);

        // Load and add logo
        $logo = imagecreatefrompng(FCPATH . 'assets/images/logo.png');
        imagecopyresized($card, $logo, 30, 20, 0, 0, 60, 60, imagesx($logo), imagesy($logo));

        // Add header text
        $fontBold = FCPATH . 'assets/fonts/Roboto-Bold.ttf';
        $fontRegular = FCPATH . 'assets/fonts/Roboto-Regular.ttf';

        imagettftext($card, 24, 0, 110, 45, $white, $fontBold, 'SERIKAT PEKERJA KAMPUS');
        imagettftext($card, 16, 0, 110, 75, $white, $fontRegular, 'Kartu Anggota');

        // Add member photo
        if ($member['foto_path'] && file_exists(FCPATH . $member['foto_path'])) {
            $photo = $this->loadImage(FCPATH . $member['foto_path']);
            imagecopyresized($card, $photo, 30, 130, 0, 0, 150, 200, imagesx($photo), imagesy($photo));
        } else {
            // Default photo placeholder
            imagefilledrectangle($card, 30, 130, 180, 330, $gray);
        }

        // Add member information
        $infoX = 210;
        $infoY = 150;
        $lineHeight = 35;

        imagettftext($card, 14, 0, $infoX, $infoY, $gray, $fontRegular, 'Nama: ' . $member['nama']);
        imagettftext($card, 14, 0, $infoX, $infoY + $lineHeight, $gray, $fontRegular, 'NIK: ' . $member['nik']);
        imagettftext($card, 14, 0, $infoX, $infoY + 2 * $lineHeight, $gray, $fontRegular, 'Divisi: ' . $member['divisi']);
        imagettftext($card, 14, 0, $infoX, $infoY + 3 * $lineHeight, $gray, $fontRegular, 'Bergabung: ' . $member['tanggal_gabung']);

        // Generate QR code
        $qrCode = QrCode::create($member['nik']);
        $writer = new PngWriter();
        $qrResult = $writer->write($qrCode);

        $qrImage = imagecreatefromstring($qrResult->getString());
        imagecopyresized($card, $qrImage, $width - 170, $height - 170, 0, 0, 140, 140, imagesx($qrImage), imagesy($qrImage));

        // Output image as PNG string
        ob_start();
        imagepng($card);
        $imageData = ob_get_clean();

        // Clean up
        imagedestroy($card);
        if (isset($logo)) imagedestroy($logo);
        if (isset($photo)) imagedestroy($photo);
        if (isset($qrImage)) imagedestroy($qrImage);

        return $imageData;
    }

    /**
     * Load image from file (supports PNG and JPEG)
     */
    protected function loadImage($filePath)
    {
        $info = getimagesize($filePath);
        switch ($info[2]) {
            case IMAGETYPE_PNG:
                return imagecreatefrompng($filePath);
            case IMAGETYPE_JPEG:
                return imagecreatefromjpeg($filePath);
            default:
                return false;
        }
    }
}
