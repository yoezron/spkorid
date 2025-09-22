<?php
// app/Controllers/RegistrationController.php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\UserModel;
use App\Models\PaymentHistoryModel;
use App\Models\RefStatusKepegawaianModel;
use App\Models\RefPemberiGajiModel;
use App\Models\RefRangeGajiModel;
use App\Models\RefProvinsiModel;
use App\Models\RefKotaModel;
use App\Models\RefJenisPTModel;
use App\Models\RefKampusModel;
use App\Models\RefProdiModel;

class RegistrationController extends BaseController
{
    protected $memberModel;
    protected $userModel;
    protected $paymentModel;
    protected $validation;
    protected $email;
    protected $db;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->userModel = new UserModel();
        $this->paymentModel = new PaymentHistoryModel();
        $this->validation = \Config\Services::validation();
        $this->email = \Config\Services::email();
        $this->db = \Config\Database::connect();
    }

    /**
     * Display registration form
     */
    public function index()
    {
        // Load all reference data for dropdowns
        $data = [
            'title' => 'Registrasi Anggota SPK',
            'status_kepegawaian' => (new RefStatusKepegawaianModel())->where('is_active', 1)->findAll(),
            'pemberi_gaji' => (new RefPemberiGajiModel())->where('is_active', 1)->findAll(),
            'range_gaji' => (new RefRangeGajiModel())->where('is_active', 1)->findAll(),
            'provinsi' => (new RefProvinsiModel())->where('is_active', 1)->findAll(),
            'jenis_pt' => (new RefJenisPTModel())->where('is_active', 1)->findAll(),
        ];

        return view('registration/form', $data);
    }

    /**
     * Get cities by province (AJAX)
     */
    public function getCities($provinsi_id)
    {
        $kotaModel = new RefKotaModel();
        $cities = $kotaModel->where('provinsi_id', $provinsi_id)
            ->where('is_active', 1)
            ->findAll();

        return $this->response->setJSON($cities);
    }

    /**
     * Get kampus by jenis PT (AJAX)
     */
    public function getKampus($jenis_pt_id)
    {
        $kampusModel = new RefKampusModel();
        $kampus = $kampusModel->where('jenis_pt_id', $jenis_pt_id)
            ->where('is_active', 1)
            ->findAll();

        return $this->response->setJSON($kampus);
    }

    /**
     * Get prodi by kampus (AJAX)
     */
    public function getProdi($kampus_id)
    {
        $prodiModel = new RefProdiModel();
        $prodi = $prodiModel->where('kampus_id', $kampus_id)
            ->where('is_active', 1)
            ->findAll();

        return $this->response->setJSON($prodi);
    }

    /**
     * Process registration form
     */
    public function register()
    {
        // Validation rules
        $rules = [
            'nama_lengkap' => [
                'rules' => 'required|min_length[3]|max_length[200]',
                'errors' => [
                    'required' => 'Nama lengkap wajib diisi',
                    'min_length' => 'Nama lengkap minimal 3 karakter',
                    'max_length' => 'Nama lengkap maksimal 200 karakter'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[members.email]|is_unique[users.email]',
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
                    'matches' => 'Konfirmasi password tidak cocok'
                ]
            ],
            'jenis_kelamin' => [
                'rules' => 'required|in_list[Laki-laki,Perempuan]',
                'errors' => [
                    'required' => 'Jenis kelamin wajib dipilih',
                    'in_list' => 'Jenis kelamin tidak valid'
                ]
            ],
            'alamat_lengkap' => [
                'rules' => 'required|min_length[10]',
                'errors' => [
                    'required' => 'Alamat lengkap wajib diisi',
                    'min_length' => 'Alamat lengkap minimal 10 karakter'
                ]
            ],
            'nomor_whatsapp' => [
                'rules' => 'required|regex_match[/^(\+62|62|0)[0-9]{9,12}$/]',
                'errors' => [
                    'required' => 'Nomor WhatsApp wajib diisi',
                    'regex_match' => 'Format nomor WhatsApp tidak valid'
                ]
            ],
            'status_kepegawaian_id' => [
                'rules' => 'required|is_natural_no_zero',
                'errors' => ['required' => 'Status kepegawaian wajib dipilih']
            ],
            'pemberi_gaji_id' => [
                'rules' => 'required|is_natural_no_zero',
                'errors' => ['required' => 'Pemberi gaji wajib dipilih']
            ],
            'range_gaji_id' => [
                'rules' => 'required|is_natural_no_zero',
                'errors' => ['required' => 'Range gaji wajib dipilih']
            ],
            'gaji_pokok' => [
                'rules' => 'required|numeric|greater_than[0]',
                'errors' => [
                    'required' => 'Gaji pokok wajib diisi',
                    'numeric' => 'Gaji pokok harus berupa angka',
                    'greater_than' => 'Gaji pokok harus lebih dari 0'
                ]
            ],
            'provinsi_id' => [
                'rules' => 'required|is_natural_no_zero',
                'errors' => ['required' => 'Provinsi wajib dipilih']
            ],
            'kota_id' => [
                'rules' => 'required|is_natural_no_zero',
                'errors' => ['required' => 'Kota/Kabupaten wajib dipilih']
            ],
            'jenis_pt_id' => [
                'rules' => 'required|is_natural_no_zero',
                'errors' => ['required' => 'Jenis perguruan tinggi wajib dipilih']
            ],
            'kampus_id' => [
                'rules' => 'required|is_natural_no_zero',
                'errors' => ['required' => 'Kampus wajib dipilih']
            ],
            'prodi_id' => [
                'rules' => 'required|is_natural_no_zero',
                'errors' => ['required' => 'Program studi wajib dipilih']
            ],
            'bidang_keahlian' => [
                'rules' => 'required|min_length[5]',
                'errors' => [
                    'required' => 'Bidang keahlian wajib diisi',
                    'min_length' => 'Bidang keahlian minimal 5 karakter'
                ]
            ],
            'motivasi_berserikat' => [
                'rules' => 'required|min_length[20]',
                'errors' => [
                    'required' => 'Motivasi berserikat wajib diisi',
                    'min_length' => 'Motivasi berserikat minimal 20 karakter'
                ]
            ],
            'foto' => [
                'rules' => 'uploaded[foto]|is_image[foto]|mime_in[foto,image/jpg,image/jpeg,image/png]|max_size[foto,2048]',
                'errors' => [
                    'uploaded' => 'Foto wajib diupload',
                    'is_image' => 'File harus berupa gambar',
                    'mime_in' => 'Format foto harus JPG, JPEG, atau PNG',
                    'max_size' => 'Ukuran foto maksimal 2MB'
                ]
            ],
            'bukti_pembayaran' => [
                'rules' => 'uploaded[bukti_pembayaran]|mime_in[bukti_pembayaran,image/jpg,image/jpeg,image/png,application/pdf]|max_size[bukti_pembayaran,5120]',
                'errors' => [
                    'uploaded' => 'Bukti pembayaran wajib diupload',
                    'mime_in' => 'Format bukti pembayaran harus JPG, JPEG, PNG, atau PDF',
                    'max_size' => 'Ukuran bukti pembayaran maksimal 5MB'
                ]
            ]
        ];

        // Validate
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validation->getErrors());
        }

        // Start transaction
        $this->db->transStart();

        try {
            // Handle file uploads
            $foto = $this->request->getFile('foto');
            $buktiPembayaran = $this->request->getFile('bukti_pembayaran');

            // Generate unique filenames
            $fotoName = $foto->getRandomName();
            $buktiName = $buktiPembayaran->getRandomName();

            // Move uploaded files
            $foto->move(ROOTPATH . 'public/uploads/photos', $fotoName);
            $buktiPembayaran->move(ROOTPATH . 'public/uploads/payments', $buktiName);

            // Generate verification token
            $verificationToken = bin2hex(random_bytes(32));

            // Prepare member data
            $memberData = [
                'nama_lengkap' => $this->request->getPost('nama_lengkap'),
                'email' => $this->request->getPost('email'),
                'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
                'alamat_lengkap' => $this->request->getPost('alamat_lengkap'),
                'nomor_whatsapp' => $this->request->getPost('nomor_whatsapp'),
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
                'bidang_keahlian' => $this->request->getPost('bidang_keahlian'),
                'motivasi_berserikat' => $this->request->getPost('motivasi_berserikat'),
                'media_sosial' => $this->request->getPost('media_sosial'),
                'foto_path' => 'uploads/photos/' . $fotoName,
                'bukti_pembayaran_path' => 'uploads/payments/' . $buktiName,
                'status_keanggotaan' => 'pending'
            ];

            // Insert member
            $memberId = $this->memberModel->insert($memberData);

            // Create user account
            $userData = [
                'member_id' => $memberId,
                'username' => strtolower(str_replace(' ', '', $this->request->getPost('nama_lengkap'))),
                'email' => $this->request->getPost('email'),
                'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                'role_id' => 3, // Default role: anggota
                'is_active' => 0,
                'is_verified' => 0,
                'verification_token' => $verificationToken
            ];

            $userId = $this->userModel->insert($userData);

            // Create payment record
            $paymentData = [
                'member_id' => $memberId,
                'nomor_transaksi' => 'TRX-' . date('YmdHis') . '-' . $memberId,
                'jenis_pembayaran' => 'iuran_pertama',
                'jumlah' => 100000, // Default first payment amount
                'metode_pembayaran' => 'transfer',
                'bukti_pembayaran' => 'uploads/payments/' . $buktiName,
                'tanggal_pembayaran' => date('Y-m-d H:i:s'),
                'status_pembayaran' => 'pending'
            ];

            $this->paymentModel->insert($paymentData);

            // Send verification email
            $this->sendVerificationEmail(
                $this->request->getPost('email'),
                $this->request->getPost('nama_lengkap'),
                $verificationToken
            );

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Terjadi kesalahan saat menyimpan data');
            }

            // Set success message
            session()->setFlashdata('success', 'Registrasi berhasil! Silakan cek email Anda untuk verifikasi akun.');
            return redirect()->to('/login');
        } catch (\Exception $e) {
            $this->db->transRollback();

            // Delete uploaded files if exists
            if (isset($fotoName) && file_exists(ROOTPATH . 'public/uploads/photos/' . $fotoName)) {
                unlink(ROOTPATH . 'public/uploads/photos/' . $fotoName);
            }
            if (isset($buktiName) && file_exists(ROOTPATH . 'public/uploads/payments/' . $buktiName)) {
                unlink(ROOTPATH . 'public/uploads/payments/' . $buktiName);
            }

            session()->setFlashdata('error', 'Registrasi gagal: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Send verification email
     */
    private function sendVerificationEmail($email, $name, $token)
    {
        $verificationLink = base_url('verify-email/' . $token);

        $this->email->setFrom('noreply@spk.org', 'SPK - Serikat Pekerja Kampus');
        $this->email->setTo($email);
        $this->email->setSubject('Verifikasi Email - SPK');

        $message = view('emails/verification', [
            'name' => $name,
            'verification_link' => $verificationLink
        ]);

        $this->email->setMessage($message);
        $this->email->send();
    }

    /**
     * Verify email
     */
    public function verifyEmail($token)
    {
        $user = $this->userModel->where('verification_token', $token)
            ->where('is_verified', 0)
            ->first();

        if (!$user) {
            session()->setFlashdata('error', 'Token verifikasi tidak valid atau sudah kadaluarsa.');
            return redirect()->to('/login');
        }

        // Update user verification status
        $this->userModel->update($user['id'], [
            'is_verified' => 1,
            'email_verified_at' => date('Y-m-d H:i:s'),
            'verification_token' => null
        ]);

        session()->setFlashdata('success', 'Email berhasil diverifikasi! Silakan tunggu konfirmasi dari pengurus.');
        return redirect()->to('/login');
    }
}
