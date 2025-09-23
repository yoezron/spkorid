<?php
// app/Config/Routes.php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ============================================
// PUBLIC ROUTES
// ============================================

// Home & Static Pages
$routes->get('/', 'Home::index');
$routes->get('about', 'Home::about');
$routes->get('contact', 'Home::contact');
$routes->post('contact/send', 'Home::sendContact');

// Authentication Routes
$routes->group('', ['filter' => 'noauth'], function ($routes) {
    $routes->get('login', 'AuthController::login');
    $routes->post('login', 'AuthController::attemptLogin');
    $routes->get('register', 'RegistrationController::index');
    $routes->post('register', 'RegistrationController::register');
    $routes->get('verify-email/(:segment)', 'RegistrationController::verifyEmail/$1');
    $routes->get('forgot-password', 'AuthController::forgotPassword');
    $routes->post('forgot-password', 'AuthController::sendResetLink');
    $routes->get('reset-password/(:segment)', 'AuthController::resetPassword/$1');
    $routes->post('reset-password', 'AuthController::updatePassword');
});

$routes->get('logout', 'AuthController::logout');

// AJAX Routes for Registration
$routes->get('register/get-cities/(:num)', 'RegistrationController::getCities/$1');
$routes->get('register/get-kampus/(:num)', 'RegistrationController::getKampus/$1');
$routes->get('register/get-prodi/(:num)', 'RegistrationController::getProdi/$1');

// Public Blog
$routes->group('blog', function ($routes) {
    $routes->get('/', 'BlogController::index');
    $routes->get('view/(:segment)', 'BlogController::view/$1');
    $routes->get('search', 'BlogController::search');
    $routes->get('category/(:segment)', 'BlogController::category/$1');
    $routes->get('tag/(:segment)', 'BlogController::tag/$1');
});

// Public Informasi
$routes->group('informasi', function ($routes) {
    $routes->get('/', 'InformasiController::index');
    $routes->get('(:segment)', 'InformasiController::view/$1');
});

// Public Documents
$routes->get('ad-art', 'DocumentController::adArt');
$routes->get('manifesto', 'DocumentController::manifesto');
$routes->get('sejarah-spk', 'DocumentController::sejarah');

// Public Pengaduan
$routes->group('pengaduan', function ($routes) {
    $routes->get('create', 'PengaduanController::create');
    $routes->post('store', 'PengaduanController::store');
    $routes->get('success', 'PengaduanController::success');
    $routes->get('track', 'PengaduanController::track');
});

// ============================================
// AUTHENTICATED ROUTES (ALL LOGGED IN USERS)
// ============================================

$routes->group('', ['filter' => 'auth'], function ($routes) {
    // Dashboard (redirects based on role)
    $routes->get('dashboard', 'DashboardController::index');

    // Common Profile Routes
    $routes->get('profile', 'MemberController::profile');
    $routes->get('change-password', 'MemberController::changePassword');
    $routes->post('change-password', 'MemberController::updatePassword');
});

// ============================================
// MEMBER ROUTES (Role: Anggota)
// ============================================

$routes->group('member', ['filter' => 'auth:anggota'], function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');

    // Profile Management
    $routes->get('profile', 'MemberController::profile');
    $routes->get('edit-profile', 'MemberController::editProfile');
    $routes->post('update-profile', 'MemberController::updateProfile');

    // Member Card
    $routes->get('card', 'MemberController::memberCard');
    $routes->get('card/download', 'MemberController::downloadCard');

    // Payment
    $routes->group('payment', function ($routes) {
        $routes->get('history', 'PaymentController::history');
        $routes->get('create', 'PaymentController::create');
        $routes->post('store', 'PaymentController::store');
        $routes->get('invoice/(:num)', 'PaymentController::invoice/$1');
    });

    // Blog Posts
    $routes->group('posts', function ($routes) {
        $routes->get('/', 'BlogController::myPosts');
        $routes->get('create', 'BlogController::create');
        $routes->post('store', 'BlogController::store');
        $routes->get('edit/(:num)', 'BlogController::edit/$1');
        $routes->post('update/(:num)', 'BlogController::update/$1');
        $routes->get('delete/(:num)', 'BlogController::delete/$1');
    });

    // Forum
    $routes->group('forum', function ($routes) {
        $routes->get('/', 'ForumController::index');
        $routes->get('category/(:segment)', 'ForumController::category/$1');
        $routes->get('thread/(:num)', 'ForumController::thread/$1');
        $routes->get('create-thread', 'ForumController::createThread');
        $routes->post('store-thread', 'ForumController::storeThread');
        $routes->post('reply/(:num)', 'ForumController::reply/$1');
        $routes->post('edit-reply/(:num)', 'ForumController::editReply/$1');
        $routes->get('search', 'ForumController::search');
    });

    // Surveys
    $routes->group('surveys', function ($routes) {
        $routes->get('/', 'SurveyController::index');
        $routes->get('take/(:num)', 'SurveyController::take/$1');
        $routes->post('submit/(:num)', 'SurveyController::submit/$1');
        $routes->get('results/(:num)', 'SurveyController::viewResults/$1');
    });

    // My Complaints
    $routes->get('complaints', 'PengaduanController::myComplaints');
    $routes->get('complaint/view/(:num)', 'PengaduanController::viewComplaint/$1');
});

// ============================================
// PENGURUS ROUTES (Role: Pengurus)
// ============================================

$routes->group('pengurus', ['filter' => 'auth:pengurus'], function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');

    // Member Verification
    $routes->group('members', function ($routes) {
        $routes->get('pending', 'Admin\MemberManagementController::pending');
        $routes->get('view/(:num)', 'Admin\MemberManagementController::view/$1');
        $routes->post('verify/(:num)', 'Admin\MemberManagementController::verify/$1');
        $routes->post('reject/(:num)', 'Admin\MemberManagementController::reject/$1');
        $routes->get('list', 'Admin\MemberManagementController::index');
        $routes->post('suspend/(:num)', 'Admin\MemberManagementController::suspend/$1');
        $routes->post('reactivate/(:num)', 'Admin\MemberManagementController::reactivate/$1');
        $routes->get('search', 'Admin\MemberManagementController::search');
    });

    // Payment Verification
    $routes->group('payments', function ($routes) {
        $routes->get('pending', 'Admin\PaymentManagementController::pending');
        $routes->post('verify/(:num)', 'Admin\PaymentManagementController::verify/$1');
        $routes->post('reject/(:num)', 'Admin\PaymentManagementController::reject/$1');
        $routes->get('report', 'Admin\PaymentManagementController::report');
        $routes->get('export', 'Admin\PaymentManagementController::export');
    });

    // Blog Management
    $routes->group('blog', function ($routes) {
        $routes->get('pending', 'Admin\BlogManagementController::pending');
        $routes->get('review/(:num)', 'Admin\BlogManagementController::review/$1');
        $routes->post('approve/(:num)', 'Admin\BlogManagementController::approve/$1');
        $routes->post('reject/(:num)', 'Admin\BlogManagementController::reject/$1');
        $routes->get('published', 'Admin\BlogManagementController::published');
    });

    // Informasi Serikat Management
    $routes->group('informasi', function ($routes) {
        $routes->get('/', 'Admin\InformasiManagementController::index');
        $routes->get('create', 'Admin\InformasiManagementController::create');
        $routes->post('store', 'Admin\InformasiManagementController::store');
        $routes->get('edit/(:num)', 'Admin\InformasiManagementController::edit/$1');
        $routes->post('update/(:num)', 'Admin\InformasiManagementController::update/$1');
        $routes->get('delete/(:num)', 'Admin\InformasiManagementController::delete/$1');
    });

    // Pengaduan Management
    $routes->group('pengaduan', function ($routes) {
        $routes->get('/', 'Admin\PengaduanManagementController::index');
        $routes->get('view/(:num)', 'Admin\PengaduanManagementController::view/$1');
        $routes->post('assign/(:num)', 'Admin\PengaduanManagementController::assign/$1');
        $routes->post('update-status/(:num)', 'Admin\PengaduanManagementController::updateStatus/$1');
        $routes->get('my-assigned', 'Admin\PengaduanManagementController::myAssigned');
    });

    // Survey Management
    $routes->group('surveys', function ($routes) {
        $routes->get('/', 'Admin\SurveyManagementController::index');
        $routes->get('create', 'Admin\SurveyManagementController::create');
        $routes->post('store', 'Admin\SurveyManagementController::store');
        $routes->get('edit/(:num)', 'Admin\SurveyManagementController::edit/$1');
        $routes->post('update/(:num)', 'Admin\SurveyManagementController::update/$1');
        $routes->get('results/(:num)', 'Admin\SurveyManagementController::results/$1');
        $routes->get('export/(:num)', 'Admin\SurveyManagementController::export/$1');
        $routes->post('toggle-status/(:num)', 'Admin\SurveyManagementController::toggleStatus/$1');
        $routes->get('delete/(:num)', 'Admin\SurveyManagementController::delete/$1');
    });

    // Forum Moderation
    $routes->group('forum', function ($routes) {
        $routes->post('pin-thread/(:num)', 'Admin\ForumModerationController::pinThread/$1');
        $routes->post('lock-thread/(:num)', 'Admin\ForumModerationController::lockThread/$1');
        $routes->post('delete-thread/(:num)', 'Admin\ForumModerationController::deleteThread/$1');
        $routes->post('delete-reply/(:num)', 'Admin\ForumModerationController::deleteReply/$1');
    });
});

// ============================================
// SUPER ADMIN ROUTES (Role: Super Admin)
// ============================================

$routes->group('admin', ['filter' => 'auth:super_admin'], function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');

    // All Pengurus Routes (Super Admin has access to all)
    // Member Management
    $routes->group('members', function ($routes) {
        $routes->get('/', 'Admin\MemberManagementController::index');
        $routes->get('pending', 'Admin\MemberManagementController::pending');
        $routes->get('view/(:num)', 'Admin\MemberManagementController::view/$1');
        $routes->post('verify/(:num)', 'Admin\MemberManagementController::verify/$1');
        $routes->post('reject/(:num)', 'Admin\MemberManagementController::reject/$1');
        $routes->post('suspend/(:num)', 'Admin\MemberManagementController::suspend/$1');
        $routes->post('reactivate/(:num)', 'Admin\MemberManagementController::reactivate/$1');
        $routes->get('delete/(:num)', 'Admin\MemberManagementController::delete/$1');
        $routes->get('export', 'Admin\MemberManagementController::export');
        $routes->post('import', 'Admin\MemberManagementController::import');
        $routes->get('search', 'Admin\MemberManagementController::search');
    });

    // Role Management (Super Admin Only)
    $routes->group('roles', function ($routes) {
        $routes->get('/', 'Admin\RoleController::index');
        $routes->get('create', 'Admin\RoleController::create');
        $routes->post('store', 'Admin\RoleController::store');
        $routes->get('edit/(:num)', 'Admin\RoleController::edit/$1');
        $routes->post('update/(:num)', 'Admin\RoleController::update/$1');
        $routes->get('delete/(:num)', 'Admin\RoleController::delete/$1');
        $routes->get('permissions/(:num)', 'Admin\RoleController::permissions/$1');
        $routes->post('update-permissions/(:num)', 'Admin\RoleController::updatePermissions/$1');
    });

    // Menu Management (Super Admin Only)
    $routes->group('menus', function ($routes) {
        $routes->get('/', 'Admin\MenuController::index');
        $routes->get('create', 'Admin\MenuController::create');
        $routes->post('store', 'Admin\MenuController::store');
        $routes->get('edit/(:num)', 'Admin\MenuController::edit/$1');
        $routes->post('update/(:num)', 'Admin\MenuController::update/$1');
        $routes->get('delete/(:num)', 'Admin\MenuController::delete/$1');
        $routes->post('reorder', 'Admin\MenuController::reorder');
    });

    // Content Management (Super Admin Only)
    $routes->group('content', function ($routes) {
        $routes->get('/', 'Admin\CMSController::index');
        $routes->get('create', 'Admin\CMSController::create');
        $routes->post('store', 'Admin\CMSController::store');
        $routes->get('edit/(:num)', 'Admin\CMSController::edit/$1');
        $routes->post('update/(:num)', 'Admin\CMSController::update/$1');
        $routes->get('delete/(:num)', 'Admin\CMSController::delete/$1');
        $routes->post('toggle-publish/(:num)', 'Admin\CMSController::togglePublish/$1');
    });

    // System Configuration (Super Admin Only)
    $routes->group('system', function ($routes) {
        $routes->get('settings', 'Admin\SystemController::settings');
        $routes->post('update-settings', 'Admin\SystemController::updateSettings');
        $routes->get('backup', 'Admin\SystemController::backup');
        $routes->post('create-backup', 'Admin\SystemController::createBackup');
        $routes->get('logs', 'Admin\SystemController::logs');
        $routes->get('activity-logs', 'Admin\SystemController::activityLogs');
        $routes->get('clear-cache', 'Admin\SystemController::clearCache');
        $routes->get('maintenance', 'Admin\SystemController::maintenance');
        $routes->post('toggle-maintenance', 'Admin\SystemController::toggleMaintenance');
    });

    // Reference Data Management (Super Admin Only)
    $routes->group('reference', function ($routes) {
        // Status Kepegawaian
        $routes->get('status-kepegawaian', 'Admin\ReferenceController::statusKepegawaian');
        $routes->post('status-kepegawaian/store', 'Admin\ReferenceController::storeStatusKepegawaian');
        $routes->post('status-kepegawaian/update/(:num)', 'Admin\ReferenceController::updateStatusKepegawaian/$1');
        $routes->get('status-kepegawaian/delete/(:num)', 'Admin\ReferenceController::deleteStatusKepegawaian/$1');

        // Pemberi Gaji
        $routes->get('pemberi-gaji', 'Admin\ReferenceController::pemberiGaji');
        $routes->post('pemberi-gaji/store', 'Admin\ReferenceController::storePemberiGaji');
        $routes->post('pemberi-gaji/update/(:num)', 'Admin\ReferenceController::updatePemberiGaji/$1');
        $routes->get('pemberi-gaji/delete/(:num)', 'Admin\ReferenceController::deletePemberiGaji/$1');

        // Range Gaji
        $routes->get('range-gaji', 'Admin\ReferenceController::rangeGaji');
        $routes->post('range-gaji/store', 'Admin\ReferenceController::storeRangeGaji');
        $routes->post('range-gaji/update/(:num)', 'Admin\ReferenceController::updateRangeGaji/$1');
        $routes->get('range-gaji/delete/(:num)', 'Admin\ReferenceController::deleteRangeGaji/$1');

        // Wilayah (Provinsi & Kota)
        $routes->get('provinsi', 'Admin\ReferenceController::provinsi');
        $routes->post('provinsi/store', 'Admin\ReferenceController::storeProvinsi');
        $routes->post('provinsi/update/(:num)', 'Admin\ReferenceController::updateProvinsi/$1');
        $routes->get('provinsi/delete/(:num)', 'Admin\ReferenceController::deleteProvinsi/$1');

        $routes->get('kota', 'Admin\ReferenceController::kota');
        $routes->post('kota/store', 'Admin\ReferenceController::storeKota');
        $routes->post('kota/update/(:num)', 'Admin\ReferenceController::updateKota/$1');
        $routes->get('kota/delete/(:num)', 'Admin\ReferenceController::deleteKota/$1');

        // Jenis PT, Kampus, Prodi
        $routes->get('jenis-pt', 'Admin\ReferenceController::jenisPT');
        $routes->post('jenis-pt/store', 'Admin\ReferenceController::storeJenisPT');
        $routes->post('jenis-pt/update/(:num)', 'Admin\ReferenceController::updateJenisPT/$1');
        $routes->get('jenis-pt/delete/(:num)', 'Admin\ReferenceController::deleteJenisPT/$1');

        $routes->get('kampus', 'Admin\ReferenceController::kampus');
        $routes->post('kampus/store', 'Admin\ReferenceController::storeKampus');
        $routes->post('kampus/update/(:num)', 'Admin\ReferenceController::updateKampus/$1');
        $routes->get('kampus/delete/(:num)', 'Admin\ReferenceController::deleteKampus/$1');

        $routes->get('prodi', 'Admin\ReferenceController::prodi');
        $routes->post('prodi/store', 'Admin\ReferenceController::storeProdi');
        $routes->post('prodi/update/(:num)', 'Admin\ReferenceController::updateProdi/$1');
        $routes->get('prodi/delete/(:num)', 'Admin\ReferenceController::deleteProdi/$1');
    });

    // User Management (Super Admin Only)
    $routes->group('users', function ($routes) {
        $routes->get('/', 'Admin\UserController::index');
        $routes->get('create', 'Admin\UserController::create');
        $routes->post('store', 'Admin\UserController::store');
        $routes->get('edit/(:num)', 'Admin\UserController::edit/$1');
        $routes->post('update/(:num)', 'Admin\UserController::update/$1');
        $routes->post('reset-password/(:num)', 'Admin\UserController::resetPassword/$1');
        $routes->post('toggle-status/(:num)', 'Admin\UserController::toggleStatus/$1');
        $routes->get('activity/(:num)', 'Admin\UserController::viewActivity/$1');
    });

    // Reports (Super Admin has full access)
    $routes->group('reports', function ($routes) {
        $routes->get('members', 'Admin\ReportController::members');
        $routes->get('payments', 'Admin\ReportController::payments');
        $routes->get('activities', 'Admin\ReportController::activities');
        $routes->get('surveys', 'Admin\ReportController::surveys');
        $routes->get('pengaduan', 'Admin\ReportController::pengaduan');
        $routes->get('export/(:segment)', 'Admin\ReportController::export/$1');
    });
});

// ============================================
// API ROUTES (Optional - untuk future mobile app)
// ============================================

$routes->group('api/v1', ['filter' => 'api_auth'], function ($routes) {
    // Authentication
    $routes->post('login', 'Api\AuthController::login');
    $routes->post('refresh-token', 'Api\AuthController::refreshToken');
    $routes->post('logout', 'Api\AuthController::logout');

    // Member
    $routes->get('member/profile', 'Api\MemberController::profile');
    $routes->post('member/update-profile', 'Api\MemberController::updateProfile');
    $routes->get('member/card', 'Api\MemberController::getCard');

    // Informasi
    $routes->get('informasi', 'Api\InformasiController::index');
    $routes->get('informasi/(:num)', 'Api\InformasiController::view/$1');

    // Blog
    $routes->get('blog', 'Api\BlogController::index');
    $routes->get('blog/(:num)', 'Api\BlogController::view/$1');

    // Forum
    $routes->get('forum/threads', 'Api\ForumController::threads');
    $routes->get('forum/thread/(:num)', 'Api\ForumController::thread/$1');
    $routes->post('forum/reply', 'Api\ForumController::reply');
});

// ============================================
// FALLBACK ROUTES
// ============================================

// CMS Pages (Dynamic)
$routes->get('page/(:segment)', 'CMSController::page/$1');

// 404 Override
$routes->set404Override(function () {
    return view('errors/404', ['title' => 'Halaman Tidak Ditemukan']);
});

// Auto Route (Disabled for security)
$routes->setAutoRoute(false);
