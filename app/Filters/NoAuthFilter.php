<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class NoAuthFilter implements FilterInterface
{
    /**
     * Redirect if already logged in
     * PERBAIKAN: Redirect langsung berdasarkan role
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // PERBAIKAN: Gunakan key yang konsisten
        if ($session->get('logged_in')) {
            $roleId = $session->get('role_id');

            // Redirect berdasarkan role untuk menghindari loop
            switch ($roleId) {
                case 1: // Super Admin
                    return redirect()->to('/admin/dashboard');

                case 2: // Pengurus
                    return redirect()->to('/pengurus/dashboard');

                case 3: // Member/Anggota
                default:
                    return redirect()->to('/member/profile');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
