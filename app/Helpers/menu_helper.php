<?php
// app/Helpers/menu_helper.php (PERBAIKAN FINAL)

if (!function_exists('generate_sidebar_menu')) {
    /**
     * Menghasilkan menu sidebar berdasarkan peran pengguna.
     */
    function generate_sidebar_menu()
    {
        if (!is_logged_in()) {
            return [];
        }

        $db = \Config\Database::connect();
        $roleId = (int) session()->get('role_id');

        // 1. Ambil menu utama (parent)
        $builder = $db->table('menus m')
            ->select('m.*')
            ->join('role_menu_access rma', 'rma.menu_id = m.id', 'left')
            ->where('m.is_active', 1)
            ->where('m.parent_id IS NULL');

        // Gabungkan kondisi untuk hak akses normal dan super admin
        $builder->groupStart()
            ->where('rma.role_id', $roleId)
            ->where('rma.can_view', 1)
            ->orWhere("{$roleId} = 1") // <-- INI PERBAIKANNYA: Menggunakan raw string
            ->groupEnd();

        $builder->distinct()->orderBy('m.menu_order', 'ASC');

        $menus = $builder->get()->getResultArray();

        // 2. Untuk setiap menu utama, ambil submenu-nya
        foreach ($menus as &$menu) {
            $menu['submenus'] = get_submenus($menu['id'], $roleId);
            // Menyeragamkan key untuk kemudahan di view
            $menu['url'] = $menu['menu_url'];
            $menu['icon'] = $menu['menu_icon'];
            $menu['title'] = $menu['menu_name'];
        }

        return $menus;
    }
}

if (!function_exists('get_submenus')) {
    /**
     * Mengambil submenu untuk menu parent tertentu.
     */
    function get_submenus($parentId, $roleId)
    {
        $db = \Config\Database::connect();
        $roleId = (int) $roleId;

        $builder = $db->table('menus m')
            ->select('m.*')
            ->join('role_menu_access rma', 'rma.menu_id = m.id', 'left')
            ->where('m.is_active', 1)
            ->where('m.parent_id', $parentId);

        $builder->groupStart()
            ->where('rma.role_id', $roleId)
            ->where('rma.can_view', 1)
            ->orWhere("{$roleId} = 1") // <-- INI PERBAIKANNYA: Menggunakan raw string
            ->groupEnd();

        $builder->distinct()->orderBy('m.menu_order', 'ASC');

        $submenus = $builder->get()->getResultArray();

        // Menyeragamkan key untuk submenu
        foreach ($submenus as &$submenu) {
            $submenu['url'] = $submenu['menu_url'];
            $submenu['icon'] = $submenu['menu_icon'];
            $submenu['title'] = $submenu['menu_name'];
        }

        return $submenus;
    }
}


if (!function_exists('get_submenus')) {
    /**
     * Get submenus for a parent menu
     */
    function get_submenus($parentId, $roleId)
    {
        $db = \Config\Database::connect();

        $builder = $db->table('menus m')
            ->select('m.*')
            ->join('role_menu_access rma', 'rma.menu_id = m.id', 'left')
            ->where('m.is_active', 1)
            ->where('m.parent_id', $parentId)
            ->groupStart()
            ->where('rma.role_id', $roleId)
            ->where('rma.can_view', 1)
            ->orWhere('m.id IS NOT NULL AND ' . $roleId . ' = 1') // Super admin
            ->groupEnd()
            ->orderBy('m.menu_order', 'ASC'); // Changed from order_priority to menu_order

        $submenus = $builder->get()->getResultArray();

        foreach ($submenus as &$submenu) {
            $submenu['is_active'] = is_menu_active($submenu['menu_url']);
            $submenu['url'] = $submenu['menu_url'];
            $submenu['icon'] = $submenu['menu_icon'];
            $submenu['title'] = $submenu['menu_name'];
        }

        return $submenus;
    }
}

if (!function_exists('is_menu_active')) {
    /**
     * Check if menu is active based on current URL
     */
    function is_menu_active($menuUrl)
    {
        if (empty($menuUrl)) {
            return false;
        }
        $currentUrl = current_url();
        $baseUrl = base_url();
        $currentPath = str_replace($baseUrl, '', $currentUrl);
        $currentPath = '/' . ltrim($currentPath, '/');
        $menuPath = '/' . ltrim($menuUrl, '/');
        if ($currentPath === $menuPath) {
            return true;
        }
        return strpos($currentPath, $menuPath) === 0;
    }
}

if (!function_exists('get_menu_by_role')) {
    /**
     * Get predefined menus by role (fallback if database is empty)
     */
    function get_menu_by_role($roleId = null)
    {
        if ($roleId === null) {
            $roleId = session()->get('role_id');
        }
        $menus = [
            // Super Admin Menus
            1 => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard', 'icon' => 'dashboard'],
                ['title' => 'Role Management', 'url' => '/admin/roles', 'icon' => 'supervisor_account'],
                ['title' => 'Member List', 'url' => '/admin/members', 'icon' => 'people', 'menu_name' => 'Member List', 'menu_url' => '/admin/members', 'menu_icon' => 'people'],
                ['title' => 'Menu Management', 'url' => '/admin/menus', 'icon' => 'menu', 'menu_name' => 'Menu Management', 'menu_url' => '/admin/menus', 'menu_icon' => 'menu'],
                ['title' => 'Content Management', 'url' => '/admin/content', 'icon' => 'article', 'menu_name' => 'Content Management', 'menu_url' => '/admin/content', 'menu_icon' => 'article'],
                ['title' => 'System Settings', 'url' => '/admin/settings', 'icon' => 'settings', 'menu_name' => 'System Settings', 'menu_url' => '/admin/settings', 'menu_icon' => 'settings'],
                ['title' => 'Activity Logs', 'url' => '/admin/logs', 'icon' => 'history', 'menu_name' => 'Activity Logs', 'menu_url' => '/admin/logs', 'menu_icon' => 'history'],
            ],

            // Pengurus Menus
            2 => [
                ['title' => 'Dashboard', 'url' => '/pengurus/dashboard', 'icon' => 'dashboard'],
                ['title' => 'Konfirmasi Anggota', 'url' => '/pengurus/members/pending', 'icon' => 'verified_user', 'menu_name' => 'Konfirmasi Anggota', 'menu_url' => '/pengurus/members/pending', 'menu_icon' => 'verified_user'],
                ['title' => 'Kelola Anggota', 'url' => '/pengurus/members', 'icon' => 'manage_accounts', 'menu_name' => 'Kelola Anggota', 'menu_url' => '/pengurus/members', 'menu_icon' => 'manage_accounts'],
                ['title' => 'Verifikasi Pembayaran', 'url' => '/pengurus/payments', 'icon' => 'payment', 'menu_name' => 'Verifikasi Pembayaran', 'menu_url' => '/pengurus/payments', 'menu_icon' => 'payment'],
                ['title' => 'Kirim Informasi', 'url' => '/pengurus/informasi/create', 'icon' => 'campaign', 'menu_name' => 'Kirim Informasi', 'menu_url' => '/pengurus/informasi/create', 'menu_icon' => 'campaign'],
                ['title' => 'Tulis Blog', 'url' => '/pengurus/blog/create', 'icon' => 'create', 'menu_name' => 'Tulis Blog', 'menu_url' => '/pengurus/blog/create', 'menu_icon' => 'create'],
                ['title' => 'Pengaduan Masuk', 'url' => '/pengurus/complaints', 'icon' => 'report', 'menu_name' => 'Pengaduan Masuk', 'menu_url' => '/pengurus/complaints', 'menu_icon' => 'report'],
                ['title' => 'Data Survei', 'url' => '/pengurus/surveys/results', 'icon' => 'analytics', 'menu_name' => 'Data Survei', 'menu_url' => '/pengurus/surveys/results', 'menu_icon' => 'analytics'],
                ['title' => 'Buat Survei', 'url' => '/pengurus/surveys/create', 'icon' => 'poll', 'menu_name' => 'Buat Survei', 'menu_url' => '/pengurus/surveys/create', 'menu_icon' => 'poll'],
            ],

            // Member Menus
            3 => [
                ['title' => 'Dashboard', 'url' => '/member/dashboard', 'icon' => 'dashboard'],
                ['title' => 'Profile', 'url' => '/member/profile', 'icon' => 'person', 'menu_name' => 'Profile', 'menu_url' => '/member/profile', 'menu_icon' => 'person'],
                ['title' => 'Kartu Anggota', 'url' => '/member/card', 'icon' => 'badge', 'menu_name' => 'Kartu Anggota', 'menu_url' => '/member/card', 'menu_icon' => 'badge'],
                ['title' => 'Edit Profile', 'url' => '/member/profile/edit', 'icon' => 'edit', 'menu_name' => 'Edit Profile', 'menu_url' => '/member/profile/edit', 'menu_icon' => 'edit'],
                ['title' => 'AD/ART', 'url' => '/member/documents/ad-art', 'icon' => 'description', 'menu_name' => 'AD/ART', 'menu_url' => '/member/documents/ad-art', 'menu_icon' => 'description'],
                ['title' => 'Manifesto', 'url' => '/member/documents/manifesto', 'icon' => 'campaign', 'menu_name' => 'Manifesto', 'menu_url' => '/member/documents/manifesto', 'menu_icon' => 'campaign'],
                ['title' => 'Informasi Serikat', 'url' => '/member/informasi', 'icon' => 'info', 'menu_name' => 'Informasi Serikat', 'menu_url' => '/member/informasi', 'menu_icon' => 'info'],
                ['title' => 'Forum Diskusi', 'url' => '/member/forum', 'icon' => 'forum', 'menu_name' => 'Forum Diskusi', 'menu_url' => '/member/forum', 'menu_icon' => 'forum'],
                ['title' => 'Survei Anggota', 'url' => '/member/surveys', 'icon' => 'poll', 'menu_name' => 'Survei Anggota', 'menu_url' => '/member/surveys', 'menu_icon' => 'poll'],
                ['title' => 'Sejarah SPK', 'url' => '/member/documents/sejarah', 'icon' => 'history', 'menu_name' => 'Sejarah SPK', 'menu_url' => '/member/documents/sejarah', 'menu_icon' => 'history'],
                ['title' => 'Ubah Password', 'url' => '/member/change-password', 'icon' => 'lock', 'menu_name' => 'Ubah Password', 'menu_url' => '/member/change-password', 'menu_icon' => 'lock'],
            ],
        ];
        return $menus[$roleId] ?? [];
    }
}

if (!function_exists('is_logged_in')) {
    function is_logged_in()
    {
        $session = session();
        return $session->get('logged_in') === true;
    }
}

if (!function_exists('get_dashboard_url')) {
    function get_dashboard_url()
    {
        $roleId = session()->get('role_id');
        switch ($roleId) {
            case 1:
                return base_url('/admin/dashboard');
            case 2:
                return base_url('/pengurus/dashboard');
            case 3:
                return base_url('/member/dashboard');
            default:
                return base_url('/dashboard');
        }
    }
}
