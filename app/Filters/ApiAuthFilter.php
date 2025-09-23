<?php
// ============================================
// API AUTH FILTER
// ============================================

// app/Filters/ApiAuthFilter.php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\API\ResponseTrait;
use Config\Services;

class ApiAuthFilter implements FilterInterface
{
    use ResponseTrait;

    /**
     * Verify API token
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeader('Authorization');

        if (empty($header)) {
            return $this->failUnauthorized('Missing authorization header');
        }

        $token = str_replace('Bearer ', '', $header->getValue());

        if (!$this->validateToken($token)) {
            return $this->failUnauthorized('Invalid or expired token');
        }

        // Check rate limiting
        if (!$this->checkRateLimit($request)) {
            return $this->failTooManyRequests('Rate limit exceeded');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Add security headers
        $response->setHeader('X-Content-Type-Options', 'nosniff');
        $response->setHeader('X-Frame-Options', 'DENY');
        $response->setHeader('X-XSS-Protection', '1; mode=block');
    }

    /**
     * Validate JWT token
     */
    private function validateToken($token)
    {
        try {
            // Decode JWT token
            $key = getenv('JWT_SECRET_KEY');

            // Simple validation for example
            // In production, use proper JWT library like Firebase JWT
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                return false;
            }

            $payload = json_decode(base64_decode($parts[1]), true);

            // Check expiration
            if (isset($payload['exp']) && $payload['exp'] < time()) {
                return false;
            }

            // Store user data in request for later use
            Services::request()->user = $payload;

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check rate limiting
     */
    private function checkRateLimit($request)
    {
        $cache = Services::cache();
        $ip = $request->getIPAddress();
        $key = 'api_rate_limit_' . $ip;

        $attempts = $cache->get($key);

        if ($attempts === null) {
            $cache->save($key, 1, 3600); // 1 hour
            return true;
        }

        if ($attempts >= 100) { // 100 requests per hour
            return false;
        }

        $cache->save($key, $attempts + 1, 3600);
        return true;
    }

    /**
     * Helper to return JSON error response
     */
    private function failUnauthorized($message)
    {
        $response = Services::response();
        $response->setStatusCode(401);
        $response->setJSON([
            'status' => 'error',
            'message' => $message
        ]);
        return $response;
    }

    private function failTooManyRequests($message)
    {
        $response = Services::response();
        $response->setStatusCode(429);
        $response->setJSON([
            'status' => 'error',
            'message' => $message
        ]);
        return $response;
    }
}
