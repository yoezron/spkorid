<?php
// ============================================
// PERBAIKAN AuthController.php
// ============================================
// Path: app/Controllers/AuthController.php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\MemberModel;

class AuthController extends BaseController
{
    protected $userModel;
    protected $memberModel;
    protected $validation;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->memberModel = new MemberModel();
        $this->validation = \Config\Services::validation();
    }

    /**
     * Display login form
     */
    public function login()
    {
        // PERBAIKAN: Gunakan key session yang konsisten
        if (session()->get('logged_in')) {
            return $this->redirectBasedOnRole();
        }

        $data = [
            'title' => 'Login - SPK'
        ];

        return view('auth/login', $data);
    }

    /**
     * Process login attempt
     */
    public function attemptLogin()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Get user from database WITH ROLE
        $user = $this->userModel->getUserByEmailWithRole($email);

        // Check if user exists
        if (!$user) {
            session()->setFlashdata('error', 'Email tidak ditemukan.');
            return redirect()->back()->withInput();
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            session()->setFlashdata('error', 'Password salah.');
            return redirect()->back()->withInput();
        }

        // Check if email is verified
        if (!$user['is_verified']) {
            session()->setFlashdata('error', 'Akun Anda belum diverifikasi. Silakan cek email untuk link verifikasi.');
            return redirect()->back()->withInput();
        }

        // Check if account is active
        if (!$user['is_active']) {
            session()->setFlashdata('error', 'Akun Anda belum diaktifkan atau telah dinonaktifkan. Hubungi pengurus untuk informasi lebih lanjut.');
            return redirect()->back()->withInput();
        }

        // Set session data - GUNAKAN KEY YANG KONSISTEN
        $this->setUserSession($user);

        // Update last login
        $this->userModel->update($user['id'], [
            'last_login' => date('Y-m-d H:i:s'),
            'login_attempts' => 0
        ]);

        // Redirect based on role - LANGSUNG RETURN REDIRECT
        return $this->redirectBasedOnRole();
    }

    /**
     * Set user session data - PERBAIKAN: Gunakan key yang konsisten
     */
    private function setUserSession($user)
    {
        $sessionData = [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'username' => $user['username'] ?? $user['email'],
            'nama_lengkap' => $user['nama_lengkap'] ?? 'User',
            'role_id' => $user['role_id'],
            'role_name' => $user['role_name'] ?? 'member',
            'foto' => $user['foto'] ?? 'default.png',
            'member_id' => $user['member_id'],
            'logged_in' => true  // KUNCI UTAMA: Gunakan 'logged_in' bukan 'isLoggedIn'
        ];

        session()->set($sessionData);
        session()->regenerate(); // Security: Regenerate session ID
    }

    /**
     * Redirect based on user role - PERBAIKAN: Gunakan role_id, bukan role_name
     */
    private function redirectBasedOnRole()
    {
        $roleId = session()->get('role_id');

        // PERBAIKAN: Redirect berdasarkan role_id yang lebih reliable
        switch ($roleId) {
            case 1: // Super Admin
                return redirect()->to('/dashboard');

            case 2: // Pengurus
                return redirect()->to('/pengurus/dashboard');

            case 3: // Member/Anggota
            default:
                return redirect()->to('/member/profile');
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        // Clear all session data
        session()->destroy();

        // Set success message
        session()->setFlashdata('success', 'Anda telah berhasil logout.');

        return redirect()->to('/login');
    }

    /**
     * Forgot password form
     */
    public function forgotPassword()
    {
        $data = [
            'title' => 'Lupa Password - SPK'
        ];

        return view('auth/forgot_password', $data);
    }

    /**
     * Process forgot password
     */
    public function processForgotPassword()
    {
        $rules = [
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            // Tetap tampilkan pesan sukses untuk keamanan (mencegah email enumeration)
            session()->setFlashdata('success', 'Jika email terdaftar, link reset password telah dikirim.');
            return redirect()->to('/login');
        }

        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Update user dengan reset token
        $this->userModel->update($user['id'], [
            'reset_token' => $token,
            'reset_token_expiry' => $expiry
        ]);

        // Send email (implementasi email service)
        $this->sendResetEmail($user['email'], $token);

        session()->setFlashdata('success', 'Link reset password telah dikirim ke email Anda.');
        return redirect()->to('/login');
    }

    /**
     * Reset password form
     */
    public function resetPassword($token = null)
    {
        if (!$token) {
            session()->setFlashdata('error', 'Token tidak valid.');
            return redirect()->to('/login');
        }

        // Verify token exists and not expired
        $user = $this->userModel->where('reset_token', $token)
            ->where('reset_token_expiry >', date('Y-m-d H:i:s'))
            ->first();

        if (!$user) {
            session()->setFlashdata('error', 'Token tidak valid atau sudah kadaluarsa.');
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'Reset Password - SPK',
            'token' => $token
        ];

        return view('auth/reset_password', $data);
    }

    /**
     * Process reset password
     */
    public function processResetPassword()
    {
        $rules = [
            'token' => 'required',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors());
        }

        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('reset_token', $token)
            ->where('reset_token_expiry >', date('Y-m-d H:i:s'))
            ->first();

        if (!$user) {
            session()->setFlashdata('error', 'Token tidak valid atau sudah kadaluarsa.');
            return redirect()->to('/login');
        }

        // Update password and clear token
        $this->userModel->update($user['id'], [
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'reset_token' => null,
            'reset_token_expiry' => null
        ]);

        session()->setFlashdata('success', 'Password berhasil direset. Silakan login dengan password baru.');
        return redirect()->to('/login');
    }

    /**
     * Send reset email (stub - implement dengan Email Service)
     */
    private function sendResetEmail($email, $token)
    {
        // TODO: Implement email sending
        // Gunakan CodeIgniter Email class atau service seperti PHPMailer
        $resetLink = base_url("reset-password/{$token}");

        // Log untuk development
        log_message('info', "Reset password link for {$email}: {$resetLink}");
    }
}
