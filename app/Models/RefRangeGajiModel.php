<?php

namespace App\Models;

use CodeIgniter\Model;

class RefRangeGajiModel extends Model
{
    protected $table = 'ref_range_gaji';
    protected $primaryKey = 'id';
    protected $allowedFields = ['range_gaji', 'min_gaji', 'max_gaji', 'is_active'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getActiveRanges()
    {
        return $this->where('is_active', 1)
            ->orderBy('min_gaji', 'ASC')
            ->findAll();
    }
}
