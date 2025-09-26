<?php
// app/Filters/AuthFilter.php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

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

            $allowedRoles = is_array($arguments) ? $arguments : [$arguments];

            $roleHierarchy = [
                'super_admin' => 1,
                'pengurus'    => 2,
                'member'      => 3,
                'anggota'     => 3 // Alias for member
            ];

            // Convert allowed role names to their IDs
            $allowedRoleIds = [];
            foreach ($allowedRoles as $role) {
                if (isset($roleHierarchy[$role])) {
                    $allowedRoleIds[] = $roleHierarchy[$role];
                }
            }

            // *** INI ADALAH PERBAIKAN UTAMA ***
            // Check if the user's role is in the list of allowed roles.
            if (!in_array($userRoleId, $allowedRoleIds)) {
                // If not allowed, throw a PageNotFoundException for security.
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Halaman tidak ditemukan.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
