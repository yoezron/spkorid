<?php
// ============================================
// USER MANAGEMENT CONTROLLER
// ============================================

// app/Controllers/Admin/UserController.php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\MemberModel;
use App\Models\RoleModel;
use App\Models\ActivityLogModel;

class UserController extends BaseController
{
    protected $userModel;
    protected $memberModel;
    protected $roleModel;
    protected $activityLog;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->memberModel = new MemberModel();
        $this->roleModel = new RoleModel();
        $this->activityLog = new ActivityLogModel();
    }

    /**
     * List all users
     */
    public function index()
    {
        $data = [
            'title' => 'User Management - SPK',
            'users' => $this->userModel->select('users.*, roles.role_name, members.nama_lengkap, members.nomor_anggota')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->join('members', 'members.id = users.member_id', 'left')
                ->findAll()
        ];

        return view('admin/users/index', $data);
    }

    /**
     * Create new user
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah User - SPK',
            'roles' => $this->roleModel->where('is_active', 1)->findAll(),
            'members' => $this->memberModel->select('members.id, members.nama_lengkap, members.nomor_anggota')
                ->join('users', 'users.member_id = members.id', 'left')
                ->where('users.id IS NULL')
                ->findAll()
        ];

        return view('admin/users/create', $data);
    }

    /**
     * Store new user
     */
    public function store()
    {
        $rules = [
            'member_id' => 'required|numeric',
            'username' => 'required|min_length[3]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'role_id' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userData = [
            'member_id' => $this->request->getPost('member_id'),
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role_id' => $this->request->getPost('role_id'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'is_verified' => 1
        ];

        $this->userModel->insert($userData);

        // Log activity
        $this->activityLog->logActivity(
            session()->get('user_id'),
            'create_user',
            'Created user: ' . $userData['username']
        );

        return redirect()->to('/admin/users')->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Edit user
     */
    public function edit($id)
    {
        $user = $this->userModel->getUserWithDetails($id);

        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan');
        }

        $data = [
            'title' => 'Edit User - SPK',
            'user' => $user,
            'roles' => $this->roleModel->where('is_active', 1)->findAll()
        ];

        return view('admin/users/edit', $data);
    }

    /**
     * Update user
     */
    public function update($id)
    {
        $rules = [
            'username' => 'required|min_length[3]',
            'email' => 'required|valid_email',
            'role_id' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'role_id' => $this->request->getPost('role_id'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        // Update password if provided
        $newPassword = $this->request->getPost('password');
        if (!empty($newPassword)) {
            $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $updateData);

        // Log activity
        $this->activityLog->logActivity(
            session()->get('user_id'),
            'update_user',
            'Updated user ID: ' . $id
        );

        return redirect()->to('/admin/users')->with('success', 'User berhasil diperbarui');
    }

    /**
     * Reset user password
     */
    public function resetPassword($id)
    {
        $newPassword = bin2hex(random_bytes(4)); // Generate 8 character password

        $this->userModel->update($id, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            'reset_token' => null,
            'reset_token_expires' => null
        ]);

        // Send email with new password
        $user = $this->userModel->find($id);
        $this->sendPasswordResetEmail($user, $newPassword);

        // Log activity
        $this->activityLog->logActivity(
            session()->get('user_id'),
            'reset_password',
            'Reset password for user ID: ' . $id
        );

        return redirect()->back()->with('success', 'Password berhasil direset dan dikirim ke email user');
    }

    /**
     * Toggle user status
     */
    public function toggleStatus($id)
    {
        $user = $this->userModel->find($id);

        $this->userModel->update($id, [
            'is_active' => !$user['is_active']
        ]);

        $status = !$user['is_active'] ? 'activated' : 'deactivated';

        // Log activity
        $this->activityLog->logActivity(
            session()->get('user_id'),
            'toggle_user_status',
            'User ' . $status . ' ID: ' . $id
        );

        return redirect()->back()->with('success', 'Status user berhasil diubah');
    }

    /**
     * View user activity
     */
    public function viewActivity($id)
    {
        $user = $this->userModel->getUserWithDetails($id);

        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan');
        }

        $data = [
            'title' => 'Aktivitas User - SPK',
            'user' => $user,
            'activities' => $this->activityLog->getUserActivities($id, 100)
        ];

        return view('admin/users/activity', $data);
    }

    /**
     * Send password reset email
     */
    private function sendPasswordResetEmail($user, $newPassword)
    {
        $emailService = \Config\Services::email();

        $emailService->setFrom('noreply@spk.org', 'SPK Indonesia');
        $emailService->setTo($user['email']);
        $emailService->setSubject('Password Reset - SPK');

        $message = view('emails/admin_password_reset', [
            'username' => $user['username'],
            'new_password' => $newPassword
        ]);

        $emailService->setMessage($message);
        $emailService->send();
    }
}
