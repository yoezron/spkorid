<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ============================================
// PUBLIC ROUTES (TANPA AUTH)
// ============================================
$routes->get('/', 'Home::index');
$routes->get('blog', 'BlogController::publicIndex');
$routes->get('blog/(:segment)', 'BlogController::publicView/$1');
$routes->get('about', 'Home::about');
$routes->get('contact', 'Home::contact');
$routes->post('contact/send', 'Home::sendContact');
$routes->get('ad-art', 'Home::adArt');
$routes->get('manifesto', 'Home::manifesto');
$routes->get('sejarah', 'Home::sejarah');

// ============================================
// AUTHENTICATION ROUTES
// ============================================
$routes->get('login', 'AuthController::login', ['as' => 'login', 'filter' => 'noauth']);
$routes->post('login', 'AuthController::attemptLogin', ['filter' => 'noauth']);
$routes->get('logout', 'AuthController::logout');

// Registration & Email Verification
$routes->get('register', 'RegistrationController::index', ['as' => 'register', 'filter' => 'noauth']);
$routes->post('register', 'RegistrationController::register', ['filter' => 'noauth']);
$routes->get('verify-email/(:segment)', 'RegistrationController::verifyEmail/$1');
$routes->get('resend-verification', 'RegistrationController::resendVerification');
$routes->post('resend-verification', 'RegistrationController::processResendVerification');

// Password Reset
$routes->get('forgot-password', 'AuthController::forgotPassword', ['filter' => 'noauth']);
$routes->post('forgot-password', 'AuthController::processForgotPassword', ['filter' => 'noauth']);
$routes->get('reset-password/(:segment)', 'AuthController::resetPassword/$1', ['filter' => 'noauth']);
$routes->post('reset-password', 'AuthController::processResetPassword', ['filter' => 'noauth']);

// ============================================
// DASHBOARD ROUTE (SEMUA ROLE)
// ============================================
$routes->get('dashboard', 'DashboardController::index', ['filter' => 'auth']);

// ============================================
// MEMBER ROUTES (ROLE: ANGGOTA)
// ============================================
$routes->group('member', ['namespace' => 'App\Controllers', 'filter' => 'auth:member'], function ($routes) {
    // Dashboard
    $routes->get('/', 'Member\DashboardController::index');
    $routes->get('dashboard', 'Member\DashboardController::index');

    // Profile Management
    $routes->get('profile', 'Member\ProfileController::index');
    $routes->get('profile/edit', 'Member\ProfileController::edit');
    $routes->post('profile/update', 'Member\ProfileController::update');
    $routes->post('profile/upload-photo', 'Member\ProfileController::uploadPhoto');

    // Kartu Anggota
    $routes->get('card', 'Member\MemberCardController::index');
    $routes->get('card/download/(:segment)', 'Member\MemberCardController::download/$1');
    $routes->get('card/print/(:segment)', 'Member\MemberCardController::print/$1');

    // Change Password
    $routes->get('change-password', 'Member\ProfileController::changePassword');
    $routes->post('change-password', 'Member\ProfileController::updatePassword');

    // AD/ART & Documents
    $routes->get('ad-art', 'Member\DocumentController::adArt');
    $routes->get('manifesto', 'Member\DocumentController::manifesto');
    $routes->get('sejarah', 'Member\DocumentController::sejarah');

    // Informasi Serikat
    $routes->get('informasi', 'Member\InformationController::index');
    $routes->get('informasi/view/(:num)', 'Member\InformationController::view/$1');

    // Forum Diskusi
    $routes->get('forum', 'Member\ForumController::index');
    $routes->get('forum/thread/(:num)', 'Member\ForumController::viewThread/$1');
    $routes->get('forum/create', 'Member\ForumController::createThread');
    $routes->post('forum/store', 'Member\ForumController::storeThread');
    $routes->post('forum/comment/(:num)', 'Member\ForumController::addComment/$1');
    $routes->post('forum/comment/delete/(:num)', 'Member\ForumController::deleteComment/$1');

    // Survei Anggota
    $routes->get('survey', 'Member\SurveyController::index');
    $routes->get('survey/(:num)', 'Member\SurveyController::view/$1');
    $routes->post('survey/submit/(:num)', 'Member\SurveyController::submit/$1');
    $routes->get('survey/history', 'Member\SurveyController::history');

    // Payment History
    $routes->get('payment', 'Member\PaymentController::index');
    $routes->get('payment/history', 'Member\PaymentController::history');
    $routes->post('payment/upload', 'Member\PaymentController::uploadProof');

    // Blog/Tulisan Anggota
    $routes->get('my-posts', 'Member\BlogController::myPosts');
    $routes->get('my-posts/view/(:num)', 'Member\BlogController::viewPost/$1');
});

// ============================================
// PENGURUS ROUTES (ROLE: PENGURUS)
// ============================================
$routes->group('pengurus', ['namespace' => 'App\Controllers\Pengurus', 'filter' => 'auth:pengurus'], function ($routes) {
    // Dashboard
    $routes->get('/', 'DashboardController::index');
    $routes->get('dashboard', 'DashboardController::index');

    // Inherit Member Routes (Profile, Kartu, dll)
    $routes->get('profile', 'ProfileController::index');
    $routes->get('profile/edit', 'ProfileController::edit');
    $routes->post('profile/update', 'ProfileController::update');
    $routes->get('card', 'MemberCardController::index');
    $routes->get('change-password', 'ProfileController::changePassword');
    $routes->post('change-password', 'ProfileController::updatePassword');

    // Tambah Informasi Serikat
    $routes->get('information', 'InformationController::index');
    $routes->get('information/create', 'InformationController::create');
    $routes->post('information/store', 'InformationController::store');
    $routes->get('information/edit/(:num)', 'InformationController::edit/$1');
    $routes->post('information/update/(:num)', 'InformationController::update/$1');
    $routes->delete('information/delete/(:num)', 'InformationController::delete/$1');

    // Kirim Tulisan Blog
    $routes->get('blog', 'BlogController::index');
    $routes->get('blog/create', 'BlogController::create');
    $routes->post('blog/store', 'BlogController::store');
    $routes->get('blog/edit/(:num)', 'BlogController::edit/$1');
    $routes->post('blog/update/(:num)', 'BlogController::update/$1');
    $routes->get('blog/preview/(:num)', 'BlogController::preview/$1');
    $routes->post('blog/publish/(:num)', 'BlogController::publish/$1');
    $routes->delete('blog/delete/(:num)', 'BlogController::delete/$1');

    // Pengaduan Masuk
    $routes->get('complaints', 'ComplaintController::index');
    $routes->get('complaints/view/(:num)', 'ComplaintController::view/$1');
    $routes->post('complaints/respond/(:num)', 'ComplaintController::respond/$1');
    $routes->post('complaints/status/(:num)', 'ComplaintController::updateStatus/$1');

    // Data Survei Upah & Survei Lainnya
    $routes->get('survey-data', 'SurveyDataController::index');
    $routes->get('survey-data/(:num)', 'SurveyDataController::view/$1');
    $routes->get('survey-data/export/(:num)', 'SurveyDataController::export/$1');
    $routes->get('survey-data/statistics/(:num)', 'SurveyDataController::statistics/$1');

    // Konfirmasi Calon Anggota
    $routes->get('members/pending', 'MemberManagementController::pending');
    $routes->get('members/pending/(:num)', 'MemberManagementController::viewPending/$1');
    $routes->post('members/approve/(:num)', 'MemberManagementController::approve/$1');
    $routes->post('members/reject/(:num)', 'MemberManagementController::reject/$1');

    // Hapus/Tangguhkan Anggota
    $routes->get('members', 'MemberManagementController::index');
    $routes->get('members/view/(:num)', 'MemberManagementController::view/$1');
    $routes->post('members/suspend/(:num)', 'MemberManagementController::suspend/$1');
    $routes->post('members/activate/(:num)', 'MemberManagementController::activate/$1');
    $routes->delete('members/delete/(:num)', 'MemberManagementController::delete/$1');

    // Buat Survei untuk Anggota
    $routes->get('survey', 'SurveyController::index');
    $routes->get('survey/create', 'SurveyController::create');
    $routes->post('survey/store', 'SurveyController::store');
    $routes->get('survey/edit/(:num)', 'SurveyController::edit/$1');
    $routes->post('survey/update/(:num)', 'SurveyController::update/$1');
    $routes->get('survey/questions/(:num)', 'SurveyController::questions/$1');
    $routes->post('survey/questions/store/(:num)', 'SurveyController::storeQuestion/$1');
    $routes->post('survey/questions/update/(:num)', 'SurveyController::updateQuestion/$1');
    $routes->delete('survey/questions/delete/(:num)', 'SurveyController::deleteQuestion/$1');
    $routes->post('survey/publish/(:num)', 'SurveyController::publish/$1');
    $routes->post('survey/close/(:num)', 'SurveyController::close/$1');
});

// ============================================
// SUPER ADMIN ROUTES
// ============================================
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'auth:super_admin'], function ($routes) {
    // Dashboard dengan Statistik
    $routes->get('/', 'DashboardController::index');
    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('dashboard/stats', 'DashboardController::statistics');
    $routes->get('dashboard/export', 'DashboardController::exportStats');

    // Role Management
    $routes->get('roles', 'RoleController::index');
    $routes->get('roles/create', 'RoleController::create');
    $routes->post('roles/store', 'RoleController::store');
    $routes->get('roles/edit/(:num)', 'RoleController::edit/$1');
    $routes->post('roles/update/(:num)', 'RoleController::update/$1');
    $routes->delete('roles/delete/(:num)', 'RoleController::delete/$1');
    $routes->get('roles/permissions/(:num)', 'RoleController::permissions/$1');
    $routes->post('roles/update-permissions/(:num)', 'RoleController::updatePermissions/$1');

    // Member List & Management
    $routes->get('members', 'MemberManagementController::index');
    $routes->get('members/create', 'MemberManagementController::create');
    $routes->post('members/store', 'MemberManagementController::store');
    $routes->get('members/view/(:num)', 'MemberManagementController::view/$1');
    $routes->get('members/edit/(:num)', 'MemberManagementController::edit/$1');
    $routes->post('members/update/(:num)', 'MemberManagementController::update/$1');
    $routes->delete('members/delete/(:num)', 'MemberManagementController::delete/$1');
    $routes->get('members/export', 'MemberManagementController::export');
    $routes->get('members/import', 'MemberManagementController::importForm');
    $routes->post('members/import', 'MemberManagementController::import');
    $routes->post('members/bulk-action', 'MemberManagementController::bulkAction');
    $routes->post('members/assign-role/(:num)', 'MemberManagementController::assignRole/$1');
    $routes->post('members/verify/(:num)', 'MemberManagementController::verify/$1');
    $routes->post('members/suspend/(:num)', 'MemberManagementController::suspend/$1');
    $routes->post('members/activate/(:num)', 'MemberManagementController::reactivate/$1');

    // Menu Management
    $routes->get('menus', 'MenuController::index');
    $routes->get('menus/create', 'MenuController::create');
    $routes->get('menus/create/(:num)', 'MenuController::create/$1');
    $routes->post('menus/store', 'MenuController::store');
    $routes->get('menus/edit/(:num)', 'MenuController::edit/$1');
    $routes->post('menus/update/(:num)', 'MenuController::update/$1');
    $routes->delete('menus/delete/(:num)', 'MenuController::delete/$1');
    $routes->post('menus/reorder', 'MenuController::reorder');
    $routes->post('menus/toggle/(:num)', 'MenuController::toggle/$1');
    $routes->post('menus/delete/(:num)', 'MenuController::delete/$1');

    // Sub Menu Management
    $routes->get('submenus', 'SubMenuController::index');
    $routes->get('submenus/create', 'SubMenuController::create');
    $routes->post('submenus/store', 'SubMenuController::store');
    $routes->get('submenus/edit/(:num)', 'SubMenuController::edit/$1');
    $routes->post('submenus/update/(:num)', 'SubMenuController::update/$1');
    $routes->delete('submenus/delete/(:num)', 'SubMenuController::delete/$1');
    $routes->post('submenus/reorder', 'SubMenuController::reorder');
    $routes->get('submenus/by-menu/(:num)', 'SubMenuController::getByMenu/$1');

    // Content Management (Landing Page)
    $routes->get('content', 'ContentController::index');
    $routes->get('content/create', 'ContentController::create');
    $routes->post('content/store', 'ContentController::store');
    $routes->get('content/edit/(:num)', 'ContentController::edit/$1');
    $routes->post('content/update/(:num)', 'ContentController::update/$1');
    $routes->delete('content/delete/(:num)', 'ContentController::delete/$1');
    $routes->post('content/publish/(:num)', 'ContentController::publish/$1');
    $routes->post('content/unpublish/(:num)', 'ContentController::unpublish/$1');

    // Master Data Tables Management
    $routes->group('master', function ($routes) {
        // Status Kepegawaian
        $routes->get('employment-status', 'MasterDataController::employmentStatus');
        $routes->post('employment-status/store', 'MasterDataController::storeEmploymentStatus');
        $routes->post('employment-status/update/(:num)', 'MasterDataController::updateEmploymentStatus/$1');
        $routes->delete('employment-status/delete/(:num)', 'MasterDataController::deleteEmploymentStatus/$1');

        // Pemberi Gaji
        $routes->get('salary-provider', 'MasterDataController::salaryProvider');
        $routes->post('salary-provider/store', 'MasterDataController::storeSalaryProvider');
        $routes->post('salary-provider/update/(:num)', 'MasterDataController::updateSalaryProvider/$1');
        $routes->delete('salary-provider/delete/(:num)', 'MasterDataController::deleteSalaryProvider/$1');

        // Range Gaji
        $routes->get('salary-range', 'MasterDataController::salaryRange');
        $routes->post('salary-range/store', 'MasterDataController::storeSalaryRange');
        $routes->post('salary-range/update/(:num)', 'MasterDataController::updateSalaryRange/$1');
        $routes->delete('salary-range/delete/(:num)', 'MasterDataController::deleteSalaryRange/$1');

        // Wilayah
        $routes->get('region', 'MasterDataController::region');
        $routes->post('region/store', 'MasterDataController::storeRegion');
        $routes->post('region/update/(:num)', 'MasterDataController::updateRegion/$1');
        $routes->delete('region/delete/(:num)', 'MasterDataController::deleteRegion/$1');

        // Jenis Perguruan Tinggi
        $routes->get('university-type', 'MasterDataController::universityType');
        $routes->post('university-type/store', 'MasterDataController::storeUniversityType');
        $routes->post('university-type/update/(:num)', 'MasterDataController::updateUniversityType/$1');
        $routes->delete('university-type/delete/(:num)', 'MasterDataController::deleteUniversityType/$1');

        // Asal Kampus
        $routes->get('university', 'MasterDataController::university');
        $routes->post('university/store', 'MasterDataController::storeUniversity');
        $routes->post('university/update/(:num)', 'MasterDataController::updateUniversity/$1');
        $routes->delete('university/delete/(:num)', 'MasterDataController::deleteUniversity/$1');

        // Program Studi
        $routes->get('study-program', 'MasterDataController::studyProgram');
        $routes->post('study-program/store', 'MasterDataController::storeStudyProgram');
        $routes->post('study-program/update/(:num)', 'MasterDataController::updateStudyProgram/$1');
        $routes->delete('study-program/delete/(:num)', 'MasterDataController::deleteStudyProgram/$1');
    });

    // Inherit All Pengurus Features
    $routes->get('information', 'InformationController::index');
    $routes->get('blog/manage', 'BlogManagementController::index');
    $routes->get('complaints', 'ComplaintController::index');
    $routes->get('survey-data', 'SurveyDataController::index');
    $routes->get('survey/manage', 'SurveyController::index');

    // Payment Management
    $routes->get('payments', 'PaymentController::index');
    $routes->get('payments/pending', 'PaymentController::pending');
    $routes->get('payments/verified', 'PaymentController::verified');
    $routes->post('payments/verify/(:num)', 'PaymentController::verify/$1');
    $routes->post('payments/reject/(:num)', 'PaymentController::reject/$1');
    $routes->get('payments/report', 'PaymentController::report');
    $routes->get('payments/export', 'PaymentController::export');

    // System Settings
    $routes->get('settings', 'SettingsController::index');
    $routes->post('settings/update', 'SettingsController::update');
    $routes->get('settings/backup', 'SettingsController::backup');
    $routes->post('settings/restore', 'SettingsController::restore');

    // Activity Log
    $routes->get('logs', 'LogController::index');
    $routes->get('logs/user/(:num)', 'LogController::userLogs/$1');
    $routes->get('logs/export', 'LogController::export');
});

// ============================================
// API ROUTES (AJAX/JSON RESPONSES)
// ============================================
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    // Dropdown Dependencies
    $routes->get('universities/by-type/(:num)', 'DropdownController::getUniversitiesByType/$1');
    $routes->get('programs/by-university/(:num)', 'DropdownController::getProgramsByUniversity/$1');
    $routes->get('regions/provinces', 'DropdownController::getProvinces');
    $routes->get('regions/cities/(:num)', 'DropdownController::getCities/$1');

    // Member Verification
    $routes->get('member/check-email', 'MemberManagementController::checkEmail');
    $routes->get('member/check-nidn', 'MemberManagementController::checkNidn');

    // Notification
    $routes->get('notifications', 'NotificationController::index');
    $routes->post('notifications/read/(:num)', 'NotificationController::markAsRead/$1');
    $routes->post('notifications/read-all', 'NotificationController::markAllAsRead');

    // File Upload Handlers
    $routes->post('upload/image', 'UploadController::image');
    $routes->post('upload/document', 'UploadController::document');
    $routes->delete('upload/delete', 'UploadController::delete');
});

// ============================================
// CATCH-ALL ROUTE FOR 404
// ============================================
// $routes->set404Override('App\Controllers\ErrorController::show404');

// ============================================
// ENVIRONMENT-SPECIFIC ROUTES
// ============================================
if (ENVIRONMENT === 'development') {
    $routes->get('test/email', 'TestController::testEmail');
    $routes->get('test/pdf', 'TestController::testPdf');
    $routes->get('migrate', 'MigrateController::index');
    $routes->get('seed', 'SeederController::index');
}
