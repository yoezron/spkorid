<?php

namespace App\Models;

use CodeIgniter\Model;

class RefPemberiGajiModel extends Model
{
    protected $table = 'ref_pemberi_gaji';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_pemberi', 'keterangan', 'is_active'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getActivePemberiGaji()
    {
        return $this->where('is_active', 1)
            ->orderBy('nama_pemberi', 'ASC')
            ->findAll();
    }
}
