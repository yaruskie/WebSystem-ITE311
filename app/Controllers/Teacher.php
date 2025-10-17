<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Teacher extends Controller
{
    public function dashboard()
    {
        $session = session();
        
        // Debug logging
        log_message('info', 'Teacher::dashboard() called');
        log_message('info', 'Session data: ' . print_r($session->get(), true));
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            log_message('info', 'Teacher::dashboard() - User not logged in, redirecting to login');
            $session->setFlashdata('login_error', 'Please login to access the dashboard.');
            return redirect()->to('login');
        }
        
        // Check if user has teacher role
        $role = $session->get('role');
        log_message('info', "Teacher::dashboard() - User role: {$role}");
        
        if ($role !== 'teacher') {
            log_message('info', 'Teacher::dashboard() - User role is not teacher, redirecting to announcements');
            $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
            return redirect()->to('/announcements');
        }
        
        $data = [
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role' => $role
        ];
        
        log_message('info', 'Teacher::dashboard() - Loading teacher_dashboard view with data: ' . print_r($data, true));
        return view('teacher_dashboard', $data);
    }
}
