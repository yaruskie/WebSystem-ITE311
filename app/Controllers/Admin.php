<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\UserModel;
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

        // Fetch all courses with teacher information
        $courseModel = new CourseModel();
        $courses = $courseModel->getAllCoursesWithTeachers();

        // Get summary data
        $totalCourses = count($courses);
        $activeCourses = count(array_filter($courses, function($course) {
            return ($course['status'] ?? 'Active') === 'Active';
        }));

        // Get teachers for dropdown
        $userModel = new UserModel();
        $teachers = $userModel->where('role', 'teacher')->findAll();

        $data = [
            'user_name' => $session->get('user_name'),
            'courses' => $courses,
            'totalCourses' => $totalCourses,
            'activeCourses' => $activeCourses,
            'teachers' => $teachers
        ];

        return view('admin/courses', $data);
    }

    public function updateCourse()
    {
        $session = session();

        // Check if user is logged in and is admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access Denied: Insufficient Permissions'
            ]);
        }

        // Validate input
        $rules = [
            'course_id' => 'required|numeric',
            'course_code' => 'permit_empty|max_length[50]',
            'title' => 'required|min_length[2]|max_length[255]',
            'school_year' => 'required|max_length[20]',
            'semester' => 'required|in_list[1st Semester,2nd Semester,Summer]',
            'description' => 'permit_empty|max_length[1000]',
            'schedule' => 'permit_empty|max_length[100]',
            'status' => 'required|in_list[Active,Inactive]',
            'teacher_id' => 'required|numeric',
            'start_date' => 'permit_empty|valid_date[Y-m-d]',
            'end_date' => 'permit_empty|valid_date[Y-m-d]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $this->validator->getErrors())
            ]);
        }

        $courseId = $this->request->getPost('course_id');
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');

        // Validate date logic
        if (!empty($startDate) && !empty($endDate)) {
            if (strtotime($startDate) >= strtotime($endDate)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Start date must be before end date'
                ]);
            }
        }

        $courseModel = new CourseModel();
        $data = [
            'course_code' => $this->request->getPost('course_code'),
            'title' => $this->request->getPost('title'),
            'school_year' => $this->request->getPost('school_year'),
            'semester' => $this->request->getPost('semester'),
            'description' => $this->request->getPost('description'),
            'schedule' => $this->request->getPost('schedule'),
            'status' => $this->request->getPost('status'),
            'teacher_id' => $this->request->getPost('teacher_id'),
            'start_date' => $startDate ?: null,
            'end_date' => $endDate ?: null,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Log the update attempt
        log_message('info', 'Admin::updateCourse - Attempting to update course ID: ' . $courseId . ' with data: ' . print_r($data, true));

        try {
            if ($courseModel->update($courseId, $data)) {
                log_message('info', 'Admin::updateCourse - Course updated successfully: ' . $courseId);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Course updated successfully'
                ]);
            } else {
                log_message('error', 'Admin::updateCourse - CourseModel update returned false for course ID: ' . $courseId);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update course - database update failed'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Admin::updateCourse - Exception occurred: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update course - ' . $e->getMessage()
            ]);
        }
    }

    public function updateCourseStatus()
    {
        $session = session();

        // Check if user is logged in and is admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access Denied: Insufficient Permissions'
            ]);
        }

        $courseId = $this->request->getPost('course_id');
        $status = $this->request->getPost('status');

        if (!$courseId || !$status) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Missing required parameters'
            ]);
        }

        $courseModel = new CourseModel();

        if ($courseModel->update($courseId, ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Course status updated successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update course status'
            ]);
        }
    }

    public function users()
    {
        $session = session();

        // Check if user is logged in and is admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
            return redirect()->to('/dashboard');
        }

        // Fetch all users
        $userModel = new UserModel();
        $users = $userModel->orderBy('id', 'ASC')->findAll();

        $data = [
            'user_name' => $session->get('user_name'),
            'users' => $users
        ];

        return view('admin/users', $data);
    }

    public function updateRole()
    {
        $session = session();

        // Check if user is logged in and is admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access Denied: Insufficient Permissions'
            ]);
        }

        $userId = $this->request->getPost('user_id');
        $role = $this->request->getPost('role');

        if (!$userId || !$role) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Missing required parameters'
            ]);
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found'
            ]);
        }

        // Prevent changing protected admin role
        if (strtolower($user['email']) === strtolower(PROTECTED_ADMIN_EMAIL)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cannot modify protected admin account'
            ]);
        }

        if ($userModel->update($userId, ['role' => $role])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'User role updated successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update user role'
            ]);
        }
    }

    public function toggleStatus()
    {
        $session = session();

        // Check if user is logged in and is admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access Denied: Insufficient Permissions'
            ]);
        }

        $userId = $this->request->getPost('user_id');
        $status = $this->request->getPost('status');

        if (!$userId || !$status) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Missing required parameters'
            ]);
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found'
            ]);
        }

        // Prevent modifying protected admin
        if (strtolower($user['email']) === strtolower(PROTECTED_ADMIN_EMAIL)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cannot modify protected admin account'
            ]);
        }

        if ($userModel->update($userId, ['status' => $status])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'User status updated successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update user status'
            ]);
        }
    }

    public function addUser()
    {
        $session = session();

        // Check if user is logged in and is admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access Denied: Insufficient Permissions'
            ]);
        }

        // Validate input
        $rules = [
            'name' => 'required|min_length[2]|max_length[255]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'role' => 'required|in_list[student,teacher,admin]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $this->validator->getErrors())
            ]);
        }

        $userModel = new UserModel();
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role' => $this->request->getPost('role'),
            'status' => 'active'
        ];

        if ($userModel->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'User added successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to add user'
            ]);
        }
    }

    public function editUser()
    {
        $session = session();

        // Check if user is logged in and is admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access Denied: Insufficient Permissions'
            ]);
        }

        $userId = $this->request->getPost('user_id');
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        if (!$userId || !$name || !$email) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Missing required parameters'
            ]);
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found'
            ]);
        }

        // Prevent modifying protected admin
        if (strtolower($user['email']) === strtolower(PROTECTED_ADMIN_EMAIL)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cannot modify protected admin account'
            ]);
        }

        // Check if email is being changed and if it's unique
        if ($email !== $user['email']) {
            $existingUser = $userModel->where('email', $email)->first();
            if ($existingUser) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Email already exists'
                ]);
            }
        }

        $data = [
            'name' => $name,
            'email' => $email
        ];

        // Only update password if provided
        if (!empty($password)) {
            if (strlen($password) < 8) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Password must be at least 8 characters'
                ]);
            }
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($userModel->update($userId, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'User updated successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update user'
            ]);
        }
    }

    public function deleteUser()
    {
        $session = session();

        // Check if user is logged in and is admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access Denied: Insufficient Permissions'
            ]);
        }

        $userId = $this->request->getPost('user_id');

        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Missing user ID'
            ]);
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found'
            ]);
        }

        // Prevent deleting protected admin
        if (strtolower($user['email']) === strtolower(PROTECTED_ADMIN_EMAIL)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cannot delete protected admin account'
            ]);
        }

        // Soft delete by setting status to 'deleted'
        if ($userModel->update($userId, ['status' => 'deleted'])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete user'
            ]);
        }
    }
}
