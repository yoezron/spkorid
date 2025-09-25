<?php
// app/Helpers/auth_helper.php

if (!function_exists('is_logged_in')) {
    /**
     * Check if user is logged in
     */
    function is_logged_in(): bool
    {
        $session = session();
        return $session->get('logged_in') === true;
    }
}

if (!function_exists('current_user')) {
    /**
     * Get current logged in user data
     */
    function current_user($field = null)
    {
        $session = session();

        if (!is_logged_in()) {
            return null;
        }

        if ($field === null) {
            return [
                'id' => $session->get('user_id'),
                'member_id' => $session->get('member_id'),
                'email' => $session->get('email'),
                'username' => $session->get('username'),
                'nama_lengkap' => $session->get('nama_lengkap'),
                'nomor_anggota' => $session->get('nomor_anggota'),
                'role_id' => $session->get('role_id'),
                'role_name' => $session->get('role_name'),
                'foto_path' => $session->get('foto_path'),
            ];
        }

        return $session->get($field);
    }
}

if (!function_exists('user_id')) {
    /**
     * Get current user ID
     */
    function user_id(): ?int
    {
        return current_user('user_id');
    }
}

if (!function_exists('member_id')) {
    /**
     * Get current member ID
     */
    function member_id(): ?int
    {
        return current_user('member_id');
    }
}

if (!function_exists('user_role')) {
    /**
     * Get current user role
     */
    function user_role(): ?string
    {
        return current_user('role_name');
    }
}

if (!function_exists('role_id')) {
    /**
     * Get current user role ID
     */
    function role_id(): ?int
    {
        return current_user('role_id');
    }
}

if (!function_exists('is_admin')) {
    /**
     * Check if current user is admin
     */
    function is_admin(): bool
    {
        return role_id() === 1;
    }
}

if (!function_exists('is_pengurus')) {
    /**
     * Check if current user is pengurus
     */
    function is_pengurus(): bool
    {
        return in_array(role_id(), [1, 2]); // Admin or Pengurus
    }
}

if (!function_exists('is_member')) {
    /**
     * Check if current user is regular member
     */
    function is_member(): bool
    {
        return role_id() === 3;
    }
}

if (!function_exists('has_permission')) {
    /**
     * Check if user has specific permission
     */
    function has_permission(string $permission): bool
    {
        if (!is_logged_in()) {
            return false;
        }

        // Super admin has all permissions
        if (is_admin()) {
            return true;
        }

        // Check specific permission in database
        $db = \Config\Database::connect();
        $builder = $db->table('role_permissions');

        $result = $builder->where('role_id', role_id())
            ->where('permission', $permission)
            ->where('is_allowed', 1)
            ->countAllResults();

        return $result > 0;
    }
}

if (!function_exists('can_access_menu')) {
    /**
     * Check if user can access a menu
     */
    function can_access_menu(int $menuId): bool
    {
        if (!is_logged_in()) {
            return false;
        }

        // Super admin can access all menus
        if (is_admin()) {
            return true;
        }

        $db = \Config\Database::connect();
        $builder = $db->table('role_menu_access');

        $result = $builder->where('role_id', role_id())
            ->where('menu_id', $menuId)
            ->where('can_view', 1)
            ->countAllResults();

        return $result > 0;
    }
}

if (!function_exists('get_user_menus')) {
    /**
     * Get menus accessible by current user
     */
    function get_user_menus()
    {
        if (!is_logged_in()) {
            return [];
        }

        $db = \Config\Database::connect();
        $roleId = role_id();

        // Build query to get menus with access rights
        $builder = $db->table('menus m')
            ->select('m.*, rma.can_create, rma.can_update, rma.can_delete')
            ->join('role_menu_access rma', 'rma.menu_id = m.id AND rma.role_id = ' . $roleId, 'left')
            ->where('m.is_active', 1)
            ->where('(rma.can_view = 1 OR ' . $roleId . ' = 1)') // Admin can see all
            ->orderBy('m.order_priority', 'ASC');

        return $builder->get()->getResultArray();
    }
}

if (!function_exists('get_breadcrumb')) {
    /**
     * Generate breadcrumb based on current URL
     */
    function get_breadcrumb(): array
    {
        $uri = service('uri');
        $segments = $uri->getSegments();

        $breadcrumb = [
            ['title' => 'Home', 'url' => base_url()]
        ];

        $url = base_url();
        foreach ($segments as $segment) {
            $url .= '/' . $segment;
            $title = ucfirst(str_replace(['-', '_'], ' ', $segment));
            $breadcrumb[] = [
                'title' => $title,
                'url' => $url
            ];
        }

        return $breadcrumb;
    }
}

if (!function_exists('log_activity')) {
    /**
     * Log user activity
     */
    function log_activity(string $type, $details = null): bool
    {
        try {
            $activityLog = new \App\Models\ActivityLogModel();

            $data = [
                'user_id' => user_id(),
                'activity_type' => $type,
                'activity_details' => is_array($details) ? json_encode($details) : $details,
                'ip_address' => service('request')->getIPAddress(),
                'user_agent' => service('request')->getUserAgent()->getAgentString(),
                'created_at' => date('Y-m-d H:i:s')
            ];

            return $activityLog->insert($data);
        } catch (\Exception $e) {
            log_message('error', 'Failed to log activity: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('format_role_name')) {
    /**
     * Format role name for display
     */
    function format_role_name($roleId): string
    {
        $roles = [
            1 => 'Super Admin',
            2 => 'Pengurus',
            3 => 'Anggota'
        ];

        return $roles[$roleId] ?? 'Unknown';
    }
}

if (!function_exists('check_session_timeout')) {
    /**
     * Check if session has timed out
     */
    function check_session_timeout(): bool
    {
        $session = session();
        $loginTime = $session->get('login_time');

        if (!$loginTime) {
            return false;
        }

        $timeout = env('session.timeout', 7200); // Default 2 hours
        $elapsed = time() - $loginTime;

        if ($elapsed > $timeout) {
            $session->destroy();
            return true;
        }

        // Update last activity
        $session->set('login_time', time());

        return false;
    }
}

if (!function_exists('require_login')) {
    /**
     * Require user to be logged in
     */
    function require_login($redirect = true)
    {
        if (!is_logged_in()) {
            if ($redirect) {
                session()->setFlashdata('error', 'Silakan login terlebih dahulu');
                header('Location: ' . base_url('login'));
                exit;
            }
            return false;
        }
        return true;
    }
}

if (!function_exists('require_role')) {
    /**
     * Require user to have specific role(s)
     */
    function require_role($roles, $redirect = true)
    {
        if (!is_logged_in()) {
            if ($redirect) {
                header('Location: ' . base_url('login'));
                exit;
            }
            return false;
        }

        $roles = is_array($roles) ? $roles : [$roles];
        $userRole = role_id();

        // Map role names to IDs if needed
        $roleMap = [
            'super_admin' => 1,
            'pengurus' => 2,
            'member' => 3,
            'anggota' => 3
        ];

        $allowedRoles = [];
        foreach ($roles as $role) {
            if (is_string($role) && isset($roleMap[$role])) {
                $allowedRoles[] = $roleMap[$role];
            } elseif (is_numeric($role)) {
                $allowedRoles[] = (int)$role;
            }
        }

        if (!in_array($userRole, $allowedRoles)) {
            if ($redirect) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Halaman tidak ditemukan');
            }
            return false;
        }

        return true;
    }

    /**
     * ===================================================================
     * FUNGSI-FUNGSI BARU DAN YANG DIPERBAIKI
     * ===================================================================
     */

    if (!function_exists('get_user_menus')) {
        /**
         * Get menus accessible by current user
         */
        function get_user_menus()
        {
            if (!is_logged_in()) {
                return [];
            }

            $db = \Config\Database::connect();
            $roleId = role_id();

            // Build query to get menus with access rights
            $builder = $db->table('menus m')
                ->select('m.*, rma.can_view')
                ->join('role_menu_access rma', 'rma.menu_id = m.id AND rma.role_id = ' . $db->escape($roleId), 'left')
                ->where('m.is_active', 1)
                ->where('(rma.can_view = 1 OR ' . $db->escape($roleId) . ' = 1)') // Super admin can see all
                ->orderBy('m.menu_order', 'ASC'); // <-- PERBAIKAN: dari order_priority menjadi menu_order

            return $builder->get()->getResultArray();
        }
    }


    if (!function_exists('get_profile_url')) {
        /**
         * Get the correct profile URL based on user role.
         */
        function get_profile_url(): string
        {
            $roleId = role_id();

            switch ($roleId) {
                case 1: // Super Admin
                    // Asumsi admin diarahkan ke dashboard karena mungkin tidak punya profil 'publik'
                    return base_url('admin/dashboard');
                case 2: // Pengurus
                    // Asumsi pengurus diarahkan ke dashboard juga
                    return base_url('pengurus/dashboard');
                case 3: // Anggota
                default:
                    return base_url('member/profile');
            }
        }
    }

    if (!function_exists('get_change_password_url')) {
        /**
         * Get the correct change password URL based on user role.
         */
        function get_change_password_url(): string
        {
            $roleId = role_id();

            switch ($roleId) {
                case 1:
                    return base_url('admin/change-password');
                case 2:
                    return base_url('pengurus/change-password');
                case 3:
                default:
                    return base_url('member/change-password');
            }
        }
    }
}
