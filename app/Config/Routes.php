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
$routes->group('member', ['namespace' => 'App\Controllers', 'filter' => 'auth:member, pengurus, super_admin'], function ($routes) {
    // Dashboard
    $routes->get('/', 'DashboardController::index');
    $routes->get('dashboard', 'DashboardController::index');

    // Profile Management - UBAH KE MemberController
    $routes->get('profile', 'MemberController::profile');
    $routes->get('profile/edit', 'MemberController::editProfile');
    $routes->post('profile/update', 'MemberController::updateProfile');
    $routes->post('profile/upload-photo', 'MemberController::uploadPhoto');

    // Kartu Anggota - UBAH KE MemberController
    $routes->get('card', 'MemberController::memberCard');
    $routes->get('card/download/(:segment)', 'MemberController::downloadCard/$1');
    $routes->get('card/print/(:segment)', 'MemberController::printCard/$1');

    // Change Password - UBAH KE MemberController
    $routes->get('change-password', 'MemberController::changePassword');
    $routes->post('change-password', 'MemberController::updatePassword');

    // AD/ART & Documents - Buat DocumentController atau tambah method di MemberController
    $routes->get('ad-art', 'MemberController::adArt');
    $routes->get('manifesto', 'MemberController::manifesto');
    $routes->get('sejarah', 'MemberController::sejarah');

    // Informasi Serikat - Buat InformationController atau tambah method
    $routes->get('informasi', 'MemberController::informasi');
    $routes->get('informasi/view/(:num)', 'MemberController::viewInformasi/$1');

    // Forum Routes (Complete)
    $routes->group('forum', function ($routes) {
        // Main forum page
        $routes->get('/', 'ForumController::index');

        // Category view
        $routes->get('category/(:segment)', 'ForumController::category/$1');

        // Thread operations
        $routes->get('thread/(:num)', 'ForumController::thread/$1');
        $routes->get('create', 'ForumController::createThread');
        $routes->post('store', 'ForumController::storeThread');
        $routes->get('edit-thread/(:num)', 'ForumController::editThread/$1');
        $routes->post('update-thread/(:num)', 'ForumController::updateThread/$1');
        $routes->get('delete-thread/(:num)', 'ForumController::deleteThread/$1');

        // Reply operations
        $routes->post('reply/(:num)', 'ForumController::storeReply/$1');
        $routes->get('edit-reply/(:num)', 'ForumController::editReply/$1');
        $routes->post('update-reply/(:num)', 'ForumController::updateReply/$1');
        $routes->get('delete-reply/(:num)', 'ForumController::deleteReply/$1');

        // Solution marking
        $routes->get('mark-solution/(:num)', 'ForumController::markSolution/$1');

        // Admin actions
        $routes->get('toggle-pin/(:num)', 'ForumController::togglePin/$1');
        $routes->get('toggle-lock/(:num)', 'ForumController::toggleLock/$1');

        // Search
        $routes->get('search', 'ForumController::search');

        // User profile
        $routes->get('user/(:num)', 'ForumController::userProfile/$1');
    });

    // Survei Anggota - Gunakan SurveyController existing atau tambah method
    $routes->get('survey', 'SurveyController::index');
    $routes->get('survey/(:num)', 'SurveyController::view/$1');
    $routes->post('survey/submit/(:num)', 'SurveyController::submit/$1');
    $routes->get('survey/history', 'SurveyController::history');

    // Payment History - Gunakan PaymentController existing atau tambah method
    $routes->get('payment', 'PaymentController::index');
    $routes->get('payment/history', 'PaymentController::history');
    $routes->post('payment/uploadProof', 'PaymentController::uploadProof');
    $routes->get('payment/invoice/(:num)', 'PaymentController::invoice/$1');
    $routes->get('payment/download-invoice/(:num)', 'PaymentController::downloadInvoice/$1');

    // Blog/Tulisan Anggota - Gunakan BlogController existing
    $routes->get('my-posts', 'BlogController::myPosts');
    $routes->get('my-posts/view/(:num)', 'BlogController::viewPost/$1');
});

// ============================================
// PENGURUS ROUTES (ROLE: PENGURUS)
// ============================================
$routes->group('pengurus', ['filter' => 'auth:pengurus, super_admin'], function ($routes) {
    // Dashboard
    $routes->get('/', 'DashboardController::index');
    $routes->get('dashboard', 'DashboardController::index');

    // Inherit Member Routes (Profile, Kartu, dll)
    $routes->get('profile', 'MemberController::profile');
    $routes->get('profile/edit', 'MemberController::edit');
    $routes->post('profile/update', 'MemberController::update');
    $routes->get('card', 'MemberCardController::index');
    $routes->get('change-password', 'MemberController::changePassword');
    $routes->post('change-password', 'MemberController::updatePassword');

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

    // Profile Management untuk Admin
    $routes->get('profile', 'ProfileController::index');
    $routes->get('profile/edit', 'ProfileController::edit');
    $routes->post('profile/update', 'ProfileController::update');
    $routes->get('change-password', 'ProfileController::changePassword');
    $routes->post('change-password', 'ProfileController::updatePassword');

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
    $routes->get('content', 'CMSController::index');
    $routes->get('content/create', 'CMSController::create');
    $routes->post('content/store', 'CMSController::store');
    $routes->get('content/edit/(:num)', 'CMSController::edit/$1');
    $routes->post('content/update/(:num)', 'CMSController::update/$1');
    $routes->delete('content/delete/(:num)', 'CMSController::delete/$1');
    $routes->post('content/publish/(:num)', 'CMSController::publish/$1');
    $routes->post('content/unpublish/(:num)', 'CMSController::unpublish/$1');

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
    $routes->get('payments', 'PaymentManagementController::index');
    $routes->get('payments/pending', 'PaymentManagementController::pending');
    $routes->get('payments/verified', 'PaymentManagementController::verified');
    $routes->post('payments/verify/(:num)', 'PaymentManagementController::verify/$1');
    $routes->post('payments/reject/(:num)', 'PaymentManagementController::reject/$1');
    $routes->get('payments/report', 'PaymentManagementController::report');
    $routes->get('payments/export', 'PaymentManagementController::export');

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

// ============================================
// MEMBER ROUTES - Survei
// ============================================
$routes->group('member', ['namespace' => 'App\Controllers\Member', 'filter' => 'auth:member, super_admin'], function ($routes) {
    // Existing member routes...

    // Survey routes
    $routes->get('surveys', 'SurveyController::index');
    $routes->get('surveys/take/(:num)', 'SurveyController::take/$1');
    $routes->post('surveys/submit/(:num)', 'SurveyController::submit/$1');
    $routes->post('surveys/auto-save/(:num)', 'SurveyController::autoSave/$1');
    $routes->get('surveys/result/(:num)', 'SurveyController::result/$1');
    $routes->get('surveys/my-response/(:num)', 'SurveyController::myResponse/$1');
    $routes->get('surveys/download-file/(:num)', 'SurveyController::downloadFile/$1');
});

// ============================================
// PENGURUS ROUTES - Survei
// ============================================
$routes->group('pengurus', ['namespace' => 'App\Controllers\Pengurus', 'filter' => 'auth:pengurus,super_admin'], function ($routes) {
    // Existing pengurus routes...

    // Survey Management
    $routes->get('surveys', 'SurveyController::index');
    $routes->get('surveys/create', 'SurveyController::create');
    $routes->post('surveys/store', 'SurveyController::store');
    $routes->get('surveys/edit/(:num)', 'SurveyController::edit/$1');
    $routes->post('surveys/update/(:num)', 'SurveyController::update/$1');
    $routes->get('surveys/results/(:num)', 'SurveyController::results/$1');
    $routes->get('surveys/export/(:num)', 'SurveyController::export/$1');
    $routes->post('surveys/toggle-status/(:num)', 'SurveyController::toggleStatus/$1');
    $routes->delete('surveys/delete/(:num)', 'SurveyController::delete/$1');

    // Data Survei (menu khusus pengurus)
    $routes->get('survey-data', 'SurveyController::surveyData');
});

// ============================================
// ADMIN ROUTES - Survei
// ============================================
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'auth:super_admin'], function ($routes) {
    // Existing admin routes...

    // Survey Management (Full Access)
    $routes->get('surveys', 'SurveyManagementController::index');
    $routes->get('surveys/create', 'SurveyManagementController::create');
    $routes->post('surveys/store', 'SurveyManagementController::store');
    $routes->get('surveys/edit/(:num)', 'SurveyManagementController::edit/$1');
    $routes->post('surveys/update/(:num)', 'SurveyManagementController::update/$1');
    $routes->get('surveys/results/(:num)', 'SurveyManagementController::results/$1');
    $routes->get('surveys/view-response/(:num)/(:num)', 'SurveyManagementController::viewResponse/$1/$2');
    $routes->get('surveys/export/(:num)', 'SurveyManagementController::export/$1');
    $routes->post('surveys/toggle-status/(:num)', 'SurveyManagementController::toggleStatus/$1');
    $routes->delete('surveys/delete/(:num)', 'SurveyManagementController::delete/$1');
    $routes->get('surveys/clone/(:num)', 'SurveyManagementController::clone/$1');
});

// ============================================
// API ROUTES untuk AJAX calls (jika diperlukan)
// ============================================
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    // Survey API endpoints
    $routes->get('surveys/active', 'SurveyApiController::getActiveSurveys');
    $routes->get('surveys/(:num)/questions', 'SurveyApiController::getQuestions/$1');
    $routes->get('surveys/(:num)/statistics', 'SurveyApiController::getStatistics/$1');
    $routes->post('surveys/(:num)/validate', 'SurveyApiController::validateAnswers/$1');
});
