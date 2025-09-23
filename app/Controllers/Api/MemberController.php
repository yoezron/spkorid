<?php
// app/Controllers/Api/MemberController.php
namespace App\Controllers\Api;

use App\Models\MemberModel;

class MemberController extends BaseApiController
{
    protected $memberModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
    }

    /**
     * Get member profile
     */
    public function profile()
    {
        if (!$this->verifyToken()) {
            return $this->failUnauthorized('Unauthorized');
        }

        $user = $this->getUserFromToken();
        $member = $this->memberModel->getMemberWithDetails($user['member_id']);

        if (!$member) {
            return $this->failNotFound('Member not found');
        }

        return $this->respond([
            'status' => 'success',
            'data' => $member
        ]);
    }

    /**
     * Update profile
     */
    public function updateProfile()
    {
        if (!$this->verifyToken()) {
            return $this->failUnauthorized('Unauthorized');
        }

        $user = $this->getUserFromToken();

        $rules = [
            'nama_lengkap' => 'required|min_length[3]',
            'nomor_whatsapp' => 'required',
            'alamat_lengkap' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = $this->request->getJSON(true);

        $this->memberModel->update($user['member_id'], $data);

        return $this->respond([
            'status' => 'success',
            'message' => 'Profile updated successfully'
        ]);
    }

    /**
     * Get member card data
     */
    public function getCard()
    {
        if (!$this->verifyToken()) {
            return $this->failUnauthorized('Unauthorized');
        }

        $user = $this->getUserFromToken();
        $member = $this->memberModel->find($user['member_id']);

        if (!$member || $member['status_keanggotaan'] !== 'active') {
            return $this->failNotFound('Member card not available');
        }

        return $this->respond([
            'status' => 'success',
            'data' => [
                'nomor_anggota' => $member['nomor_anggota'],
                'nama_lengkap' => $member['nama_lengkap'],
                'tanggal_bergabung' => $member['tanggal_bergabung'],
                'status' => $member['status_keanggotaan'],
                'qr_code' => $this->generateQRCode($member['nomor_anggota'])
            ]
        ]);
    }

    /**
     * Generate QR code for member card
     */
    private function generateQRCode($memberNumber)
    {
        // Implementation of QR code generation
        // You can use libraries like endroid/qr-code

        return 'data:image/png;base64,' . base64_encode($memberNumber);
    }
}
