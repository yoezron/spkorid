<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ============================================
// PUBLIC ROUTES
// ============================================
$routes->get('/', 'Home::index');
$routes->get('about', 'Home::about');
$routes->get('contact', 'Home::contact');

// AUTHENTICATION (for guests)
$routes->group('', ['filter' => 'noauth'], function ($routes) {
    $routes->get('login', 'AuthController::login', ['as' => 'login']);
    $routes->post('login', 'AuthController::attemptLogin');
    $routes->get('register', 'RegistrationController::index', ['as' => 'register']);
    $routes->post('register', 'RegistrationController::register');
    // Password Reset routes are assumed to be in AuthController based on its methods
    $routes->get('forgot-password', 'AuthController::forgotPassword');
    $routes->post('forgot-password', 'AuthController::sendResetLink');
    $routes->get('reset-password/(:segment)', 'AuthController::resetPassword/$1');
    $routes->post('reset-password', 'AuthController::updatePassword');
});

// LOGOUT (for authenticated users)
$routes->get('logout', 'AuthController::logout');


// ============================================
// AUTHENTICATED ROUTES (All Logged In Users)
// ============================================
$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'DashboardController::index');
    // Note: Profile routes are consolidated into MemberController for members.
    // For admin/pengurus, profile actions might be in their respective controllers or a shared one.
    // Assuming a general profile might not exist, redirecting to role-specific dashboards is safer.
});


// ============================================
// MEMBER ROUTES (Role: Anggota)
// ============================================
$routes->group('member', ['filter' => 'auth:anggota', 'namespace' => 'App\Controllers'], function ($routes) {
    // Corrected to point to DashboardController which handles role-based views
    $routes->get('/', 'DashboardController::index');
    $routes->get('dashboard', 'DashboardController::index');

    // Corrected: Pointing to actual methods within MemberController
    $routes->get('profile', 'MemberController::profile');
    $routes->get('profile/edit', 'MemberController::editProfile');
    $routes->post('profile/update', 'MemberController::updateProfile');
    $routes->get('card', 'MemberController::memberCard');
    $routes->get('download-card', 'MemberController::downloadCard'); // Menambahkan rute untuk download
    $routes->get('change-password', 'MemberController::change_password');
    $routes->post('change-password', 'MemberController::update_password'); // Assumed method name

    // Corrected: Pointing to actual controllers
    $routes->group('payment', function ($routes) {
        $routes->get('history', 'PaymentController::history');
        $routes->get('create', 'PaymentController::create');
        $routes->post('store', 'PaymentController::store');
        $routes->get('invoice/(:num)', 'PaymentController::invoice/$1');
    });

    $routes->group('posts', function ($routes) {
        $routes->get('/', 'BlogController::index'); // Assuming BlogController handles member's post list
        $routes->get('create', 'BlogController::create');
        $routes->post('store', 'BlogController::store');
        $routes->get('edit/(:num)', 'BlogController::edit/$1');
        $routes->put('update/(:num)', 'BlogController::update/$1');
        $routes->post('delete/(:num)', 'BlogController::delete/$1');
    });

    $routes->group('forum', function ($routes) {
        $routes->get('/', 'ForumController::index');
        $routes->get('category/(:segment)', 'ForumController::category/$1');
        $routes->get('thread/(:num)', 'ForumController::thread/$1');
        $routes->get('create-thread', 'ForumController::createThread');
        $routes->post('store-thread', 'ForumController::storeThread');
        $routes->post('reply/(:num)', 'ForumController::reply/$1');
    });

    $routes->group('surveys', function ($routes) {
        $routes->get('/', 'SurveyController::index');
        $routes->get('take/(:num)', 'SurveyController::take/$1');
        $routes->post('submit/(:num)', 'SurveyController::submit/$1');
    });
});


// ============================================
// PENGURUS ROUTES (Role: Pengurus)
// ============================================
$routes->group('pengurus', ['filter' => 'auth:pengurus'], function ($routes) {
    $routes->get('/', 'DashboardController::index');
    $routes->get('dashboard', 'DashboardController::index');

    // These routes point to Admin controllers but are filtered for 'pengurus' role
    $routes->get('members/pending', 'MemberManagementController::pending');
    $routes->get('payments', 'PaymentManagementController::pending');
    $routes->get('surveys/results', 'SurveyManagementController::results');
    $routes->get('surveys/create', 'SurveyManagementController::create');
});


// ============================================
// SUPER ADMIN ROUTES (Role: Super Admin)
// ============================================
$routes->group('admin', ['filter' => 'auth:super_admin', 'namespace' => 'App\Controllers\Admin'], function ($routes) {
    $routes->get('/', 'DashboardController::index'); // Mengasumsikan ada DashboardController di namespace Admin
    $routes->get('dashboard', 'DashboardController::index');

    $routes->group('members', function ($routes) {
        $routes->get('/', 'MemberManagementController::index');
        $routes->get('pending', 'MemberManagementController::pending');
        $routes->get('view/(:num)', 'MemberManagementController::view/$1');
        $routes->post('verify/(:num)', 'MemberManagementController::verify/$1');
        $routes->post('reject/(:num)', 'MemberManagementController::reject/$1');
        $routes->get('create', 'MemberManagementController::create');
        $routes->post('store', 'MemberManagementController::store');
        $routes->get('edit/(:num)', 'MemberManagementController::edit/$1');
        $routes->put('update/(:num)', 'MemberManagementController::update/$1');
    });

    $routes->group('roles', function ($routes) {
        $routes->get('/', 'RoleController::index');
        $routes->get('permissions/(:num)', 'RoleController::permissions/$1');
        $routes->post('update-permissions/(:num)', 'RoleController::updatePermissions/$1');
        $routes->get('create', 'RoleController::create');
        $routes->post('store', 'RoleController::store');
        $routes->get('edit/(:num)', 'RoleController::edit/$1');
        $routes->put('update/(:num)', 'RoleController::update/$1');
        $routes->delete('delete/(:num)', 'RoleController::delete/$1');
        $routes->get('permissions/(:num)', 'RoleController::permissions/$1');
        $routes->post('update-permissions/(:num)', 'RoleController::updatePermissions/$1');
    });

    // NOTE: MenuController was not provided. These routes are commented out.
    // Uncomment and create Admin/MenuController.php when ready.
    /*
    $routes->group('menus', function ($routes) {
        $routes->get('/', 'MenuController::index');
        // ... other menu routes
    });
    */

    $routes->group('users', function ($routes) {
        $routes->get('/', 'UserController::index');
        $routes->get('activity/(:num)', 'UserController::viewActivity/$1');
        $routes->get('create', 'UserController::create');
        $routes->post('store', 'UserController::store');
        $routes->get('edit/(:num)', 'UserController::edit/$1');
        $routes->put('update/(:num)', 'UserController::update/$1');
        $routes->post('delete/(:num)', 'UserController::delete/$1');
        $routes->get('activity/(:num)', 'UserController::viewActivity/$1');
    });


    // Rute untuk Content Management
    $routes->group('content', function ($routes) {
        $routes->get('/', 'CMSController::index');
        $routes->get('create', 'CMSController::create');
        $routes->post('store', 'CMSController::store');
        $routes->get('edit/(:num)', 'CMSController::edit/$1');
        $routes->put('update/(:num)', 'CMSController::update/$1');
    });
});
