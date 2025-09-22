<?php

// app/Models/RefProdiModel.php
namespace App\Models;

use CodeIgniter\Model;

class RefProdiModel extends Model
{
    protected $table = 'ref_prodi';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'kampus_id',
        'kode_prodi',
        'nama_prodi',
        'jenjang',
        'is_active'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'nama_prodi' => 'required|min_length[3]',
        'jenjang' => 'required|in_list[D3,D4,S1,S2,S3,Profesi,Spesialis]'
    ];

    public function getProdiByKampus($kampusId)
    {
        return $this->where('kampus_id', $kampusId)
            ->where('is_active', 1)
            ->orderBy('jenjang', 'ASC')
            ->orderBy('nama_prodi', 'ASC')
            ->findAll();
    }

    public function getProdiWithKampus($prodiId)
    {
        return $this->select('ref_prodi.*, ref_kampus.nama_kampus')
            ->join('ref_kampus', 'ref_kampus.id = ref_prodi.kampus_id')
            ->where('ref_prodi.id', $prodiId)
            ->first();
    }
}
