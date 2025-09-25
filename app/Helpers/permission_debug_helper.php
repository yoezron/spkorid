<?php

// ============================================
// HELPER FOR DEBUGGING PERMISSION ISSUES
// ============================================

if (!function_exists('debug_permissions')) {
    /**
     * Debug permissions untuk troubleshooting
     * Gunakan function ini untuk melihat data permissions yang dikirim ke view
     */
    function debug_permissions($roleId, $menuData = null, $permissionData = null)
    {
        if (!ENVIRONMENT === 'development') {
            return; // Hanya jalankan di development mode
        }

        echo "<div style='background: #f8f9fa; border: 2px solid #007bff; padding: 15px; margin: 10px; border-radius: 5px;'>";
        echo "<h4 style='color: #007bff; margin-bottom: 10px;'>🔍 DEBUG PERMISSIONS - Role ID: {$roleId}</h4>";

        // Debug Menu Data
        if ($menuData) {
            echo "<h5>📋 Menu Data:</h5>";
            echo "<pre style='background: white; padding: 10px; border-radius: 3px; overflow-x: auto;'>";
            print_r($menuData);
            echo "</pre>";
        }

        // Debug Permission Data
        if ($permissionData) {
            echo "<h5>🔐 Permission Data:</h5>";
            echo "<pre style='background: white; padding: 10px; border-radius: 3px; overflow-x: auto;'>";
            print_r($permissionData);
            echo "</pre>";
        }

        // Debug Database Queries
        $db = \Config\Database::connect();

        echo "<h5>🗃️ Database Queries Result:</h5>";

        // Check role exists
        $role = $db->table('roles')->where('id', $roleId)->get()->getRowArray();
        echo "<p><strong>Role:</strong> " . ($role ? json_encode($role) : 'NOT FOUND') . "</p>";

        // Check role permissions
        $permissions = $db->table('role_menu_access')
            ->where('role_id', $roleId)
            ->get()
            ->getResultArray();
        echo "<p><strong>Role Permissions Count:</strong> " . count($permissions) . "</p>";

        if (!empty($permissions)) {
            echo "<pre style='background: white; padding: 10px; border-radius: 3px; overflow-x: auto;'>";
            print_r($permissions);
            echo "</pre>";
        }

        // Check menus
        $menus = $db->table('menus')->where('is_active', 1)->get()->getResultArray();
        echo "<p><strong>Active Menus Count:</strong> " . count($menus) . "</p>";

        echo "</div>";
    }
}

if (!function_exists('debug_menu_tree')) {
    /**
     * Debug menu tree structure
     */
    function debug_menu_tree($menus)
    {
        if (!ENVIRONMENT === 'development') {
            return;
        }

        echo "<div style='background: #e9ecef; border: 2px solid #6c757d; padding: 15px; margin: 10px; border-radius: 5px;'>";
        echo "<h4 style='color: #6c757d; margin-bottom: 10px;'>🌳 DEBUG MENU TREE</h4>";

        if (empty($menus)) {
            echo "<p style='color: red;'>❌ No menus found!</p>";
        } else {
            echo "<p style='color: green;'>✅ Found " . count($menus) . " parent menus</p>";

            foreach ($menus as $index => $menu) {
                echo "<div style='margin-left: 20px; padding: 5px; border-left: 3px solid #007bff;'>";
                echo "<strong>Menu {$index}: {$menu['menu_name']} (ID: {$menu['id']})</strong><br>";
                echo "URL: {$menu['menu_url']}<br>";
                echo "Icon: {$menu['menu_icon']}<br>";

                if (isset($menu['submenus']) && !empty($menu['submenus'])) {
                    echo "Submenus: " . count($menu['submenus']) . "<br>";
                    foreach ($menu['submenus'] as $subindex => $submenu) {
                        echo "<div style='margin-left: 20px; padding: 3px; border-left: 2px solid #28a745;'>";
                        echo "└─ Submenu {$subindex}: {$submenu['menu_name']} (ID: {$submenu['id']})<br>";
                        echo "   URL: {$submenu['menu_url']}<br>";
                        echo "</div>";
                    }
                } else {
                    echo "Submenus: None<br>";
                }
                echo "</div><br>";
            }
        }

        echo "</div>";
    }
}

if (!function_exists('debug_view_data')) {
    /**
     * Debug all data yang dikirim ke view
     */
    function debug_view_data($data)
    {
        if (!ENVIRONMENT === 'development') {
            return;
        }

        echo "<div style='background: #fff3cd; border: 2px solid #ffc107; padding: 15px; margin: 10px; border-radius: 5px;'>";
        echo "<h4 style='color: #856404; margin-bottom: 10px;'>📊 DEBUG VIEW DATA</h4>";

        foreach ($data as $key => $value) {
            echo "<div style='margin-bottom: 10px;'>";
            echo "<strong style='color: #856404;'>{$key}:</strong> ";

            if (is_array($value) || is_object($value)) {
                echo "<span style='color: #6c757d;'>(" . gettype($value) . " with " . count((array)$value) . " items)</span>";
                echo "<pre style='background: white; padding: 5px; border-radius: 3px; margin-top: 5px; max-height: 200px; overflow-y: auto;'>";
                print_r($value);
                echo "</pre>";
            } else {
                echo "<span style='color: #495057;'>" . (is_bool($value) ? ($value ? 'true' : 'false') : $value) . "</span>";
            }
            echo "</div>";
        }

        echo "</div>";
    }
}

if (!function_exists('debug_checkbox_data')) {
    /**
     * Debug data untuk checkbox permissions
     */
    function debug_checkbox_data($menuId, $permissions)
    {
        if (!ENVIRONMENT === 'development') {
            return;
        }

        $currentPermissions = $permissions[$menuId] ?? [];

        echo "<div style='background: #d1ecf1; border: 1px solid #bee5eb; padding: 10px; margin: 5px; border-radius: 3px; font-size: 12px;'>";
        echo "<strong>Debug Menu ID {$menuId}:</strong> ";

        if (empty($currentPermissions)) {
            echo "<span style='color: red;'>No permissions found</span>";
        } else {
            echo "<span style='color: green;'>Permissions found:</span> ";
            echo "View: " . ($currentPermissions['can_view'] ?? '0') . ", ";
            echo "Add: " . ($currentPermissions['can_add'] ?? '0') . ", ";
            echo "Edit: " . ($currentPermissions['can_edit'] ?? '0') . ", ";
            echo "Delete: " . ($currentPermissions['can_delete'] ?? '0');
        }
        echo "</div>";
    }
}

if (!function_exists('test_database_connection')) {
    /**
     * Test koneksi database dan tabel yang diperlukan
     */
    function test_database_connection()
    {
        if (!ENVIRONMENT === 'development') {
            return;
        }

        try {
            $db = \Config\Database::connect();

            echo "<div style='background: #d4edda; border: 2px solid #28a745; padding: 15px; margin: 10px; border-radius: 5px;'>";
            echo "<h4 style='color: #155724; margin-bottom: 10px;'>🔗 DATABASE CONNECTION TEST</h4>";

            // Test connection
            if ($db->connID) {
                echo "<p style='color: green;'>✅ Database connection: OK</p>";
            } else {
                echo "<p style='color: red;'>❌ Database connection: FAILED</p>";
                echo "</div>";
                return;
            }

            // Test required tables
            $requiredTables = ['roles', 'menus', 'role_menu_access'];

            foreach ($requiredTables as $table) {
                if ($db->tableExists($table)) {
                    $count = $db->table($table)->countAllResults(false);
                    echo "<p style='color: green;'>✅ Table '{$table}': EXISTS (Records: {$count})</p>";
                } else {
                    echo "<p style='color: red;'>❌ Table '{$table}': NOT EXISTS</p>";
                }
            }

            echo "</div>";
        } catch (\Exception $e) {
            echo "<div style='background: #f8d7da; border: 2px solid #dc3545; padding: 15px; margin: 10px; border-radius: 5px;'>";
            echo "<h4 style='color: #721c24;'>❌ DATABASE ERROR</h4>";
            echo "<p style='color: #721c24;'>Error: " . $e->getMessage() . "</p>";
            echo "</div>";
        }
    }
}
