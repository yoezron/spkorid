<?php
// app/Helpers/menu_helper.php

if (!function_exists('generate_sidebar_menu')) {
    /**
     * Generate sidebar menu based on user role
     */
    function generate_sidebar_menu()
    {
        if (!is_logged_in()) {
            return [];
        }

        $db = \Config\Database::connect();
        $roleId = role_id();

        // Get menus for current role
        $builder = $db->table('menus m')
            ->select('m.*')
            ->join('role_menu_access rma', 'rma.menu_id = m.id', 'left')
            ->where('m.is_active', 1)
            ->where('m.parent_id', null) // Only parent menus
            ->groupStart()
            ->where('rma.role_id', $roleId)
            ->where('rma.can_view', 1)
            ->orWhere('1 =', $roleId) // Super admin can see all
            ->groupEnd()
            ->orderBy('m.order_priority', 'ASC');

        $menus = $builder->get()->getResultArray();

        // Get submenus for each parent
        foreach ($menus as &$menu) {
            $menu['submenus'] = get_submenus($menu['id'], $roleId);
            $menu['is_active'] = is_menu_active($menu['url']);
        }

        return $menus;
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
            ->orWhere('1 =', $roleId) // Super admin
            ->groupEnd()
            ->orderBy('m.order_priority', 'ASC');

        $submenus = $builder->get()->getResultArray();

        foreach ($submenus as &$submenu) {
            $submenu['is_active'] = is_menu_active($submenu['url']);
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
        $currentUrl = current_url();
        $baseUrl = base_url();

        // Remove base URL to get the path
        $currentPath = str_replace($baseUrl, '', $currentUrl);
        $menuPath = ltrim($menuUrl, '/');

        // Check if current path starts with menu path
        if (empty($menuPath)) {
            return $currentPath === '/' || $currentPath === '';
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
            $roleId = role_id();
        }

        $menus = [
            // Super Admin Menus
            1 => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard', 'icon' => 'dashboard'],
                ['title' => 'Role Management', 'url' => '/admin/roles', 'icon' => 'supervisor_account'],
                ['title' => 'Member List', 'url' => '/admin/members', 'icon' => 'people'],
                ['title' => 'Menu Management', 'url' => '/admin/menus', 'icon' => 'menu'],
                ['title' => 'Content Management', 'url' => '/admin/content', 'icon' => 'article'],
                ['title' => 'System Settings', 'url' => '/admin/settings', 'icon' => 'settings'],
                ['title' => 'Activity Logs', 'url' => '/admin/logs', 'icon' => 'history'],
            ],

            // Pengurus Menus
            2 => [
                ['title' => 'Dashboard', 'url' => '/pengurus/dashboard', 'icon' => 'dashboard'],
                ['title' => 'Konfirmasi Anggota', 'url' => '/pengurus/members/pending', 'icon' => 'verified_user'],
                ['title' => 'Kelola Anggota', 'url' => '/pengurus/members', 'icon' => 'manage_accounts'],
                ['title' => 'Verifikasi Pembayaran', 'url' => '/pengurus/payments', 'icon' => 'payment'],
                ['title' => 'Kirim Informasi', 'url' => '/pengurus/informasi/create', 'icon' => 'campaign'],
                ['title' => 'Tulis Blog', 'url' => '/pengurus/blog/create', 'icon' => 'create'],
                ['title' => 'Pengaduan Masuk', 'url' => '/pengurus/complaints', 'icon' => 'report'],
                ['title' => 'Data Survei', 'url' => '/pengurus/surveys/results', 'icon' => 'analytics'],
                ['title' => 'Buat Survei', 'url' => '/pengurus/surveys/create', 'icon' => 'poll'],
            ],

            // Member Menus
            3 => [
                ['title' => 'Profile', 'url' => '/member/profile', 'icon' => 'person'],
                ['title' => 'Kartu Anggota', 'url' => '/member/card', 'icon' => 'badge'],
                ['title' => 'Edit Profile', 'url' => '/member/profile/edit', 'icon' => 'edit'],
                ['title' => 'AD/ART', 'url' => '/member/documents/ad-art', 'icon' => 'description'],
                ['title' => 'Manifesto', 'url' => '/member/documents/manifesto', 'icon' => 'campaign'],
                ['title' => 'Informasi Serikat', 'url' => '/member/informasi', 'icon' => 'info'],
                ['title' => 'Forum Diskusi', 'url' => '/member/forum', 'icon' => 'forum'],
                ['title' => 'Survei Anggota', 'url' => '/member/surveys', 'icon' => 'poll'],
                ['title' => 'Sejarah SPK', 'url' => '/member/documents/sejarah', 'icon' => 'history'],
                ['title' => 'Ubah Password', 'url' => '/member/change-password', 'icon' => 'lock'],
            ],
        ];

        return $menus[$roleId] ?? [];
    }
}

if (!function_exists('render_sidebar_menu')) {
    /**
     * Render sidebar menu HTML
     */
    function render_sidebar_menu()
    {
        $menus = generate_sidebar_menu();

        // If no menus from database, use fallback
        if (empty($menus)) {
            $menus = get_menu_by_role();
        }

        $html = '<ul class="sidebar-menu">';

        foreach ($menus as $menu) {
            $hasSubmenu = isset($menu['submenus']) && !empty($menu['submenus']);
            $isActive = $menu['is_active'] ?? false;
            $icon = $menu['icon'] ?? 'circle';
            $url = $menu['url'] ?? '#';
            $title = $menu['title'] ?? $menu['menu_name'] ?? 'Menu';

            if ($hasSubmenu) {
                $html .= '<li class="menu-item has-submenu' . ($isActive ? ' active' : '') . '">';
                $html .= '<a href="#" class="menu-link" data-toggle="submenu">';
                $html .= '<i class="material-icons">' . $icon . '</i>';
                $html .= '<span>' . $title . '</span>';
                $html .= '<i class="material-icons submenu-arrow">keyboard_arrow_down</i>';
                $html .= '</a>';
                $html .= '<ul class="submenu">';

                foreach ($menu['submenus'] as $submenu) {
                    $subIsActive = $submenu['is_active'] ?? false;
                    $subIcon = $submenu['icon'] ?? 'chevron_right';
                    $subUrl = $submenu['url'] ?? '#';
                    $subTitle = $submenu['title'] ?? $submenu['menu_name'] ?? 'Submenu';

                    $html .= '<li class="submenu-item' . ($subIsActive ? ' active' : '') . '">';
                    $html .= '<a href="' . base_url($subUrl) . '" class="submenu-link">';
                    $html .= '<i class="material-icons">' . $subIcon . '</i>';
                    $html .= '<span>' . $subTitle . '</span>';
                    $html .= '</a>';
                    $html .= '</li>';
                }

                $html .= '</ul>';
                $html .= '</li>';
            } else {
                $html .= '<li class="menu-item' . ($isActive ? ' active' : '') . '">';
                $html .= '<a href="' . base_url($url) . '" class="menu-link">';
                $html .= '<i class="material-icons">' . $icon . '</i>';
                $html .= '<span>' . $title . '</span>';
                $html .= '</a>';
                $html .= '</li>';
            }
        }

        $html .= '</ul>';

        return $html;
    }
}

if (!function_exists('get_dashboard_url')) {
    /**
     * Get dashboard URL based on user role
     */
    function get_dashboard_url()
    {
        $roleId = role_id();

        switch ($roleId) {
            case 1:
                return base_url('admin/dashboard');
            case 2:
                return base_url('pengurus/dashboard');
            case 3:
                return base_url('member/profile');
            default:
                return base_url('/');
        }
    }
}
