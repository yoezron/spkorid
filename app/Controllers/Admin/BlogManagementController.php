<?php

// app/Controllers/Admin/BlogManagementController.php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BlogPostModel;
use App\Models\ActivityLogModel;

class BlogManagementController extends BaseController
{
    protected $blogModel;
    protected $activityLog;

    public function __construct()
    {
        $this->blogModel = new BlogPostModel();
        $this->activityLog = new ActivityLogModel();
    }

    /**
     * List pending posts for review
     */
    public function pending()
    {
        $data = [
            'title' => 'Review Artikel - SPK',
            'posts' => $this->blogModel->getPendingPosts()
        ];

        return view('admin/blog/pending', $data);
    }

    /**
     * Review post
     */
    public function review($id)
    {
        $post = $this->blogModel->find($id);

        if (!$post) {
            return redirect()->back()->with('error', 'Post tidak ditemukan');
        }

        $data = [
            'title' => 'Review: ' . $post['title'],
            'post' => $post
        ];

        return view('admin/blog/review', $data);
    }

    /**
     * Approve post
     */
    public function approve($id)
    {
        $notes = $this->request->getPost('notes') ?? '';
        $reviewerId = session()->get('user_id');

        $this->blogModel->approvePost($id, $reviewerId, $notes);

        // Log activity
        $this->activityLog->logActivity(
            $reviewerId,
            'approve_blog_post',
            'Approved blog post ID: ' . $id
        );

        return redirect()->to('/pengurus/blog/pending')->with('success', 'Artikel berhasil disetujui');
    }

    /**
     * Reject post
     */
    public function reject($id)
    {
        $notes = $this->request->getPost('rejection_reason');
        $reviewerId = session()->get('user_id');

        $this->blogModel->rejectPost($id, $reviewerId, $notes);

        // Log activity
        $this->activityLog->logActivity(
            $reviewerId,
            'reject_blog_post',
            'Rejected blog post ID: ' . $id
        );

        return redirect()->to('/pengurus/blog/pending')->with('success', 'Artikel ditolak');
    }
}
