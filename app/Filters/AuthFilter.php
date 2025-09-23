<?php
// ============================================
// AUTH FILTER
// ============================================

// app/Filters/AuthFilter.php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Check if user is logged in
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Check if user is logged in
        if (!$session->get('logged_in')) {
            // Save intended URL
            $session->set('redirect_url', current_url());

            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Check if user is active
        if (!$session->get('user_id')) {
            $session->destroy();
            return redirect()->to('/login')->with('error', 'Sesi tidak valid');
        }

        // Check specific role if provided
        if ($arguments !== null) {
            $userRole = $session->get('role_id');
            $allowedRoles = [];

            // Map role names to IDs
            foreach ($arguments as $role) {
                switch ($role) {
                    case 'super_admin':
                        $allowedRoles[] = 1;
                        break;
                    case 'pengurus':
                        $allowedRoles[] = 2;
                        break;
                    case 'anggota':
                        $allowedRoles[] = 3;
                        break;
                }
            }

            // Check if user has required role
            if (!in_array($userRole, $allowedRoles)) {
                return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini');
            }
        }

        // Check if password needs to be changed
        if ($session->get('force_password_change') && !in_array($request->uri->getPath(), ['/change-password', '/logout'])) {
            return redirect()->to('/change-password')->with('warning', 'Anda harus mengganti password terlebih dahulu');
        }

        // Update last activity
        $this->updateLastActivity();
    }

    /**
     * After filter
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }

    /**
     * Update last activity timestamp
     */
    private function updateLastActivity()
    {
        $session = session();
        $userId = $session->get('user_id');

        if ($userId) {
            // Update last activity every 5 minutes
            $lastUpdate = $session->get('last_activity_update');
            $now = time();

            if (!$lastUpdate || ($now - $lastUpdate) > 300) {
                $db = \Config\Database::connect();
                $db->table('users')
                    ->where('id', $userId)
                    ->update(['last_activity' => date('Y-m-d H:i:s')]);

                $session->set('last_activity_update', $now);
            }
        }
    }
}
