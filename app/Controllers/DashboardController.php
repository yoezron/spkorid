<?php
// ============================================
// DASHBOARD CONTROLLERS
// ============================================

// app/Controllers/DashboardController.php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\PaymentHistoryModel;
use App\Models\InformasiSerikatModel;
use App\Models\PengaduanModel;
use App\Models\BlogPostModel;
use App\Models\ForumThreadModel;

class DashboardController extends BaseController
{
    protected $memberModel;
    protected $paymentModel;
    protected $informasiModel;
    protected $pengaduanModel;
    protected $blogModel;
    protected $forumModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->paymentModel = new PaymentHistoryModel();
        $this->informasiModel = new InformasiSerikatModel();
        $this->pengaduanModel = new PengaduanModel();
        $this->blogModel = new BlogPostModel();
        $this->forumModel = new ForumThreadModel();
    }

    /**
     * Main dashboard based on role
     */
    public function index()
    {
        $roleId = session()->get('role_id');

        switch ($roleId) {
            case 1: // Super Admin
                return $this->adminDashboard();
            case 2: // Pengurus
                return $this->pengurusDashboard();
            case 3: // Anggota
                return $this->memberDashboard();
            default:
                return redirect()->to('/login');
        }
    }

    /**
     * Admin Dashboard
     */
    private function adminDashboard()
    {
        $data = [
            'title' => 'Dashboard Admin - SPK',
            'statistics' => $this->memberModel->getMemberStatistics(),
            'payment_summary' => $this->paymentModel->getPaymentSummary(),
            'recent_members' => $this->memberModel->orderBy('created_at', 'DESC')->limit(10)->findAll(),
            'pending_members' => $this->memberModel->getPendingMembers(),
            'pending_payments' => $this->paymentModel->getPendingPayments(),
            'pengaduan_stats' => $this->pengaduanModel->getPengaduanStatistics(),
            'total_posts' => $this->blogModel->countAll(),
            'total_threads' => $this->forumModel->countAll()
        ];

        return view('admin/dashboard', $data);
    }

    /**
     * Pengurus Dashboard
     */
    private function pengurusDashboard()
    {
        $data = [
            'title' => 'Dashboard Pengurus - SPK',
            'member_stats' => [
                'total' => $this->memberModel->where('status_keanggotaan', 'active')->countAllResults(),
                'pending' => $this->memberModel->where('status_keanggotaan', 'pending')->countAllResults()
            ],
            'pending_members' => $this->memberModel->getPendingMembers(),
            'pending_posts' => $this->blogModel->getPendingPosts(),
            'open_pengaduan' => $this->pengaduanModel->getPengaduanByStatus('open'),
            'recent_activities' => $this->getRecentActivities(),
            'payment_summary' => $this->paymentModel->getPaymentSummary(date('Y-m-01'), date('Y-m-t'))
        ];

        return view('pengurus/dashboard', $data);
    }

    /**
     * Member Dashboard
     */
    private function memberDashboard()
    {
        $memberId = session()->get('member_id');
        $member = $this->memberModel->getMemberWithDetails($memberId);

        $data = [
            'title' => 'Dashboard Anggota - SPK',
            'member' => $member,
            'payment_history' => $this->paymentModel->getMemberPayments($memberId),
            'recent_info' => $this->informasiModel->getRecent(5),
            'my_posts' => $this->blogModel->getPostsByAuthor(session()->get('user_id'), 'published'),
            'announcements' => $this->informasiModel->getPublished('pengumuman', 5)
        ];

        return view('member/dashboard', $data);
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities()
    {
        // Implement logic to get recent activities
        $activities = [];

        // Recent members
        $recentMembers = $this->memberModel->orderBy('created_at', 'DESC')->limit(5)->findAll();
        foreach ($recentMembers as $member) {
            $activities[] = [
                'type' => 'new_member',
                'message' => 'Anggota baru: ' . $member['nama_lengkap'],
                'date' => $member['created_at']
            ];
        }

        // Recent posts
        $recentPosts = $this->blogModel->orderBy('created_at', 'DESC')->limit(5)->findAll();
        foreach ($recentPosts as $post) {
            $activities[] = [
                'type' => 'new_post',
                'message' => 'Post baru: ' . $post['title'],
                'date' => $post['created_at']
            ];
        }

        // Sort by date
        usort($activities, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return array_slice($activities, 0, 10);
    }
}
