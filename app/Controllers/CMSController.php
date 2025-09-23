<?php

// ============================================
// CMS CONTROLLER
// ============================================

// app/Controllers/CMSController.php
namespace App\Controllers;

use App\Models\CMSPageModel;

class CMSController extends BaseController
{
    protected $cmsModel;

    public function __construct()
    {
        $this->cmsModel = new CMSPageModel();
    }

    /**
     * Display CMS page
     */
    public function page($slug)
    {
        $page = $this->cmsModel->getPageBySlug($slug);

        if (!$page) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Halaman tidak ditemukan');
        }

        $data = [
            'title' => $page['meta_title'] ?? $page['page_title'],
            'page' => $page,
            'meta_description' => $page['meta_description'],
            'meta_keywords' => $page['meta_keywords']
        ];

        return view('cms/page', $data);
    }
}
