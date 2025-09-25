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
            if ($request->isAjax()) {
                return response()->setJSON([
                    'status' => false,
                    'message' => 'Session expired',
                    'redirect' => base_url('login')
                ])->setStatusCode(401);
            }

            session()->setFlashdata('error', 'Silakan login terlebih dahulu');
            return redirect()->to('/login');
        }

        // Check role permissions if arguments provided
        if ($arguments !== null) {
            $userRole = $session->get('role_id');

            // SUPER ADMIN BYPASS - Super Admin bisa akses semua area
            if ($userRole == 1) {
                return; // Allow access
            }

            $allowedRoles = is_array($arguments) ? $arguments : [$arguments];

            // Role hierarchy
            $roleHierarchy = [
                'super_admin' => 1,
                'pengurus' => 2,
                'member' => 3
            ];

            // Get minimum required role
            $minRequiredRole = 3; // Default member
            foreach ($allowedRoles as $role) {
                $roleId = $roleHierarchy[$role] ?? $role;
                if ($roleId < $minRequiredRole) {
                    $minRequiredRole = $roleId;
                }
            }

            // Check if user role is equal or higher (lower number = higher privilege)
            if ($userRole > $minRequiredRole) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Halaman tidak ditemukan');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
