<?php

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
     * (PERBAIKAN DI METHOD INI)
     */
    public function index()
    {
        // Menggunakan Query Builder untuk mengambil data yang dibutuhkan view
        $roles = $this->roleModel
            ->select('roles.id, roles.role_name, roles.role_description as description, COUNT(users.id) as user_count')
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
     * Menyimpan data role baru ke database.
     */
    public function store()
    {
        $rules = [
            'role_name'        => 'required|is_unique[roles.role_name]|alpha_dash',
            'role_description' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->roleModel->save([
            'role_name'        => strtolower($this->request->getPost('role_name')),
            'role_description' => $this->request->getPost('role_description'),
            'is_active'        => $this->request->getPost('is_active') ?? 0,
        ]);

        return redirect()->to('/admin/roles')->with('success', 'Role baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit role.
     */
    public function edit($id)
    {
        $data = [
            'title' => 'Edit Role',
            'role'  => $this->roleModel->find($id),
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

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->roleModel->update($id, [
            'role_name'        => strtolower($this->request->getPost('role_name')),
            'role_description' => $this->request->getPost('role_description'),
            'is_active'        => $this->request->getPost('is_active') ?? 0,
        ]);

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

        $this->roleModel->delete($id);
        return redirect()->to('/admin/roles')->with('success', 'Role berhasil dihapus.');
    }

    /**
     * Menampilkan halaman untuk mengatur hak akses menu.
     */
    public function permissions($id)
    {
        $role = $this->roleModel->find($id);
        if (! $role) {
            return redirect()->back()->with('error', 'Role tidak ditemukan.');
        }

        $role_permissions = $this->roleModel->getRoleWithPermissions($id)['permissions'];

        // Format ulang array permissions agar key-nya adalah menu_id
        $formatted_permissions = [];
        foreach ($role_permissions as $permission) {
            $formatted_permissions[$permission['menu_id']] = $permission;
        }

        $data = [
            'title'       => 'Hak Akses Role: ' . $role['role_name'],
            'role'        => $role,
            'permissions' => $formatted_permissions, // Kirim data yang sudah diformat
            'menus'       => $this->menuModel->buildMenuTree(null),
        ];

        return view('admin/roles/permissions', $data);
    }

    /**
     * Mengupdate hak akses menu untuk sebuah role.
     */
    public function updatePermissions($id)
    {
        $permissions = $this->request->getPost('permissions');
        $db = \Config\Database::connect();

        // 1. Hapus semua permission lama untuk role ini
        $db->table('role_menu_access')->where('role_id', $id)->delete();

        // 2. Masukkan permission yang baru (yang dicentang)
        if (! empty($permissions)) {
            foreach ($permissions as $menu_id => $actions) {
                $data = [
                    'role_id'    => $id,
                    'menu_id'    => $menu_id,
                    'can_view'   => isset($actions['can_view']) ? 1 : 0,
                    'can_add'    => isset($actions['can_add']) ? 1 : 0,
                    'can_edit'   => isset($actions['can_edit']) ? 1 : 0,
                    'can_delete' => isset($actions['can_delete']) ? 1 : 0,
                ];
                $db->table('role_menu_access')->insert($data);
            }
        }

        return redirect()->to('/admin/roles')->with('success', 'Hak akses berhasil diperbarui.');
    }
}
