<?php
// ============================================
// CONTROLLER UNTUK ROLE MANAGEMENT (FIXED)
// ============================================

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RoleModel;
use App\Models\MenuModel;

class RoleController extends BaseController
{
    protected $roleModel;
    protected $menuModel;

    public function __construct()
    {
        $this->roleModel = new RoleModel();
        $this->menuModel = new MenuModel();
    }

    /**
     * Menampilkan daftar semua role.
     */
    public function index()
    {
        // Menggunakan Query Builder untuk mengambil data yang dibutuhkan view
        $roles = $this->roleModel
            ->select('roles.id, roles.role_name, roles.role_description, COUNT(users.id) as user_count') // PERBAIKAN: Hapus alias 'as description'
            ->join('users', 'users.role_id = roles.id', 'left')
            ->groupBy('roles.id, roles.role_name, roles.role_description')
            ->findAll();

        $data = [
            'title' => 'Manajemen Role & Hak Akses',
            'roles' => $roles,
        ];
        return view('admin/roles/index', $data);
    }

    /**
     * Menampilkan form untuk membuat role baru.
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Role Baru',
        ];
        return view('admin/roles/create', $data);
    }

    /**
     * Menyimpan role baru ke database.
     */
    public function store()
    {
        $rules = [
            'role_name'        => 'required|is_unique[roles.role_name]|alpha_dash',
            'role_description' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->roleModel->save([
            'role_name'        => strtolower($this->request->getPost('role_name')),
            'role_description' => $this->request->getPost('role_description'),
            'is_active'        => $this->request->getPost('is_active') ?? 0,
        ]);

        return redirect()->to('/admin/roles')->with('success', 'Role berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail role.
     */
    public function show($id)
    {
        $roleWithPermissions = $this->roleModel->getRoleWithPermissions($id);

        if (!$roleWithPermissions) {
            return redirect()->back()->with('error', 'Role tidak ditemukan.');
        }

        $data = [
            'title' => 'Detail Role: ' . $roleWithPermissions['role_name'],
            'role'  => $roleWithPermissions,
        ];

        return view('admin/roles/show', $data);
    }

    /**
     * Menampilkan form untuk mengedit role.
     */
    public function edit($id)
    {
        $role = $this->roleModel->find($id);

        if (!$role) {
            return redirect()->back()->with('error', 'Role tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Role',
            'role'  => $role,
        ];
        return view('admin/roles/edit', $data);
    }

    /**
     * Mengupdate data role di database.
     */
    public function update($id)
    {
        $rules = [
            'role_name'        => "required|is_unique[roles.role_name,id,{$id}]|alpha_dash",
            'role_description' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'role_name'        => strtolower($this->request->getPost('role_name')),
            'role_description' => $this->request->getPost('role_description'),
            'is_active'        => $this->request->getPost('is_active') ?? 0,
        ];

        if (!$this->roleModel->update($id, $updateData)) {
            return redirect()->back()->with('error', 'Gagal memperbarui role.');
        }

        return redirect()->to('/admin/roles')->with('success', 'Role berhasil diperbarui.');
    }

    /**
     * Menghapus role dari database.
     */
    public function delete($id)
    {
        // Mencegah penghapusan role default
        if ($id <= 3) {
            return redirect()->back()->with('error', 'Role default tidak dapat dihapus.');
        }

        // Check if role has users
        $db = \Config\Database::connect();
        $userCount = $db->table('users')->where('role_id', $id)->countAllResults();

        if ($userCount > 0) {
            return redirect()->back()->with('error', 'Role tidak dapat dihapus karena masih digunakan oleh ' . $userCount . ' pengguna.');
        }

        // Delete role permissions first
        $db->table('role_menu_access')->where('role_id', $id)->delete();

        // Delete role
        if (!$this->roleModel->delete($id)) {
            return redirect()->back()->with('error', 'Gagal menghapus role.');
        }

        return redirect()->to('/admin/roles')->with('success', 'Role berhasil dihapus.');
    }

    /**
     * Menampilkan halaman untuk mengatur hak akses menu.
     */
    public function permissions($id)
    {
        $role = $this->roleModel->find($id);
        if (!$role) {
            return redirect()->back()->with('error', 'Role tidak ditemukan.');
        }

        // Get current role permissions
        $rolePermissions = $this->roleModel->getRoleWithPermissions($id);
        $currentPermissions = $rolePermissions['permissions'] ?? [];

        // Format permissions array with menu_id as key
        $formattedPermissions = [];
        foreach ($currentPermissions as $permission) {
            $formattedPermissions[$permission['menu_id']] = $permission;
        }

        // Get all menus for permission management (no role filtering)
        $menus = $this->menuModel->buildMenuTreeForPermissions();

        $data = [
            'title'       => 'Hak Akses Role: ' . $role['role_name'],
            'role'        => $role,
            'permissions' => $formattedPermissions,
            'menus'       => $menus,
        ];

        return view('admin/roles/permissions', $data);
    }

    /**
     * Mengupdate hak akses menu untuk sebuah role.
     */
    public function updatePermissions($id)
    {
        $role = $this->roleModel->find($id);
        if (!$role) {
            return redirect()->back()->with('error', 'Role tidak ditemukan.');
        }

        // Prevent modifying super admin permissions
        if ($id == 1) {
            return redirect()->back()->with('error', 'Hak akses Super Admin tidak dapat diubah.');
        }

        $permissions = $this->request->getPost('permissions') ?? [];
        $db = \Config\Database::connect();

        try {
            // Begin transaction
            $db->transBegin();

            // 1. Delete all existing permissions for this role
            $db->table('role_menu_access')->where('role_id', $id)->delete();

            // 2. Insert new permissions (only for checked items)
            if (!empty($permissions)) {
                foreach ($permissions as $menuId => $actions) {
                    // Only insert if at least one permission is granted
                    $canView = isset($actions['can_view']) ? 1 : 0;
                    $canAdd = isset($actions['can_add']) ? 1 : 0;
                    $canEdit = isset($actions['can_edit']) ? 1 : 0;
                    $canDelete = isset($actions['can_delete']) ? 1 : 0;

                    // If any permission is granted, insert the record
                    if ($canView || $canAdd || $canEdit || $canDelete) {
                        $data = [
                            'role_id'    => $id,
                            'menu_id'    => $menuId,
                            'can_view'   => $canView,
                            'can_add'    => $canAdd,
                            'can_edit'   => $canEdit,
                            'can_delete' => $canDelete,
                            'created_at' => date('Y-m-d H:i:s')
                        ];

                        $db->table('role_menu_access')->insert($data);
                    }
                }
            }

            // Commit transaction
            if ($db->transStatus() === false) {
                $db->transRollback();
                return redirect()->back()->with('error', 'Gagal memperbarui hak akses.');
            } else {
                $db->transCommit();
                return redirect()->to('/admin/roles')->with('success', 'Hak akses untuk role "' . $role['role_name'] . '" berhasil diperbarui.');
            }
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error updating permissions: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui hak akses: ' . $e->getMessage());
        }
    }

    /**
     * Mengaktifkan/menonaktifkan role
     */
    public function toggleStatus($id)
    {
        // Prevent disabling default roles
        if ($id <= 3) {
            return redirect()->back()->with('error', 'Status role default tidak dapat diubah.');
        }

        $role = $this->roleModel->find($id);
        if (!$role) {
            return redirect()->back()->with('error', 'Role tidak ditemukan.');
        }

        $newStatus = $role['is_active'] ? 0 : 1;
        $this->roleModel->update($id, ['is_active' => $newStatus]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Role '{$role['role_name']}' berhasil {$statusText}.");
    }

    /**
     * Copy permissions from one role to another
     */
    public function copyPermissions()
    {
        $sourceRoleId = $this->request->getPost('source_role_id');
        $targetRoleId = $this->request->getPost('target_role_id');

        if (!$sourceRoleId || !$targetRoleId) {
            return redirect()->back()->with('error', 'Role sumber dan target harus dipilih.');
        }

        if ($sourceRoleId == $targetRoleId) {
            return redirect()->back()->with('error', 'Role sumber dan target tidak boleh sama.');
        }

        // Prevent copying to/from super admin
        if ($targetRoleId == 1) {
            return redirect()->back()->with('error', 'Tidak dapat mengubah hak akses Super Admin.');
        }

        $db = \Config\Database::connect();

        try {
            $db->transBegin();

            // Get source permissions
            $sourcePermissions = $db->table('role_menu_access')
                ->where('role_id', $sourceRoleId)
                ->get()
                ->getResultArray();

            if (empty($sourcePermissions)) {
                return redirect()->back()->with('error', 'Role sumber tidak memiliki hak akses yang dapat disalin.');
            }

            // Delete existing permissions for target role
            $db->table('role_menu_access')->where('role_id', $targetRoleId)->delete();

            // Copy permissions
            foreach ($sourcePermissions as $permission) {
                $newPermission = [
                    'role_id'    => $targetRoleId,
                    'menu_id'    => $permission['menu_id'],
                    'can_view'   => $permission['can_view'],
                    'can_add'    => $permission['can_add'],
                    'can_edit'   => $permission['can_edit'],
                    'can_delete' => $permission['can_delete'],
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $db->table('role_menu_access')->insert($newPermission);
            }

            if ($db->transStatus() === false) {
                $db->transRollback();
                return redirect()->back()->with('error', 'Gagal menyalin hak akses.');
            } else {
                $db->transCommit();

                $sourceRole = $this->roleModel->find($sourceRoleId);
                $targetRole = $this->roleModel->find($targetRoleId);

                return redirect()->back()->with(
                    'success',
                    "Hak akses berhasil disalin dari role '{$sourceRole['role_name']}' ke role '{$targetRole['role_name']}'."
                );
            }
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error copying permissions: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyalin hak akses.');
        }
    }
}
