<?php
// ============================================
// MODEL UNTUK USER & AUTHENTICATION
// ============================================

// app/Models/UserModel.php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'member_id',
        'username',
        'email',
        'password',
        'role_id',
        'is_active',
        'is_verified',
        'email_verified_at',
        'verification_token',
        'reset_token',
        'reset_token_expires',
        'last_login',
        'login_attempts',
        'locked_until'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username,id,{id}]',
        'password' => 'required|min_length[8]',
        'role_id' => 'required|numeric'
    ];

    protected $validationMessages = [
        'email' => [
            'required' => 'Email wajib diisi',
            'valid_email' => 'Format email tidak valid',
            'is_unique' => 'Email sudah terdaftar'
        ],
        'username' => [
            'required' => 'Username wajib diisi',
            'min_length' => 'Username minimal 3 karakter',
            'is_unique' => 'Username sudah digunakan'
        ]
    ];

    // Get user with role and member details
    public function getUserWithDetails($userId)
    {
        return $this->select('users.*, roles.role_name, roles.role_description, 
                             members.nama_lengkap, members.nomor_anggota, members.foto_path')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->join('members', 'members.id = users.member_id', 'left')
            ->where('users.id', $userId)
            ->first();
    }

    // Authenticate user
    public function authenticate($email, $password)
    {
        $user = $this->where('email', $email)->first();

        if (!$user) {
            return false;
        }

        // Check if account is locked
        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            return ['error' => 'Account locked. Please try again later.'];
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            // Increment login attempts
            $this->incrementLoginAttempts($user['id']);
            return false;
        }

        // Check if account is active and verified
        if (!$user['is_active'] || !$user['is_verified']) {
            return ['error' => 'Account not active or not verified.'];
        }

        // Reset login attempts and update last login
        $this->update($user['id'], [
            'login_attempts' => 0,
            'last_login' => date('Y-m-d H:i:s'),
            'locked_until' => null
        ]);

        return $user;
    }

    // Increment login attempts
    private function incrementLoginAttempts($userId)
    {
        $user = $this->find($userId);
        $attempts = $user['login_attempts'] + 1;

        $updateData = ['login_attempts' => $attempts];

        // Lock account after 5 failed attempts
        if ($attempts >= 5) {
            $updateData['locked_until'] = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        }

        $this->update($userId, $updateData);
    }

    // Get users by role
    public function getUsersByRole($roleId)
    {
        return $this->select('users.*, members.nama_lengkap, members.nomor_anggota')
            ->join('members', 'members.id = users.member_id', 'left')
            ->where('users.role_id', $roleId)
            ->findAll();
    }

    // Add these methods
    public function getUserWithRole($id)
    {
        return $this->select('users.*, roles.role_name, roles.role_slug')
            ->join('roles', 'roles.id = users.role_id')
            ->find($id);
    }

    public function attemptLogin($email, $password)
    {
        // Implementation with brute force protection
    }
}
