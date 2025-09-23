<?php
// app/Controllers/Api/BlogController.php
namespace App\Controllers\Api;

use App\Models\BlogPostModel;

class BlogController extends BaseApiController
{
    protected $blogModel;

    public function __construct()
    {
        $this->blogModel = new BlogPostModel();
    }

    /**
     * Get blog posts
     */
    public function index()
    {
        $limit = $this->request->getGet('limit') ?? 10;
        $offset = $this->request->getGet('offset') ?? 0;
        $category = $this->request->getGet('category');

        $query = $this->blogModel->where('status', 'published');

        if ($category) {
            $query->where('category', $category);
        }

        $posts = $query->orderBy('published_at', 'DESC')
            ->limit($limit, $offset)
            ->findAll();

        return $this->respond([
            'status' => 'success',
            'data' => $posts,
            'meta' => [
                'total' => $this->blogModel->where('status', 'published')->countAllResults(),
                'limit' => $limit,
                'offset' => $offset
            ]
        ]);
    }

    /**
     * Get single blog post
     */
    public function view($id)
    {
        $post = $this->blogModel->find($id);

        if (!$post || $post['status'] !== 'published') {
            return $this->failNotFound('Post not found');
        }

        // Increment view count
        $this->blogModel->update($id, [
            'view_count' => $post['view_count'] + 1
        ]);

        return $this->respond([
            'status' => 'success',
            'data' => $post
        ]);
    }
}
