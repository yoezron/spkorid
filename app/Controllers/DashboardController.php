<?php
// ============================================
// PERBAIKAN DashboardController.php
// ============================================
// Path: app/Controllers/DashboardController.php

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
     * PERBAIKAN: Hapus redirect loop, langsung tampilkan dashboard sesuai role
     */
    public function index()
    {
        $roleId = session()->get('role_id');

        // PERBAIKAN: Langsung tampilkan dashboard, tidak redirect lagi
        switch ($roleId) {
            case 1: // Super Admin
                return $this->adminDashboard();

            case 2: // Pengurus
                return $this->pengurusDashboard();

            case 3: // Anggota
                return $this->memberDashboard();

            default:
                // Jika role tidak valid, logout dan redirect ke login
                session()->destroy();
                return redirect()->to('/login')->with('error', 'Session tidak valid');
        }
    }

    /**
     * Admin Dashboard
     */
    private function adminDashboard()
    {
        // Statistik member
        $statistics = [
            'total' => $this->memberModel->countAll(),
            'active' => $this->memberModel->where('status_keanggotaan', 'active')->countAllResults(false),
            'pending' => $this->memberModel->where('status_keanggotaan', 'pending')->countAllResults(false),
            'suspended' => $this->memberModel->where('status_keanggotaan', 'suspended')->countAllResults(false),
        ];

        // Payment summary
        $paymentSummary = [
            'pending' => $this->paymentModel->where('status_pembayaran', 'pending')->countAllResults(false),
            'verified' => $this->paymentModel->where('status_pembayaran', 'verified')->countAllResults(false),
        ];

        $data = [
            'title' => 'Dashboard Admin - SPK',
            'statistics' => $statistics,
            'payment_summary' => $paymentSummary,
            'recent_members' => $this->memberModel->orderBy('created_at', 'DESC')->limit(10)->findAll(),
            'pending_members' => $this->memberModel->where('status_keanggotaan', 'pending')->findAll(),
            'pending_payments' => $this->paymentModel->where('status_pembayaran', 'pending')->findAll(),
        ];

        return view('admin/dashboard', $data);
    }

    /**
     * Pengurus Dashboard
     */
    private function pengurusDashboard()
    {
        // Statistics untuk pengurus
        $statistics = [
            'total_members' => $this->memberModel->where('status_keanggotaan', 'active')->countAllResults(false),
            'pending_members' => $this->memberModel->where('status_keanggotaan', 'pending')->countAllResults(false),
            'pending_payments' => $this->paymentModel->where('status_pembayaran', 'pending')->countAllResults(false),
            'pending_pengaduan' => $this->pengaduanModel->where('status', 'pending')->countAllResults(false),
        ];

        $data = [
            'title' => 'Dashboard Pengurus - SPK',
            'statistics' => $statistics,
            'recent_members' => $this->memberModel->orderBy('created_at', 'DESC')->limit(5)->findAll(),
            'pending_confirmations' => $this->memberModel->where('status_keanggotaan', 'pending')->limit(10)->findAll(),
            'recent_pengaduan' => $this->pengaduanModel->orderBy('created_at', 'DESC')->limit(5)->findAll(),
        ];

        return view('pengurus/dashboard', $data);
    }

    /**
     * Member Dashboard
     */
    private function memberDashboard()
    {
        $userId = session()->get('user_id');
        $memberId = session()->get('member_id');

        // Get member details
        $member = $this->memberModel->find($memberId);

        if (!$member) {
            session()->setFlashdata('error', 'Data anggota tidak ditemukan');
            return redirect()->to('/logout');
        }

        // Get payment history
        $paymentHistory = $this->paymentModel
            ->where('member_id', $memberId)
            ->orderBy('tanggal_pembayaran', 'DESC')
            ->limit(5)
            ->findAll();

        // Get latest informasi
        $latestInfo = $this->informasiModel
            ->where('status', 'published')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();

        // Get forum statistics
        $forumStats = [
            'total_threads' => $this->forumModel->where('author_id', $userId)->countAllResults(false),
            'latest_threads' => $this->forumModel->orderBy('created_at', 'DESC')->limit(5)->findAll(),
        ];

        $data = [
            'title' => 'Dashboard - SPK',
            'member' => $member,
            'payment_history' => $paymentHistory,
            'latest_info' => $latestInfo,
            'forum_stats' => $forumStats,
        ];

        return view('member/dashboard', $data);
    }

    /**
     * Get statistics for charts/graphs
     */
    public function statistics()
    {
        // Hanya untuk Super Admin
        if (session()->get('role_id') != 1) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Unauthorized'
            ])->setStatusCode(403);
        }

        $data = [
            'members_by_status' => $this->getMembersByStatus(),
            'members_by_wilayah' => $this->getMembersByWilayah(),
            'payments_by_month' => $this->getPaymentsByMonth(),
            'members_growth' => $this->getMembersGrowth(),
        ];

        return $this->response->setJSON([
            'status' => true,
            'data' => $data
        ]);
    }

    /**
     * Helper: Get members by status
     */
    private function getMembersByStatus()
    {
        $db = \Config\Database::connect();
        return $db->table('members')
            ->select('status_keanggotaan, COUNT(*) as count')
            ->groupBy('status_keanggotaan')
            ->get()
            ->getResultArray();
    }

    /**
     * Helper: Get members by wilayah
     */
    private function getMembersByWilayah()
    {
        $db = \Config\Database::connect();
        return $db->table('members')
            ->select('wilayah_id, wilayah.nama_wilayah, COUNT(*) as count')
            ->join('wilayah', 'wilayah.id = members.wilayah_id', 'left')
            ->where('status_keanggotaan', 'active')
            ->groupBy('wilayah_id, wilayah.nama_wilayah')
            ->get()
            ->getResultArray();
    }

    /**
     * Helper: Get payments by month
     */
    private function getPaymentsByMonth()
    {
        $db = \Config\Database::connect();
        return $db->table('payment_history')
            ->select("DATE_FORMAT(tanggal_pembayaran, '%Y-%m') as month, COUNT(*) as count, SUM(jumlah_pembayaran) as total")
            ->where('status_pembayaran', 'verified')
            ->where('tanggal_pembayaran >=', date('Y-m-d', strtotime('-12 months')))
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Helper: Get members growth
     */
    private function getMembersGrowth()
    {
        $db = \Config\Database::connect();
        return $db->table('members')
            ->select("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->where('created_at >=', date('Y-m-d', strtotime('-12 months')))
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Export statistics to Excel
     */
    public function exportStats()
    {
        // Hanya untuk Super Admin
        if (session()->get('role_id') != 1) {
            session()->setFlashdata('error', 'Unauthorized access');
            return redirect()->back();
        }

        // TODO: Implement Excel export
        // Gunakan PhpSpreadsheet library

        session()->setFlashdata('info', 'Fitur export masih dalam pengembangan');
        return redirect()->back();
    }
}
