<?php

namespace App\Controllers;

use App\Models\CourseModel;
use CodeIgniter\Controller;

class Admin extends Controller
{
    public function dashboard()
    {
        $session = session();

        // Debug logging
        log_message('info', 'Admin::dashboard() called');
        log_message('info', 'Session data: ' . print_r($session->get(), true));

        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            log_message('info', 'Admin::dashboard() - User not logged in, redirecting to login');
            $session->setFlashdata('login_error', 'Please login to access the dashboard.');
            return redirect()->to('login');
        }

        // Check if user has admin role
        $role = strtolower((string) $session->get('role'));
        log_message('info', "Admin::dashboard() - User role: {$role}");

        if ($role !== 'admin') {
            log_message('info', 'Admin::dashboard() - User role is not admin, redirecting to announcements');
            $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
            return redirect()->to('/announcements');
        }
        
        // Fetch all courses for admin
        $courseModel = new CourseModel();
        $courses = $courseModel->orderBy('title', 'ASC')->findAll();
        
        $data = [
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role' => $role,
            'courses' => $courses
        ];
        
        log_message('info', 'Admin::dashboard() - Loading admin_dashboard view with data: ' . print_r($data, true));
        return view('admin_dashboard', $data);
    }

    public function courses()
    {
        $session = session();

        // Check if user is logged in and is admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
            return redirect()->to('/dashboard');
        }

        // Fetch all courses
        $courseModel = new CourseModel();
        $courses = $courseModel->orderBy('title', 'ASC')->findAll();

        $data = [
            'user_name' => $session->get('user_name'),
            'courses' => $courses
        ];

        return view('admin/courses', $data);
    }
}
