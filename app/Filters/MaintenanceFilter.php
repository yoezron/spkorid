<?php
// ============================================
// MAINTENANCE FILTER
// ============================================

// app/Filters/MaintenanceFilter.php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class MaintenanceFilter implements FilterInterface
{
    /**
     * Check if site is in maintenance mode
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $db = \Config\Database::connect();

        // Get maintenance status from settings
        $setting = $db->table('settings')
            ->where('key', 'maintenance_mode')
            ->get()
            ->getRowArray();

        if ($setting && $setting['value'] === 'true') {
            // Allow admin access
            $session = session();
            if ($session->get('role_id') == 1) {
                return;
            }

            // Allow certain routes (login, etc)
            $allowedRoutes = ['/login', '/logout', '/maintenance'];
            if (in_array($request->uri->getPath(), $allowedRoutes)) {
                return;
            }

            // Get maintenance message
            $message = $db->table('settings')
                ->where('key', 'maintenance_message')
                ->get()
                ->getRowArray();

            return view('errors/maintenance', [
                'message' => $message['value'] ?? 'Situs sedang dalam perbaikan. Mohon kembali lagi nanti.'
            ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
