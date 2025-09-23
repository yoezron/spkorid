<?php
// ============================================
// HOME & PUBLIC CONTROLLERS
// ============================================

// app/Controllers/Home.php
namespace App\Controllers;

use App\Models\InformasiSerikatModel;
use App\Models\BlogPostModel;
use App\Models\MemberModel;
use App\Models\CMSPageModel;

class Home extends BaseController
{
    protected $informasiModel;
    protected $blogModel;
    protected $memberModel;
    protected $cmsModel;

    public function __construct()
    {
        $this->informasiModel = new InformasiSerikatModel();
        $this->blogModel = new BlogPostModel();
        $this->memberModel = new MemberModel();
        $this->cmsModel = new CMSPageModel();
    }

    /**
     * Homepage
     */
    public function index()
    {
        $data = [
            'title' => 'SPK - Serikat Pekerja Kampus Indonesia',
            'recent_news' => $this->informasiModel->getPublished('berita', 3),
            'recent_posts' => $this->blogModel->getPublishedPosts(3),
            'announcements' => $this->informasiModel->getPublished('pengumuman', 5),
            'statistics' => $this->getPublicStatistics(),
            'upcoming_events' => $this->getUpcomingEvents()
        ];

        // Menyesuaikan nama variabel untuk view baru
        $data['latest_posts'] = $data['recent_posts'];
        // --------------------------

        return view('public/home', $data);
    }

    /**
     * About page
     */
    public function about()
    {
        $page = $this->cmsModel->getPageBySlug('about');

        $data = [
            'title' => 'Tentang Kami - SPK',
            'page' => $page,
            'leadership' => $this->getLeadership(),
            'history' => $this->cmsModel->getPageBySlug('sejarah')
        ];

        return view('public/about', $data);
    }

    /**
     * Contact page
     */
    public function contact()
    {
        $data = [
            'title' => 'Hubungi Kami - SPK',
            'page' => $this->cmsModel->getPageBySlug('contact')
        ];

        return view('public/contact', $data);
    }

    /**
     * Send contact message
     */
    public function sendContact()
    {
        $rules = [
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email',
            'subject' => 'required|min_length[5]',
            'message' => 'required|min_length[20]',
            'g-recaptcha-response' => 'required' // If using reCAPTCHA
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Verify reCAPTCHA
        if (!$this->verifyRecaptcha($this->request->getPost('g-recaptcha-response'))) {
            return redirect()->back()->withInput()->with('error', 'Verifikasi reCAPTCHA gagal');
        }

        // Send email to admin
        $this->sendContactEmail($this->request->getPost());

        // Save to database if needed
        $this->saveContactMessage($this->request->getPost());

        return redirect()->to('/contact')->with('success', 'Pesan Anda telah terkirim. Kami akan segera merespons.');
    }

    /**
     * Get public statistics
     */
    private function getPublicStatistics()
    {
        return [
            'total_members' => $this->memberModel->where('status_keanggotaan', 'active')->countAllResults(),
            'total_kampus' => $this->memberModel->select('kampus_id')->distinct()->countAllResults(),
            'total_posts' => $this->blogModel->where('status', 'published')->countAllResults(),
            'total_events' => $this->informasiModel->where('kategori', 'kegiatan')->where('status', 'published')->countAllResults()
        ];
    }

    /**
     * Get upcoming events
     */
    private function getUpcomingEvents()
    {
        return $this->informasiModel->where('kategori', 'kegiatan')
            ->where('status', 'published')
            ->where('published_at >=', date('Y-m-d'))
            ->orderBy('published_at', 'ASC')
            ->limit(5)
            ->findAll();
    }

    /**
     * Get leadership data
     */
    private function getLeadership()
    {
        // Get members with pengurus role
        $db = \Config\Database::connect();
        return $db->table('members m')
            ->select('m.nama_lengkap, m.foto_path, m.bidang_keahlian')
            ->join('users u', 'u.member_id = m.id')
            ->whereIn('u.role_id', [1, 2]) // Super Admin & Pengurus
            ->where('m.status_keanggotaan', 'active')
            ->get()
            ->getResultArray();
    }

    /**
     * Verify reCAPTCHA
     */
    private function verifyRecaptcha($response)
    {
        $secret = getenv('RECAPTCHA_SECRET_KEY');
        $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$response}");
        $captcha_success = json_decode($verify);
        return $captcha_success->success;
    }

    /**
     * Send contact email
     */
    private function sendContactEmail($data)
    {
        $emailService = \Config\Services::email();

        $emailService->setFrom($data['email'], $data['name']);
        $emailService->setTo('admin@spk.org');
        $emailService->setSubject('Pesan Kontak: ' . $data['subject']);

        $message = view('emails/contact_message', ['data' => $data]);
        $emailService->setMessage($message);

        return $emailService->send();
    }

    /**
     * Save contact message to database
     */
    private function saveContactMessage($data)
    {
        $db = \Config\Database::connect();
        return $db->table('contact_messages')->insert([
            'name' => $data['name'],
            'email' => $data['email'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'ip_address' => $this->request->getIPAddress(),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
