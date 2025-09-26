<?php

namespace App\Models;

use CodeIgniter\Model;

class ForumThreadModel extends Model
{
    protected $table            = 'forum_threads';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title',
        'slug',
        'content',
        'category_id',
        'user_id',
        'views',
        'is_pinned',
        'is_locked',
        'is_featured',
        'last_reply_at',
        'last_reply_by'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'title' => 'required|min_length[5]|max_length[255]',
        'content' => 'required|min_length[10]',
        'category_id' => 'required|is_natural_no_zero|is_not_unique[forum_categories.id]',
        'user_id' => 'required|is_natural_no_zero|is_not_unique[users.id]'
    ];

    protected $validationMessages = [
        'title' => [
            'required' => 'Judul diskusi harus diisi',
            'min_length' => 'Judul diskusi minimal 5 karakter',
            'max_length' => 'Judul diskusi maksimal 255 karakter'
        ],
        'content' => [
            'required' => 'Isi diskusi harus diisi',
            'min_length' => 'Isi diskusi minimal 10 karakter'
        ],
        'category_id' => [
            'required' => 'Kategori harus dipilih',
            'is_not_unique' => 'Kategori tidak valid'
        ]
    ];

    // Callbacks
    protected $beforeInsert = ['generateSlug', 'setUserId'];
    protected $beforeUpdate = ['generateSlug'];

    /**
     * Generate slug dari title
     */
    protected function generateSlug(array $data)
    {
        if (isset($data['data']['title']) && !isset($data['data']['slug'])) {
            $data['data']['slug'] = url_title($data['data']['title'], '-', true) . '-' . time();
        }
        return $data;
    }

    /**
     * Set user_id dari session jika tidak ada
     */
    protected function setUserId(array $data)
    {
        if (!isset($data['data']['user_id'])) {
            $data['data']['user_id'] = session()->get('user_id');
        }
        return $data;
    }

    /**
     * Mendapatkan threads berdasarkan kategori dengan pagination
     */
    public function getThreadsByCategory($categoryId, $perPage = 20)
    {
        return $this->select('forum_threads.*, 
                         users.nama_lengkap as author_name,
                         members.foto_path as author_photo') // DIUBAH: Mengambil foto_path dari members
            ->select('(SELECT COUNT(id) FROM forum_replies WHERE thread_id = forum_threads.id AND deleted_at IS NULL) as reply_count')
            ->select('(SELECT MAX(created_at) FROM forum_replies WHERE thread_id = forum_threads.id AND deleted_at IS NULL) as last_reply_time')
            ->join('users', 'users.id = forum_threads.user_id')
            ->join('members', 'members.id = users.member_id', 'left') // DITAMBAHKAN: Join ke tabel members
            ->where('forum_threads.category_id', $categoryId)
            ->where('forum_threads.deleted_at', null)
            ->orderBy('forum_threads.is_pinned', 'DESC')
            ->orderBy('forum_threads.created_at', 'DESC')
            ->paginate($perPage);
    }

    /**
     * Mendapatkan thread dengan detail lengkap
     */
    public function getThreadWithDetails($threadId)
    {
        $thread = $this->select('forum_threads.*, 
                            users.nama_lengkap as author_name,
                            members.foto_path as author_photo,  
                            users.email as author_email,
                            forum_categories.name as category_name,
                            forum_categories.slug as category_slug')
            ->join('users', 'users.id = forum_threads.user_id')
            ->join('members', 'members.id = users.member_id', 'left') // DITAMBAHKAN
            ->join('forum_categories', 'forum_categories.id = forum_threads.category_id')
            ->where('forum_threads.id', $threadId)
            ->where('forum_threads.deleted_at', null)
            ->first();

        if ($thread) {
            // Increment view count
            $this->incrementViews($threadId);
        }

        return $thread;
    }

    /**
     * Increment view count
     */
    public function incrementViews($threadId)
    {
        return $this->set('views', 'views + 1', false)
            ->where('id', $threadId)
            ->update();
    }

    /**
     * Mendapatkan threads populer
     */
    public function getPopularThreads($limit = 5)
    {
        return $this->select('forum_threads.*, users.nama_lengkap as author_name')
            ->join('users', 'users.id = forum_threads.user_id')
            ->where('forum_threads.deleted_at', null)
            ->orderBy('views', 'DESC')
            ->limit($limit)
            ->find();
    }

    /**
     * Mendapatkan threads terbaru
     */
    public function getRecentThreads($limit = 5)
    {
        return $this->select('forum_threads.*, 
                             users.nama_lengkap as author_name,
                             forum_categories.name as category_name')
            ->join('users', 'users.id = forum_threads.user_id')
            ->join('forum_categories', 'forum_categories.id = forum_threads.category_id')
            ->where('forum_threads.deleted_at', null)
            ->orderBy('forum_threads.created_at', 'DESC')
            ->limit($limit)
            ->find();
    }

    /**
     * Mendapatkan threads milik user
     */
    public function getUserThreads($userId, $limit = null)
    {
        $query = $this->select('forum_threads.*, 
                           forum_categories.name as category_name,
                           members.foto_path as author_photo, 
                           COUNT(DISTINCT forum_replies.id) as reply_count')
            ->join('forum_categories', 'forum_categories.id = forum_threads.category_id')
            ->join('users', 'users.id = forum_threads.user_id', 'left') // DITAMBAHKAN
            ->join('members', 'members.id = users.member_id', 'left') // DITAMBAHKAN
            ->join('forum_replies', 'forum_replies.thread_id = forum_threads.id', 'left')
            ->where('forum_threads.user_id', $userId)
            ->where('forum_threads.deleted_at', null)
            ->groupBy('forum_threads.id')
            ->orderBy('forum_threads.created_at', 'DESC');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->find();
    }

    /**
     * Search threads
     */
    public function searchThreads($keyword, $categoryId = null)
    {
        $query = $this->select('forum_threads.*, 
                               users.nama_lengkap as author_name,
                               forum_categories.name as category_name')
            ->join('users', 'users.id = forum_threads.user_id')
            ->join('forum_categories', 'forum_categories.id = forum_threads.category_id')
            ->where('forum_threads.deleted_at', null)
            ->groupStart()
            ->like('forum_threads.title', $keyword)
            ->orLike('forum_threads.content', $keyword)
            ->groupEnd();

        if ($categoryId) {
            $query->where('forum_threads.category_id', $categoryId);
        }

        return $query->orderBy('forum_threads.created_at', 'DESC')
            ->paginate(20);
    }

    /**
     * Update last reply info
     */
    public function updateLastReply($threadId, $userId)
    {
        return $this->update($threadId, [
            'last_reply_at' => date('Y-m-d H:i:s'),
            'last_reply_by' => $userId
        ]);
    }

    /**
     * Toggle pin status
     */
    public function togglePin($threadId)
    {
        $thread = $this->find($threadId);
        if ($thread) {
            return $this->update($threadId, [
                'is_pinned' => !$thread['is_pinned']
            ]);
        }
        return false;
    }

    /**
     * Toggle lock status
     */
    public function toggleLock($threadId)
    {
        $thread = $this->find($threadId);
        if ($thread) {
            return $this->update($threadId, [
                'is_locked' => !$thread['is_locked']
            ]);
        }
        return false;
    }
}
