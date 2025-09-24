<?php
// app/Controllers/DashboardController.php - FIXED VERSION
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
     * Admin Dashboard - FINAL FIX: Using only existing methods and correct columns
     */
    private function adminDashboard()
    {
        // Create statistics manually using existing methods
        $statistics = [
            'total' => $this->memberModel->countAll(),
            'active' => $this->memberModel->where('status_keanggotaan', 'active')->countAllResults(),
            'pending' => $this->memberModel->where('status_keanggotaan', 'pending')->countAllResults(),
            'suspended' => $this->memberModel->where('status_keanggotaan', 'suspended')->countAllResults(),
        ];

        // Create payment summary manually using correct column names
        $paymentSummary = [
            'pending' => $this->paymentModel->where('status_pembayaran', 'pending')->countAllResults(),
            'verified' => $this->paymentModel->where('status_pembayaran', 'verified')->countAllResults(),
        ];

        $data = [
            'title' => 'Dashboard Admin - SPK',
            'statistics' => $statistics,
            'payment_summary' => $paymentSummary,
            'recent_members' => $this->memberModel->orderBy('created_at', 'DESC')->limit(10)->findAll(),
            'pending_members' => $this->memberModel->getPendingMembers(), // This method exists
            'pending_payments' => $this->paymentModel->getPendingPayments(), // This method exists
            'pengaduan_stats' => $this->pengaduanModel->getPengaduanStatistics(), // This method exists!
            'total_posts' => $this->blogModel->countAll(),
            'total_threads' => $this->forumModel->countAll()
        ];

        return view('admin/dashboard', $data);
    }

    /**
     * Pengurus Dashboard - FINAL FIX: Using existing methods only
     */
    private function pengurusDashboard()
    {
        $data = [
            'title' => 'Dashboard Pengurus - SPK',
            'member_stats' => [
                'total' => $this->memberModel->where('status_keanggotaan', 'active')->countAllResults(),
                'pending' => $this->memberModel->where('status_keanggotaan', 'pending')->countAllResults()
            ],
            'pending_members' => $this->memberModel->getPendingMembers(), // This method exists
            'pending_posts' => [], // Keep empty for now if getPendingPosts method doesn't exist
            'open_pengaduan' => $this->pengaduanModel->where('status', 'open')->countAllResults(), // Correct column name
        ];

        return view('pengurus/dashboard', $data);
    }

    /**
     * Member Dashboard - Simple version
     */
    private function memberDashboard()
    {
        $memberId = session()->get('member_id');

        $data = [
            'title' => 'Dashboard Anggota - SPK',
            'member' => $this->memberModel->find($memberId),
            'recent_informasi' => $this->informasiModel->orderBy('created_at', 'DESC')->limit(5)->findAll(),
            'forum_stats' => [
                'total_threads' => $this->forumModel->countAll(),
                'recent_threads' => $this->forumModel->orderBy('created_at', 'DESC')->limit(5)->findAll()
            ]
        ];

        return view('member/dashboard', $data);
    }
}
