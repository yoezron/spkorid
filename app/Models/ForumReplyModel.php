<?php
// app/Models/ForumReplyModel.php
namespace App\Models;

use CodeIgniter\Model;

class ForumReplyModel extends Model
{
    protected $table = 'forum_replies';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'thread_id',
        'user_id',
        'reply_to_id',
        'content',
        'is_edited',
        'edited_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Get replies for a thread
    public function getThreadReplies($threadId)
    {
        return $this->select('forum_replies.*, u.username, m.nama_lengkap as author_name, m.foto_path')
            ->join('users u', 'u.id = forum_replies.user_id')
            ->join('members m', 'm.id = u.member_id', 'left')
            ->where('forum_replies.thread_id', $threadId)
            ->orderBy('forum_replies.created_at', 'ASC')
            ->findAll();
    }

    // Add reply and update thread
    public function addReply($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Insert reply
            $replyId = $this->insert($data);

            // Update thread reply count and last reply info
            $db->table('forum_threads')
                ->where('id', $data['thread_id'])
                ->set('reply_count', 'reply_count + 1', false)
                ->set('last_reply_at', date('Y-m-d H:i:s'))
                ->set('last_reply_by', $data['user_id'])
                ->update();

            $db->transComplete();
            return $db->transStatus() ? $replyId : false;
        } catch (\Exception $e) {
            $db->transRollback();
            return false;
        }
    }
}
