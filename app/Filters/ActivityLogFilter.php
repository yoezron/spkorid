<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use App\Models\ActivityLogModel;

class ActivityLogFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Nothing to do here
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $session = Services::session();

        if (! $session->get('logged_in')) {
            return;
        }

        // Skip logging for GET requests to reduce noise
        if ($request->getMethod() === 'get') {
            return;
        }

        // Log the activity
        $userId = $session->get('user_id');

        // --- BARIS YANG DIPERBAIKI ---
        $path = $request->getUri()->getPath();
        $activityType = $request->getMethod() . '_' . str_replace('/', '_', $path);
        $description = sprintf(
            '%s request to %s',
            strtoupper($request->getMethod()),
            $path
        );

        // Add POST data to description (excluding sensitive fields)
        if ($request->getMethod() === 'post') {
            $postData = $request->getPost();
            unset($postData['password'], $postData['confirm_password'], $postData['current_password']);

            if (!empty($postData)) {
                $description .= ' with data: ' . json_encode($postData);
            }
        }

        $activityLog = new ActivityLogModel();
        $activityLog->logActivity($userId, $activityType, $description);
    }
}
