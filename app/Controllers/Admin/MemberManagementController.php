<?php

namespace App\Controllers\Admin;

// ============================================
// ADMIN MEMBER MANAGEMENT CONTROLLER
// ============================================

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\UserModel;
use App\Models\PaymentHistoryModel;
use App\Models\ActivityLogModel;

class MemberManagementController extends BaseController
{
    protected $memberModel;
    protected $userModel;
    protected $paymentModel;
    protected $activityLog;
    protected $db; // <-- ADD THIS LINE

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->userModel = new UserModel();
        $this->paymentModel = new PaymentHistoryModel();
        $this->activityLog = new ActivityLogModel();
        $this->db = \Config\Database::connect(); // <-- AND ADD THIS LINE
    }

    /**
     * List all members
     */
    public function index()
    {
        $data = [
            'title' => 'Daftar Anggota - SPK',
            'members' => $this->memberModel->getActiveMembers(),
            'statistics' => $this->memberModel->getStatistics()
        ];

        return view('admin/members/index', $data);
    }

    /**
     * Pending members for verification
     */
    public function pending()
    {
        $data = [
            'title' => 'Verifikasi Anggota - SPK',
            'pending_members' => $this->memberModel->getPendingMembers()
        ];

        return view('admin/members/pending', $data);
    }

    /**
     * View member details
     */
    public function view($id)
    {
        $member = $this->memberModel->getMemberWithDetails($id);

        if (!$member) {
            return redirect()->back()->with('error', 'Anggota tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Anggota - SPK',
            'member' => $member,
            'payment_history' => $this->paymentModel->getMemberPayments($id),
            'user' => $this->userModel->where('member_id', $id)->first()
        ];

        return view('admin/members/view', $data);
    }

    /**
     * Show the form for creating a new member.
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Anggota Baru',
        ];
        return view('admin/members/create', $data);
    }

    /**
     * Store a newly created member in storage.
     */
    public function store()
    {
        $rules = [
            'nama_lengkap' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[members.email]',
            'password' => 'required|min_length[8]',
            'status_keanggotaan' => 'required|in_list[pending,active,suspended]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Generate member number if empty
        $nomorAnggota = $this->request->getPost('nomor_anggota');
        if (empty($nomorAnggota)) {
            $nomorAnggota = $this->memberModel->generateNomorAnggota();
        }

        // Start transaction
        $this->db->transStart();

        // Insert Member
        $memberId = $this->memberModel->insert([
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'email' => $this->request->getPost('email'),
            'nomor_anggota' => $nomorAnggota,
            'nomor_whatsapp' => $this->request->getPost('nomor_telepon'),
            'status_keanggotaan' => $this->request->getPost('status_keanggotaan'),
            'tanggal_bergabung' => date('Y-m-d'),
        ]);

        // Insert User
        $this->userModel->insert([
            'member_id' => $memberId,
            'email' => $this->request->getPost('email'),
            'username' => $this->request->getPost('email'), // Or generate a unique username
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role_id' => 3, // Default to member role
            'is_active' => 1,
            'is_verified' => 1,
            'email_verified_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data anggota.');
        }

        return redirect()->to('admin/members')->with('success', 'Anggota baru berhasil ditambahkan.');
    }


    /**
     * Verify member
     */
    public function verify($id)
    {
        $verifiedBy = session()->get('user_id');
        $notes = $this->request->getPost('notes') ?? '';

        $result = $this->memberModel->verifyMember($id, $verifiedBy, $notes);

        if ($result) {
            // Send notification email to member
            $this->sendVerificationNotification($id);

            // Log activity
            $this->activityLog->logActivity(
                $verifiedBy,
                'verify_member',
                'Verified member ID: ' . $id
            );

            return redirect()->back()->with('success', 'Anggota berhasil diverifikasi');
        }

        return redirect()->back()->with('error', 'Gagal memverifikasi anggota');
    }

    /**
     * Reject member
     */
    public function reject($id)
    {
        $notes = $this->request->getPost('rejection_reason');

        $this->memberModel->update($id, [
            'status_keanggotaan' => 'terminated',
            'catatan_verifikasi' => $notes,
            'tanggal_verifikasi' => date('Y-m-d H:i:s'),
            'verified_by' => session()->get('user_id')
        ]);

        // Send rejection email
        $this->sendRejectionNotification($id, $notes);

        // Log activity
        $this->activityLog->logActivity(
            session()->get('user_id'),
            'reject_member',
            'Rejected member ID: ' . $id
        );

        return redirect()->to('/admin/members/pending')->with('success', 'Pendaftaran anggota ditolak');
    }

    /**
     * Suspend member
     */
    public function suspend($id)
    {
        $reason = $this->request->getPost('suspension_reason');

        $this->memberModel->update($id, [
            'status_keanggotaan' => 'suspended',
            'catatan_verifikasi' => $reason
        ]);

        // Deactivate user account
        $this->userModel->where('member_id', $id)->set(['is_active' => 0])->update();

        // Log activity
        $this->activityLog->logActivity(
            session()->get('user_id'),
            'suspend_member',
            'Suspended member ID: ' . $id
        );

        return redirect()->back()->with('success', 'Anggota berhasil ditangguhkan');
    }

    /**
     * Reactivate member
     */
    public function reactivate($id)
    {
        $this->memberModel->update($id, [
            'status_keanggotaan' => 'active'
        ]);

        // Reactivate user account
        $this->userModel->where('member_id', $id)->set(['is_active' => 1])->update();

        // Log activity
        $this->activityLog->logActivity(
            session()->get('user_id'),
            'reactivate_member',
            'Reactivated member ID: ' . $id
        );

        return redirect()->back()->with('success', 'Anggota berhasil diaktifkan kembali');
    }

    /**
     * Delete member (soft delete)
     */
    public function delete($id)
    {
        // Check if member can be deleted
        $member = $this->memberModel->find($id);
        if (!$member) {
            return redirect()->back()->with('error', 'Anggota tidak ditemukan');
        }

        // Update status to terminated
        $this->memberModel->update($id, [
            'status_keanggotaan' => 'terminated'
        ]);

        // Deactivate user account
        $this->userModel->where('member_id', $id)->set(['is_active' => 0])->update();

        // Log activity
        $this->activityLog->logActivity(
            session()->get('user_id'),
            'delete_member',
            'Deleted member ID: ' . $id
        );

        return redirect()->to('/admin/members')->with('success', 'Anggota berhasil dihapus');
    }

    /**
     * Export members to Excel
     */
    public function export()
    {
        $members = $this->memberModel->getActiveMembers();

        // Create CSV
        $filename = 'members_' . date('Y-m-d_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Header row
        fputcsv($output, [
            'No. Anggota',
            'Nama Lengkap',
            'Email',
            'Jenis Kelamin',
            'No. WhatsApp',
            'Kampus',
            'Program Studi',
            'Status',
            'Tanggal Bergabung'
        ]);

        // Data rows
        foreach ($members as $member) {
            fputcsv($output, [
                $member['nomor_anggota'],
                $member['nama_lengkap'],
                $member['email'],
                $member['jenis_kelamin'],
                $member['nomor_whatsapp'],
                $member['nama_kampus'] ?? '',
                $member['nama_prodi'] ?? '',
                $member['status_keanggotaan'],
                $member['tanggal_bergabung']
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Import members from Excel
     */
    public function import()
    {
        $file = $this->request->getFile('import_file');

        if (!$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid');
        }

        // Process CSV
        $handle = fopen($file->getTempName(), 'r');
        $header = fgetcsv($handle); // Skip header

        $imported = 0;
        $failed = 0;

        while (($data = fgetcsv($handle)) !== FALSE) {
            try {
                // Process each row
                // Implement import logic here
                $imported++;
            } catch (\Exception $e) {
                $failed++;
            }
        }

        fclose($handle);

        return redirect()->back()->with(
            'success',
            "Import selesai. Berhasil: $imported, Gagal: $failed"
        );
    }

    /**
     * Search members
     */
    public function search()
    {
        $keyword = $this->request->getGet('q');

        $data = [
            'title' => 'Pencarian Anggota - SPK',
            'members' => $this->memberModel->searchMembers($keyword),
            'keyword' => $keyword
        ];

        return view('admin/members/search', $data);
    }

    /**
     * Send verification notification
     */
    private function sendVerificationNotification($memberId)
    {
        $member = $this->memberModel->find($memberId);

        $emailService = \Config\Services::email();
        $emailService->setFrom('noreply@spk.org', 'SPK Indonesia');
        $emailService->setTo($member['email']);
        $emailService->setSubject('Keanggotaan Anda Telah Diverifikasi');

        $message = view('emails/member_verified', ['member' => $member]);
        $emailService->setMessage($message);
        $emailService->send();
    }

    /**
     * Send rejection notification
     */
    private function sendRejectionNotification($memberId, $reason)
    {
        $member = $this->memberModel->find($memberId);

        $emailService = \Config\Services::email();
        $emailService->setFrom('noreply@spk.org', 'SPK Indonesia');
        $emailService->setTo($member['email']);
        $emailService->setSubject('Pendaftaran Keanggotaan Ditolak');

        $message = view('emails/member_rejected', [
            'member' => $member,
            'reason' => $reason
        ]);
        $emailService->setMessage($message);
        $emailService->send();
    }

    public function edit($id)
    {
        $member = $this->memberModel->find($id);
        if (!$member) {
            return redirect()->to('admin/members')->with('error', 'Anggota tidak ditemukan.');
        }

        $data = [
            'title'  => 'Edit Anggota',
            'member' => $member,
            'user'   => $this->userModel->where('member_id', $id)->first()
        ];

        return view('admin/members/edit', $data);
    }

    /**
     * Memproses pembaruan data anggota.
     */
    public function update($id)
    {
        $rules = [
            'nama_lengkap'    => 'required|min_length[3]',
            'nomor_anggota'   => "required|is_unique[members.nomor_anggota,id,{$id}]",
            'status_keanggotaan' => 'required|in_list[pending,active,suspended,terminated]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->memberModel->update($id, [
            'nama_lengkap'       => $this->request->getPost('nama_lengkap'),
            'nomor_anggota'      => $this->request->getPost('nomor_anggota'),
            'nomor_whatsapp'     => $this->request->getPost('nomor_telepon'), // Sesuaikan dengan nama field di view
            'alamat_lengkap'     => $this->request->getPost('alamat'), // Sesuaikan dengan nama field di view
            'status_keanggotaan' => $this->request->getPost('status_keanggotaan'),
            'tanggal_bergabung'  => $this->request->getPost('tanggal_bergabung'),
        ]);

        return redirect()->to('admin/members/view/' . $id)->with('success', 'Data anggota berhasil diperbarui.');
    }
}
