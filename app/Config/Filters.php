<?php
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
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     *
     * @var array<string, class-string|list<class-string>> [filter_name => classname]
     *                                                      or [filter_name => [classname1, classname2, ...]]
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        // Custom filters
        'auth'          => \App\Filters\AuthFilter::class,
        'role'          => \App\Filters\RoleFilter::class,
        'throttle'      => \App\Filters\ThrottleFilter::class,
        'verified'      => \App\Filters\VerifiedFilter::class,
        'noauth'        => \App\Filters\NoAuthFilter::class,
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     *
     * @var array<string, array<string, array<string, string>>>|array<string, list<string>>
     */
    public array $globals = [
        'before' => [
            // 'honeypot',
            'csrf' => ['except' => ['api/*']],
            'invalidchars',
        ],
        'after' => [
            'toolbar',
            'secureheaders',
        ],
    ];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     *
     * Example:
     * 'post' => ['foo', 'bar']
     *
     * If you use this, you should disable auto-routing because auto-routing
     * permits any HTTP method to access a controller. Accessing the controller
     * with a method you don't expect could bypass the filter.
     *
     * @var array<string, list<string>>
     */
    public array $methods = [];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     *
     * @var array<string, array<string, list<string>>>
     */
    public array $filters = [
        'auth' => [
            'before' => [
                'admin/*',
                'member/*',
                'pengurus/*',
                'dashboard',
                'profile/*',
                'forum/create',
                'forum/reply/*',
                'survey/respond/*',
            ]
        ],
        'role:super_admin' => [
            'before' => [
                'admin/*',
            ]
        ],
        'role:super_admin,pengurus' => [
            'before' => [
                'pengurus/*',
            ]
        ],
        'verified' => [
            'before' => [
                'member/surveys/*',
                'member/card',
                'forum/create',
            ]
        ],
        'throttle:5,1,15' => [
            'before' => [
                'login',
                'register',
                'forgot-password',
            ]
        ],
        'throttle:30,1' => [
            'before' => [
                'api/*',
                'ajax/*',
            ]
        ],
        'noauth' => [
            'before' => [
                'login',
                'register',
                'forgot-password',
                'reset-password/*',
            ]
        ]
    ];
}
