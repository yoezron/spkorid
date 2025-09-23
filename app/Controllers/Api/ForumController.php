<?php

// app/Controllers/Api/ForumController.php
namespace App\Controllers\Api;

use App\Models\ForumThreadModel;
use App\Models\ForumReplyModel;

class ForumController extends BaseApiController
{
    protected $threadModel;
    protected $replyModel;

    public function __construct()
    {
        $this->threadModel = new ForumThreadModel();
        $this->replyModel = new ForumReplyModel();
    }

    /**
     * Get forum threads
     */
    public function threads()
    {
        $categoryId = $this->request->getGet('category_id');
        $limit = $this->request->getGet('limit') ?? 20;
        $offset = $this->request->getGet('offset') ?? 0;

        $threads = $this->threadModel->getThreads($categoryId, $limit, $offset);

        return $this->respond([
            'status' => 'success',
            'data' => $threads,
            'meta' => [
                'total' => $categoryId
                    ? $this->threadModel->where('category_id', $categoryId)->countAllResults()
                    : $this->threadModel->countAll(),
                'limit' => $limit,
                'offset' => $offset
            ]
        ]);
    }

    /**
     * Get single thread with replies
     */
    public function thread($id)
    {
        $thread = $this->threadModel->getThreadWithDetails($id);

        if (!$thread) {
            return $this->failNotFound('Thread not found');
        }

        $replies = $this->replyModel->getThreadReplies($id);

        return $this->respond([
            'status' => 'success',
            'data' => [
                'thread' => $thread,
                'replies' => $replies
            ]
        ]);
    }

    /**
     * Post reply
     */
    public function reply()
    {
        if (!$this->verifyToken()) {
            return $this->failUnauthorized('Unauthorized');
        }

        $user = $this->getUserFromToken();

        $rules = [
            'thread_id' => 'required|numeric',
            'content' => 'required|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = $this->request->getJSON(true);
        $data['user_id'] = $user['id'];

        $replyId = $this->replyModel->addReply($data);

        if (!$replyId) {
            return $this->fail('Failed to add reply');
        }

        return $this->respond([
            'status' => 'success',
            'message' => 'Reply added successfully',
            'data' => ['reply_id' => $replyId]
        ]);
    }
}
