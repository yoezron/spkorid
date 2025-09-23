<?php

// ============================================
// FORUM CONTROLLERS
// ============================================

// app/Controllers/ForumController.php
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
        $this->categoryModel = new ForumCategoryModel();
        $this->threadModel = new ForumThreadModel();
        $this->replyModel = new ForumReplyModel();
    }

    /**
     * Forum index - show categories
     */
    public function index()
    {
        $data = [
            'title' => 'Forum Diskusi - SPK',
            'categories' => $this->categoryModel->getCategoriesWithCount(),
            'recent_threads' => $this->threadModel->getThreads(null, 10),
            'popular_threads' => $this->threadModel->getPopularThreads(5)
        ];

        return view('forum/index', $data);
    }

    /**
     * View category threads
     */
    public function category($slug)
    {
        $category = $this->categoryModel->where('slug', $slug)->first();

        if (!$category) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Kategori tidak ditemukan');
        }

        $data = [
            'title' => $category['name'] . ' - Forum SPK',
            'category' => $category,
            'threads' => $this->threadModel->getThreads($category['id']),
            'pager' => $this->threadModel->pager
        ];

        return view('forum/category', $data);
    }

    /**
     * View thread
     */
    public function thread($id)
    {
        $thread = $this->threadModel->getThreadWithDetails($id);

        if (!$thread) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Thread tidak ditemukan');
        }

        $data = [
            'title' => $thread['title'] . ' - Forum SPK',
            'thread' => $thread,
            'replies' => $this->replyModel->getThreadReplies($id)
        ];

        return view('forum/thread', $data);
    }

    /**
     * Create new thread
     */
    public function createThread()
    {
        $data = [
            'title' => 'Buat Thread Baru - Forum SPK',
            'categories' => $this->categoryModel->where('is_active', 1)->findAll()
        ];

        return view('forum/create_thread', $data);
    }

    /**
     * Store new thread
     */
    public function storeThread()
    {
        $rules = [
            'category_id' => 'required|numeric',
            'title' => 'required|min_length[5]|max_length[255]',
            'content' => 'required|min_length[20]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $threadData = [
            'category_id' => $this->request->getPost('category_id'),
            'user_id' => session()->get('user_id'),
            'title' => $this->request->getPost('title'),
            'content' => $this->request->getPost('content')
        ];

        $threadId = $this->threadModel->insert($threadData);

        return redirect()->to('/forum/thread/' . $threadId)->with('success', 'Thread berhasil dibuat');
    }

    /**
     * Reply to thread
     */
    public function reply($threadId)
    {
        $thread = $this->threadModel->find($threadId);

        if (!$thread || $thread['is_locked']) {
            return redirect()->back()->with('error', 'Thread tidak dapat dibalas');
        }

        $rules = [
            'content' => 'required|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $replyData = [
            'thread_id' => $threadId,
            'user_id' => session()->get('user_id'),
            'content' => $this->request->getPost('content'),
            'reply_to_id' => $this->request->getPost('reply_to_id') ?? null
        ];

        $this->replyModel->addReply($replyData);

        return redirect()->to('/forum/thread/' . $threadId . '#latest')
            ->with('success', 'Balasan berhasil ditambahkan');
    }

    /**
     * Edit reply
     */
    public function editReply($id)
    {
        $reply = $this->replyModel->find($id);

        // Check ownership
        if ($reply['user_id'] != session()->get('user_id')) {
            return redirect()->back()->with('error', 'Anda tidak dapat mengedit balasan ini');
        }

        $content = $this->request->getPost('content');

        $this->replyModel->update($id, [
            'content' => $content,
            'is_edited' => 1,
            'edited_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->back()->with('success', 'Balasan berhasil diperbarui');
    }

    /**
     * Search forum
     */
    public function search()
    {
        $keyword = $this->request->getGet('q');

        $data = [
            'title' => 'Pencarian Forum: ' . $keyword,
            'threads' => $this->threadModel->searchThreads($keyword),
            'keyword' => $keyword
        ];

        return view('forum/search', $data);
    }
}
