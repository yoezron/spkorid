<?php

// ============================================
// MODEL UNTUK CMS & ACTIVITY LOG
// ============================================

// app/Models/CMSPageModel.php
namespace App\Models;

use CodeIgniter\Model;

class CMSPageModel extends Model
{
    protected $table = 'cms_pages';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'page_title',
        'page_slug',
        'page_content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_published',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $beforeInsert = ['generateSlug'];
    protected $beforeUpdate = ['generateSlug'];

    protected function generateSlug(array $data)
    {
        if (isset($data['data']['page_title']) && !isset($data['data']['page_slug'])) {
            $data['data']['page_slug'] = url_title($data['data']['page_title'], '-', true);
        }
        return $data;
    }

    // Get page by slug
    public function getPageBySlug($slug)
    {
        return $this->where('page_slug', $slug)
            ->where('is_published', 1)
            ->first();
    }

    // Get published pages
    public function getPublishedPages()
    {
        return $this->select('id, page_title, page_slug')
            ->where('is_published', 1)
            ->findAll();
    }
}
