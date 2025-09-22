<?php
// app/Models/BlogPostModel.php
namespace App\Models;

use CodeIgniter\Model;

class BlogPostModel extends Model
{
    protected $table = 'blog_posts';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'author_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'category',
        'tags',
        'status',
        'view_count',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'published_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $beforeInsert = ['generateSlug'];
    protected $beforeUpdate = ['generateSlug'];

    protected function generateSlug(array $data)
    {
        if (isset($data['data']['title']) && !isset($data['data']['slug'])) {
            $data['data']['slug'] = url_title($data['data']['title'], '-', true);
        }
        return $data;
    }

    // Get published posts
    public function getPublishedPosts($limit = null, $offset = null)
    {
        $builder = $this->select('blog_posts.*, users.username, members.nama_lengkap as author_name')
            ->join('users', 'users.id = blog_posts.author_id')
            ->join('members', 'members.id = users.member_id', 'left')
            ->where('blog_posts.status', 'published')
            ->orderBy('blog_posts.published_at', 'DESC');

        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }

        return $builder->findAll();
    }

    // Get post by slug
    public function getPostBySlug($slug)
    {
        $post = $this->select('blog_posts.*, users.username, members.nama_lengkap as author_name')
            ->join('users', 'users.id = blog_posts.author_id')
            ->join('members', 'members.id = users.member_id', 'left')
            ->where('blog_posts.slug', $slug)
            ->where('blog_posts.status', 'published')
            ->first();

        if ($post) {
            // Increment view count
            $this->update($post['id'], ['view_count' => $post['view_count'] + 1]);
        }

        return $post;
    }

    // Get posts by author
    public function getPostsByAuthor($authorId, $status = null)
    {
        $builder = $this->where('author_id', $authorId)
            ->orderBy('created_at', 'DESC');

        if ($status) {
            $builder->where('status', $status);
        }

        return $builder->findAll();
    }

    // Get pending posts for review
    public function getPendingPosts()
    {
        return $this->select('blog_posts.*, users.username, members.nama_lengkap as author_name')
            ->join('users', 'users.id = blog_posts.author_id')
            ->join('members', 'members.id = users.member_id', 'left')
            ->where('blog_posts.status', 'pending_review')
            ->orderBy('blog_posts.created_at', 'DESC')
            ->findAll();
    }

    // Approve post
    public function approvePost($postId, $reviewerId, $notes = '')
    {
        return $this->update($postId, [
            'status' => 'published',
            'reviewed_by' => $reviewerId,
            'reviewed_at' => date('Y-m-d H:i:s'),
            'review_notes' => $notes,
            'published_at' => date('Y-m-d H:i:s')
        ]);
    }

    // Reject post
    public function rejectPost($postId, $reviewerId, $notes)
    {
        return $this->update($postId, [
            'status' => 'rejected',
            'reviewed_by' => $reviewerId,
            'reviewed_at' => date('Y-m-d H:i:s'),
            'review_notes' => $notes
        ]);
    }

    // Search posts
    public function searchPosts($keyword)
    {
        return $this->select('blog_posts.*, users.username, members.nama_lengkap as author_name')
            ->join('users', 'users.id = blog_posts.author_id')
            ->join('members', 'members.id = users.member_id', 'left')
            ->where('blog_posts.status', 'published')
            ->groupStart()
            ->like('blog_posts.title', $keyword)
            ->orLike('blog_posts.content', $keyword)
            ->orLike('blog_posts.tags', $keyword)
            ->groupEnd()
            ->orderBy('blog_posts.published_at', 'DESC')
            ->findAll();
    }
}
