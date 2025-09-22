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

    // Get published information
    public function getPublished($kategori = null, $limit = null)
    {
        $builder = $this->where('status', 'published')
            ->orderBy('published_at', 'DESC');

        if ($kategori) {
            $builder->where('kategori', $kategori);
        }

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    // Get by slug
    public function getBySlug($slug)
    {
        $info = $this->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if ($info) {
            // Increment view count
            $this->update($info['id'], ['view_count' => $info['view_count'] + 1]);
        }

        return $info;
    }

    // Get recent informasi
    public function getRecent($limit = 5)
    {
        return $this->select('judul, slug, kategori, published_at, view_count')
            ->where('status', 'published')
            ->orderBy('published_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    // Get popular informasi
    public function getPopular($limit = 5)
    {
        return $this->select('judul, slug, kategori, view_count, published_at')
            ->where('status', 'published')
            ->orderBy('view_count', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
