<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MenuModel;

class MenuController extends BaseController
{
    protected $menuModel;

    public function __construct()
    {
        $this->menuModel = new MenuModel();
    }

    public function index()
    {
        // Ambil semua parent menus
        $parentMenus = $this->menuModel->where('parent_id', null)->orderBy('menu_order', 'ASC')->findAll();

        // Untuk setiap parent menu, tambahkan submenus
        foreach ($parentMenus as &$menu) {
            $menu['submenus'] = $this->menuModel->where('parent_id', $menu['id'])->orderBy('menu_order', 'ASC')->findAll();
        }

        $data = [
            'title' => 'Menu Management - SPK',
            'menus' => $parentMenus
        ];

        return view('admin/menus/index', $data);
    }

    public function create($parentId = null)
    {
        $data = [
            'title' => $parentId ? 'Tambah Sub Menu Baru - SPK' : 'Tambah Menu Baru - SPK',
            'parent_menus' => $this->menuModel->where('parent_id', null)->findAll(),
            'parent_id' => $parentId  // Pass the parent ID to view
        ];

        return view('admin/menus/create', $data);
    }

    public function store()
    {
        $rules = [
            'menu_name' => 'required|min_length[3]',
            'menu_url' => 'required',
            'menu_icon' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->menuModel->save([
            'menu_name' => $this->request->getPost('menu_name'),
            'menu_url' => $this->request->getPost('menu_url'),
            'menu_icon' => $this->request->getPost('menu_icon'),
            'parent_id' => $this->request->getPost('parent_id'),
            'menu_order' => $this->request->getPost('menu_order') ?? 0,
            'is_active' => $this->request->getPost('is_active') ?? 1
        ]);

        return redirect()->to('/admin/menus')->with('success', 'Menu berhasil ditambahkan');
    }

    public function edit($id)
    {
        $menu = $this->menuModel->find($id);

        if (!$menu) {
            return redirect()->back()->with('error', 'Menu tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Menu - SPK',
            'menu' => $menu,
            'parent_menus' => $this->menuModel->where('parent_id', null)->where('id !=', $id)->findAll()
        ];

        return view('admin/menus/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'menu_name' => 'required|min_length[3]',
            'menu_url' => 'required',
            'menu_icon' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->menuModel->update($id, [
            'menu_name' => $this->request->getPost('menu_name'),
            'menu_url' => $this->request->getPost('menu_url'),
            'menu_icon' => $this->request->getPost('menu_icon'),
            'parent_id' => $this->request->getPost('parent_id'),
            'menu_order' => $this->request->getPost('menu_order') ?? 0,
            'is_active' => $this->request->getPost('is_active') ?? 1
        ]);

        return redirect()->to('/admin/menus')->with('success', 'Menu berhasil diperbarui');
    }

    public function delete($id)
    {
        $this->menuModel->delete($id);
        return redirect()->to('/admin/menus')->with('success', 'Menu berhasil dihapus');
    }

    public function reorder()
    {
        $order = $this->request->getPost('order');

        if ($order) {
            foreach ($order as $position => $menuId) {
                $this->menuModel->update($menuId, ['menu_order' => $position]);
            }
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function toggle($id)
    {
        $menu = $this->menuModel->find($id);

        if ($menu) {
            $this->menuModel->update($id, [
                'is_active' => !$menu['is_active']
            ]);
        }

        return redirect()->back()->with('success', 'Status menu berhasil diubah');
    }
}
