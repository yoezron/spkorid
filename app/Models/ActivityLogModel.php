<?php
// app/Models/ActivityLogModel.php
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
        'user_agent'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

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

    // Get user activities
    public function getUserActivities($userId, $limit = 50)
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    // Get recent activities
    public function getRecentActivities($limit = 100)
    {
        return $this->select('activity_logs.*, users.username, members.nama_lengkap')
            ->join('users', 'users.id = activity_logs.user_id')
            ->join('members', 'members.id = users.member_id', 'left')
            ->orderBy('activity_logs.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
