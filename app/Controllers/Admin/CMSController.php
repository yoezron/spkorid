<?php
// app/Controllers/Admin/CMSController.php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CMSPageModel;

class CMSController extends BaseController
{
    protected $cmsModel;

    public function __construct()
    {
        $this->cmsModel = new CMSPageModel();
    }

    /**
     * List all CMS pages
     */
    public function index()
    {
        $data = [
            'title' => 'Content Management - SPK',
            'pages' => $this->cmsModel->findAll()
        ];

        return view('admin/cms/index', $data);
    }

    /**
     * Create new page
     */
    public function create()
    {
        $data = [
            'title' => 'Buat Halaman Baru - SPK'
        ];

        return view('admin/cms/create', $data);
    }

    /**
     * Store new page
     */
    public function store()
    {
        $rules = [
            'page_title' => 'required|min_length[3]|max_length[255]',
            'page_content' => 'required|min_length[50]',
            'meta_title' => 'max_length[255]',
            'meta_description' => 'max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $pageData = [
            'page_title' => $this->request->getPost('page_title'),
            'page_content' => $this->request->getPost('page_content'),
            'meta_title' => $this->request->getPost('meta_title'),
            'meta_description' => $this->request->getPost('meta_description'),
            'meta_keywords' => $this->request->getPost('meta_keywords'),
            'is_published' => $this->request->getPost('is_published') ? 1 : 0,
            'created_by' => session()->get('user_id'),
            'updated_by' => session()->get('user_id')
        ];

        $this->cmsModel->insert($pageData);

        return redirect()->to('/admin/content')->with('success', 'Halaman berhasil dibuat');
    }

    /**
     * Edit page
     */
    public function edit($id)
    {
        $page = $this->cmsModel->find($id);

        if (!$page) {
            return redirect()->back()->with('error', 'Halaman tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Halaman - SPK',
            'page' => $page
        ];

        return view('admin/cms/edit', $data);
    }

    /**
     * Update page
     */
    public function update($id)
    {
        $rules = [
            'page_title' => 'required|min_length[3]|max_length[255]',
            'page_content' => 'required|min_length[50]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'page_title' => $this->request->getPost('page_title'),
            'page_content' => $this->request->getPost('page_content'),
            'meta_title' => $this->request->getPost('meta_title'),
            'meta_description' => $this->request->getPost('meta_description'),
            'meta_keywords' => $this->request->getPost('meta_keywords'),
            'is_published' => $this->request->getPost('is_published') ? 1 : 0,
            'updated_by' => session()->get('user_id')
        ];

        $this->cmsModel->update($id, $updateData);

        return redirect()->to('/admin/content')->with('success', 'Halaman berhasil diperbarui');
    }

    /**
     * Delete page
     */
    public function delete($id)
    {
        $this->cmsModel->delete($id);
        return redirect()->to('/admin/content')->with('success', 'Halaman berhasil dihapus');
    }

    /**
     * Toggle publish status
     */
    public function togglePublish($id)
    {
        $page = $this->cmsModel->find($id);

        $this->cmsModel->update($id, [
            'is_published' => !$page['is_published']
        ]);

        return redirect()->back()->with('success', 'Status publikasi berhasil diubah');
    }
}
