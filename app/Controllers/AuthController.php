<?php
// app/Controllers/AuthController.php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\Authentication;
use App\Libraries\EmailService;

class AuthController extends BaseController
{
    protected $auth;
    protected $emailService;
    protected $session;

    public function __construct()
    {
        $this->auth = new Authentication();
        $this->emailService = new EmailService();
        $this->session = \Config\Services::session();
    }

    /**
     * Display login page
     */
    public function login()
    {
        // Redirect if already logged in
        if ($this->session->get('logged_in')) {
            return redirect()->to($this->getRedirectUrl());
        }

        return view('auth/login', [
            'title' => 'Login - Serikat Pekerja Kampus'
        ]);
    }

    /**
     * Process login attempt
     */
    public function attemptLogin()
    {
        // Validate input
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[8]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember') ? true : false;

        // Attempt login using Authentication library
        $result = $this->auth->attemptLogin($email, $password, $remember);

        if ($result['success']) {
            // Login successful
            session()->setFlashdata('success', $result['message']);
            return redirect()->to($result['redirect']);
        } else {
            // Login failed
            if (isset($result['locked']) && $result['locked']) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', $result['message']);
            }

            if (isset($result['unverified']) && $result['unverified']) {
                // Store user_id for resend verification
                session()->setFlashdata('unverified_user_id', $result['user_id']);
                return redirect()->to('/verify-reminder')
                    ->with('warning', $result['message']);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', $result['message']);
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->auth->logout();
        session()->setFlashdata('success', 'Anda telah berhasil logout.');
        return redirect()->to('/login');
    }

    /**
     * Display forgot password page
     */
    public function forgotPassword()
    {
        return view('auth/forgot_password', [
            'title' => 'Lupa Password - Serikat Pekerja Kampus'
        ]);
    }

    /**
     * Send password reset link
     */
    public function sendResetLink()
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

        // Generate reset token
        $result = $this->auth->generateResetToken($email);

        if ($result['success'] && isset($result['user'])) {
            // Send reset email
            $emailSent = $this->emailService->sendPasswordResetEmail(
                $result['user'],
                $result['token']
            );

            if ($emailSent) {
                session()->setFlashdata('success', 'Link reset password telah dikirim ke email Anda.');
            } else {
                session()->setFlashdata('error', 'Gagal mengirim email. Silakan coba lagi.');
            }
        } else {
            // Always show success message for security (don't reveal if email exists)
            session()->setFlashdata('success', 'Jika email terdaftar, link reset password akan dikirim.');
        }

        return redirect()->to('/forgot-password');
    }

    /**
     * Display reset password form
     */
    public function resetPassword($token)
    {
        return view('auth/reset_password', [
            'title' => 'Reset Password - Serikat Pekerja Kampus',
            'token' => $token
        ]);
    }

    /**
     * Update password with token
     */
    public function updatePassword()
    {
        $rules = [
            'token' => 'required',
            'password' => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]',
            'password_confirm' => 'required|matches[password]'
        ];

        $errors = [
            'password' => [
                'regex_match' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan karakter khusus'
            ]
        ];

        if (!$this->validate($rules, $errors)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $token = $this->request->getPost('token');
        $newPassword = $this->request->getPost('password');

        // Reset password using token
        $result = $this->auth->resetPasswordWithToken($token, $newPassword);

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
            return redirect()->to('/login');
        } else {
            session()->setFlashdata('error', $result['message']);
            return redirect()->back();
        }
    }

    /**
     * Verify email with token
     */
    public function verifyEmail($token)
    {
        if (empty($token)) {
            session()->setFlashdata('error', 'Token verifikasi tidak valid.');
            return redirect()->to('/login');
        }

        // Verify the token
        $result = $this->auth->verifyEmailToken($token);

        if ($result['success']) {
            session()->setFlashdata('success', $result['message']);
            return redirect()->to('/login');
        } else {
            session()->setFlashdata('error', $result['message']);
            return redirect()->to('/register');
        }
    }

    /**
     * Display verify reminder page
     */
    public function verifyReminder()
    {
        $unverifiedUserId = session()->getFlashdata('unverified_user_id');

        return view('auth/verify_reminder', [
            'title' => 'Verifikasi Email - Serikat Pekerja Kampus',
            'user_id' => $unverifiedUserId
        ]);
    }

    /**
     * Resend verification email
     */
    public function resendVerification()
    {
        $userId = $this->request->getPost('user_id');

        if (!$userId) {
            session()->setFlashdata('error', 'User ID tidak valid.');
            return redirect()->to('/login');
        }

        // Get user data
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        if (!$user || $user['is_verified']) {
            session()->setFlashdata('error', 'User tidak ditemukan atau sudah terverifikasi.');
            return redirect()->to('/login');
        }

        // Generate new verification token
        $token = $this->auth->generateVerificationToken();
        $userModel->update($userId, ['verification_token' => $token]);

        // Get member data
        $memberModel = new \App\Models\MemberModel();
        $member = $memberModel->find($user['member_id']);

        // Send verification email
        $emailSent = $this->emailService->sendVerificationEmail(
            [
                'email' => $user['email'],
                'nama_lengkap' => $member['nama_lengkap'] ?? 'User'
            ],
            $token
        );

        if ($emailSent) {
            session()->setFlashdata('success', 'Email verifikasi telah dikirim ulang. Silakan cek inbox Anda.');
        } else {
            session()->setFlashdata('error', 'Gagal mengirim email verifikasi. Silakan coba lagi.');
        }

        return redirect()->to('/verify-reminder');
    }

    /**
     * Get redirect URL based on user role
     */
    protected function getRedirectUrl()
    {
        $roleId = $this->session->get('role_id');

        switch ($roleId) {
            case 1: // Super Admin
                return '/admin/dashboard';
            case 2: // Pengurus
                return '/pengurus/dashboard';
            case 3: // Member
            default:
                return '/member/profile';
        }
    }
}
