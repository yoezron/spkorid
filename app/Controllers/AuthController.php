<?php

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
        // Jika sudah login, redirect sesuai role
        if (session()->get('isLoggedIn')) {
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
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Check user credentials
        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            session()->setFlashdata('error', 'Email tidak terdaftar.');
            return redirect()->back()->withInput();
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            session()->setFlashdata('error', 'Password salah.');
            return redirect()->back()->withInput();
        }

        // Check if account is active
        if ($user['status'] !== 'active') {
            session()->setFlashdata('error', 'Akun Anda belum aktif atau telah dinonaktifkan.');
            return redirect()->back()->withInput();
        }

        // Set session data
        $this->setUserSession($user);

        // Log activity
        $this->logActivity('login', 'User logged in: ' . $user['email']);

        session()->setFlashdata('success', 'Selamat datang, ' . $user['nama_lengkap'] . '!');

        return $this->redirectBasedOnRole();
    }

    /**
     * Set user session data
     */
    private function setUserSession($user)
    {
        $sessionData = [
            'user_id' => $user['id'],
            'id' => $user['id'], // For backward compatibility
            'email' => $user['email'],
            'username' => $user['username'] ?? $user['email'],
            'nama_lengkap' => $user['nama_lengkap'],
            'role' => $user['role'],
            'isLoggedIn' => true
        ];

        // If user is a member, add member data
        $member = $this->memberModel->where('user_id', $user['id'])->first();
        if ($member) {
            $sessionData['member_id'] = $member['id'];
            $sessionData['member'] = [
                'id' => $member['id'],
                'nomor_anggota' => $member['nomor_anggota'],
                'status_keanggotaan' => $member['status_keanggotaan']
            ];
        }

        session()->set($sessionData);
    }

    /**
     * Redirect based on user role
     */
    private function redirectBasedOnRole()
    {
        $role = session()->get('role');

        switch ($role) {
            case 'super_admin':
                return redirect()->to('/admin/dashboard');
            case 'pengurus':
                return redirect()->to('/pengurus/dashboard');
            case 'member':
                return redirect()->to('/member/dashboard');
            default:
                return redirect()->to('/dashboard');
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
     * Display forgot password form
     */
    public function forgotPassword()
    {
        $data = [
            'title' => 'Lupa Password - SPK'
        ];

        return view('auth/forgot_password', $data);
    }

    /**
     * Process forgot password request
     */
    public function processForgotPassword()
    {
        $rules = [
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            session()->setFlashdata('error', 'Email tidak terdaftar.');
            return redirect()->back()->withInput();
        }

        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Save token to database
        $this->userModel->update($user['id'], [
            'reset_token' => $token,
            'reset_token_expiry' => $expiry
        ]);

        // Send reset email
        $this->sendResetEmail($email, $token);

        session()->setFlashdata('success', 'Link reset password telah dikirim ke email Anda.');
        return redirect()->to('/login');
    }

    /**
     * Send password reset email
     */
    private function sendResetEmail($email, $token)
    {
        $resetLink = base_url('reset-password/' . $token);

        $emailService = \Config\Services::email();
        $emailService->setTo($email);
        $emailService->setSubject('Reset Password - SPK');
        $emailService->setMessage("
            <h2>Reset Password</h2>
            <p>Klik link berikut untuk reset password Anda:</p>
            <p><a href='{$resetLink}'>{$resetLink}</a></p>
            <p>Link ini akan kadaluarsa dalam 1 jam.</p>
            <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
        ");

        $emailService->send();
    }

    /**
     * Display reset password form
     */
    public function resetPassword($token)
    {
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
}
