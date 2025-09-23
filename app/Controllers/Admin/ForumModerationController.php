<?php

// ============================================
// FORUM MODERATION CONTROLLER
// ============================================

// app/Controllers/Admin/ForumModerationController.php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ForumThreadModel;
use App\Models\ForumReplyModel;
use App\Models\ActivityLogModel;

class ForumModerationController extends BaseController
{
    protected $threadModel;
    protected $replyModel;
    protected $activityLog;
    protected $db;

    public function __construct()
    {
        $this->threadModel = new ForumThreadModel();
        $this->replyModel = new ForumReplyModel();
        $this->activityLog = new ActivityLogModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Forum moderation dashboard
     */
    public function index()
    {
        $data = [
            'title' => 'Forum Moderation - SPK',
            'recent_threads' => $this->threadModel->getThreads(null, 20),
            'reported_content' => $this->getReportedContent(),
            'statistics' => $this->getForumStatistics()
        ];

        return view('admin/forum/moderation', $data);
    }

    /**
     * Pin/unpin thread
     */
    public function pinThread($id)
    {
        $thread = $this->threadModel->find($id);

        if (!$thread) {
            return redirect()->back()->with('error', 'Thread tidak ditemukan');
        }

        $this->threadModel->update($id, [
            'is_pinned' => !$thread['is_pinned']
        ]);

        $action = !$thread['is_pinned'] ? 'pinned' : 'unpinned';

        // Log activity
        $this->activityLog->logActivity(
            session()->get('user_id'),
            'forum_moderation',
            'Thread ' . $action . ' ID: ' . $id
        );

        return redirect()->back()->with('success', 'Thread berhasil di-' . $action);
    }

    /**
     * Lock/unlock thread
     */
    public function lockThread($id)
    {
        $thread = $this->threadModel->find($id);

        if (!$thread) {
            return redirect()->back()->with('error', 'Thread tidak ditemukan');
        }

        $this->threadModel->update($id, [
            'is_locked' => !$thread['is_locked']
        ]);

        $action = !$thread['is_locked'] ? 'locked' : 'unlocked';

        // Log activity
        $this->activityLog->logActivity(
            session()->get('user_id'),
            'forum_moderation',
            'Thread ' . $action . ' ID: ' . $id
        );

        return redirect()->back()->with('success', 'Thread berhasil di-' . $action);
    }

    /**
     * Delete thread
     */
    public function deleteThread($id)
    {
        $thread = $this->threadModel->find($id);

        if (!$thread) {
            return redirect()->back()->with('error', 'Thread tidak ditemukan');
        }

        // Delete all replies first
        $this->db->table('forum_replies')->where('thread_id', $id)->delete();

        // Delete thread
        $this->threadModel->delete($id);

        // Log activity
        $this->activityLog->logActivity(
            session()->get('user_id'),
            'forum_moderation',
            'Deleted thread ID: ' . $id
        );

        return redirect()->to('/forum')->with('success', 'Thread berhasil dihapus');
    }

    /**
     * Delete reply
     */
    public function deleteReply($id)
    {
        $reply = $this->replyModel->find($id);

        if (!$reply) {
            return redirect()->back()->with('error', 'Reply tidak ditemukan');
        }

        // Update thread reply count
        $this->db->table('forum_threads')
            ->where('id', $reply['thread_id'])
            ->set('reply_count', 'reply_count - 1', false)
            ->update();

        // Delete reply
        $this->replyModel->delete($id);

        // Log activity
        $this->activityLog->logActivity(
            session()->get('user_id'),
            'forum_moderation',
            'Deleted reply ID: ' . $id
        );

        return redirect()->back()->with('success', 'Reply berhasil dihapus');
    }

    /**
     * Bulk moderation actions
     */
    public function bulkAction()
    {
        $action = $this->request->getPost('action');
        $ids = $this->request->getPost('ids');

        if (empty($ids) || !is_array($ids)) {
            return redirect()->back()->with('error', 'Tidak ada item yang dipilih');
        }

        switch ($action) {
            case 'delete_threads':
                foreach ($ids as $id) {
                    $this->db->table('forum_replies')->where('thread_id', $id)->delete();
                    $this->threadModel->delete($id);
                }
                $message = 'Threads berhasil dihapus';
                break;

            case 'lock_threads':
                $this->threadModel->whereIn('id', $ids)->set(['is_locked' => 1])->update();
                $message = 'Threads berhasil dikunci';
                break;

            case 'unlock_threads':
                $this->threadModel->whereIn('id', $ids)->set(['is_locked' => 0])->update();
                $message = 'Threads berhasil dibuka';
                break;

            default:
                return redirect()->back()->with('error', 'Aksi tidak valid');
        }

        // Log activity
        $this->activityLog->logActivity(
            session()->get('user_id'),
            'forum_bulk_moderation',
            'Bulk action: ' . $action . ' on ' . count($ids) . ' items'
        );

        return redirect()->back()->with('success', $message);
    }

    /**
     * Get reported content
     */
    private function getReportedContent()
    {
        // Assuming there's a report system
        return $this->db->table('forum_reports fr')
            ->select('fr.*, ft.title as thread_title, u.username as reporter_name')
            ->join('forum_threads ft', 'ft.id = fr.thread_id', 'left')
            ->join('forum_replies frp', 'frp.id = fr.reply_id', 'left')
            ->join('users u', 'u.id = fr.reported_by')
            ->where('fr.status', 'pending')
            ->orderBy('fr.created_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();
    }

    /**
     * Get forum statistics
     */
    private function getForumStatistics()
    {
        return [
            'total_threads' => $this->threadModel->countAll(),
            'total_replies' => $this->replyModel->countAll(),
            'active_threads' => $this->threadModel->where('is_locked', 0)->countAllResults(),
            'locked_threads' => $this->threadModel->where('is_locked', 1)->countAllResults(),
            'pinned_threads' => $this->threadModel->where('is_pinned', 1)->countAllResults(),
            'today_threads' => $this->threadModel->where('DATE(created_at)', date('Y-m-d'))->countAllResults(),
            'today_replies' => $this->replyModel->where('DATE(created_at)', date('Y-m-d'))->countAllResults()
        ];
    }
}
