<?php
// ============================================
// app/Models/RoleModel.php
namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $allowedFields = ['role_name', 'role_description', 'is_active'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Get role with permissions
    public function getRoleWithPermissions($roleId)
    {
        $role = $this->find($roleId);
        if (!$role) return null;

        $db = \Config\Database::connect();
        $permissions = $db->table('role_menu_access')
            ->select('role_menu_access.*, menus.menu_name, menus.menu_url, menus.menu_icon')
            ->join('menus', 'menus.id = role_menu_access.menu_id')
            ->where('role_menu_access.role_id', $roleId)
            ->get()
            ->getResultArray();

        $role['permissions'] = $permissions;
        return $role;
    }

    // Check if role has permission
    public function hasPermission($roleId, $menuUrl, $action = 'view')
    {
        $db = \Config\Database::connect();
        $permission = $db->table('role_menu_access')
            ->select('role_menu_access.*')
            ->join('menus', 'menus.id = role_menu_access.menu_id')
            ->where('role_menu_access.role_id', $roleId)
            ->where('menus.menu_url', $menuUrl)
            ->get()
            ->getRowArray();

        if (!$permission) return false;

        switch ($action) {
            case 'view':
                return $permission['can_view'] == 1;
            case 'add':
                return $permission['can_add'] == 1;
            case 'edit':
                return $permission['can_edit'] == 1;
            case 'delete':
                return $permission['can_delete'] == 1;
            default:
                return false;
        }
    }
}
