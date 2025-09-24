<?php
// app/Models/ActivityLogModel.php
namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'user_id',
        'activity_type',
        'activity_details',
        'ip_address',
        'user_agent',
        'created_at'
    ];

    protected $useTimestamps = false; // We handle timestamps manually

    /**
     * Get user activities
     */
    public function getUserActivities($userId, $limit = 50)
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->find();
    }

    /**
     * Get activities by type
     */
    public function getActivitiesByType($type, $limit = 100)
    {
        return $this->where('activity_type', $type)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->find();
    }

    /**
     * Get failed login attempts
     */
    public function getFailedLogins($timeframe = '-24 hours')
    {
        $since = date('Y-m-d H:i:s', strtotime($timeframe));

        return $this->whereIn('activity_type', ['login_failed', 'account_locked'])
            ->where('created_at >', $since)
            ->orderBy('created_at', 'DESC')
            ->find();
    }

    /**
     * Get suspicious activities
     */
    public function getSuspiciousActivities($timeframe = '-7 days')
    {
        $since = date('Y-m-d H:i:s', strtotime($timeframe));

        return $this->whereIn('activity_type', [
            'login_failed',
            'account_locked',
            'unauthorized_access',
            'throttle_blocked',
            'invalid_token',
            'suspicious_activity'
        ])
            ->where('created_at >', $since)
            ->orderBy('created_at', 'DESC')
            ->find();
    }

    /**
     * Clean old logs
     */
    public function cleanOldLogs($days = 90)
    {
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        return $this->where('created_at <', $cutoff)->delete();
    }

    /**
     * Get activity statistics
     */
    public function getActivityStats($timeframe = '-30 days')
    {
        $since = date('Y-m-d H:i:s', strtotime($timeframe));

        $builder = $this->db->table($this->table);

        return $builder->select('activity_type, COUNT(*) as count')
            ->where('created_at >', $since)
            ->groupBy('activity_type')
            ->orderBy('count', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Log activity with additional context
     */
    public function logActivity($userId, $type, $details = [], $ipAddress = null, $userAgent = null)
    {
        $data = [
            'user_id' => $userId,
            'activity_type' => $type,
            'activity_details' => is_array($details) ? json_encode($details) : $details,
            'ip_address' => $ipAddress ?? service('request')->getIPAddress(),
            'user_agent' => $userAgent ?? service('request')->getUserAgent()->getAgentString(),
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->insert($data);
    }
}
