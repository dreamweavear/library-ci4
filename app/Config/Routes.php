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

// Admin app
$routes->group('admin', ['filter' => 'adminauth'], static function ($routes) {
    $routes->get('', 'Admin\Dashboard::index');
    $routes->get('/', 'Admin\Dashboard::index');
    $routes->get('dashboard', 'Admin\Dashboard::index');

    $routes->get('seats', 'Admin\Seats::index');
    $routes->post('seats/(:num)/floor', 'Admin\Seats::updateFloor/$1');

    $routes->get('students', 'Admin\Students::index');
    $routes->get('students/new', 'Admin\Students::new');
    $routes->post('students', 'Admin\Students::create');
    $routes->get('students/(:num)', 'Admin\Students::show/$1');
    $routes->get('students/(:num)/edit', 'Admin\Students::edit/$1');
    $routes->post('students/(:num)/update', 'Admin\Students::update/$1');

    $routes->get('enrollments', 'Admin\Enrollments::index');
    $routes->get('enrollments/new', 'Admin\Enrollments::new');
    $routes->post('enrollments', 'Admin\Enrollments::create');
    $routes->post('enrollments/(:num)/end', 'Admin\Enrollments::end/$1');

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
    $routes->get('fees/receipt/(:num)', 'Admin\Fees::receipt/$1');

    // Existing pages route (kept).
    $routes->get('pages', 'Admin\Pages::index');
    $routes->resource('pages', ['controller' => 'Admin\Pages']);
});
