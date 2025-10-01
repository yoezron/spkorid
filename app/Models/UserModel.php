<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'member_id',
        'username',
        'nama_lengkap',
        'foto',
        'status_kepegawaian',
        'asal_kampus',
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
        'last_activity',
        'login_attempts',
        'locked_until',
        'remember_token',
        'remember_expires'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    /**
     * Get user with role information
     */
    public function getUserWithRole($userId)
    {
        return $this->select('users.*, roles.role_name, roles.id as role_id')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('users.id', $userId)
            ->first();
    }

    /**
     * Get user by email with role information
     * PERBAIKAN: Gunakan roles.role_name bukan roles.name
     */
    public function getUserByEmailWithRole($email)
    {
        return $this->select('users.*, roles.role_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('users.email', $email)
            ->first();
    }

    /**
     * Get complete user data with member and role information
     */
    public function getUserComplete($userId)
    {
        return $this->select('users.*, roles.role_name, members.nama_lengkap as member_name, members.nomor_anggota')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->join('members', 'members.id = users.member_id', 'left')
            ->where('users.id', $userId)
            ->first();
    }

    /**
     * Check if user is locked due to failed login attempts
     */
    public function isLocked($userId)
    {
        $user = $this->find($userId);

        if (!$user || !$user['locked_until']) {
            return false;
        }

        return strtotime($user['locked_until']) > time();
    }

    /**
     * Reset login attempts
     */
    public function resetLoginAttempts($userId)
    {
        return $this->update($userId, [
            'login_attempts' => 0,
            'locked_until' => null
        ]);
    }

    /**
     * Increment login attempts
     */
    public function incrementLoginAttempts($userId, $maxAttempts = 5, $lockoutTime = 900)
    {
        $user = $this->find($userId);

        if (!$user) {
            return false;
        }

        $attempts = $user['login_attempts'] + 1;
        $updateData = ['login_attempts' => $attempts];

        // Lock account if max attempts reached
        if ($attempts >= $maxAttempts) {
            $updateData['locked_until'] = date('Y-m-d H:i:s', time() + $lockoutTime);
        }

        return $this->update($userId, $updateData);
    }
}
