<?php
// ============================================
// MODEL UNTUK MENU MANAGEMENT
// ============================================

// app/Models/MenuModel.php
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

    // Get menus for specific role
    public function getMenusByRole($roleId)
    {
        $db = \Config\Database::connect();

        return $db->table('menus')
            ->select('menus.*')
            ->join('role_menu_access', 'role_menu_access.menu_id = menus.id')
            ->where('role_menu_access.role_id', $roleId)
            ->where('role_menu_access.can_view', 1)
            ->where('menus.is_active', 1)
            ->where('menus.parent_id', null)
            ->orderBy('menus.menu_order', 'ASC')
            ->get()
            ->getResultArray();
    }

    // Get sub menus
    public function getSubMenus($parentId, $roleId = null)
    {
        $builder = $this->where('parent_id', $parentId)
            ->where('is_active', 1);

        if ($roleId) {
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
            }
        }

        return $builder->orderBy('menu_order', 'ASC')->findAll();
    }

    // Build menu tree
    public function buildMenuTree($roleId = null)
    {
        $menus = $this->getMenusByRole($roleId);

        foreach ($menus as &$menu) {
            $menu['submenu'] = $this->getSubMenus($menu['id'], $roleId);
        }

        return $menus;
    }
}
