<?php
// ============================================
// CORS FILTER
// ============================================

// app/Filters/CorsFilter.php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class CorsFilter implements FilterInterface
{
    /**
     * Handle CORS headers
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Handle preflight requests
        if ($request->getMethod() === 'options') {
            $response = Services::response();

            $response->setHeader('Access-Control-Allow-Origin', $this->getAllowedOrigin($request));
            $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
            $response->setHeader('Access-Control-Max-Age', '86400');
            $response->setStatusCode(200);

            return $response;
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $response->setHeader('Access-Control-Allow-Origin', $this->getAllowedOrigin($request));
        $response->setHeader('Access-Control-Allow-Credentials', 'true');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    }

    /**
     * Get allowed origin based on environment
     */
    private function getAllowedOrigin($request)
    {
        $origin = $request->getHeader('Origin');

        // List of allowed origins
        $allowedOrigins = [
            'http://localhost:3000',
            'http://localhost:8080',
            'https://spk.org',
            'https://app.spk.org'
        ];

        if ($origin && in_array($origin->getValue(), $allowedOrigins)) {
            return $origin->getValue();
        }

        // Default to first allowed origin or wildcard in development
        return ENVIRONMENT === 'development' ? '*' : $allowedOrigins[0];
    }
}
