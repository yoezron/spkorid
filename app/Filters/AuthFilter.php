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
            $allowedRoles = is_array($arguments) ? $arguments : [$arguments];
            $userRole = $session->get('role_id');

            // Map role names to IDs
            $roleMap = [
                'super_admin' => 1,
                'pengurus' => 2,
                'member' => 3
            ];

            $allowedRoleIds = array_map(function ($role) use ($roleMap) {
                return $roleMap[$role] ?? $role;
            }, $allowedRoles);

            if (!in_array($userRole, $allowedRoleIds)) {
                if ($request->isAjax()) {
                    return response()->setJSON([
                        'status' => false,
                        'message' => 'Unauthorized access'
                    ])->setStatusCode(403);
                }

                throw new \CodeIgniter\Exceptions\PageNotFoundException('Halaman tidak ditemukan');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
