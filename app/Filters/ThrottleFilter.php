<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class ThrottleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $cache = Services::cache();
        $session = session();

        $identifier = $session->get('user_id') ?? $request->getIPAddress();

        // --- BARIS YANG DIPERBAIKI ---
        $key = 'throttle_' . md5($identifier . $request->getUri()->getPath());

        $maxAttempts = $arguments[0] ?? 60;
        $decayMinutes = $arguments[1] ?? 1;

        $attempts = $cache->get($key) ?? 0;

        if ($attempts >= $maxAttempts) {
            return Services::response()
                ->setStatusCode(429)
                ->setBody('Terlalu banyak percobaan. Silakan coba lagi nanti.');
        }

        $cache->save($key, $attempts + 1, $decayMinutes * 60);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
