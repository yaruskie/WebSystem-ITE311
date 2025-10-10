<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use CodeIgniter\Controller;

class Course extends Controller
{
    /**
     * Display course details
     *
     * @param int $course_id Course ID
     * @return mixed
     */
    public function view($course_id = null)
    {
        $session = session();

        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $courseModel = new CourseModel();

        if ($course_id) {
            $course = $courseModel->find($course_id);
            if (!$course) {
                return redirect()->to('dashboard')->with('error', 'Course not found');
            }
            $data['course'] = $course;
        } else {
            $data['courses'] = $courseModel->getAllCoursesWithTeachers();
        }

        return view('courses/view', $data);
    }

    /**
     * Handle AJAX enrollment request
     *
     * @return mixed JSON response
     */
    public function enroll()
    {
        $session = session();

        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to enroll in courses'
            ]);
        }

        // Only accept POST requests
        if (!$this->request->isAJAX() || !$this->request->getMethod() === 'post') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        $course_id = $this->request->getPost('course_id');

        // Validate course_id
        if (!$course_id || !is_numeric($course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid course ID'
            ]);
        }

        $user_id = $session->get('user_id');
        $enrollmentModel = new EnrollmentModel();
        $courseModel = new CourseModel();

        // Check if course exists
        $course = $courseModel->find($course_id);
        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found'
            ]);
        }

        // Check if user is already enrolled
        if ($enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You are already enrolled in this course'
            ]);
        }

        // Attempt to enroll the user
        $enrollmentData = [
            'user_id' => $user_id,
            'course_id' => $course_id
        ];

        if ($enrollmentModel->enrollUser($enrollmentData)) {
            // Get updated enrolled courses for the user
            $enrolledCourses = $enrollmentModel->getUserEnrollments($user_id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Successfully enrolled in ' . $course['title'],
                'enrolled_courses' => $enrolledCourses,
                'enrolled_course' => $course
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to enroll in course. Please try again.'
            ]);
        }
    }

    /**
     * Get user's enrolled courses
     *
     * @return mixed JSON response
     */
    public function getEnrolledCourses()
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to view enrolled courses'
            ]);
        }

        $user_id = $session->get('user_id');
        $enrollmentModel = new EnrollmentModel();

        $enrolledCourses = $enrollmentModel->getUserEnrollments($user_id);

        return $this->response->setJSON([
            'success' => true,
            'courses' => $enrolledCourses
        ]);
    }

    /**
     * Get available courses for enrollment
     *
     * @return mixed JSON response
     */
    public function getAvailableCourses()
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to view available courses'
            ]);
        }

        $user_id = $session->get('user_id');
        $enrollmentModel = new EnrollmentModel();

        $availableCourses = $enrollmentModel->getAvailableCourses($user_id);

        return $this->response->setJSON([
            'success' => true,
            'courses' => $availableCourses
        ]);
    }
}
