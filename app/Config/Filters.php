<?php

// ============================================
// FILTER CONFIGURATION
// ============================================

// app/Config/Filters.php
namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseConfig
{
    /**
     * Configures aliases for Filter classes
     */
    public $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        // Custom filters
        'auth'          => \App\Filters\AuthFilter::class,
        'noauth'        => \App\Filters\NoAuthFilter::class,
        'api_auth'      => \App\Filters\ApiAuthFilter::class,
        'permission'    => \App\Filters\PermissionFilter::class,
        'maintenance'   => \App\Filters\MaintenanceFilter::class,
        'throttle'      => \App\Filters\ThrottleFilter::class,
        'cors'          => \App\Filters\CorsFilter::class,
        'xss'           => \App\Filters\XssFilter::class,
        'activity_log'  => \App\Filters\ActivityLogFilter::class,
    ];

    /**
     * List of filters to run before every request
     */
    public $globals = [
        'before' => [
            'maintenance',
            'honeypot',
            'csrf' => ['except' => ['api/*']],
            'xss' => ['except' => ['api/*']],
            'invalidchars',
        ],
        'after' => [
            'toolbar',
            'secureheaders',
            'activity_log',
        ],
    ];

    /**
     * List of filters for specific HTTP methods
     */
    public $methods = [
        'post' => ['throttle:30,1'], // 30 requests per minute for POST
        'put'  => ['throttle:30,1'],
        'delete' => ['throttle:30,1'],
    ];

    /**
     * List of filters for specific routes
     */
    public $filters = [
        'api/*' => [
            'before' => ['cors', 'api_auth'],
            'after' => ['cors']
        ],
        'admin/*' => [
            'before' => ['auth:super_admin'],
            'after' => []
        ],
        'pengurus/*' => [
            'before' => ['auth:super_admin,pengurus'],
            'after' => []
        ],
        'member/*' => [
            'before' => ['auth'],
            'after' => []
        ],
    ];

    public $aliases = [
        'auth'     => \App\Filters\AuthFilter::class,
        'role'     => \App\Filters\RoleFilter::class,
        'throttle' => \App\Filters\ThrottleFilter::class,
        'verified' => \App\Filters\VerifiedFilter::class,
    ];

    public $filters = [
        'auth' => [
            'before' => [
                'admin/*',
                'member/*',
                'pengurus/*'
            ]
        ],
        'throttle' => [
            'before' => [
                'auth/login',
                'auth/register'
            ]
        ]
    ];
}
