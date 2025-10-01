<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // PERBAIKAN: Gunakan key yang konsisten
        if (!$session->get('logged_in')) {
            if ($request->isAJAX()) {
                return response()->setJSON([
                    'status' => false,
                    'message' => 'Session expired',
                    'redirect' => base_url('login')
                ])->setStatusCode(401);
            }

            session()->setFlashdata('error', 'Silakan login terlebih dahulu');
            return redirect()->to('/login');
        }

        // Check role permissions if arguments are provided
        if ($arguments !== null) {
            $userRoleId = (int) $session->get('role_id');

            // SUPER ADMIN BYPASS - Super Admin can access all areas
            if ($userRoleId === 1) {
                return; // Allow access
            }

            // PERBAIKAN: Handle arguments yang bisa berupa string atau array
            $allowedRoles = $arguments;
            if (!is_array($arguments)) {
                // Split by comma jika string
                $allowedRoles = array_map('trim', explode(',', $arguments));
            }

            $roleHierarchy = [
                'super_admin' => 1,
                'pengurus'    => 2,
                'member'      => 3,
                'anggota'     => 3 // Alias for member
            ];

            // Convert allowed role names to their IDs
            $allowedRoleIds = [];
            foreach ($allowedRoles as $role) {
                $role = trim($role); // Trim whitespace
                if (isset($roleHierarchy[$role])) {
                    $allowedRoleIds[] = $roleHierarchy[$role];
                }
            }

            // Check if the user's role is in the list of allowed roles
            if (!in_array($userRoleId, $allowedRoleIds)) {
                // Log unauthorized access for security
                log_message('warning', "Unauthorized access attempt by user {$session->get('user_id')} to {$request->getUri()->getPath()}");

                if ($request->isAJAX()) {
                    return response()->setJSON([
                        'status' => false,
                        'message' => 'Unauthorized access'
                    ])->setStatusCode(403);
                }

                // Redirect ke dashboard sesuai role user
                session()->setFlashdata('error', 'Anda tidak memiliki akses ke halaman tersebut');
                return redirect()->to($this->getDashboardByRole($userRoleId));
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }

    /**
     * Get dashboard URL based on role ID
     */
    private function getDashboardByRole($roleId)
    {
        switch ($roleId) {
            case 1: // Super Admin
                return '/admin/dashboard';

            case 2: // Pengurus
                return '/pengurus/dashboard';

            case 3: // Member
            default:
                return '/member/profile';
        }
    }
}
