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
    protected $allowedFields    = ['name', 'slug', 'description'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Mengambil data kategori beserta statistik jumlah thread.
     *
     * @return \CodeIgniter\Database\BaseBuilder
     */
    public function getCategoriesWithStats()
    {
        // PERBAIKAN: Mengganti 'kategori_id' menjadi 'category_id' yang benar
        return $this->select('forum_categories.*, COUNT(forum_threads.id) as thread_count')
            ->join('forum_threads', 'forum_threads.category_id = forum_categories.id', 'left')
            ->groupBy('forum_categories.id')
            ->orderBy('forum_categories.name', 'ASC');
    }
}
