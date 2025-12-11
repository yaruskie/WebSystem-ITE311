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

        // Load teacher's courses
        $courseModel = new \App\Models\CourseModel();
        $courses = $courseModel->getCoursesByTeacher($session->get('user_id'));

        $data = [
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role' => $role,
            'courses' => $courses
        ];

        log_message('info', 'Teacher::dashboard() - Loading teacher_dashboard view with data: ' . print_r($data, true));
        return view('teacher_dashboard', $data);
    }

    public function manageStudents($course_id)
    {
        $session = session();

        // Check permissions
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'teacher') {
            return redirect()->to('login');
        }

        // Verify teacher owns this course
        $courseModel = new \App\Models\CourseModel();
        $course = $courseModel->where('id', $course_id)->where('teacher_id', $session->get('user_id'))->first();

        if (!$course) {
            $session->setFlashdata('error', 'Access Denied: You do not have permission to manage this course.');
            return redirect()->to('teacher/dashboard');
        }

        $data = [
            'user_name' => $session->get('user_name'),
            'course' => $course
        ];

        return view('teacher/manage_students', $data);
    }

    public function manageStudentsSimple($course_id)
    {
        $session = session();

        // Check permissions
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'teacher') {
            return redirect()->to('login');
        }

        // Verify teacher owns this course
        $courseModel = new \App\Models\CourseModel();
        $course = $courseModel->where('id', $course_id)->where('teacher_id', $session->get('user_id'))->first();

        if (!$course) {
            $session->setFlashdata('error', 'Access Denied: You do not have permission to manage this course.');
            return redirect()->to('teacher/dashboard');
        }

        $data = [
            'user_name' => $session->get('user_name'),
            'course' => $course
        ];

        return view('teacher/manage_students_simple', $data);
    }

    public function manageStudentsOverview()
    {
        $session = session();

        // Check permissions
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'teacher') {
            return redirect()->to('login');
        }

        // Load teacher's courses
        $courseModel = new \App\Models\CourseModel();
        $courses = $courseModel->getCoursesByTeacher($session->get('user_id'));

        $data = [
            'user_name' => $session->get('user_name'),
            'courses' => $courses
        ];

        return view('teacher/manage_students_overview', $data);
    }

    public function getStudents($course_id)
    {
        $session = session();

        // Check permissions
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'teacher') {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        // Verify teacher owns this course
        $courseModel = new \App\Models\CourseModel();
        $course = $courseModel->where('id', $course_id)->where('teacher_id', $session->get('user_id'))->first();

        if (!$course) {
            return $this->response->setJSON(['error' => 'Access Denied'])->setStatusCode(403);
        }

        // Get enrolled students
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $enrollments = $enrollmentModel->select('enrollments.*, users.name, users.email, users.status as user_status')
                                      ->join('users', 'users.id = enrollments.user_id')
                                      ->where('enrollments.course_id', $course_id)
                                      ->findAll();

        return $this->response->setJSON($enrollments);
    }

    public function updateStudentStatus()
    {
        $session = session();

        // Check permissions
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'teacher') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $data = $this->request->getJSON(true);

        if (!isset($data['user_id']) || !isset($data['course_id']) || !isset($data['status'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing required parameters']);
        }

        $user_id = $data['user_id'];
        $course_id = $data['course_id'];
        $status = $data['status'];
        $remarks = $data['remarks'] ?? '';

        // Verify teacher owns this course
        $courseModel = new \App\Models\CourseModel();
        $course = $courseModel->where('id', $course_id)->where('teacher_id', $session->get('user_id'))->first();

        if (!$course) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access Denied']);
        }

        // Update user status
        $userModel = new \App\Models\UserModel();
        $success = $userModel->setStatusSafe($user_id, $status);

        if ($success) {
            // Log the action (you might want to add a logging system)
            return $this->response->setJSON(['success' => true, 'message' => 'Student status updated successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update student status']);
        }
    }

    public function removeStudent()
    {
        $session = session();

        // Check permissions
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'teacher') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $data = $this->request->getJSON(true);

        if (!isset($data['user_id']) || !isset($data['course_id'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing required parameters']);
        }

        $user_id = $data['user_id'];
        $course_id = $data['course_id'];

        // Verify teacher owns this course
        $courseModel = new \App\Models\CourseModel();
        $course = $courseModel->where('id', $course_id)->where('teacher_id', $session->get('user_id'))->first();

        if (!$course) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access Denied']);
        }

        // Remove enrollment
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $success = $enrollmentModel->where('user_id', $user_id)->where('course_id', $course_id)->delete();

        if ($success) {
            return $this->response->setJSON(['success' => true, 'message' => 'Student removed from course successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to remove student from course']);
        }
    }

    public function getCoursesForNav()
    {
        $session = session();

        // Check permissions
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'teacher') {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        // Get teacher's courses
        $courseModel = new \App\Models\CourseModel();
        $courses = $courseModel->getCoursesByTeacher($session->get('user_id'));

        return $this->response->setJSON($courses);
    }
}
