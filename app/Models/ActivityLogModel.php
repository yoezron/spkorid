<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id',
        'activity_type',
        'activity_description',
        'ip_address',
        'user_agent' // Hapus 'created_at' dari sini
    ];

    // --- PERUBAHAN DI BAWAH INI ---
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // Kosongkan ini agar tidak mencari kolom updated_at

    // Log activity
    public function logActivity($userId, $type, $description)
    {
        $request = \Config\Services::request();

        return $this->insert([
            'user_id' => $userId,
            'activity_type' => $type,
            'activity_description' => $description,
            'ip_address' => $request->getIPAddress(),
            'user_agent' => $request->getUserAgent()->getAgentString()
        ]);
    }
}
