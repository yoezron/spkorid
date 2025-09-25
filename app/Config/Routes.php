<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ============================================
// PUBLIC & AUTH ROUTES
// ============================================
$routes->get('/', 'Home::index');
$routes->get('login', 'AuthController::login', ['as' => 'login', 'filter' => 'noauth']);
$routes->post('login', 'AuthController::attemptLogin', ['filter' => 'noauth']);
$routes->get('register', 'RegistrationController::index', ['as' => 'register', 'filter' => 'noauth']);
$routes->post('register', 'RegistrationController::register', ['filter' => 'noauth']);
$routes->get('logout', 'AuthController::logout');
$routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);


// ============================================
// MEMBER ROUTES (ROLE: ANGGOTA)
// ============================================
$routes->group('member', ['namespace' => 'App\Controllers', 'filter' => 'auth:member'], function ($routes) {
    $routes->get('/', 'DashboardController::index');
    $routes->get('dashboard', 'DashboardController::index');

    // Profile & Password
    $routes->get('profile', 'MemberController::profile');
    $routes->get('profile/edit', 'MemberController::editProfile');
    $routes->post('profile/update', 'MemberController::updateProfile');
    $routes->get('change-password', 'MemberController::changePassword');
    $routes->post('change-password', 'MemberController::updatePassword');

    // Member Card
    $routes->get('card', 'MemberController::memberCard');
    $routes->get('card/download', 'MemberController::downloadCard');

    // Other member modules...
    $routes->get('posts', 'BlogController::myPosts');
    $routes->get('forum', 'ForumController::index');
    $routes->get('surveys', 'SurveyController::index');
    $routes->get('payment/history', 'PaymentController::history');
});


// ============================================
// PENGURUS ROUTES (ROLE: PENGURUS)
// ============================================
$routes->group('pengurus', ['namespace' => 'App\Controllers\Admin', 'filter' => 'auth:pengurus'], function ($routes) {
    $routes->get('/', 'DashboardController::index');
    $routes->get('dashboard', 'DashboardController::index');

    // Specific Pengurus tasks
    $routes->get('members/pending', 'MemberManagementController::pending');
    $routes->get('payments/pending', 'PaymentManagementController::pending');
    $routes->get('blog/pending', 'BlogManagementController::pending');
});


// ============================================
// ADMIN ROUTES (ROLE: SUPER ADMIN)
// ============================================
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'auth:super_admin'], function ($routes) {
    // Admin Dashboard
    // Menggunakan \App\Controllers\DashboardController karena tidak ada Admin\DashboardController
    $routes->get('/', '\App\Controllers\DashboardController::index');
    $routes->get('dashboard', '\App\Controllers\DashboardController::index');
    $routes->get('members/view/(:num)', 'MemberManagementController::view/$1');
    $routes->get('members/edit/(:num)', 'MemberManagementController::edit/$1');

    // Resourceful routes for cleaner CRUD management
    $routes->resource('members', ['controller' => 'MemberManagementController']);
    $routes->resource('users', ['controller' => 'UserController']);
    $routes->resource('roles', ['controller' => 'RoleController']);
    $routes->resource('menus', ['controller' => 'MenuController']);

    // Additional specific routes for admin
    $routes->get('members/pending', 'MemberManagementController::pending');
    $routes->post('members/verify/(:num)', 'MemberManagementController::verify/$1');
    $routes->post('members/reject/(:num)', 'MemberManagementController::reject/$1');

    $routes->get('roles/permissions/(:num)', 'RoleController::permissions/$1');
    $routes->post('roles/update-permissions/(:num)', 'RoleController::updatePermissions/$1');

    $routes->get('payments/pending', 'PaymentManagementController::pending');
    $routes->get('payments/report', 'PaymentManagementController::report');
});
