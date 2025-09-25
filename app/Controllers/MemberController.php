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
use Dompdf\Dompdf;
use Dompdf\Options;

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
        $userId = session()->get('user_id');

        $member = $this->memberModel->getMemberWithDetails($memberId);
        $user = $this->userModel->find($userId);

        if (!$member || !$user) {
            return redirect()->to('/dashboard')->with('error', 'Profil tidak ditemukan');
        }

        $data = [
            'title' => 'Profil Saya - SPK',
            'member' => $member,
            'user' => $user,
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
        $userId = session()->get('user_id');

        $member = $this->memberModel->find($memberId);
        $user = $this->userModel->find($userId);

        if (!$member) {
            return redirect()->to('/dashboard')->with('error', 'Profil tidak ditemukan');
        }

        // --- PENYESUAIAN DIMULAI DI SINI ---
        // Mengambil data referensi untuk dropdown di form
        // Pastikan Anda sudah membuat Model untuk tabel-tabel referensi
        // seperti RefStatusKepegawaianModel, RefKampusModel, dll.
        $refStatusKepegawaianModel = new RefStatusKepegawaianModel();
        $refKampusModel = new RefKampusModel();

        $data = [
            'title' => 'Edit Profil - SPK',
            'member' => $member,
            'user' => $user,
            'status_kepegawaian' => $refStatusKepegawaianModel->findAll(),
            'kampus' => $refKampusModel->findAll(),
            // Anda bisa tambahkan data referensi lain di sini jika diperlukan
            // 'prodi' => (new RefProdiModel())->findAll(),
        ];
        // --- PENYESUAIAN SELESAI ---

        return view('member/edit_profile', $data);
    }

    /**
     * Update profile
     */
    public function updateProfile()
    {
        $memberId = session()->get('member_id');

        // --- PENYESUAIAN DIMULAI DI SINI ---
        // Melengkapi rules validasi sesuai form baru
        $rules = [
            'nama_lengkap'        => 'required|min_length[3]|max_length[200]',
            'jenis_kelamin'       => 'required|in_list[Laki-laki,Perempuan]',
            'nomor_whatsapp'      => 'required|max_length[20]',
            'alamat_lengkap'      => 'required',
            'tempat_lahir'        => 'permit_empty|max_length[100]',
            'tanggal_lahir'       => 'permit_empty|valid_date',
            'nidn_nip'            => 'permit_empty|max_length[50]',
            'jabatan_fungsional'  => 'permit_empty|max_length[100]',
            'golongan_pangkat'    => 'permit_empty|max_length[50]',
            'pendidikan_terakhir' => 'permit_empty|in_list[S1,S2,S3,Profesi]',
            'foto_profil'         => 'if_exist|uploaded[foto_profil]|max_size[foto_profil,2048]|is_image[foto_profil]|mime_in[foto_profil,image/jpg,image/jpeg,image/png]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Menyiapkan data untuk disimpan
        $dataToSave = [
            'nama_lengkap'        => $this->request->getPost('nama_lengkap'),
            'jenis_kelamin'       => $this->request->getPost('jenis_kelamin'),
            'nomor_whatsapp'      => $this->request->getPost('nomor_whatsapp'),
            'alamat_lengkap'      => $this->request->getPost('alamat_lengkap'),
            'tempat_lahir'        => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir'       => $this->request->getPost('tanggal_lahir') ?: null,
            'media_sosial'        => $this->request->getPost('media_sosial'),
            'nidn_nip'            => $this->request->getPost('nidn_nip'),
            'jabatan_fungsional'  => $this->request->getPost('jabatan_fungsional'),
            'golongan_pangkat'    => $this->request->getPost('golongan_pangkat'),
            'status_kepegawaian_id' => $this->request->getPost('status_kepegawaian_id') ?: null,
            'pendidikan_terakhir' => $this->request->getPost('pendidikan_terakhir'),
            'kampus_id'           => $this->request->getPost('kampus_id') ?: null,
            'bidang_keahlian'     => $this->request->getPost('bidang_keahlian'),
            'motivasi_berserikat' => $this->request->getPost('motivasi_berserikat'),
        ];

        // Proses upload foto profil baru
        $foto = $this->request->getFile('foto_profil');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            // Hapus foto lama jika ada
            $oldMemberData = $this->memberModel->find($memberId);
            if (!empty($oldMemberData['foto_path']) && file_exists(FCPATH . $oldMemberData['foto_path'])) {
                unlink(FCPATH . $oldMemberData['foto_path']);
            }

            // Simpan foto baru
            $newName = $foto->getRandomName();
            $foto->move('uploads/photos', $newName);
            $dataToSave['foto_path'] = 'uploads/photos/' . $newName;
        }

        // Lakukan update data
        if ($this->memberModel->update($memberId, $dataToSave)) {
            // Update data di session jika perlu
            session()->set('nama_lengkap', $dataToSave['nama_lengkap']);
            if (isset($dataToSave['foto_path'])) {
                session()->set('foto_path', $dataToSave['foto_path']);
            }

            return redirect()->to('member/profile')->with('success', 'Profil Anda berhasil diperbarui.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui profil. Silakan coba lagi.');
        }
        // --- PENYESUAIAN SELESAI ---
    }


    /**
     * Download member card as PDF
     */
    public function downloadCard()
    {
        $memberId = session()->get('member_id');
        $member = $this->memberModel->getMemberWithDetails($memberId);

        if (!$member || $member['status_keanggotaan'] !== 'active') {
            return redirect()->to('/dashboard')->with('error', 'Kartu anggota tidak tersedia.');
        }

        $html = view('member/member_card_pdf', ['member' => $member]);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A8', 'landscape');
        $dompdf->render();

        $dompdf->stream('Kartu_Anggota_' . $member['nomor_anggota'] . '.pdf', ['Attachment' => 1]);
    }

    // Tambahkan method-method ini di MemberController.php

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

        if (!password_verify($this->request->getPost('current_password'), $user['password'])) {
            return redirect()->back()->with('error', 'Password lama tidak sesuai');
        }

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
        // --- PERBAIKAN DIMULAI DI SINI ---
        // Menggunakan getMemberWithDetails() untuk mendapatkan nama_kampus dan data join lainnya
        $member = $this->memberModel->getMemberWithDetails($memberId);
        // --- PERBAIKAN SELESAI ---

        if (!$member || $member['status_keanggotaan'] !== 'active') {
            return redirect()->to('/member/profile')->with('error', 'Kartu anggota tidak tersedia');
        }

        $data = [
            'title' => 'Kartu Anggota - SPK',
            'member' => $member
        ];

        return view('member/member_card', $data);
    }

    /**
     * Print member card
     */
    public function printCard($id = null)
    {
        $memberId = $id ?? session()->get('member_id');
        return $this->downloadCard($memberId);
    }


    /**
     * Upload photo
     */
    public function uploadPhoto()
    {
        $memberId = session()->get('member_id');

        $rules = [
            'photo' => 'uploaded[photo]|max_size[photo,2048]|is_image[photo]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $this->validator->getErrors()
            ]);
        }

        $file = $this->request->getFile('photo');
        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/photos', $newName);

            // Delete old photo if exists
            $member = $this->memberModel->find($memberId);
            if ($member['foto_path'] && file_exists(FCPATH . $member['foto_path'])) {
                unlink(FCPATH . $member['foto_path']);
            }

            $this->memberModel->update($memberId, [
                'foto_path' => 'uploads/photos/' . $newName
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Foto berhasil diupload',
                'photo_url' => base_url('uploads/photos/' . $newName)
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal upload foto'
        ]);
    }

    /**
     * AD/ART Document
     */
    public function adArt()
    {
        $data = [
            'title' => 'AD/ART - SPK',
            'content' => $this->getCMSContent('ad-art')
        ];

        return view('member/documents/ad_art', $data);
    }

    /**
     * Manifesto
     */
    public function manifesto()
    {
        $data = [
            'title' => 'Manifesto Serikat - SPK',
            'content' => $this->getCMSContent('manifesto')
        ];

        return view('member/documents/manifesto', $data);
    }

    /**
     * Sejarah SPK
     */
    public function sejarah()
    {
        $data = [
            'title' => 'Sejarah SPK',
            'content' => $this->getCMSContent('sejarah')
        ];

        return view('member/documents/sejarah', $data);
    }

    /**
     * Informasi Serikat
     */
    public function informasi()
    {
        $informasiModel = new \App\Models\InformasiSerikatModel();

        $data = [
            'title' => 'Informasi Serikat - SPK',
            'informasi' => $informasiModel->getPublished('all', 20)
        ];

        return view('member/informasi/index', $data);
    }

    /**
     * View detail informasi
     */
    public function viewInformasi($id)
    {
        $informasiModel = new \App\Models\InformasiSerikatModel();
        $info = $informasiModel->find($id);

        if (!$info) {
            return redirect()->to('/member/informasi')->with('error', 'Informasi tidak ditemukan');
        }

        $data = [
            'title' => $info['judul'] . ' - SPK',
            'info' => $info
        ];

        return view('member/informasi/detail', $data);
    }

    /**
     * Helper method to get CMS content
     */
    private function getCMSContent($slug)
    {
        $cmsModel = new \App\Models\CMSPageModel();
        $page = $cmsModel->getPageBySlug($slug);
        return $page ? $page['page_content'] : '';
    }
}
