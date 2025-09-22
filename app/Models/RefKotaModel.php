<?php

namespace App\Models;

use CodeIgniter\Model;

class RefKotaModel extends Model
{
    protected $table = 'ref_kota';
    protected $primaryKey = 'id';
    protected $allowedFields = ['provinsi_id', 'nama_kota', 'kode_kota', 'is_active'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    public function getKotaByProvinsi($provinsiId)
    {
        return $this->where('provinsi_id', $provinsiId)
            ->where('is_active', 1)
            ->orderBy('nama_kota', 'ASC')
            ->findAll();
    }

    public function getKotaWithProvinsi($kotaId)
    {
        return $this->select('ref_kota.*, ref_provinsi.nama_provinsi')
            ->join('ref_provinsi', 'ref_provinsi.id = ref_kota.provinsi_id')
            ->where('ref_kota.id', $kotaId)
            ->first();
    }
}
