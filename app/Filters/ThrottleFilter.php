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
        // Parse arguments if provided (e.g., 'throttle:10,2' for 10 attempts in 2 minutes)
        if ($arguments !== null) {
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

        $key = $this->resolveRequestSignature($request);

        // Check if IP is blocked
        $blockKey = 'blocked:' . $key;
        if ($this->cache->get($blockKey)) {
            return $this->buildResponse($request, true);
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
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // If login was successful, clear the throttle
        if ($response->getStatusCode() === 200) {
            $session = session();
            if ($session->get('logged_in')) {
                $key = $this->resolveRequestSignature($request);
                $this->cache->delete($key);
                $this->cache->delete('blocked:' . $key);
            }
        }
    }

    /**
     * Generate unique signature for the request
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
                $identifier = ':' . md5($email);
            }
        }

        return 'throttle:' . md5($ip . ':' . $route . $identifier);
    }

    /**
     * Build throttle response
     */
    protected function buildResponse(RequestInterface $request, bool $blocked = false)
    {
        $message = $blocked
            ? 'Terlalu banyak percobaan. Silakan coba lagi dalam ' . $this->blockDuration . ' menit.'
            : 'Terlalu banyak permintaan. Silakan coba lagi nanti.';

        if ($request->isAJAX()) {
            return Services::response()
                ->setJSON([
                    'status' => false,
                    'message' => $message,
                    'retry_after' => $blocked ? $this->blockDuration * 60 : $this->decayMinutes * 60
                ])
                ->setStatusCode(429);
        }

        return Services::response()
            ->setStatusCode(429)
            ->setBody(view('errors/html/error_429', [
                'message' => $message,
                'retry_after' => $blocked ? $this->blockDuration : $this->decayMinutes
            ]));
    }

    /**
     * Log blocking event
     */
    protected function logBlocking(RequestInterface $request)
    {
        log_message('warning', 'IP blocked due to too many attempts: ' . $request->getIPAddress() . ' on route: ' . $request->getUri()->getPath());

        // Optional: Log to database
        try {
            $db = \Config\Database::connect();
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
        } catch (\Exception $e) {
            log_message('error', 'Failed to log throttle blocking: ' . $e->getMessage());
        }
    }
}
