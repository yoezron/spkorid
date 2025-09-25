<?php
// app/Libraries/Authentication.php
namespace App\Libraries;

use App\Models\UserModel;
use App\Models\MemberModel;
use App\Models\ActivityLogModel;
use Config\Services;

class Authentication
{
    protected $userModel;
    protected $memberModel;
    protected $activityLog;
    protected $session;
    protected $config;

    // Security constants
    const MAX_LOGIN_ATTEMPTS = 5;
    const LOCKOUT_DURATION = 1800; // 30 minutes in seconds
    const TOKEN_EXPIRY = 3600; // 1 hour for email verification
    const RESET_TOKEN_EXPIRY = 7200; // 2 hours for password reset

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->memberModel = new MemberModel();
        $this->activityLog = new ActivityLogModel();
        $this->session = Services::session();
        $this->config = config('App');
    }

    /**
     * Authenticate user with enhanced security
     */
    public function attemptLogin(string $email, string $password, bool $remember = false): array
    {
        try {
            // Get user by email
            $user = $this->userModel->where('email', $email)->first();

            if (!$user) {
                $this->logActivity(null, 'login_failed', ['reason' => 'user_not_found', 'email' => $email]);
                return [
                    'success' => false,
                    'message' => 'Email atau password salah'
                ];
            }

            // Check if account is locked
            if ($this->isAccountLocked($user)) {
                $remainingTime = $this->getRemainingLockTime($user);
                return [
                    'success' => false,
                    'message' => "Akun terkunci. Silakan coba lagi dalam {$remainingTime} menit.",
                    'locked' => true
                ];
            }

            // Verify password
            if (!password_verify($password, $user['password'])) {
                $this->handleFailedLogin($user['id']);
                return [
                    'success' => false,
                    'message' => 'Email atau password salah'
                ];
            }

            // Check account status
            if (!$user['is_active']) {
                return [
                    'success' => false,
                    'message' => 'Akun Anda tidak aktif. Silakan hubungi administrator.'
                ];
            }

            if (!$user['is_verified']) {
                return [
                    'success' => false,
                    'message' => 'Email belum diverifikasi. Silakan cek email Anda.',
                    'unverified' => true,
                    'user_id' => $user['id']
                ];
            }

            // Login successful - reset attempts and create session
            $this->resetLoginAttempts($user['id']);
            $this->createUserSession($user, $remember);
            $this->updateLastLogin($user['id']);
            $this->logActivity($user['id'], 'login_success');

            return [
                'success' => true,
                'message' => 'Login berhasil',
                'redirect' => $this->getRedirectUrl($user['role_id'])
            ];
        } catch (\Exception $e) {
            log_message('error', 'Login error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ];
        }
    }

    /**
     * Create user session with complete data
     */
    protected function createUserSession(array $user, bool $remember = false): void
    {
        // Get user details with role and member info
        $userDetails = $this->userModel->getUserWithDetails($user['id']);

        $sessionData = [
            'user_id' => $user['id'],
            'member_id' => $user['member_id'],
            'email' => $user['email'],
            'username' => $user['username'],
            'role_id' => $user['role_id'],
            'role_name' => $userDetails['role_name'] ?? 'member',
            'nama_lengkap' => $userDetails['nama_lengkap'] ?? '',
            'nomor_anggota' => $userDetails['nomor_anggota'] ?? '',
            'foto_path' => $userDetails['foto_path'] ?? null,
            'logged_in' => true,
            'login_time' => time()
        ];

        $this->session->set($sessionData);

        // Handle remember me functionality
        if ($remember) {
            $this->setRememberMeCookie($user['id']);
        }

        // Regenerate session ID for security
        $this->session->regenerate();
    }

    /**
     * Handle failed login attempt
     */
    protected function handleFailedLogin(int $userId): void
    {
        $user = $this->userModel->find($userId);
        $attempts = ($user['login_attempts'] ?? 0) + 1;

        $updateData = ['login_attempts' => $attempts];

        // Lock account after max attempts
        if ($attempts >= self::MAX_LOGIN_ATTEMPTS) {
            $updateData['locked_until'] = date('Y-m-d H:i:s', time() + self::LOCKOUT_DURATION);
            $this->logActivity($userId, 'account_locked', ['attempts' => $attempts]);
        }

        $this->userModel->update($userId, $updateData);
        $this->logActivity($userId, 'login_failed', ['attempt' => $attempts]);
    }

    /**
     * Check if account is locked
     */
    protected function isAccountLocked(array $user): bool
    {
        if (!isset($user['locked_until']) || $user['locked_until'] === null) {
            return false;
        }

        return strtotime($user['locked_until']) > time();
    }

    /**
     * Get remaining lock time in minutes
     */
    protected function getRemainingLockTime(array $user): int
    {
        if (!isset($user['locked_until'])) {
            return 0;
        }

        $remaining = strtotime($user['locked_until']) - time();
        return max(1, ceil($remaining / 60));
    }

    /**
     * Reset login attempts
     */
    protected function resetLoginAttempts(int $userId): void
    {
        $this->userModel->update($userId, [
            'login_attempts' => 0,
            'locked_until' => null
        ]);
    }

    /**
     * Update last login timestamp
     */
    protected function updateLastLogin(int $userId): void
    {
        $this->userModel->update($userId, [
            'last_login' => date('Y-m-d H:i:s'),
            'last_activity' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get redirect URL based on role
     */
    protected function getRedirectUrl(int $roleId): string
    {
        switch ($roleId) {
            case 1: // Super Admin
                return base_url('dashboard');
            case 2: // Pengurus
                return base_url('pengurus/dashboard');
            case 3: // Member
            default:
                return base_url('member/profile');
        }
    }

    /**
     * Set remember me cookie
     */
    protected function setRememberMeCookie(int $userId): void
    {
        helper('cookie'); // Load cookie helper
        $token = bin2hex(random_bytes(32));
        $expire = time() + (30 * 24 * 60 * 60); // 30 days

        // Store token in database
        $this->userModel->update($userId, [
            'remember_token' => hash('sha256', $token),
            'remember_expires' => date('Y-m-d H:i:s', $expire)
        ]);

        // Set cookie
        set_cookie([
            'name' => 'remember_me',
            'value' => $userId . ':' . $token,
            'expire' => $expire,
            'httponly' => true,
            'secure' => true,
            'samesite' => 'Lax'
        ]);
    }

    /**
     * Check remember me cookie
     */
    public function checkRememberMe(): bool
    {
        helper('cookie'); // Load cookie helper
        $cookie = get_cookie('remember_me');

        if (!$cookie) {
            return false;
        }

        list($userId, $token) = explode(':', $cookie, 2);
        $user = $this->userModel->find($userId);

        if (!$user || !isset($user['remember_token'])) {
            return false;
        }

        // Verify token
        if (!hash_equals($user['remember_token'], hash('sha256', $token))) {
            return false;
        }

        // Check expiry
        if (strtotime($user['remember_expires']) < time()) {
            return false;
        }

        // Auto login
        $this->createUserSession($user);
        $this->updateLastLogin($user['id']);

        return true;
    }

    /**
     * Logout user
     */
    public function logout(): void
    {
        $userId = $this->session->get('user_id');

        if ($userId) {
            // Clear remember me token
            $this->userModel->update($userId, [
                'remember_token' => null,
                'remember_expires' => null
            ]);

            $this->logActivity($userId, 'logout');
        }

        // Destroy session
        $this->session->destroy();

        // Delete remember me cookie
        helper('cookie'); // Load cookie helper
        delete_cookie('remember_me');
    }

    /**
     * Generate verification token
     */
    public function generateVerificationToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Verify email token
     */
    public function verifyEmailToken(string $token): array
    {
        $user = $this->userModel->where('verification_token', $token)
            ->where('is_verified', 0)
            ->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Token tidak valid atau sudah kadaluarsa'
            ];
        }

        // Check token expiry (24 hours)
        $tokenAge = time() - strtotime($user['created_at']);
        if ($tokenAge > 86400) {
            return [
                'success' => false,
                'message' => 'Token sudah kadaluarsa. Silakan minta token baru.'
            ];
        }

        // Verify email
        $this->userModel->update($user['id'], [
            'is_verified' => 1,
            'is_active' => 1,
            'email_verified_at' => date('Y-m-d H:i:s'),
            'verification_token' => null
        ]);

        // Update member status
        if ($user['member_id']) {
            $this->memberModel->update($user['member_id'], [
                'status_keanggotaan' => 'active',
                'tanggal_verifikasi' => date('Y-m-d H:i:s')
            ]);
        }

        $this->logActivity($user['id'], 'email_verified');

        return [
            'success' => true,
            'message' => 'Email berhasil diverifikasi. Silakan login.'
        ];
    }

    /**
     * Generate password reset token
     */
    public function generateResetToken(string $email): array
    {
        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            // Don't reveal if email exists
            return [
                'success' => true,
                'message' => 'Jika email terdaftar, link reset password akan dikirim.'
            ];
        }

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + self::RESET_TOKEN_EXPIRY);

        $this->userModel->update($user['id'], [
            'reset_token' => hash('sha256', $token),
            'reset_token_expires' => $expires
        ]);

        $this->logActivity($user['id'], 'password_reset_requested');

        return [
            'success' => true,
            'token' => $token,
            'user' => $user,
            'message' => 'Link reset password telah dikirim ke email Anda.'
        ];
    }

    /**
     * Reset password with token
     */
    public function resetPasswordWithToken(string $token, string $newPassword): array
    {
        $hashedToken = hash('sha256', $token);
        $user = $this->userModel->where('reset_token', $hashedToken)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Token tidak valid'
            ];
        }

        // Check token expiry
        if (strtotime($user['reset_token_expires']) < time()) {
            return [
                'success' => false,
                'message' => 'Token sudah kadaluarsa'
            ];
        }

        // Update password
        $this->userModel->update($user['id'], [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            'reset_token' => null,
            'reset_token_expires' => null,
            'login_attempts' => 0,
            'locked_until' => null
        ]);

        $this->logActivity($user['id'], 'password_reset_success');

        return [
            'success' => true,
            'message' => 'Password berhasil direset. Silakan login dengan password baru.'
        ];
    }

    /**
     * Check if user has permission
     */
    public function hasPermission(string $permission): bool
    {
        $roleId = $this->session->get('role_id');

        if (!$roleId) {
            return false;
        }

        // Super admin has all permissions
        if ($roleId == 1) {
            return true;
        }

        // Check specific permission in database
        $db = \Config\Database::connect();
        $builder = $db->table('role_permissions');
        $result = $builder->where('role_id', $roleId)
            ->where('permission', $permission)
            ->where('is_allowed', 1)
            ->countAllResults();

        return $result > 0;
    }

    /**
     * Log user activity
     */
    protected function logActivity(?int $userId, string $type, array $data = []): void
    {
        try {
            $this->activityLog->insert([
                'user_id' => $userId,
                'activity_type' => $type,
                'activity_details' => json_encode($data),
                'ip_address' => $this->getClientIP(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to log activity: ' . $e->getMessage());
        }
    }

    /**
     * Get client IP address
     */
    protected function getClientIP(): string
    {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];

        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                $ip = $_SERVER[$key];
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
