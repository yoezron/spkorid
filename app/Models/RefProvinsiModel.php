<?php

namespace App\Models;

use CodeIgniter\Model;

class RefProvinsiModel extends Model
{
    protected $table = 'ref_provinsi';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_provinsi', 'kode_provinsi', 'is_active'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    public function getActiveProvinsi()
    {
        return $this->where('is_active', 1)
            ->orderBy('nama_provinsi', 'ASC')
            ->findAll();
    }
}
