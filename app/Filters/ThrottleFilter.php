<?php
// ============================================
// THROTTLE FILTER
// ============================================

// app/Filters/ThrottleFilter.php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Services;

class ThrottleFilter implements FilterInterface
{
    /**
     * Implement request throttling
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $cache = Services::cache();
        $session = session();

        // Get identifier (user ID or IP)
        $identifier = $session->get('user_id') ?? $request->getIPAddress();
        $key = 'throttle_' . md5($identifier . $request->uri->getPath());

        // Get max attempts from arguments or use default
        $maxAttempts = isset($arguments[0]) ? (int)$arguments[0] : 60;
        $decayMinutes = isset($arguments[1]) ? (int)$arguments[1] : 1;

        $attempts = $cache->get($key);

        if ($attempts === null) {
            $cache->save($key, 1, $decayMinutes * 60);
            return;
        }

        if ($attempts >= $maxAttempts) {
            return Services::response()
                ->setStatusCode(429)
                ->setBody('Too Many Requests. Please try again later.');
        }

        $cache->save($key, $attempts + 1, $decayMinutes * 60);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Add rate limit headers
        $cache = Services::cache();
        $session = session();

        $identifier = $session->get('user_id') ?? $request->getIPAddress();
        $key = 'throttle_' . md5($identifier . $request->uri->getPath());

        $maxAttempts = isset($arguments[0]) ? (int)$arguments[0] : 60;
        $attempts = $cache->get($key) ?? 0;

        $response->setHeader('X-RateLimit-Limit', $maxAttempts);
        $response->setHeader('X-RateLimit-Remaining', max(0, $maxAttempts - $attempts));
    }
}
