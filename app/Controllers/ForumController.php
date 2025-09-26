<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ForumCategoryModel;
use App\Models\ForumThreadModel;
use App\Models\ForumReplyModel;
use App\Models\UserModel;

class ForumController extends BaseController
{
    protected $categoryModel;
    protected $threadModel;
    protected $replyModel;
    protected $userModel;
    protected $helpers = ['text', 'form'];

    public function __construct()
    {
        $this->categoryModel = new ForumCategoryModel();
        $this->threadModel = new ForumThreadModel();
        $this->replyModel = new ForumReplyModel();
        $this->userModel = new UserModel();
    }

    /**
     * Halaman utama forum - menampilkan kategori
     */
    public function index()
    {
        $data = [
            'title'      => 'Forum Diskusi Anggota',
            'categories' => $this->categoryModel->getCategoriesWithStats()->paginate(10),
            'pager'      => $this->categoryModel->pager,
            'recent_threads' => $this->threadModel->getRecentThreads(5),
            'popular_threads' => $this->threadModel->getPopularThreads(5),
            'user_stats' => $this->getUserForumStats()
        ];

        return view('member/forum/index', $data);
    }

    /**
     * Menampilkan threads dalam kategori
     */
    public function category($slug)
    {
        $category = $this->categoryModel->getCategoryBySlug($slug);

        if (!$category) {
            return redirect()->to('member/forum')->with('error', 'Kategori forum tidak ditemukan.');
        }

        $keyword = $this->request->getGet('search');
        $sort = $this->request->getGet('sort') ?? 'latest';

        // Build query - menggunakan getThreadsByCategory dari model
        $perPage = 20;
        $threads = $this->threadModel->getThreadsByCategory($category['id'], $perPage);

        // Apply search
        if ($keyword) {
            $this->threadModel->like('forum_threads.title', $keyword)
                ->orLike('forum_threads.content', $keyword);
        }

        // Apply sorting  
        switch ($sort) {
            case 'popular':
                $this->threadModel->orderBy('forum_threads.views', 'DESC');
                break;
            case 'unanswered':
                $threads = $this->threadModel
                    ->select('forum_threads.*, users.nama_lengkap as author_name, users.foto as author_photo')
                    ->selectRaw('(SELECT COUNT(id) FROM forum_replies WHERE thread_id = forum_threads.id AND deleted_at IS NULL) as reply_count')
                    ->join('users', 'users.id = forum_threads.user_id')
                    ->where('forum_threads.category_id', $category['id'])
                    ->where('forum_threads.deleted_at', null)
                    ->having('reply_count', 0)
                    ->orderBy('forum_threads.created_at', 'DESC')
                    ->paginate($perPage);
                break;
            case 'oldest':
                $this->threadModel->orderBy('forum_threads.created_at', 'ASC');
                break;
            default: // latest
                $this->threadModel->orderBy('forum_threads.is_pinned', 'DESC')
                    ->orderBy('forum_threads.created_at', 'DESC');
        }

        // Get threads if not already fetched (for unanswered case)
        if (!isset($threads) || $sort != 'unanswered') {
            $threads = $this->threadModel->getThreadsByCategory($category['id'], $perPage);
        }

        $data = [
            'title'    => 'Forum: ' . esc($category['name']),
            'category' => $category,
            'threads'  => $threads,
            'pager'    => $this->threadModel->pager,
            'keyword'  => $keyword,
            'sort'     => $sort
        ];

        return view('member/forum/category', $data);
    }

    /**
     * Menampilkan single thread dengan replies
     */
    public function thread($threadId)
    {
        $thread = $this->threadModel->getThreadWithDetails($threadId);

        if (!$thread) {
            return redirect()->to('member/forum')->with('error', 'Diskusi tidak ditemukan.');
        }

        // Check if thread is locked for non-admin users
        $canReply = !$thread['is_locked'] || $this->isAdmin();

        $data = [
            'title'   => esc($thread['title']),
            'thread'  => $thread,
            'replies' => $this->replyModel->getRepliesByThread($threadId, 20),
            'pager'   => $this->replyModel->pager,
            'canReply' => $canReply,
            'isAuthor' => ($thread['user_id'] == session()->get('user_id')),
            'isAdmin' => $this->isAdmin()
        ];

        return view('member/forum/thread', $data);
    }

    /**
     * Form membuat thread baru
     */
    public function createThread()
    {
        $categories = $this->categoryModel->getActiveCategories();

        if (empty($categories)) {
            return redirect()->to('member/forum')
                ->with('error', 'Belum ada kategori forum yang tersedia.');
        }

        $data = [
            'title'      => 'Buat Diskusi Baru',
            'categories' => $categories,
            'validation' => \Config\Services::validation()
        ];

        return view('member/forum/create_thread', $data);
    }

    /**
     * Menyimpan thread baru
     */
    public function storeThread()
    {
        $rules = [
            'title'       => 'required|min_length[5]|max_length[255]',
            'content'     => 'required|min_length[10]',
            'category_id' => 'required|is_natural_no_zero|is_not_unique[forum_categories.id]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'title'       => $this->request->getPost('title'),
            'content'     => $this->request->getPost('content'),
            'category_id' => $this->request->getPost('category_id'),
            'user_id'     => session()->get('user_id'),
            'slug'        => url_title($this->request->getPost('title'), '-', true) . '-' . time()
        ];

        if ($this->threadModel->save($data)) {
            $threadId = $this->threadModel->getInsertID();

            // Send notification to admins
            $this->sendNewThreadNotification($threadId);

            return redirect()->to('member/forum/thread/' . $threadId)
                ->with('success', 'Diskusi baru berhasil dipublikasikan.');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Gagal membuat diskusi baru.');
    }

    /**
     * Edit thread (untuk author dan admin)
     */
    public function editThread($threadId)
    {
        $thread = $this->threadModel->find($threadId);

        if (!$thread) {
            return redirect()->to('member/forum')->with('error', 'Diskusi tidak ditemukan.');
        }

        // Check permission
        if ($thread['user_id'] != session()->get('user_id') && !$this->isAdmin()) {
            return redirect()->to('member/forum/thread/' . $threadId)
                ->with('error', 'Anda tidak memiliki izin untuk mengedit diskusi ini.');
        }

        $data = [
            'title'      => 'Edit Diskusi',
            'thread'     => $thread,
            'categories' => $this->categoryModel->getActiveCategories(),
            'validation' => \Config\Services::validation()
        ];

        return view('member/forum/edit_thread', $data);
    }

    /**
     * Update thread
     */
    public function updateThread($threadId)
    {
        $thread = $this->threadModel->find($threadId);

        if (!$thread) {
            return redirect()->to('member/forum')->with('error', 'Diskusi tidak ditemukan.');
        }

        // Check permission
        if ($thread['user_id'] != session()->get('user_id') && !$this->isAdmin()) {
            return redirect()->to('member/forum/thread/' . $threadId)
                ->with('error', 'Anda tidak memiliki izin untuk mengedit diskusi ini.');
        }

        $rules = [
            'title'       => 'required|min_length[5]|max_length[255]',
            'content'     => 'required|min_length[10]',
            'category_id' => 'required|is_natural_no_zero|is_not_unique[forum_categories.id]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'title'       => $this->request->getPost('title'),
            'content'     => $this->request->getPost('content'),
            'category_id' => $this->request->getPost('category_id')
        ];

        if ($this->threadModel->update($threadId, $data)) {
            return redirect()->to('member/forum/thread/' . $threadId)
                ->with('success', 'Diskusi berhasil diperbarui.');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Gagal memperbarui diskusi.');
    }

    /**
     * Delete thread (soft delete)
     */
    public function deleteThread($threadId)
    {
        $thread = $this->threadModel->find($threadId);

        if (!$thread) {
            return redirect()->to('member/forum')->with('error', 'Diskusi tidak ditemukan.');
        }

        // Check permission
        if ($thread['user_id'] != session()->get('user_id') && !$this->isAdmin()) {
            return redirect()->to('member/forum')
                ->with('error', 'Anda tidak memiliki izin untuk menghapus diskusi ini.');
        }

        if ($this->threadModel->delete($threadId)) {
            $category = $this->categoryModel->find($thread['category_id']);
            return redirect()->to('member/forum/category/' . $category['slug'])
                ->with('success', 'Diskusi berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Gagal menghapus diskusi.');
    }

    /**
     * Menyimpan reply/balasan
     */
    public function storeReply($threadId)
    {
        $thread = $this->threadModel->find($threadId);

        if (!$thread) {
            return redirect()->back()->with('error', 'Diskusi tidak ditemukan.');
        }

        // Check if thread is locked
        if ($thread['is_locked'] && !$this->isAdmin()) {
            return redirect()->back()->with('error', 'Diskusi ini telah dikunci.');
        }

        $rules = [
            'content' => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->validator->getError('content'));
        }

        $data = [
            'content'   => $this->request->getPost('content'),
            'thread_id' => $threadId,
            'user_id'   => session()->get('user_id'),
            'parent_id' => $this->request->getPost('parent_id') ?? null
        ];

        if ($this->replyModel->save($data)) {
            // Update thread last activity
            $this->threadModel->updateLastReply($threadId, session()->get('user_id'));

            // Send notification to thread author
            if ($thread['user_id'] != session()->get('user_id')) {
                $this->sendReplyNotification($threadId, $thread['user_id']);
            }

            return redirect()->to('member/forum/thread/' . $threadId . '#reply-' . $this->replyModel->getInsertID())
                ->with('success', 'Balasan berhasil dikirim.');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Gagal mengirim balasan.');
    }

    /**
     * Edit reply
     */
    public function editReply($replyId)
    {
        $reply = $this->replyModel->find($replyId);

        if (!$reply) {
            return response()->setJSON(['error' => 'Balasan tidak ditemukan'])->setStatusCode(404);
        }

        // Check permission
        if ($reply['user_id'] != session()->get('user_id') && !$this->isAdmin()) {
            return response()->setJSON(['error' => 'Tidak memiliki izin'])->setStatusCode(403);
        }

        if ($this->request->isAJAX()) {
            return response()->setJSON(['content' => $reply['content']]);
        }

        return redirect()->back()->with('error', 'Invalid request');
    }

    /**
     * Update reply
     */
    public function updateReply($replyId)
    {
        $reply = $this->replyModel->find($replyId);

        if (!$reply) {
            return redirect()->back()->with('error', 'Balasan tidak ditemukan.');
        }

        // Check permission
        if ($reply['user_id'] != session()->get('user_id') && !$this->isAdmin()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengedit balasan ini.');
        }

        $rules = [
            'content' => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->validator->getError('content'));
        }

        $data = [
            'content' => $this->request->getPost('content')
        ];

        if ($this->replyModel->update($replyId, $data)) {
            return redirect()->to('member/forum/thread/' . $reply['thread_id'] . '#reply-' . $replyId)
                ->with('success', 'Balasan berhasil diperbarui.');
        }

        return redirect()->back()->with('error', 'Gagal memperbarui balasan.');
    }

    /**
     * Delete reply (soft delete)
     */
    public function deleteReply($replyId)
    {
        $reply = $this->replyModel->find($replyId);

        if (!$reply) {
            return redirect()->back()->with('error', 'Balasan tidak ditemukan.');
        }

        // Check permission
        if ($reply['user_id'] != session()->get('user_id') && !$this->isAdmin()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menghapus balasan ini.');
        }

        if ($this->replyModel->delete($replyId)) {
            return redirect()->to('member/forum/thread/' . $reply['thread_id'])
                ->with('success', 'Balasan berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Gagal menghapus balasan.');
    }

    /**
     * Mark reply as solution
     */
    public function markSolution($replyId)
    {
        $reply = $this->replyModel->find($replyId);

        if (!$reply) {
            return redirect()->back()->with('error', 'Balasan tidak ditemukan.');
        }

        $thread = $this->threadModel->find($reply['thread_id']);

        // Only thread author or admin can mark solution
        if ($thread['user_id'] != session()->get('user_id') && !$this->isAdmin()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menandai solusi.');
        }

        if ($this->replyModel->markAsSolution($replyId)) {
            return redirect()->to('member/forum/thread/' . $reply['thread_id'] . '#reply-' . $replyId)
                ->with('success', 'Balasan ditandai sebagai solusi.');
        }

        return redirect()->back()->with('error', 'Gagal menandai solusi.');
    }

    /**
     * Toggle pin thread (admin only)
     */
    public function togglePin($threadId)
    {
        if (!$this->isAdmin()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin.');
        }

        if ($this->threadModel->togglePin($threadId)) {
            return redirect()->back()->with('success', 'Status pin berhasil diubah.');
        }

        return redirect()->back()->with('error', 'Gagal mengubah status pin.');
    }

    /**
     * Toggle lock thread (admin only)
     */
    public function toggleLock($threadId)
    {
        if (!$this->isAdmin()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin.');
        }

        if ($this->threadModel->toggleLock($threadId)) {
            return redirect()->back()->with('success', 'Status kunci berhasil diubah.');
        }

        return redirect()->back()->with('error', 'Gagal mengubah status kunci.');
    }

    /**
     * Search forum
     */
    public function search()
    {
        $keyword = $this->request->getGet('q');
        $category_id = $this->request->getGet('category');
        $type = $this->request->getGet('type') ?? 'all';

        if (empty($keyword)) {
            return redirect()->to('member/forum');
        }

        $results = [];

        if ($type == 'all' || $type == 'threads') {
            $results['threads'] = $this->threadModel->searchThreads($keyword, $category_id);
        }

        if ($type == 'all' || $type == 'replies') {
            $results['replies'] = $this->replyModel->searchReplies($keyword);
        }

        $data = [
            'title' => 'Hasil Pencarian: ' . esc($keyword),
            'keyword' => $keyword,
            'results' => $results,
            'type' => $type,
            'categories' => $this->categoryModel->getActiveCategories()
        ];

        return view('member/forum/search', $data);
    }

    /**
     * User forum profile
     */
    public function userProfile($userId)
    {
        $user = $this->userModel
            ->select('users.*, members.foto_path, members.status_kepegawaian_id, members.created_at as member_created_at')
            ->join('members', 'members.id = users.member_id', 'left')
            ->find($userId);

        if (!$user) {
            return redirect()->to('member/forum')->with('error', 'Pengguna tidak ditemukan.');
        }

        $data = [
            'title' => 'Profil Forum: ' . esc($user['nama_lengkap']),
            'user' => $user,
            'threads' => $this->threadModel->getUserThreads($userId),
            'replies' => $this->replyModel->getUserRecentReplies($userId, 10),
            'stats' => array_merge(
                $this->replyModel->getUserStats($userId),
                ['total_threads' => count($this->threadModel->getUserThreads($userId))]
            )
        ];

        return view('member/forum/user_profile', $data);
    }

    /**
     * Helper: Check if current user is admin
     */
    private function isAdmin()
    {
        $role = session()->get('role');
        return in_array($role, ['super_admin', 'pengurus']);
    }

    /**
     * Helper: Get user forum statistics
     */
    private function getUserForumStats()
    {
        $userId = session()->get('user_id');
        if (!$userId) return null;

        $threads = $this->threadModel->where('user_id', $userId)->countAllResults();
        $replies = $this->replyModel->where('user_id', $userId)->countAllResults();
        $solutions = $this->replyModel->where('user_id', $userId)
            ->where('is_solution', 1)
            ->countAllResults();

        return [
            'threads' => $threads,
            'replies' => $replies,
            'solutions' => $solutions
        ];
    }

    /**
     * Helper: Send notification for new thread
     */
    private function sendNewThreadNotification($threadId)
    {
        // Implementation depends on notification system
        // This is placeholder
        return true;
    }

    /**
     * Helper: Send notification for new reply
     */
    private function sendReplyNotification($threadId, $authorId)
    {
        // Implementation depends on notification system
        // This is placeholder
        return true;
    }
}
