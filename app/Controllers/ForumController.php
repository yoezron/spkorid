<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ForumCategoryModel;
use App\Models\ForumThreadModel;
use App\Models\ForumReplyModel;

class ForumController extends BaseController
{
    protected $categoryModel;
    protected $threadModel;
    protected $replyModel;

    public function __construct()
    {
        // Pastikan Anda sudah membuat ketiga model ini
        $this->categoryModel = new ForumCategoryModel();
        $this->threadModel = new ForumThreadModel();
        $this->replyModel = new ForumReplyModel();
    }

    /**
     * Menampilkan halaman utama forum (daftar kategori beserta statistik).
     */
    public function index()
    {
        $data = [
            'title'      => 'Forum Diskusi Anggota',
            'categories' => $this->categoryModel->getCategoriesWithStats()->paginate(10),
            'pager'      => $this->categoryModel->pager,
        ];

        return view('member/forum/index', $data);
    }

    /**
     * Menampilkan daftar thread dalam satu kategori berdasarkan SLUG.
     * @param string $slug
     */
    public function category($slug)
    {
        $category = $this->categoryModel->where('slug', $slug)->first();

        if (!$category) {
            return redirect()->to('member/forum')->with('error', 'Kategori forum tidak ditemukan.');
        }

        $data = [
            'title'    => 'Forum: ' . esc($category['name']),
            'category' => $category,
            'threads'  => $this->threadModel->getThreadsByCategory($category['id']),
            'pager'    => $this->threadModel->pager,
        ];

        return view('member/forum/category', $data);
    }

    /**
     * Menampilkan satu thread diskusi beserta balasannya.
     * @param int $threadId
     */
    public function thread($threadId)
    {
        $thread = $this->threadModel->getThreadWithDetails($threadId);

        if (!$thread) {
            return redirect()->to('member/forum')->with('error', 'Diskusi tidak ditemukan.');
        }

        $data = [
            'title'   => esc($thread['title']),
            'thread'  => $thread,
            'replies' => $this->replyModel->getRepliesByThread($threadId), // Asumsikan method ini ada
            'pager'   => $this->replyModel->pager
        ];

        return view('member/forum/thread', $data);
    }

    /**
     * Menampilkan form untuk membuat thread baru.
     */
    public function createThread()
    {
        $categories = $this->categoryModel->orderBy('name', 'ASC')->findAll();

        if (empty($categories)) {
            return redirect()->to('member/forum')->with('error', 'Belum ada kategori forum yang tersedia. Tidak dapat membuat diskusi baru.');
        }

        $data = [
            'title'      => 'Buat Diskusi Baru',
            'categories' => $categories
        ];
        return view('member/forum/create_thread', $data);
    }

    /**
     * Menyimpan thread baru ke database.
     */
    public function storeThread()
    {
        $rules = [
            'judul'       => 'required|min_length[5]|max_length[255]',
            'isi'         => 'required|min_length[10]',
            'category_id' => 'required|is_natural_no_zero|is_not_unique[forum_categories.id]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'title'       => $this->request->getPost('judul'),
            'content'     => $this->request->getPost('isi'),
            'category_id' => $this->request->getPost('category_id'),
            // PERBAIKAN: Menggunakan 'user_id' sesuai skema database
            'user_id'     => session()->get('user_id'),
            'slug'        => url_title($this->request->getPost('judul'), '-', true) . '-' . time()
        ];

        $this->threadModel->save($data);

        $category = $this->categoryModel->find($data['category_id']);
        $slug = $category['slug'] ?? '';

        return redirect()->to('member/forum/category/' . $slug)->with('success', 'Diskusi baru berhasil dipublikasikan.');
    }

    /**
     * Menyimpan balasan baru pada sebuah thread.
     */
    public function storeReply($threadId)
    {
        if (!$this->threadModel->find($threadId)) {
            return redirect()->back()->with('error', 'Diskusi tidak ditemukan.');
        }

        $rules = [
            'content' => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getError('content'));
        }

        $data = [
            'content'   => $this->request->getPost('content'),
            'thread_id' => $threadId,
            // PERBAIKAN: Menggunakan 'user_id' sesuai skema database
            'user_id'   => session()->get('user_id'),
        ];

        $this->replyModel->save($data);

        $this->threadModel->update($threadId, ['updated_at' => date('Y-m-d H:i:s')]);

        return redirect()->to('member/forum/thread/' . $threadId)->with('success', 'Balasan berhasil dikirim.');
    }
}
