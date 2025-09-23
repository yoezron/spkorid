<?php
// ============================================
// NO AUTH FILTER
// ============================================

// app/Filters/NoAuthFilter.php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class NoAuthFilter implements FilterInterface
{
    /**
     * Redirect if already logged in
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if ($session->get('logged_in')) {
            return redirect()->to('/dashboard');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
