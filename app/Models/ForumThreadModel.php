<?php

namespace App\Models;

use CodeIgniter\Model;

class ForumThreadModel extends Model
{
    protected $table            = 'forum_threads';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    // Menggunakan 'user_id' sesuai skema database
    protected $allowedFields    = ['title', 'slug', 'content', 'user_id', 'category_id', 'status', 'is_pinned', 'view_count'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Mengambil threads dalam sebuah kategori, lengkap dengan nama author dan jumlah balasan.
     */
    public function getThreadsByCategory(int $categoryId)
    {
        // PERBAIKAN: Menggunakan subquery untuk menghitung balasan agar tidak konflik dengan pagination
        $replyCountSubquery = "(SELECT COUNT(id) FROM forum_replies WHERE thread_id = forum_threads.id)";

        return $this->select("forum_threads.*, m.nama_lengkap as author_name, {$replyCountSubquery} as reply_count")
            ->join('users u', 'u.id = forum_threads.user_id', 'left')
            ->join('members m', 'm.id = u.member_id', 'left')
            ->where('forum_threads.category_id', $categoryId)
            ->orderBy('forum_threads.updated_at', 'DESC')
            ->paginate(15);
    }

    /**
     * Mengambil detail thread tunggal, lengkap dengan info author dan kategori.
     */
    public function getThreadWithDetails(int $threadId)
    {
        return $this->select('forum_threads.*, m.nama_lengkap as author_name, fc.name as category_name, fc.slug as category_slug')
            ->join('users u', 'u.id = forum_threads.user_id', 'left')
            ->join('members m', 'm.id = u.member_id', 'left')
            ->join('forum_categories fc', 'fc.id = forum_threads.category_id', 'left')
            ->where('forum_threads.id', $threadId)
            ->first();
    }
}
