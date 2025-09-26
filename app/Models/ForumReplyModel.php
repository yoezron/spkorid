<?php

namespace App\Models;

use CodeIgniter\Model;

class ForumReplyModel extends Model
{
    protected $table            = 'forum_replies';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'thread_id',
        'user_id',
        'content',
        'parent_id',
        'is_solution',
        'is_edited',
        'edited_at',
        'edited_by'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'thread_id' => 'required|is_natural_no_zero|is_not_unique[forum_threads.id]',
        'user_id' => 'required|is_natural_no_zero|is_not_unique[users.id]',
        'content' => 'required|min_length[5]'
    ];

    protected $validationMessages = [
        'content' => [
            'required' => 'Isi balasan harus diisi',
            'min_length' => 'Isi balasan minimal 5 karakter'
        ],
        'thread_id' => [
            'required' => 'Thread ID diperlukan',
            'is_not_unique' => 'Thread tidak valid'
        ]
    ];

    // Callbacks
    protected $beforeInsert = ['setUserId'];
    protected $afterInsert = ['updateThreadActivity'];
    protected $beforeUpdate = ['setEditInfo'];

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
     * Set edit info when updating
     */
    protected function setEditInfo(array $data)
    {
        if (isset($data['data']['content'])) {
            $data['data']['is_edited'] = 1;
            $data['data']['edited_at'] = date('Y-m-d H:i:s');
            $data['data']['edited_by'] = session()->get('user_id');
        }
        return $data;
    }

    /**
     * Update thread last activity after insert
     */
    protected function updateThreadActivity(array $data)
    {
        if (isset($data['data']['thread_id'])) {
            $threadModel = new ForumThreadModel();
            $threadModel->updateLastReply($data['data']['thread_id'], $data['data']['user_id']);
        }
        return $data;
    }

    /**
     * Mendapatkan replies berdasarkan thread dengan pagination
     */
    public function getRepliesByThread($threadId, $perPage = 20)
    {
        return $this->select('forum_replies.*, 
                             users.nama_lengkap as author_name,
                             users.foto as author_photo,
                             users.email as author_email,
                             users.status_kepegawaian,
                             editor.nama_lengkap as editor_name')
            ->join('users', 'users.id = forum_replies.user_id')
            ->join('users as editor', 'editor.id = forum_replies.edited_by', 'left')
            ->where('forum_replies.thread_id', $threadId)
            ->where('forum_replies.deleted_at', null)
            ->where('forum_replies.parent_id', null) // Only main replies, not nested
            ->orderBy('forum_replies.created_at', 'ASC')
            ->paginate($perPage);
    }

    /**
     * Mendapatkan nested replies (replies to replies)
     */
    public function getNestedReplies($parentId)
    {
        return $this->select('forum_replies.*, 
                             users.nama_lengkap as author_name,
                             users.foto as author_photo')
            ->join('users', 'users.id = forum_replies.user_id')
            ->where('forum_replies.parent_id', $parentId)
            ->where('forum_replies.deleted_at', null)
            ->orderBy('forum_replies.created_at', 'ASC')
            ->find();
    }

    /**
     * Count replies untuk thread
     */
    public function countReplies($threadId)
    {
        return $this->where('thread_id', $threadId)
            ->where('deleted_at', null)
            ->countAllResults();
    }

    /**
     * Mendapatkan recent replies untuk user
     */
    public function getUserRecentReplies($userId, $limit = 5)
    {
        return $this->select('forum_replies.*, 
                             forum_threads.title as thread_title,
                             forum_threads.slug as thread_slug')
            ->join('forum_threads', 'forum_threads.id = forum_replies.thread_id')
            ->where('forum_replies.user_id', $userId)
            ->where('forum_replies.deleted_at', null)
            ->orderBy('forum_replies.created_at', 'DESC')
            ->limit($limit)
            ->find();
    }

    /**
     * Mark as solution
     */
    public function markAsSolution($replyId)
    {
        $reply = $this->find($replyId);
        if (!$reply) {
            return false;
        }

        // Unmark other solutions in the same thread
        $this->where('thread_id', $reply['thread_id'])
            ->where('is_solution', 1)
            ->set(['is_solution' => 0])
            ->update();

        // Mark this as solution
        return $this->update($replyId, ['is_solution' => 1]);
    }

    /**
     * Search replies
     */
    public function searchReplies($keyword, $threadId = null)
    {
        $query = $this->select('forum_replies.*, 
                               users.nama_lengkap as author_name,
                               forum_threads.title as thread_title')
            ->join('users', 'users.id = forum_replies.user_id')
            ->join('forum_threads', 'forum_threads.id = forum_replies.thread_id')
            ->where('forum_replies.deleted_at', null)
            ->like('forum_replies.content', $keyword);

        if ($threadId) {
            $query->where('forum_replies.thread_id', $threadId);
        }

        return $query->orderBy('forum_replies.created_at', 'DESC')
            ->paginate(20);
    }

    /**
     * Get statistics for user
     */
    public function getUserStats($userId)
    {
        return [
            'total_replies' => $this->where('user_id', $userId)
                ->where('deleted_at', null)
                ->countAllResults(),
            'solutions' => $this->where('user_id', $userId)
                ->where('is_solution', 1)
                ->where('deleted_at', null)
                ->countAllResults()
        ];
    }
}
