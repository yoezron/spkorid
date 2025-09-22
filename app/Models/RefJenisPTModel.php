<?php
// app/Models/RefJenisPTModel.php
namespace App\Models;

use CodeIgniter\Model;

class RefJenisPTModel extends Model
{
    protected $table = 'ref_jenis_pt';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_jenis', 'keterangan', 'is_active'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    public function getActiveJenisPT()
    {
        return $this->where('is_active', 1)
            ->orderBy('nama_jenis', 'ASC')
            ->findAll();
    }
}
