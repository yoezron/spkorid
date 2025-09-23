<?php
// ============================================
// CONTENT & BLOG CONTROLLERS
// ============================================

// app/Controllers/BlogController.php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BlogPostModel;
use App\Models\ActivityLogModel;

class BlogController extends BaseController
{
    protected $blogModel;
    protected $activityLog;

    public function __construct()
    {
        $this->blogModel = new BlogPostModel();
        $this->activityLog = new ActivityLogModel();
    }

    /**
     * Public blog listing
     */
    public function index()
    {
        $data = [
            'title' => 'Blog - SPK',
            'posts' => $this->blogModel->getPublishedPosts(10, $this->request->getGet('page') ?? 0),
            'pager' => $this->blogModel->pager
        ];

        return view('blog/index', $data);
    }

    /**
     * View single blog post
     */
    public function view($slug)
    {
        $post = $this->blogModel->getPostBySlug($slug);

        if (!$post) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Post tidak ditemukan');
        }

        $data = [
            'title' => $post['title'] . ' - SPK Blog',
            'post' => $post,
            'related_posts' => $this->blogModel->where('category', $post['category'])
                ->where('id !=', $post['id'])
                ->where('status', 'published')
                ->limit(3)
                ->findAll()
        ];

        return view('blog/view', $data);
    }

    /**
     * Create new blog post (for members)
     */
    public function create()
    {
        $data = [
            'title' => 'Tulis Artikel - SPK'
        ];

        return view('blog/create', $data);
    }

    /**
     * Store new blog post
     */
    public function store()
    {
        $rules = [
            'title' => 'required|min_length[5]|max_length[255]',
            'content' => 'required|min_length[100]',
            'excerpt' => 'required|max_length[500]',
            'category' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $postData = [
            'author_id' => session()->get('user_id'),
            'title' => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
            'excerpt' => $this->request->getPost('excerpt'),
            'category' => $this->request->getPost('category'),
            'tags' => $this->request->getPost('tags'),
            'status' => 'pending_review'
        ];

        // Handle featured image
        $image = $this->request->getFile('featured_image');
        if ($image && $image->isValid()) {
            $newName = $image->getRandomName();
            $image->move(ROOTPATH . 'public/uploads/blog', $newName);
            $postData['featured_image'] = 'uploads/blog/' . $newName;
        }

        $postId = $this->blogModel->insert($postData);

        // Log activity
        $this->activityLog->logActivity(
            session()->get('user_id'),
            'create_blog_post',
            'Created blog post: ' . $postData['title']
        );

        return redirect()->to('/member/my-posts')->with(
            'success',
            'Artikel berhasil dikirim dan akan direview oleh pengurus'
        );
    }

    /**
     * Edit blog post
     */
    public function edit($id)
    {
        $post = $this->blogModel->find($id);

        // Check ownership
        if ($post['author_id'] != session()->get('user_id')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit post ini');
        }

        $data = [
            'title' => 'Edit Artikel - SPK',
            'post' => $post
        ];

        return view('blog/edit', $data);
    }

    /**
     * Update blog post
     */
    public function update($id)
    {
        $post = $this->blogModel->find($id);

        // Check ownership
        if ($post['author_id'] != session()->get('user_id')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengedit post ini');
        }

        $rules = [
            'title' => 'required|min_length[5]|max_length[255]',
            'content' => 'required|min_length[100]',
            'excerpt' => 'required|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'title' => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
            'excerpt' => $this->request->getPost('excerpt'),
            'category' => $this->request->getPost('category'),
            'tags' => $this->request->getPost('tags'),
            'status' => 'pending_review' // Reset to pending after edit
        ];

        // Handle featured image
        $image = $this->request->getFile('featured_image');
        if ($image && $image->isValid()) {
            $newName = $image->getRandomName();
            $image->move(ROOTPATH . 'public/uploads/blog', $newName);
            $updateData['featured_image'] = 'uploads/blog/' . $newName;

            // Delete old image
            if ($post['featured_image'] && file_exists(ROOTPATH . 'public/' . $post['featured_image'])) {
                unlink(ROOTPATH . 'public/' . $post['featured_image']);
            }
        }

        $this->blogModel->update($id, $updateData);

        return redirect()->to('/member/my-posts')->with('success', 'Artikel berhasil diperbarui');
    }

    /**
     * My posts (for members)
     */
    public function myPosts()
    {
        $userId = session()->get('user_id');

        $data = [
            'title' => 'Artikel Saya - SPK',
            'posts' => $this->blogModel->getPostsByAuthor($userId)
        ];

        return view('blog/my_posts', $data);
    }

    /**
     * Search blog posts
     */
    public function search()
    {
        $keyword = $this->request->getGet('q');

        $data = [
            'title' => 'Pencarian: ' . $keyword . ' - SPK Blog',
            'posts' => $this->blogModel->searchPosts($keyword),
            'keyword' => $keyword
        ];

        return view('blog/search', $data);
    }
}
