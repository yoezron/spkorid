<?php
// app/Controllers/RegistrationController.php (improved version)
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\UserModel;
use App\Models\PaymentHistoryModel;
use App\Libraries\NomorAnggotaGenerator;
use App\Libraries\EmailService;

class RegistrationController extends BaseController
{
    protected $memberModel;
    protected $userModel;
    protected $paymentModel;
    protected $validation;
    protected $emailService;
    protected $db;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->userModel = new UserModel();
        $this->paymentModel = new PaymentHistoryModel();
        $this->validation = \Config\Services::validation();
        $this->emailService = new EmailService();
        $this->db = \Config\Database::connect();
    }

    /**
     * Process registration with transaction
     */
    public function register()
    {
        // Enhanced validation rules
        $rules = [
            'nama_lengkap' => [
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'Nama lengkap wajib diisi',
                    'min_length' => 'Nama lengkap minimal 3 karakter'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[members.email]',
                'errors' => [
                    'required' => 'Email wajib diisi',
                    'valid_email' => 'Format email tidak valid',
                    'is_unique' => 'Email sudah terdaftar'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/]',
                'errors' => [
                    'required' => 'Password wajib diisi',
                    'min_length' => 'Password minimal 8 karakter',
                    'regex_match' => 'Password harus mengandung huruf besar, huruf kecil, dan angka'
                ]
            ],
            'password_confirm' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Konfirmasi password wajib diisi',
                    'matches' => 'Konfirmasi password tidak sama'
                ]
            ],
            'nomor_whatsapp' => [
                'rules' => 'required|regex_match[/^(\+62|62|0)8[1-9][0-9]{6,11}$/]',
                'errors' => [
                    'regex_match' => 'Format nomor WhatsApp tidak valid'
                ]
            ],
            'foto' => [
                'rules' => 'uploaded[foto]|max_size[foto,2048]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'uploaded' => 'Foto wajib diupload',
                    'max_size' => 'Ukuran foto maksimal 2MB',
                    'is_image' => 'File harus berupa gambar',
                    'mime_in' => 'Format foto harus JPG, JPEG, atau PNG'
                ]
            ],
            'bukti_pembayaran' => [
                'rules' => 'uploaded[bukti_pembayaran]|max_size[bukti_pembayaran,5120]|mime_in[bukti_pembayaran,image/jpg,image/jpeg,image/png,application/pdf]',
                'errors' => [
                    'uploaded' => 'Bukti pembayaran wajib diupload',
                    'max_size' => 'Ukuran file maksimal 5MB',
                    'mime_in' => 'Format file harus JPG, JPEG, PNG, atau PDF'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->db->transStart();

        try {
            // Handle file uploads with proper naming
            $foto = $this->request->getFile('foto');
            $buktiPembayaran = $this->request->getFile('bukti_pembayaran');

            $fotoName = $this->generateFileName($foto, 'FOTO');
            $buktiName = $this->generateFileName($buktiPembayaran, 'BUKTI');

            // Move files to proper directories
            $foto->move(ROOTPATH . 'public/uploads/photos', $fotoName);
            $buktiPembayaran->move(ROOTPATH . 'public/uploads/payments', $buktiName);

            // Generate verification token
            $verificationToken = bin2hex(random_bytes(32));

            // Prepare member data with sanitization
            $memberData = [
                'nama_lengkap' => esc($this->request->getPost('nama_lengkap')),
                'email' => $this->request->getPost('email'),
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'alamat_lengkap' => esc($this->request->getPost('alamat_lengkap')),
                'nomor_whatsapp' => $this->normalizePhoneNumber($this->request->getPost('nomor_whatsapp')),
                'status_kepegawaian_id' => $this->request->getPost('status_kepegawaian_id'),
                'pemberi_gaji_id' => $this->request->getPost('pemberi_gaji_id'),
                'range_gaji_id' => $this->request->getPost('range_gaji_id'),
                'gaji_pokok' => $this->request->getPost('gaji_pokok'),
                'provinsi_id' => $this->request->getPost('provinsi_id'),
                'kota_id' => $this->request->getPost('kota_id'),
                'nidn_nip' => $this->request->getPost('nidn_nip'),
                'jenis_pt_id' => $this->request->getPost('jenis_pt_id'),
                'kampus_id' => $this->request->getPost('kampus_id'),
                'prodi_id' => $this->request->getPost('prodi_id'),
                'bidang_keahlian' => esc($this->request->getPost('bidang_keahlian')),
                'motivasi_berserikat' => esc($this->request->getPost('motivasi_berserikat')),
                'media_sosial' => esc($this->request->getPost('media_sosial')),
                'foto_path' => 'uploads/photos/' . $fotoName,
                'bukti_pembayaran_path' => 'uploads/payments/' . $buktiName,
                'status_keanggotaan' => 'pending'
            ];

            // Insert member
            $memberId = $this->memberModel->insert($memberData);

            // Generate unique username
            $username = $this->generateUsername($memberData['nama_lengkap']);

            // Create user account
            $userData = [
                'member_id' => $memberId,
                'username' => $username,
                'email' => $this->request->getPost('email'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
                'role_id' => 3, // Default role: anggota
                'is_active' => 0,
                'is_verified' => 0,
                'verification_token' => $verificationToken
            ];

            $userId = $this->userModel->insert($userData);

            // Create payment record
            $paymentData = [
                'member_id' => $memberId,
                'nomor_transaksi' => $this->generateTransactionNumber($memberId),
                'jenis_pembayaran' => 'iuran_pertama',
                'periode_bulan' => date('n'),
                'periode_tahun' => date('Y'),
                'jumlah' => 100000, // Get from settings
                'metode_pembayaran' => 'transfer',
                'bukti_pembayaran' => 'uploads/payments/' . $buktiName,
                'tanggal_pembayaran' => date('Y-m-d H:i:s'),
                'status_pembayaran' => 'pending'
            ];

            $this->paymentModel->insert($paymentData);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Gagal menyimpan data registrasi');
            }

            // Send verification email
            $this->emailService->sendVerificationEmail(
                $this->request->getPost('email'),
                $memberData['nama_lengkap'],
                $verificationToken
            );

            // Send notification to admin
            $this->emailService->sendNewMemberNotification($memberId);

            session()->setFlashdata('success', 'Registrasi berhasil! Silakan cek email Anda untuk verifikasi.');
            return redirect()->to('/login');
        } catch (\Exception $e) {
            $this->db->transRollback();

            // Clean up uploaded files
            if (isset($fotoName) && file_exists(ROOTPATH . 'public/uploads/photos/' . $fotoName)) {
                unlink(ROOTPATH . 'public/uploads/photos/' . $fotoName);
            }
            if (isset($buktiName) && file_exists(ROOTPATH . 'public/uploads/payments/' . $buktiName)) {
                unlink(ROOTPATH . 'public/uploads/payments/' . $buktiName);
            }

            log_message('error', 'Registration error: ' . $e->getMessage());

            return redirect()->back()->withInput()
                ->with('error', 'Terjadi kesalahan saat registrasi. Silakan coba lagi.');
        }
    }

    /**
     * Generate unique file name
     */
    private function generateFileName($file, $prefix = '')
    {
        $extension = $file->getExtension();
        return $prefix . '_' . date('YmdHis') . '_' . uniqid() . '.' . $extension;
    }

    /**
     * Generate unique username
     */
    private function generateUsername($nama)
    {
        $base = strtolower(str_replace(' ', '', $nama));
        $username = $base;
        $counter = 1;

        while ($this->userModel->where('username', $username)->first()) {
            $username = $base . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Generate transaction number
     */
    private function generateTransactionNumber($memberId)
    {
        return 'TRX-' . date('YmdHis') . '-' . str_pad($memberId, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Normalize phone number
     */
    private function normalizePhoneNumber($phone)
    {
        // Remove non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Convert to 62 format
        if (substr($phone, 0, 1) == '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 1) != '6') {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
