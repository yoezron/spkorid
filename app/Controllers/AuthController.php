<?php
// ============================================
// AUTHENTICATION CONTROLLERS
// ============================================

// app/Controllers/AuthController.php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\MemberModel;
use App\Models\ActivityLogModel;

class AuthController extends BaseController
{
    protected $userModel;
    protected $memberModel;
    protected $activityLog;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->memberModel = new MemberModel();
        $this->activityLog = new ActivityLogModel();
        $this->session = \Config\Services::session();
    }

    /**
     * Display login page
     */
    public function login()
    {
        if ($this->session->get('logged_in')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login', ['title' => 'Login - SPK']);
    }

    /**
     * Process login
     */
    public function attemptLogin()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[8]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $result = $this->userModel->authenticate($email, $password);

        if ($result === false) {
            return redirect()->back()->withInput()
                ->with('error', 'Email atau password salah');
        }

        if (isset($result['error'])) {
            return redirect()->back()->withInput()
                ->with('error', $result['error']);
        }

        // Get user details
        $userDetails = $this->userModel->getUserWithDetails($result['id']);

        // Set session data
        $sessionData = [
            'user_id' => $result['id'],
            'member_id' => $result['member_id'],
            'email' => $result['email'],
            'username' => $result['username'],
            'role_id' => $result['role_id'],
            'role_name' => $userDetails['role_name'],
            'nama_lengkap' => $userDetails['nama_lengkap'],
            'foto_path' => $userDetails['foto_path'],
            'logged_in' => true
        ];

        $this->session->set($sessionData);

        // Log activity
        $this->activityLog->logActivity($result['id'], 'login', 'User logged in');

        // Redirect based on role
        switch ($result['role_id']) {
            case 1: // Super Admin
                return redirect()->to('/admin/dashboard');
            case 2: // Pengurus
                return redirect()->to('/pengurus/dashboard');
            case 3: // Anggota
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
        $userId = $this->session->get('user_id');

        if ($userId) {
            $this->activityLog->logActivity($userId, 'logout', 'User logged out');
        }

        $this->session->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah berhasil logout');
    }

    /**
     * Forgot password page
     */
    public function forgotPassword()
    {
        return view('auth/forgot_password', ['title' => 'Lupa Password - SPK']);
    }

    /**
     * Process forgot password
     */
    public function sendResetLink()
    {
        $rules = [
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Email tidak terdaftar');
        }

        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+2 hours'));

        // Save token
        $this->userModel->update($user['id'], [
            'reset_token' => $token,
            'reset_token_expires' => $expiry
        ]);

        // Send email (implement email sending)
        $this->sendPasswordResetEmail($email, $token);

        return redirect()->back()->with('success', 'Link reset password telah dikirim ke email Anda');
    }

    /**
     * Reset password page
     */
    public function resetPassword($token)
    {
        $user = $this->userModel->where('reset_token', $token)
            ->where('reset_token_expires >', date('Y-m-d H:i:s'))
            ->first();

        if (!$user) {
            return redirect()->to('/login')->with('error', 'Token tidak valid atau sudah kadaluarsa');
        }

        return view('auth/reset_password', [
            'title' => 'Reset Password - SPK',
            'token' => $token
        ]);
    }

    /**
     * Process reset password
     */
    public function updatePassword()
    {
        $rules = [
            'token' => 'required',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('reset_token', $token)
            ->where('reset_token_expires >', date('Y-m-d H:i:s'))
            ->first();

        if (!$user) {
            return redirect()->to('/login')->with('error', 'Token tidak valid atau sudah kadaluarsa');
        }

        // Update password
        $this->userModel->update($user['id'], [
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'reset_token' => null,
            'reset_token_expires' => null
        ]);

        return redirect()->to('/login')->with('success', 'Password berhasil direset. Silakan login dengan password baru');
    }

    /**
     * Send password reset email
     */
    private function sendPasswordResetEmail($email, $token)
    {
        $emailService = \Config\Services::email();
        $resetLink = base_url('reset-password/' . $token);

        $emailService->setFrom('noreply@spk.org', 'SPK Indonesia');
        $emailService->setTo($email);
        $emailService->setSubject('Reset Password - SPK');

        $message = view('emails/reset_password', [
            'reset_link' => $resetLink
        ]);

        $emailService->setMessage($message);
        $emailService->send();
    }
}
