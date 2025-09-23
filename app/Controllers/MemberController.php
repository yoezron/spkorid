<?php

// ============================================
// MEMBER CONTROLLERS
// ============================================

// app/Controllers/MemberController.php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\UserModel;
use App\Models\PaymentHistoryModel;
use App\Models\RefStatusKepegawaianModel;
use App\Models\RefKampusModel;
use App\Models\RefProdiModel;

// Import TCPDF class
use \TCPDF;

// Ensure TCPDF is loaded if not using Composer autoload
if (!class_exists('\TCPDF')) {
    require_once(ROOTPATH . 'vendor/tecnickcom/tcpdf/tcpdf.php');
}

class MemberController extends BaseController
{
    protected $memberModel;
    protected $userModel;
    protected $paymentModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->userModel = new UserModel();
        $this->paymentModel = new PaymentHistoryModel();
    }

    /**
     * Member profile
     */
    public function profile()
    {
        $memberId = session()->get('member_id');
        $member = $this->memberModel->getMemberWithDetails($memberId);

        if (!$member) {
            return redirect()->to('/dashboard')->with('error', 'Profil tidak ditemukan');
        }

        $data = [
            'title' => 'Profil Saya - SPK',
            'member' => $member,
            'payment_history' => $this->paymentModel->getMemberPayments($memberId)
        ];

        return view('member/profile', $data);
    }

    /**
     * Edit profile form
     */
    public function editProfile()
    {
        $memberId = session()->get('member_id');
        $member = $this->memberModel->find($memberId);

        if (!$member) {
            return redirect()->to('/dashboard')->with('error', 'Profil tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Profil - SPK',
            'member' => $member,
            'status_kepegawaian' => (new RefStatusKepegawaianModel())->getActiveStatus(),
            'kampus_list' => (new RefKampusModel())->where('is_active', 1)->findAll(),
            'prodi_list' => (new RefProdiModel())->where('kampus_id', $member['kampus_id'])->findAll()
        ];

        return view('member/edit_profile', $data);
    }

    /**
     * Update profile
     */
    public function updateProfile()
    {
        $memberId = session()->get('member_id');

        $rules = [
            'nama_lengkap' => 'required|min_length[3]',
            'nomor_whatsapp' => 'required',
            'alamat_lengkap' => 'required|min_length[10]',
            'bidang_keahlian' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'nomor_whatsapp' => $this->request->getPost('nomor_whatsapp'),
            'alamat_lengkap' => $this->request->getPost('alamat_lengkap'),
            'bidang_keahlian' => $this->request->getPost('bidang_keahlian'),
            'media_sosial' => $this->request->getPost('media_sosial')
        ];

        // Handle photo upload if exists
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid()) {
            $newName = $foto->getRandomName();
            $foto->move(ROOTPATH . 'public/uploads/photos', $newName);
            $updateData['foto_path'] = 'uploads/photos/' . $newName;

            // Delete old photo
            $member = $this->memberModel->find($memberId);
            if ($member['foto_path'] && file_exists(ROOTPATH . 'public/' . $member['foto_path'])) {
                unlink(ROOTPATH . 'public/' . $member['foto_path']);
            }
        }

        $this->memberModel->update($memberId, $updateData);

        return redirect()->to('/member/profile')->with('success', 'Profil berhasil diperbarui');
    }

    /**
     * Change password form
     */
    public function changePassword()
    {
        $data = [
            'title' => 'Ubah Password - SPK'
        ];

        return view('member/change_password', $data);
    }

    /**
     * Update password
     */
    public function updatePassword()
    {
        $userId = session()->get('user_id');

        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $user = $this->userModel->find($userId);

        // Verify current password
        if (!password_verify($this->request->getPost('current_password'), $user['password'])) {
            return redirect()->back()->with('error', 'Password saat ini salah');
        }

        // Update password
        $this->userModel->update($userId, [
            'password' => password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT)
        ]);

        return redirect()->to('/member/profile')->with('success', 'Password berhasil diubah');
    }

    /**
     * Member card
     */
    public function memberCard()
    {
        $memberId = session()->get('member_id');
        $member = $this->memberModel->getMemberWithDetails($memberId);

        if (!$member || $member['status_keanggotaan'] !== 'active') {
            return redirect()->to('/dashboard')->with('error', 'Kartu anggota tidak tersedia');
        }

        $data = [
            'title' => 'Kartu Anggota - SPK',
            'member' => $member
        ];

        return view('member/member_card', $data);
    }

    /**
     * Download member card as PDF
     */
    public function downloadCard()
    {
        $memberId = session()->get('member_id');
        $member = $this->memberModel->getMemberWithDetails($memberId);

        if (!$member || $member['status_keanggotaan'] !== 'active') {
            return redirect()->to('/dashboard')->with('error', 'Kartu anggota tidak tersedia');
        }

        // Generate PDF (using TCPDF or DomPDF)
        // Ensure TCPDF is loaded before instantiation
        if (!class_exists('\TCPDF')) {
            require_once(ROOTPATH . 'vendor/tecnickcom/tcpdf/tcpdf.php');
        }
        $pdf = new \TCPDF('L', 'mm', 'CR80', true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('SPK Indonesia');
        $pdf->SetAuthor('SPK Indonesia');
        $pdf->SetTitle('Kartu Anggota SPK');

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Add a page
        $pdf->AddPage();

        // Generate card content
        $html = view('member/member_card_pdf', ['member' => $member]);
        $pdf->writeHTML($html, true, false, true, false, '');

        // Output PDF
        $pdf->Output('Kartu_Anggota_' . $member['nomor_anggota'] . '.pdf', 'D');
    }
}
