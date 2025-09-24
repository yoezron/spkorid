<?php
// app/Filters/RoleFilter.php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    protected $roleHierarchy = [
        'super_admin' => 1,
        'pengurus' => 2,
        'member' => 3
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Check if user is logged in
        if (!$session->get('logged_in')) {
            if ($request->isAJAX()) {
                return response()->setJSON([
                    'status' => false,
                    'message' => 'Unauthorized: Not logged in'
                ])->setStatusCode(401);
            }
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Get current user role
        $userRoleId = $session->get('role_id');

        // If no arguments provided, allow any authenticated user
        if ($arguments === null || empty($arguments)) {
            return;
        }

        // Parse allowed roles from arguments
        $allowedRoles = is_array($arguments) ? $arguments : explode(',', $arguments);

        // Convert role names to IDs
        $allowedRoleIds = [];
        foreach ($allowedRoles as $role) {
            $role = trim($role);
            if (isset($this->roleHierarchy[$role])) {
                $allowedRoleIds[] = $this->roleHierarchy[$role];
            } elseif (is_numeric($role)) {
                $allowedRoleIds[] = (int)$role;
            }
        }

        // Check if user has required role
        if (!in_array($userRoleId, $allowedRoleIds)) {
            if ($request->isAJAX()) {
                return response()->setJSON([
                    'status' => false,
                    'message' => 'Unauthorized: Insufficient permissions'
                ])->setStatusCode(403);
            }

            // Log unauthorized access attempt
            $this->logUnauthorizedAccess($session->get('user_id'), $request->getUri()->getPath());

            return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }

    /**
     * Log unauthorized access attempts
     */
    protected function logUnauthorizedAccess($userId, $path)
    {
        $activityLog = new \App\Models\ActivityLogModel();
        $activityLog->insert([
            'user_id' => $userId,
            'activity_type' => 'unauthorized_access',
            'activity_details' => json_encode([
                'attempted_path' => $path,
                'timestamp' => date('Y-m-d H:i:s')
            ]),
            'ip_address' => service('request')->getIPAddress()
        ]);
    }
}
