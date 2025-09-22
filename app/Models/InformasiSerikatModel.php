<?php
// ============================================
// MODEL UNTUK CONTENT & INFORMASI
// ============================================

// app/Models/InformasiSerikatModel.php
namespace App\Models;

use CodeIgniter\Model;

class InformasiSerikatModel extends Model
{
    protected $table = 'informasi_serikat';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'judul',
        'slug',
        'konten',
        'kategori',
        'status',
        'featured_image',
        'view_count',
        'created_by',
        'published_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $beforeInsert = ['generateSlug'];
    protected $beforeUpdate = ['generateSlug'];

    protected function generateSlug(array $data)
    {
        if (isset($data['data']['judul'])) {
            $data['data']['slug'] = url_title($data['data']['judul'], '-', true);
        }
        return $data;
    }
}
