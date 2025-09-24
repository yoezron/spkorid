<?php
// app/Config/Routes.php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ============================================
// PUBLIC ROUTES (No Authentication Required)
// ============================================

// Landing Page & Static Pages
$routes->get('/', 'Home::index');
$routes->get('about', 'Home::about');
$routes->get('contact', 'Home::contact');
$routes->post('contact/send', 'Home::sendContact');
$routes->get('maintenance', 'Home::maintenance');

// Authentication Routes (Only for guests)
$routes->group('', ['filter' => 'noauth'], function ($routes) {
    // Login
    $routes->get('login', 'AuthController::login');
    $routes->post('login', 'AuthController::attemptLogin');

    // Registration
    $routes->get('register', 'RegistrationController::index');
    $routes->post('register', 'RegistrationController::register');
    $routes->get('verify-email/(:segment)', 'RegistrationController::verifyEmail/$1');
    $routes->get('resend-verification', 'RegistrationController::resendVerification');

    // Password Reset
    $routes->get('forgot-password', 'AuthController::forgotPassword');
    $routes->post('forgot-password', 'AuthController::sendResetLink');
    $routes->get('reset-password/(:segment)', 'AuthController::resetPassword/$1');
    $routes->post('reset-password', 'AuthController::updatePassword');
});

// Logout (available for all authenticated users)
$routes->get('logout', 'AuthController::logout');

// AJAX Routes for Registration (Public)
$routes->group('ajax', function ($routes) {
    $routes->get('get-cities/(:num)', 'AjaxController::getCities/$1');
    $routes->get('get-kampus/(:num)', 'AjaxController::getKampus/$1');
    $routes->get('get-prodi/(:num)', 'AjaxController::getProdi/$1');
});

// Public Blog Routes
$routes->group('blog', function ($routes) {
    $routes->get('/', 'BlogController::index');
    $routes->get('view/(:segment)', 'BlogController::view/$1');
    $routes->get('search', 'BlogController::search');
    $routes->get('category/(:segment)', 'BlogController::category/$1');
    $routes->get('tag/(:segment)', 'BlogController::tag/$1');
    $routes->get('author/(:segment)', 'BlogController::author/$1');
    $routes->get('archive/(:num)/(:num)', 'BlogController::archive/$1/$2'); // year/month
});

// Public Informasi Routes
$routes->group('informasi', function ($routes) {
    $routes->get('/', 'InformasiController::index');
    $routes->get('(:segment)', 'InformasiController::view/$1');
    $routes->get('kategori/(:segment)', 'InformasiController::kategori/$1');
});

// Public Documents
$routes->get('ad-art', 'DocumentController::adArt');
$routes->get('manifesto', 'DocumentController::manifesto');
$routes->get('sejarah-spk', 'DocumentController::sejarah');
$routes->get('dokumen/(:segment)', 'DocumentController::view/$1');

// Public Pengaduan
$routes->group('pengaduan', function ($routes) {
    $routes->get('/', 'PengaduanController::index');
    $routes->get('create', 'PengaduanController::create');
    $routes->post('store', 'PengaduanController::store');
    $routes->get('success/(:segment)', 'PengaduanController::success/$1');
    $routes->get('track/(:segment)', 'PengaduanController::track/$1');
});

// ============================================
// AUTHENTICATED ROUTES (All Logged In Users)
// ============================================

$routes->group('', ['filter' => 'auth'], function ($routes) {
    // Dashboard (redirects based on role)
    $routes->get('dashboard', 'DashboardController::index');

    // Common Profile Routes (All authenticated users)
    $routes->get('profile', 'ProfileController::index');
    $routes->get('profile/edit', 'ProfileController::edit');
    $routes->post('profile/update', 'ProfileController::update');
    $routes->post('profile/update-photo', 'ProfileController::updatePhoto');

    // Change Password (All authenticated users)
    $routes->get('change-password', 'ProfileController::changePassword');
    $routes->post('change-password', 'ProfileController::updatePassword');

    // Notifications
    $routes->get('notifications', 'NotificationController::index');
    $routes->post('notifications/mark-read/(:num)', 'NotificationController::markAsRead/$1');
    $routes->post('notifications/mark-all-read', 'NotificationController::markAllAsRead');
});

// ============================================
// MEMBER ROUTES (Role: Anggota)
// ============================================

$routes->group('member', ['filter' => 'auth:anggota'], function ($routes) {
    // Dashboard
    $routes->get('/', 'Member\DashboardController::index');
    $routes->get('dashboard', 'Member\DashboardController::index');

    // Profile Management
    $routes->get('profile', 'Member\ProfileController::index');
    $routes->get('profile/edit', 'Member\ProfileController::edit');
    $routes->post('profile/update', 'Member\ProfileController::update');
    $routes->post('profile/upload-photo', 'Member\ProfileController::uploadPhoto');

    // Member Card
    $routes->get('card', 'Member\MemberCardController::index');
    $routes->get('card/download', 'Member\MemberCardController::download');
    $routes->get('card/generate-qr', 'Member\MemberCardController::generateQR');

    // Payment Management
    $routes->group('payment', function ($routes) {
        $routes->get('/', 'Member\PaymentController::index');
        $routes->get('history', 'Member\PaymentController::history');
        $routes->get('create', 'Member\PaymentController::create');
        $routes->post('store', 'Member\PaymentController::store');
        $routes->get('invoice/(:num)', 'Member\PaymentController::invoice/$1');
        $routes->get('download-invoice/(:num)', 'Member\PaymentController::downloadInvoice/$1');
    });

    // Blog/Article Management
    $routes->group('posts', function ($routes) {
        $routes->get('/', 'Member\PostController::index');
        $routes->get('create', 'Member\PostController::create');
        $routes->post('store', 'Member\PostController::store');
        $routes->get('edit/(:num)', 'Member\PostController::edit/$1');
        $routes->post('update/(:num)', 'Member\PostController::update/$1');
        $routes->post('delete/(:num)', 'Member\PostController::delete/$1');
        $routes->get('preview/(:num)', 'Member\PostController::preview/$1');
    });

    // Forum Participation
    $routes->group('forum', function ($routes) {
        $routes->get('/', 'Member\ForumController::index');
        $routes->get('category/(:segment)', 'Member\ForumController::category/$1');
        $routes->get('thread/(:num)', 'Member\ForumController::thread/$1');
        $routes->get('create-thread', 'Member\ForumController::createThread');
        $routes->post('store-thread', 'Member\ForumController::storeThread');
        $routes->post('reply/(:num)', 'Member\ForumController::reply/$1');
        $routes->post('edit-reply/(:num)', 'Member\ForumController::editReply/$1');
        $routes->post('delete-reply/(:num)', 'Member\ForumController::deleteReply/$1');
        $routes->get('search', 'Member\ForumController::search');
        $routes->get('my-threads', 'Member\ForumController::myThreads');
    });

    // Survey Participation
    $routes->group('surveys', function ($routes) {
        $routes->get('/', 'Member\SurveyController::index');
        $routes->get('available', 'Member\SurveyController::available');
        $routes->get('completed', 'Member\SurveyController::completed');
        $routes->get('take/(:num)', 'Member\SurveyController::take/$1');
        $routes->post('submit/(:num)', 'Member\SurveyController::submit/$1');
        $routes->get('results/(:num)', 'Member\SurveyController::viewResults/$1');
    });

    // View Informasi
    $routes->get('informasi', 'Member\InformasiController::index');
    $routes->get('informasi/view/(:num)', 'Member\InformasiController::view/$1');

    // Documents
    $routes->get('documents', 'Member\DocumentController::index');
    $routes->get('documents/view/(:segment)', 'Member\DocumentController::view/$1');
    $routes->get('documents/download/(:segment)', 'Member\DocumentController::download/$1');

    // My Complaints
    $routes->get('complaints', 'Member\ComplaintController::index');
    $routes->get('complaints/create', 'Member\ComplaintController::create');
    $routes->post('complaints/store', 'Member\ComplaintController::store');
    $routes->get('complaints/view/(:num)', 'Member\ComplaintController::view/$1');
    $routes->get('complaints/track/(:segment)', 'Member\ComplaintController::track/$1');
});

// ============================================
// PENGURUS ROUTES (Role: Pengurus)
// ============================================

$routes->group('pengurus', ['filter' => 'auth:pengurus'], function ($routes) {
    // Dashboard
    $routes->get('/', 'Pengurus\DashboardController::index');
    $routes->get('dashboard', 'Pengurus\DashboardController::index');

    // Member Verification & Management
    $routes->group('members', function ($routes) {
        $routes->get('/', 'Pengurus\MemberController::index');
        $routes->get('pending', 'Pengurus\MemberController::pending');
        $routes->get('active', 'Pengurus\MemberController::active');
        $routes->get('suspended', 'Pengurus\MemberController::suspended');
        $routes->get('view/(:num)', 'Pengurus\MemberController::view/$1');
        $routes->post('verify/(:num)', 'Pengurus\MemberController::verify/$1');
        $routes->post('reject/(:num)', 'Pengurus\MemberController::reject/$1');
        $routes->post('suspend/(:num)', 'Pengurus\MemberController::suspend/$1');
        $routes->post('reactivate/(:num)', 'Pengurus\MemberController::reactivate/$1');
        $routes->get('export', 'Pengurus\MemberController::export');
        $routes->get('search', 'Pengurus\MemberController::search');
    });

    // Payment Verification
    $routes->group('payments', function ($routes) {
        $routes->get('/', 'Pengurus\PaymentController::index');
        $routes->get('pending', 'Pengurus\PaymentController::pending');
        $routes->get('verified', 'Pengurus\PaymentController::verified');
        $routes->post('verify/(:num)', 'Pengurus\PaymentController::verify/$1');
        $routes->post('reject/(:num)', 'Pengurus\PaymentController::reject/$1');
        $routes->get('report', 'Pengurus\PaymentController::report');
        $routes->get('export', 'Pengurus\PaymentController::export');
        $routes->get('statistics', 'Pengurus\PaymentController::statistics');
    });

    // Blog/Article Management
    $routes->group('blog', function ($routes) {
        $routes->get('/', 'Pengurus\BlogController::index');
        $routes->get('pending', 'Pengurus\BlogController::pending');
        $routes->get('published', 'Pengurus\BlogController::published');
        $routes->get('create', 'Pengurus\BlogController::create');
        $routes->post('store', 'Pengurus\BlogController::store');
        $routes->get('edit/(:num)', 'Pengurus\BlogController::edit/$1');
        $routes->post('update/(:num)', 'Pengurus\BlogController::update/$1');
        $routes->get('review/(:num)', 'Pengurus\BlogController::review/$1');
        $routes->post('approve/(:num)', 'Pengurus\BlogController::approve/$1');
        $routes->post('reject/(:num)', 'Pengurus\BlogController::reject/$1');
        $routes->post('delete/(:num)', 'Pengurus\BlogController::delete/$1');
    });

    // Informasi Serikat Management
    $routes->group('informasi', function ($routes) {
        $routes->get('/', 'Pengurus\InformasiController::index');
        $routes->get('create', 'Pengurus\InformasiController::create');
        $routes->post('store', 'Pengurus\InformasiController::store');
        $routes->get('edit/(:num)', 'Pengurus\InformasiController::edit/$1');
        $routes->post('update/(:num)', 'Pengurus\InformasiController::update/$1');
        $routes->post('delete/(:num)', 'Pengurus\InformasiController::delete/$1');
        $routes->post('publish/(:num)', 'Pengurus\InformasiController::publish/$1');
        $routes->post('unpublish/(:num)', 'Pengurus\InformasiController::unpublish/$1');
    });

    // Pengaduan Management
    $routes->group('pengaduan', function ($routes) {
        $routes->get('/', 'Pengurus\PengaduanController::index');
        $routes->get('new', 'Pengurus\PengaduanController::new');
        $routes->get('in-progress', 'Pengurus\PengaduanController::inProgress');
        $routes->get('resolved', 'Pengurus\PengaduanController::resolved');
        $routes->get('view/(:num)', 'Pengurus\PengaduanController::view/$1');
        $routes->post('assign/(:num)', 'Pengurus\PengaduanController::assign/$1');
        $routes->post('update-status/(:num)', 'Pengurus\PengaduanController::updateStatus/$1');
        $routes->post('add-note/(:num)', 'Pengurus\PengaduanController::addNote/$1');
        $routes->get('my-assigned', 'Pengurus\PengaduanController::myAssigned');
        $routes->get('statistics', 'Pengurus\PengaduanController::statistics');
    });

    // Survey Management
    $routes->group('surveys', function ($routes) {
        $routes->get('/', 'Pengurus\SurveyController::index');
        $routes->get('create', 'Pengurus\SurveyController::create');
        $routes->post('store', 'Pengurus\SurveyController::store');
        $routes->get('edit/(:num)', 'Pengurus\SurveyController::edit/$1');
        $routes->post('update/(:num)', 'Pengurus\SurveyController::update/$1');
        $routes->get('questions/(:num)', 'Pengurus\SurveyController::questions/$1');
        $routes->post('add-question/(:num)', 'Pengurus\SurveyController::addQuestion/$1');
        $routes->post('update-question/(:num)', 'Pengurus\SurveyController::updateQuestion/$1');
        $routes->post('delete-question/(:num)', 'Pengurus\SurveyController::deleteQuestion/$1');
        $routes->get('results/(:num)', 'Pengurus\SurveyController::results/$1');
        $routes->get('export/(:num)', 'Pengurus\SurveyController::export/$1');
        $routes->post('toggle-status/(:num)', 'Pengurus\SurveyController::toggleStatus/$1');
        $routes->post('delete/(:num)', 'Pengurus\SurveyController::delete/$1');
    });

    // Forum Moderation
    $routes->group('forum', function ($routes) {
        $routes->get('moderate', 'Pengurus\ForumController::moderate');
        $routes->get('reported', 'Pengurus\ForumController::reported');
        $routes->post('pin-thread/(:num)', 'Pengurus\ForumController::pinThread/$1');
        $routes->post('unpin-thread/(:num)', 'Pengurus\ForumController::unpinThread/$1');
        $routes->post('lock-thread/(:num)', 'Pengurus\ForumController::lockThread/$1');
        $routes->post('unlock-thread/(:num)', 'Pengurus\ForumController::unlockThread/$1');
        $routes->post('delete-thread/(:num)', 'Pengurus\ForumController::deleteThread/$1');
        $routes->post('delete-reply/(:num)', 'Pengurus\ForumController::deleteReply/$1');
        $routes->post('warn-user/(:num)', 'Pengurus\ForumController::warnUser/$1');
    });

    // Data Survei Upah
    $routes->group('salary-survey', function ($routes) {
        $routes->get('/', 'Pengurus\SalarySurveyController::index');
        $routes->get('statistics', 'Pengurus\SalarySurveyController::statistics');
        $routes->get('by-kampus', 'Pengurus\SalarySurveyController::byKampus');
        $routes->get('by-status', 'Pengurus\SalarySurveyController::byStatus');
        $routes->get('export', 'Pengurus\SalarySurveyController::export');
        $routes->get('report', 'Pengurus\SalarySurveyController::report');
    });

    // Reports
    $routes->group('reports', function ($routes) {
        $routes->get('members', 'Pengurus\ReportController::members');
        $routes->get('payments', 'Pengurus\ReportController::payments');
        $routes->get('activities', 'Pengurus\ReportController::activities');
        $routes->get('export/(:segment)', 'Pengurus\ReportController::export/$1');
    });
});

// Bagian yang perlu diubah di app/Config/Routes.php:

// ============================================
// SUPER ADMIN ROUTES (Role: Super Admin)
// ============================================

$routes->group('admin', ['filter' => 'auth:super_admin'], function ($routes) {
    // Dashboard - GUNAKAN CONTROLLER EXISTING
    $routes->get('/', 'DashboardController::index');
    $routes->get('dashboard', 'DashboardController::index');

    // Include all Pengurus routes (Super Admin has access to everything)
    // Member Management (Extended)
    $routes->group('members', function ($routes) {
        $routes->get('/', 'Admin\MemberController::index');
        $routes->get('all', 'Admin\MemberController::all');
        $routes->get('create', 'Admin\MemberController::create');
        $routes->post('store', 'Admin\MemberController::store');
        $routes->get('edit/(:num)', 'Admin\MemberController::edit/$1');
        $routes->post('update/(:num)', 'Admin\MemberController::update/$1');
        $routes->post('delete/(:num)', 'Admin\MemberController::delete/$1');
        $routes->post('import', 'Admin\MemberController::import');
        $routes->get('export', 'Admin\MemberController::export');
        $routes->post('bulk-action', 'Admin\MemberController::bulkAction');
        // Include all Pengurus member routes
        $routes->get('pending', 'Admin\MemberController::pending');
        $routes->get('active', 'Admin\MemberController::active');
        $routes->get('suspended', 'Admin\MemberController::suspended');
        $routes->get('view/(:num)', 'Admin\MemberController::view/$1');
        $routes->post('verify/(:num)', 'Admin\MemberController::verify/$1');
        $routes->post('reject/(:num)', 'Admin\MemberController::reject/$1');
        $routes->post('suspend/(:num)', 'Admin\MemberController::suspend/$1');
        $routes->post('reactivate/(:num)', 'Admin\MemberController::reactivate/$1');
    });

    // Role Management
    $routes->group('roles', function ($routes) {
        $routes->get('/', 'Admin\RoleController::index');
        $routes->get('create', 'Admin\RoleController::create');
        $routes->post('store', 'Admin\RoleController::store');
        $routes->get('edit/(:num)', 'Admin\RoleController::edit/$1');
        $routes->post('update/(:num)', 'Admin\RoleController::update/$1');
        $routes->post('delete/(:num)', 'Admin\RoleController::delete/$1');
        $routes->get('permissions/(:num)', 'Admin\RoleController::permissions/$1');
        $routes->post('update-permissions/(:num)', 'Admin\RoleController::updatePermissions/$1');
    });

    // Menu Management
    $routes->group('menus', function ($routes) {
        $routes->get('/', 'Admin\MenuController::index');
        $routes->get('create', 'Admin\MenuController::create');
        $routes->post('store', 'Admin\MenuController::store');
        $routes->get('edit/(:num)', 'Admin\MenuController::edit/$1');
        $routes->post('update/(:num)', 'Admin\MenuController::update/$1');
        $routes->post('delete/(:num)', 'Admin\MenuController::delete/$1');
        $routes->post('reorder', 'Admin\MenuController::reorder');
        $routes->get('submenu/(:num)', 'Admin\MenuController::submenu/$1');
        $routes->post('add-submenu/(:num)', 'Admin\MenuController::addSubmenu/$1');
    });

    // Content Management
    $routes->group('content', function ($routes) {
        $routes->get('/', 'Admin\ContentController::index');
        $routes->get('pages', 'Admin\ContentController::pages');
        $routes->get('create-page', 'Admin\ContentController::createPage');
        $routes->post('store-page', 'Admin\ContentController::storePage');
        $routes->get('edit-page/(:num)', 'Admin\ContentController::editPage/$1');
        $routes->post('update-page/(:num)', 'Admin\ContentController::updatePage/$1');
        $routes->post('delete-page/(:num)', 'Admin\ContentController::deletePage/$1');
        $routes->get('sections', 'Admin\ContentController::sections');
        $routes->post('update-section', 'Admin\ContentController::updateSection');
        $routes->post('toggle-publish/(:num)', 'Admin\ContentController::togglePublish/$1');
    });

    // System Configuration
    $routes->group('system', function ($routes) {
        $routes->get('settings', 'Admin\SystemController::settings');
        $routes->post('update-settings', 'Admin\SystemController::updateSettings');
        $routes->get('backup', 'Admin\SystemController::backup');
        $routes->post('create-backup', 'Admin\SystemController::createBackup');
        $routes->get('restore', 'Admin\SystemController::restore');
        $routes->post('restore-backup', 'Admin\SystemController::restoreBackup');
        $routes->get('logs', 'Admin\SystemController::logs');
        $routes->get('logs/view/(:segment)', 'Admin\SystemController::viewLog/$1');
        $routes->post('logs/clear', 'Admin\SystemController::clearLogs');
    });

    // Reference Data Management
    $routes->group('reference', function ($routes) {
        // Status Kepegawaian
        $routes->get('status-kepegawaian', 'Admin\ReferenceController::statusKepegawaian');
        $routes->post('status-kepegawaian/store', 'Admin\ReferenceController::storeStatusKepegawaian');
        $routes->post('status-kepegawaian/update/(:num)', 'Admin\ReferenceController::updateStatusKepegawaian/$1');
        $routes->post('status-kepegawaian/delete/(:num)', 'Admin\ReferenceController::deleteStatusKepegawaian/$1');

        // Pemberi Gaji
        $routes->get('pemberi-gaji', 'Admin\ReferenceController::pemberiGaji');
        $routes->post('pemberi-gaji/store', 'Admin\ReferenceController::storePemberiGaji');
        $routes->post('pemberi-gaji/update/(:num)', 'Admin\ReferenceController::updatePemberiGaji/$1');
        $routes->post('pemberi-gaji/delete/(:num)', 'Admin\ReferenceController::deletePemberiGaji/$1');

        // Range Gaji
        $routes->get('range-gaji', 'Admin\ReferenceController::rangeGaji');
        $routes->post('range-gaji/store', 'Admin\ReferenceController::storeRangeGaji');
        $routes->post('range-gaji/update/(:num)', 'Admin\ReferenceController::updateRangeGaji/$1');
        $routes->post('range-gaji/delete/(:num)', 'Admin\ReferenceController::deleteRangeGaji/$1');

        // Wilayah
        $routes->get('wilayah', 'Admin\ReferenceController::wilayah');
        $routes->get('provinsi', 'Admin\ReferenceController::provinsi');
        $routes->post('provinsi/store', 'Admin\ReferenceController::storeProvinsi');
        $routes->post('provinsi/update/(:num)', 'Admin\ReferenceController::updateProvinsi/$1');
        $routes->post('provinsi/delete/(:num)', 'Admin\ReferenceController::deleteProvinsi/$1');

        $routes->get('kota', 'Admin\ReferenceController::kota');
        $routes->post('kota/store', 'Admin\ReferenceController::storeKota');
        $routes->post('kota/update/(:num)', 'Admin\ReferenceController::updateKota/$1');
        $routes->post('kota/delete/(:num)', 'Admin\ReferenceController::deleteKota/$1');

        // Jenis PT
        $routes->get('jenis-pt', 'Admin\ReferenceController::jenisPT');
        $routes->post('jenis-pt/store', 'Admin\ReferenceController::storeJenisPT');
        $routes->post('jenis-pt/update/(:num)', 'Admin\ReferenceController::updateJenisPT/$1');
        $routes->post('jenis-pt/delete/(:num)', 'Admin\ReferenceController::deleteJenisPT/$1');

        // Kampus
        $routes->get('kampus', 'Admin\ReferenceController::kampus');
        $routes->post('kampus/store', 'Admin\ReferenceController::storeKampus');
        $routes->post('kampus/update/(:num)', 'Admin\ReferenceController::updateKampus/$1');
        $routes->post('kampus/delete/(:num)', 'Admin\ReferenceController::deleteKampus/$1');
        $routes->post('kampus/import', 'Admin\ReferenceController::importKampus');

        // Program Studi
        $routes->get('prodi', 'Admin\ReferenceController::prodi');
        $routes->post('prodi/store', 'Admin\ReferenceController::storeProdi');
        $routes->post('prodi/update/(:num)', 'Admin\ReferenceController::updateProdi/$1');
        $routes->post('prodi/delete/(:num)', 'Admin\ReferenceController::deleteProdi/$1');
        $routes->post('prodi/import', 'Admin\ReferenceController::importProdi');
    });

    // User Management
    $routes->group('users', function ($routes) {
        $routes->get('/', 'Admin\UserController::index');
        $routes->get('create', 'Admin\UserController::create');
        $routes->post('store', 'Admin\UserController::store');
        $routes->get('edit/(:num)', 'Admin\UserController::edit/$1');
        $routes->post('update/(:num)', 'Admin\UserController::update/$1');
        $routes->post('delete/(:num)', 'Admin\UserController::delete/$1');
        $routes->post('reset-password/(:num)', 'Admin\UserController::resetPassword/$1');
        $routes->post('toggle-status/(:num)', 'Admin\UserController::toggleStatus/$1');
        $routes->get('activity/(:num)', 'Admin\UserController::viewActivity/$1');
        $routes->get('sessions', 'Admin\UserController::sessions');
        $routes->post('terminate-session/(:num)', 'Admin\UserController::terminateSession/$1');
    });

    // Reports (Extended)
    $routes->group('reports', function ($routes) {
        $routes->get('/', 'Admin\ReportController::index');
        $routes->get('members', 'Admin\ReportController::members');
        $routes->get('payments', 'Admin\ReportController::payments');
        $routes->get('activities', 'Admin\ReportController::activities');
        $routes->get('surveys', 'Admin\ReportController::surveys');
        $routes->get('pengaduan', 'Admin\ReportController::pengaduan');
        $routes->get('financial', 'Admin\ReportController::financial');
        $routes->get('statistics', 'Admin\ReportController::statistics');
        $routes->get('export/(:segment)', 'Admin\ReportController::export/$1');
        $routes->get('generate/(:segment)', 'Admin\ReportController::generate/$1');
    });
});
