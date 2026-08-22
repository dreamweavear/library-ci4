<?php

use CodeIgniter\Router\RouteCollection;
use Config\Services;

/**
 * @var RouteCollection $routes
 */
$routes = Services::routes();

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();

// Auto Routing disabled.
$routes->setAutoRoute(false);

// Public site
$routes->get('/', 'Home::index');
$routes->get('about', 'Home::about');

// Student
$routes->match(['GET', 'POST'], 'student/login', 'Student\Auth::login');
$routes->get('student/logout', 'Student\Auth::logout');
$routes->group('student', ['filter' => 'studentauth'], static function ($routes) {
    $routes->get('', 'Student\Dashboard::index');
    $routes->get('/', 'Student\Dashboard::index');
});

// Admin auth
$routes->match(['GET', 'POST'], 'admin/login', 'Admin\Auth::login');
$routes->get('admin/logout', 'Admin\Auth::logout');
$routes->match(['GET', 'POST'], 'admin/change-password', 'Admin\Auth::changePassword', ['filter' => 'adminauth']);

// Admin app
$routes->group('admin', ['filter' => 'adminauth'], static function ($routes) {
    $routes->get('', 'Admin\Dashboard::index');
    $routes->get('/', 'Admin\Dashboard::index');
    $routes->get('dashboard', 'Admin\Dashboard::index');

    $routes->get('seats', 'Admin\Seats::index');
    $routes->post('seats/(:num)/floor', 'Admin\Seats::updateFloor/$1');

    $routes->get('students', 'Admin\Students::index');
    $routes->get('students/export-csv', 'Admin\Students::exportCsv');
    $routes->get('students/new', 'Admin\Students::new');
    $routes->post('students', 'Admin\Students::create');
    $routes->get('students/from-alumni/(:num)', 'Admin\Students::fromAlumni/$1');
    $routes->get('students/(:num)', 'Admin\Students::show/$1');
    $routes->get('students/(:num)/edit', 'Admin\Students::edit/$1');
    $routes->post('students/(:num)/update', 'Admin\Students::update/$1');
    $routes->post('students/(:num)/status', 'Admin\Students::changeStatus/$1');

    // Alumni
    $routes->get('alumni', 'Admin\LibraryAlumni::index');
    $routes->get('alumni/new', 'Admin\LibraryAlumni::new');
    $routes->post('alumni/create', 'Admin\LibraryAlumni::create');
    $routes->match(['GET', 'POST'], 'alumni/import', 'Admin\LibraryAlumni::importCsv');
    $routes->post('alumni/import/store', 'Admin\LibraryAlumni::importStore');
    $routes->get('alumni/export', 'Admin\LibraryAlumni::exportCsv');
    $routes->post('alumni/(:num)/readmit', 'Admin\LibraryAlumni::readmit/$1');
    $routes->get('alumni/(:num)/edit', 'Admin\LibraryAlumni::edit/$1');
    $routes->post('alumni/(:num)/update', 'Admin\LibraryAlumni::update/$1');

    $routes->get('enrollments', 'Admin\Enrollments::index');
    $routes->get('enrollments/export-csv', 'Admin\Enrollments::exportCsv');
    $routes->get('enrollments/new', 'Admin\Enrollments::new');
    $routes->post('enrollments', 'Admin\Enrollments::create');
    $routes->post('enrollments/(:num)/end', 'Admin\Enrollments::end/$1');
    $routes->get('enrollments/change-seat/(:num)', 'Admin\Enrollments::changeSeatForm/$1');
    $routes->post('enrollments/change-seat/(:num)', 'Admin\Enrollments::changeSeat/$1');

    $routes->get('student-accounts', 'Admin\StudentAccounts::index');
    $routes->match(['GET', 'POST'], 'student-accounts/new', 'Admin\StudentAccounts::new');
    $routes->match(['GET', 'POST'], 'student-accounts/(:num)/reset', 'Admin\StudentAccounts::reset/$1');

    $routes->get('users', 'Admin\Users::index');
    $routes->match(['GET', 'POST'], 'users/new', 'Admin\Users::new');
    $routes->match(['GET', 'POST'], 'users/(:num)/edit', 'Admin\Users::edit/$1');
    $routes->match(['GET', 'POST'], 'users/(:num)/reset', 'Admin\Users::reset/$1');

    $routes->get('fees', 'Admin\Fees::index');
    $routes->match(['GET', 'POST'], 'fees/collect', 'Admin\Fees::collect');
    $routes->get('fees/pending', 'Admin\Fees::pending');
    $routes->get('fees/pending/export-csv', 'Admin\Fees::exportPendingCsv');
    $routes->get('fees/export-csv', 'Admin\Fees::exportCsv');
    $routes->get('fees/receipt/(:num)', 'Admin\Fees::receipt/$1');

    // ID Cards
    $routes->get('idcard/print/(:num)', 'Admin\IdCard::print/$1');
    $routes->post('idcard/bulk', 'Admin\IdCard::bulk');

    // Existing pages route (kept).
    $routes->get('pages', 'Admin\Pages::index');
    $routes->resource('pages', ['controller' => 'Admin\Pages']);
       // WhatsApp Test  ← YAHAN ADD KAREIN
    //$routes->get('test-wa', 'Admin\Enrollments::testWa');

    // Birthday Reminder
    $routes->get('birthday-reminder', 'Admin\BirthdayReminder::index');
    $routes->post('birthday-reminder/send/(:num)', 'Admin\BirthdayReminder::send/$1');
    $routes->post('birthday-reminder/send-all', 'Admin\BirthdayReminder::sendAll');

    // Bulk WhatsApp
    $routes->get('bulk-whatsapp', 'Admin\BulkWhatsApp::index');
    $routes->post('bulk-whatsapp/send', 'Admin\BulkWhatsApp::send');

    // Message Logs
    $routes->get('message-logs', 'Admin\MessageLogs::index');
});
