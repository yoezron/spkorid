<?php

namespace App\Models;

use CodeIgniter\Model;

class ForumCategoryModel extends Model
{
    protected $table            = 'forum_categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'order_position',
        'is_active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[100]',
        'slug' => 'required|alpha_dash|is_unique[forum_categories.slug,id,{id}]',
        'description' => 'max_length[500]',
        'order_position' => 'integer',
        'is_active' => 'in_list[0,1]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Nama kategori harus diisi',
            'min_length' => 'Nama kategori minimal 3 karakter',
            'max_length' => 'Nama kategori maksimal 100 karakter'
        ],
        'slug' => [
            'required' => 'Slug kategori harus diisi',
            'alpha_dash' => 'Slug hanya boleh berisi huruf, angka, dash dan underscore',
            'is_unique' => 'Slug sudah digunakan'
        ]
    ];

    // Callbacks
    protected $beforeInsert = ['generateSlug'];
    protected $beforeUpdate = ['generateSlug'];

    /**
     * Generate slug dari nama jika slug kosong
     */
    protected function generateSlug(array $data)
    {
        if (isset($data['data']['name']) && empty($data['data']['slug'])) {
            $data['data']['slug'] = url_title($data['data']['name'], '-', true);
        }
        return $data;
    }

    /**
     * Mengambil kategori aktif saja
     */
    public function getActiveCategories()
    {
        return $this->where('is_active', 1)
            ->orderBy('order_position', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Mengambil data kategori beserta statistik
     */
    public function getCategoriesWithStats()
    {
        return $this->select('forum_categories.*, 
                             COUNT(DISTINCT forum_threads.id) as thread_count,
                             COUNT(DISTINCT forum_replies.id) as reply_count,
                             MAX(COALESCE(forum_replies.created_at, forum_threads.created_at)) as last_activity')
            ->join('forum_threads', 'forum_threads.category_id = forum_categories.id', 'left')
            ->join('forum_replies', 'forum_replies.thread_id = forum_threads.id', 'left')
            ->where('forum_categories.is_active', 1)
            ->groupBy('forum_categories.id')
            ->orderBy('forum_categories.order_position', 'ASC')
            ->orderBy('forum_categories.name', 'ASC');
    }

    /**
     * Mendapatkan kategori berdasarkan slug
     */
    public function getCategoryBySlug($slug)
    {
        return $this->where('slug', $slug)
            ->where('is_active', 1)
            ->first();
    }

    /**
     * Update statistik kategori (last_activity, thread_count, dll)
     */
    public function updateCategoryStats($categoryId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('forum_threads');

        $stats = $builder->select('COUNT(id) as thread_count, MAX(updated_at) as last_activity')
            ->where('category_id', $categoryId)
            ->get()
            ->getRowArray();

        // Update kolom statistik jika ada di tabel
        // Ini optional, tergantung struktur database
        return true;
    }
}
