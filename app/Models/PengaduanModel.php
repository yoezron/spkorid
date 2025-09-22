<?php

// app/Models/PengaduanModel.php
namespace App\Models;

use CodeIgniter\Model;

class PengaduanModel extends Model
{
    protected $table = 'pengaduan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'ticket_number',
        'nama_pelapor',
        'email_pelapor',
        'phone_pelapor',
        'member_id',
        'kategori',
        'subject',
        'deskripsi',
        'lampiran',
        'status',
        'priority',
        'assigned_to',
        'resolved_at',
        'resolution_notes'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $beforeInsert = ['generateTicketNumber'];

    protected function generateTicketNumber(array $data)
    {
        $data['data']['ticket_number'] = 'TKT-' . date('Ymd') . '-' . strtoupper(substr(md5(time()), 0, 6));
        return $data;
    }

    // Get pengaduan by status
    public function getPengaduanByStatus($status)
    {
        return $this->select('pengaduan.*, members.nama_lengkap as member_name')
            ->join('members', 'members.id = pengaduan.member_id', 'left')
            ->where('pengaduan.status', $status)
            ->orderBy('pengaduan.created_at', 'DESC')
            ->findAll();
    }

    // Get pengaduan assigned to user
    public function getAssignedPengaduan($userId)
    {
        return $this->where('assigned_to', $userId)
            ->whereIn('status', ['open', 'in_progress'])
            ->orderBy('priority', 'DESC')
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }

    // Update pengaduan status
    public function updateStatus($pengaduanId, $status, $notes = '', $userId = null)
    {
        $updateData = ['status' => $status];

        if ($status === 'resolved' || $status === 'closed') {
            $updateData['resolved_at'] = date('Y-m-d H:i:s');
            $updateData['resolution_notes'] = $notes;
        }

        if ($status === 'in_progress' && $userId) {
            $updateData['assigned_to'] = $userId;
        }

        return $this->update($pengaduanId, $updateData);
    }

    // Get pengaduan statistics
    public function getPengaduanStatistics()
    {
        $db = \Config\Database::connect();

        return [
            'total' => $this->countAll(),
            'open' => $this->where('status', 'open')->countAllResults(),
            'in_progress' => $this->where('status', 'in_progress')->countAllResults(),
            'resolved' => $this->where('status', 'resolved')->countAllResults(),
            'closed' => $this->where('status', 'closed')->countAllResults(),
            'by_category' => $db->table('pengaduan')
                ->select('kategori, COUNT(id) as total')
                ->groupBy('kategori')
                ->get()
                ->getResultArray(),
            'by_priority' => $db->table('pengaduan')
                ->select('priority, COUNT(id) as total')
                ->where('status !=', 'closed')
                ->groupBy('priority')
                ->get()
                ->getResultArray()
        ];
    }
}
