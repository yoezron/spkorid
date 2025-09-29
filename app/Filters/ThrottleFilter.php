<?php
// app/Filters/ThrottleFilter.php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Services;

class ThrottleFilter implements FilterInterface
{
    protected $cache;
    protected $maxAttempts = 5; // Maximum attempts
    protected $decayMinutes = 1; // Time window in minutes
    protected $blockDuration = 15; // Block duration in minutes after max attempts

    public function __construct()
    {
        $this->cache = Services::cache();
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        // Parse arguments if provided
        if ($arguments !== null) {
            // Handle both string and array arguments from CI4 filter system
            if (is_array($arguments)) {
                // Arguments come as array from route filters like 'throttle:5,1,15'
                if (isset($arguments[0])) {
                    $this->maxAttempts = (int)$arguments[0];
                }
                if (isset($arguments[1])) {
                    $this->decayMinutes = (int)$arguments[1];
                }
                if (isset($arguments[2])) {
                    $this->blockDuration = (int)$arguments[2];
                }
            } elseif (is_string($arguments)) {
                // If arguments is a string, explode it
                $params = explode(',', $arguments);
                if (isset($params[0])) {
                    $this->maxAttempts = (int)$params[0];
                }
                if (isset($params[1])) {
                    $this->decayMinutes = (int)$params[1];
                }
                if (isset($params[2])) {
                    $this->blockDuration = (int)$params[2];
                }
            }
        }

        $key = $this->resolveRequestSignature($request);

        // Check if IP is blocked
        $blockKey = 'blocked_' . $key; // Fixed: use underscore instead of colon
        if ($this->cache->get($blockKey)) {
            return $this->buildResponse($request, true);
        }
        // Only count attempts for modifying requests (typically POST)
        if (strtolower($request->getMethod()) !== 'post') {
            return null;
        }

        // Get current attempts
        $attempts = (int)$this->cache->get($key) ?: 0;

        if ($attempts >= $this->maxAttempts) {
            // Block the IP
            $this->cache->save($blockKey, true, $this->blockDuration * 60);
            $this->cache->delete($key);

            // Log the blocking
            $this->logBlocking($request);

            return $this->buildResponse($request, true);
        }

        // Increment attempts
        $this->cache->save($key, $attempts + 1, $this->decayMinutes * 60);

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // If login was successful, clear the throttle
        if ($response->getStatusCode() === 200 || $response->getStatusCode() === 302) {
            $session = session();
            if ($session->get('logged_in') || $session->get('user_id')) {
                $key = $this->resolveRequestSignature($request);
                $this->cache->delete($key);
                $this->cache->delete('blocked_' . $key); // Fixed: use underscore
            }
        }
    }

    /**
     * Generate unique signature for the request
     * Fixed: Remove all reserved characters from cache key
     */
    protected function resolveRequestSignature(RequestInterface $request): string
    {
        $ip = $request->getIPAddress();
        $route = $request->getUri()->getPath();

        // Include user identifier if attempting login
        $identifier = '';
        if ($request->getMethod() === 'post') {
            $email = $request->getPost('email') ?? $request->getPost('username');
            if ($email) {
                $identifier = '_' . md5($email); // Fixed: use underscore instead of colon
            }
        }

        // Fixed: Remove all reserved characters and use only safe characters
        // Original: 'throttle:' . md5($ip . ':' . $route . $identifier)
        // Fixed: Use underscores and create clean cache key
        $safeKey = 'throttle_' . md5($ip . '_' . $route . $identifier);

        return $safeKey;
    }

    /**
     * Build throttle response
     */
    protected function buildResponse(RequestInterface $request, bool $blocked = false)
    {
        $message = $blocked
            ? 'Terlalu banyak percobaan login. Silakan coba lagi dalam ' . $this->blockDuration . ' menit.'
            : 'Terlalu banyak permintaan. Silakan coba lagi nanti.';

        // Check if this is an AJAX request
        if ($request->isAJAX() || $request->hasHeader('X-Requested-With')) {
            return Services::response()
                ->setJSON([
                    'status' => false,
                    'error' => true,
                    'message' => $message,
                    'retry_after' => $blocked ? $this->blockDuration * 60 : $this->decayMinutes * 60
                ])
                ->setStatusCode(429);
        }

        // For regular requests, show error page or redirect
        $response = Services::response();
        $response->setStatusCode(429);

        // Try to load custom error view, fallback to simple message
        try {
            $body = view('errors/html/error_429', [
                'message' => $message,
                'retry_after' => $blocked ? $this->blockDuration : $this->decayMinutes
            ]);
        } catch (\Exception $e) {
            // Fallback if view doesn't exist
            $body = '<!DOCTYPE html>
<html>
<head>
    <title>Terlalu Banyak Permintaan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #f5f5f5; }
        .container { max-width: 500px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #e74c3c; margin-bottom: 20px; }
        p { color: #666; line-height: 1.6; }
        .btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>429 - Terlalu Banyak Permintaan</h1>
        <p>' . $message . '</p>
        <a href="/" class="btn">Kembali ke Beranda</a>
    </div>
</body>
</html>';
        }

        $response->setBody($body);
        return $response;
    }

    /**
     * Log blocking event
     */
    protected function logBlocking(RequestInterface $request)
    {
        log_message('warning', 'IP blocked due to too many attempts: ' . $request->getIPAddress() . ' on route: ' . $request->getUri()->getPath());

        // Optional: Log to database if table exists
        try {
            $db = \Config\Database::connect();

            // Check if table exists before inserting
            if ($db->tableExists('activity_logs')) {
                $db->table('activity_logs')->insert([
                    'user_id' => null,
                    'activity_type' => 'throttle_blocked',
                    'activity_details' => json_encode([
                        'ip' => $request->getIPAddress(),
                        'route' => $request->getUri()->getPath(),
                        'user_agent' => $request->getUserAgent()->getAgentString(),
                        'blocked_until' => date('Y-m-d H:i:s', time() + ($this->blockDuration * 60))
                    ]),
                    'ip_address' => $request->getIPAddress(),
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Failed to log throttle blocking: ' . $e->getMessage());
        }
    }
}
