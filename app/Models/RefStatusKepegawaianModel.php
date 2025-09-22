<?php

// ============================================
// MODEL UNTUK REFERENSI/MASTER DATA
// ============================================

// app/Models/RefStatusKepegawaianModel.php
namespace App\Models;

use CodeIgniter\Model;

class RefStatusKepegawaianModel extends Model
{
    protected $table = 'ref_status_kepegawaian';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_status', 'keterangan', 'is_active'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getActiveStatus()
    {
        return $this->where('is_active', 1)
            ->orderBy('nama_status', 'ASC')
            ->findAll();
    }
}
