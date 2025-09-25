<?php
// ============================================
// MODEL UNTUK MENU MANAGEMENT (FIXED)
// ============================================

namespace App\Models;

use CodeIgniter\Model;

class MenuModel extends Model
{
    protected $table = 'menus';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'menu_name',
        'menu_url',
        'menu_icon',
        'parent_id',
        'menu_order',
        'is_active'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get menus for specific role
     */
    public function getMenusByRole($roleId)
    {
        $db = \Config\Database::connect();

        return $db->table('menus')
            ->select('menus.*')
            ->join('role_menu_access', 'role_menu_access.menu_id = menus.id', 'left')
            ->where('menus.is_active', 1)
            ->where('menus.parent_id', null)
            ->groupStart()
            ->where('role_menu_access.role_id', $roleId)
            ->where('role_menu_access.can_view', 1)
            ->orWhere($roleId . ' = 1') // Super admin can access all
            ->groupEnd()
            ->orderBy('menus.menu_order', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get all parent menus (for permission management)
     */
    public function getAllParentMenus()
    {
        return $this->where('parent_id', null)
            ->where('is_active', 1)
            ->orderBy('menu_order', 'ASC')
            ->findAll();
    }

    /**
     * Get sub menus for a specific parent
     */
    public function getSubMenus($parentId, $roleId = null)
    {
        $builder = $this->where('parent_id', $parentId)
            ->where('is_active', 1);

        if ($roleId && $roleId != 1) { // Not super admin
            $db = \Config\Database::connect();
            $menuIds = $db->table('role_menu_access')
                ->select('menu_id')
                ->where('role_id', $roleId)
                ->where('can_view', 1)
                ->get()
                ->getResultArray();

            $ids = array_column($menuIds, 'menu_id');
            if (!empty($ids)) {
                $builder->whereIn('id', $ids);
            } else {
                // No permissions found, return empty
                return [];
            }
        }

        return $builder->orderBy('menu_order', 'ASC')->findAll();
    }

    /**
     * Build menu tree for sidebar (with role filtering)
     */
    public function buildMenuTree($roleId = null)
    {
        // Get parent menus
        if ($roleId) {
            $menus = $this->getMenusByRole($roleId);
        } else {
            $menus = $this->getAllParentMenus();
        }

        // Add submenus to each parent menu
        foreach ($menus as &$menu) {
            $menu['submenus'] = $this->getSubMenus($menu['id'], $roleId);
        }

        return $menus;
    }

    /**
     * Build complete menu tree for permission management (no role filtering)
     */
    public function buildMenuTreeForPermissions()
    {
        // Get all parent menus
        $menus = $this->getAllParentMenus();

        // Add all submenus to each parent menu (no role filtering)
        foreach ($menus as &$menu) {
            $menu['submenus'] = $this->where('parent_id', $menu['id'])
                ->where('is_active', 1)
                ->orderBy('menu_order', 'ASC')
                ->findAll();
        }

        return $menus;
    }

    /**
     * Get menu by URL
     */
    public function getMenuByUrl($url)
    {
        return $this->where('menu_url', $url)
            ->where('is_active', 1)
            ->first();
    }

    /**
     * Check if menu exists
     */
    public function menuExists($menuName, $excludeId = null)
    {
        $builder = $this->where('menu_name', $menuName);

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Get menu hierarchy (breadcrumb)
     */
    public function getMenuHierarchy($menuId)
    {
        $hierarchy = [];
        $menu = $this->find($menuId);

        if ($menu) {
            $hierarchy[] = $menu;

            // If has parent, get parent hierarchy
            if ($menu['parent_id']) {
                $parentHierarchy = $this->getMenuHierarchy($menu['parent_id']);
                $hierarchy = array_merge($parentHierarchy, $hierarchy);
            }
        }

        return $hierarchy;
    }

    /**
     * Update menu order
     */
    public function updateMenuOrder($menuId, $newOrder)
    {
        return $this->update($menuId, ['menu_order' => $newOrder]);
    }

    /**
     * Get next menu order for parent
     */
    public function getNextMenuOrder($parentId = null)
    {
        $builder = $this->selectMax('menu_order', 'max_order');

        if ($parentId) {
            $builder->where('parent_id', $parentId);
        } else {
            $builder->where('parent_id', null);
        }

        $result = $builder->get()->getRowArray();

        return ($result['max_order'] ?? 0) + 1;
    }
}
