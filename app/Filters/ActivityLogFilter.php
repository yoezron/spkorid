<?php

// ============================================
// ACTIVITY LOG FILTER
// ============================================

// app/Filters/ActivityLogFilter.php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\ActivityLogModel;

class ActivityLogFilter implements FilterInterface
{
    protected $activityLog;

    public function __construct()
    {
        $this->activityLog = new ActivityLogModel();
    }

    /**
     * Log user activity
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Skip logging for certain routes
        $skipRoutes = ['/api', '/assets', '/uploads'];
        $currentPath = $request->uri->getPath();

        foreach ($skipRoutes as $route) {
            if (strpos($currentPath, $route) === 0) {
                return;
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $session = session();

        // Only log for authenticated users
        if (!$session->get('logged_in')) {
            return;
        }

        // Skip logging for GET requests to reduce noise
        if ($request->getMethod() === 'get') {
            return;
        }

        // Log the activity
        $userId = $session->get('user_id');
        $activityType = $request->getMethod() . '_' . str_replace('/', '_', $request->uri->getPath());
        $description = sprintf(
            '%s request to %s',
            strtoupper($request->getMethod()),
            $request->uri->getPath()
        );

        // Add POST data to description (excluding sensitive fields)
        if ($request->getMethod() === 'post') {
            $postData = $request->getPost();
            unset($postData['password'], $postData['password_confirm'], $postData['current_password']);

            if (!empty($postData)) {
                $description .= ' | Data: ' . json_encode($postData);
            }
        }

        $this->activityLog->logActivity($userId, $activityType, $description);
    }
}
