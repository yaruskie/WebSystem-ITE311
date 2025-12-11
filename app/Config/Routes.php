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
$routes->get('/dashboard', 'Auth::dashboard');

// Course routes
$routes->get('/courses', 'Course::index');
$routes->get('/courses/search', 'Course::search');
$routes->post('/courses/search', 'Course::search');
$routes->get('/courses/view/(:num)', 'Course::view/$1');
$routes->post('/course/enroll', 'Course::enroll');

// Materials routes
$routes->get('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->post('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->get('/materials/download/(:num)', 'Materials::download/$1');
$routes->get('/materials/delete/(:num)', 'Materials::delete/$1');

// Announcements route
$routes->get('/announcements', 'Announcement::index', ['filter' => 'roleauth']);

// Role-based dashboard routes with authorization
$routes->group('teacher', ['filter' => 'roleauth'], function($routes) {
    $routes->get('dashboard', 'Teacher::dashboard');
    $routes->get('manage-students', 'Teacher::manageStudentsOverview');
    $routes->get('manage-students/(:num)', 'Teacher::manageStudents/$1');
    $routes->get('manage-students-simple/(:num)', 'Teacher::manageStudentsSimple/$1');
    $routes->get('get-students/(:num)', 'Teacher::getStudents/$1');
    $routes->get('get-courses-nav', 'Teacher::getCoursesForNav');
    $routes->post('update-student-status', 'Teacher::updateStudentStatus');
    $routes->post('remove-student', 'Teacher::removeStudent');
});

$routes->group('admin', ['filter' => 'roleauth'], function($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
    $routes->get('courses', 'Admin::courses');
    $routes->post('updateCourse', 'Admin::updateCourse');
    $routes->post('updateCourseStatus', 'Admin::updateCourseStatus');
    // Manage users
    $routes->get('users', 'UserManagement::index');
    $routes->post('users/update_role', 'UserManagement::updateRole');
    $routes->post('users/add', 'UserManagement::addUser');
    $routes->post('users/toggle_status', 'UserManagement::toggleStatus');
    $routes->post('users/edit', 'UserManagement::editUser');
    $routes->post('users/delete', 'UserManagement::deleteUser');
});

$routes->group('student', ['filter' => 'roleauth'], function($routes) {
    $routes->get('dashboard', 'Auth::dashboard');
});

// Notification routes
$routes->get('/notifications', 'Notifications::get');
$routes->post('/notifications/mark_read/(:num)', 'Notifications::mark_as_read/$1');
