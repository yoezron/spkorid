<?php

namespace App\Models;

use CodeIgniter\Model;

class ForumCategoryModel extends Model
{
    protected $table = 'forum_categories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'description', 'slug', 'order_priority', 'is_active'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $beforeInsert = ['generateSlug'];
    protected $beforeUpdate = ['generateSlug'];

    protected function generateSlug(array $data)
    {
        if (isset($data['data']['name']) && !isset($data['data']['slug'])) {
            $data['data']['slug'] = url_title($data['data']['name'], '-', true);
        }
        return $data;
    }

    // Get active categories with thread count
    public function getCategoriesWithCount()
    {
        $db = \Config\Database::connect();

        return $db->table('forum_categories fc')
            ->select('fc.*, COUNT(ft.id) as thread_count')
            ->join('forum_threads ft', 'ft.category_id = fc.id', 'left')
            ->where('fc.is_active', 1)
            ->groupBy('fc.id')
            ->orderBy('fc.order_priority', 'ASC')
            ->get()
            ->getResultArray();
    }
}
