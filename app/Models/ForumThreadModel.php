<?php
// app/Models/ForumThreadModel.php
namespace App\Models;

use CodeIgniter\Model;

class ForumThreadModel extends Model
{
    protected $table = 'forum_threads';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'category_id',
        'user_id',
        'title',
        'content',
        'is_pinned',
        'is_locked',
        'view_count',
        'reply_count',
        'last_reply_at',
        'last_reply_by'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Get threads with user info
    public function getThreads($categoryId = null, $limit = null, $offset = null)
    {
        $builder = $this->select('forum_threads.*, 
                                 u.username, m.nama_lengkap as author_name,
                                 lu.username as last_reply_username,
                                 fc.name as category_name')
            ->join('users u', 'u.id = forum_threads.user_id')
            ->join('members m', 'm.id = u.member_id', 'left')
            ->join('users lu', 'lu.id = forum_threads.last_reply_by', 'left')
            ->join('forum_categories fc', 'fc.id = forum_threads.category_id')
            ->orderBy('forum_threads.is_pinned', 'DESC')
            ->orderBy('forum_threads.last_reply_at', 'DESC');

        if ($categoryId) {
            $builder->where('forum_threads.category_id', $categoryId);
        }

        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }

        return $builder->findAll();
    }

    // Get thread with details
    public function getThreadWithDetails($threadId)
    {
        $thread = $this->select('forum_threads.*, 
                                u.username, m.nama_lengkap as author_name, m.foto_path,
                                fc.name as category_name, fc.slug as category_slug')
            ->join('users u', 'u.id = forum_threads.user_id')
            ->join('members m', 'm.id = u.member_id', 'left')
            ->join('forum_categories fc', 'fc.id = forum_threads.category_id')
            ->where('forum_threads.id', $threadId)
            ->first();

        if ($thread) {
            // Increment view count
            $this->update($threadId, ['view_count' => $thread['view_count'] + 1]);
        }

        return $thread;
    }

    // Search threads
    public function searchThreads($keyword)
    {
        return $this->select('forum_threads.*, u.username, m.nama_lengkap as author_name')
            ->join('users u', 'u.id = forum_threads.user_id')
            ->join('members m', 'm.id = u.member_id', 'left')
            ->groupStart()
            ->like('forum_threads.title', $keyword)
            ->orLike('forum_threads.content', $keyword)
            ->groupEnd()
            ->orderBy('forum_threads.last_reply_at', 'DESC')
            ->findAll();
    }

    // Get popular threads
    public function getPopularThreads($limit = 10)
    {
        return $this->select('forum_threads.*, u.username, m.nama_lengkap as author_name')
            ->join('users u', 'u.id = forum_threads.user_id')
            ->join('members m', 'm.id = u.member_id', 'left')
            ->orderBy('forum_threads.reply_count', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
