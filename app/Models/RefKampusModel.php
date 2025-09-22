<?php

namespace App\Models;

use CodeIgniter\Model;

class RefKampusModel extends Model
{
    protected $table = 'ref_kampus';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'jenis_pt_id',
        'kode_pt',
        'nama_kampus',
        'alamat',
        'kota_id',
        'website',
        'is_active'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getKampusByJenisPT($jenisPtId)
    {
        return $this->where('jenis_pt_id', $jenisPtId)
            ->where('is_active', 1)
            ->orderBy('nama_kampus', 'ASC')
            ->findAll();
    }

    public function getKampusWithDetails($kampusId)
    {
        return $this->select('ref_kampus.*, ref_jenis_pt.nama_jenis, ref_kota.nama_kota, ref_provinsi.nama_provinsi')
            ->join('ref_jenis_pt', 'ref_jenis_pt.id = ref_kampus.jenis_pt_id', 'left')
            ->join('ref_kota', 'ref_kota.id = ref_kampus.kota_id', 'left')
            ->join('ref_provinsi', 'ref_provinsi.id = ref_kota.provinsi_id', 'left')
            ->where('ref_kampus.id', $kampusId)
            ->first();
    }

    public function searchKampus($keyword)
    {
        return $this->like('nama_kampus', $keyword)
            ->orLike('kode_pt', $keyword)
            ->where('is_active', 1)
            ->findAll();
    }
}
