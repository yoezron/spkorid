<?php
// ============================================
// PERMISSION FILTER
// ============================================

// app/Filters/PermissionFilter.php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\RoleModel;

class PermissionFilter implements FilterInterface
{
    protected $roleModel;

    public function __construct()
    {
        $this->roleModel = new RoleModel();
    }

    /**
     * Check if user has permission to access menu
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('logged_in')) {
            return redirect()->to('/login');
        }

        $roleId = $session->get('role_id');
        $currentUrl = $request->uri->getPath();

        // Super admin has all permissions
        if ($roleId == 1) {
            return;
        }

        // Check permission based on URL
        if (!$this->hasPermission($roleId, $currentUrl, $arguments)) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }

    /**
     * Check if role has permission
     */
    private function hasPermission($roleId, $url, $action = null)
    {
        // Get role permissions from database
        $db = \Config\Database::connect();

        $permission = $db->table('role_menu_access rma')
            ->select('rma.*')
            ->join('menus m', 'm.id = rma.menu_id')
            ->where('rma.role_id', $roleId)
            ->like('m.menu_url', $url, 'after')
            ->get()
            ->getRowArray();

        if (!$permission) {
            return false;
        }

        // Check specific action permission
        if ($action) {
            switch ($action[0]) {
                case 'view':
                    return $permission['can_view'] == 1;
                case 'add':
                    return $permission['can_add'] == 1;
                case 'edit':
                    return $permission['can_edit'] == 1;
                case 'delete':
                    return $permission['can_delete'] == 1;
                default:
                    return $permission['can_view'] == 1;
            }
        }

        return $permission['can_view'] == 1;
    }
}
