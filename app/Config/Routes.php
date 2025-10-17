<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default route
$routes->get('/', 'Home::index');

// Custom routes
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');

// Auth & Dashboard
// Authentication routes (per instructions)
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::register');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');
$routes->get('/dashboard', 'Auth::dashboard', ['filter' => 'roleauth']);

// Course enrollment route
$routes->post('/course/enroll', 'Course::enroll');

// Announcements route
$routes->get('/announcements', 'Announcement::index', ['filter' => 'roleauth']);

// Role-based dashboard routes with authorization
$routes->group('teacher', ['filter' => 'roleauth'], function($routes) {
    $routes->get('dashboard', 'Teacher::dashboard');
});

$routes->group('admin', ['filter' => 'roleauth'], function($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
});

$routes->group('student', ['filter' => 'roleauth'], function($routes) {
    $routes->get('dashboard', 'Auth::dashboard');
});