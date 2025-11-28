<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\MaterialModel;
use App\Models\NotificationModel;
use CodeIgniter\Controller;
use CodeIgniter\Throttle\ThrottlerInterface;

class Course extends Controller
{
    protected $courseModel;
    protected $enrollmentModel;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
    }

    /**
     * Display all courses listing page
     *
     * @return mixed
     */
    public function index()
    {
        $session = session();

        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $user_id = $session->get('user_id');
        
        // Get all courses with teacher information
        $courses = $this->courseModel->getAllCoursesWithTeachers();

        // Get enrolled course IDs for the current user
        $enrolledCourseIds = $this->enrollmentModel
            ->select('course_id')
            ->where('user_id', $user_id)
            ->findAll();
        $enrolledIds = array_column($enrolledCourseIds, 'course_id');

        // Mark which courses are enrolled
        foreach ($courses as &$course) {
            $course['is_enrolled'] = in_array($course['id'], $enrolledIds);
        }

        $data = [
            'courses' => $courses,
            'user_id' => $user_id,
            'user_name' => $session->get('user_name'),
            'role' => $session->get('role')
        ];

        return view('courses/index', $data);
    }

    /**
     * Search courses - handles both AJAX and regular requests
     * Implements security measures: input sanitization, SQL injection prevention, rate limiting
     *
     * @return mixed JSON response for AJAX, view for regular requests
     */
    public function search()
    {
        $session = session();

        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Please login to search courses'
                ])->setStatusCode(401);
            }
            return redirect()->to('/login');
        }

        // Rate limiting: Allow 30 searches per minute per IP address
        $throttler = \Config\Services::throttler();
        $ipAddress = $this->request->getIPAddress();
        $user_id = $session->get('user_id');
        
        // Sanitize IP address for cache key (replace reserved characters)
        // Cache keys cannot contain: {}()/\@:
        $sanitizedIp = str_replace([':', '/', '\\', '{', '}', '(', ')', '@'], '_', $ipAddress);
        
        // Use both IP and user ID for rate limiting key
        $rateLimitKey = 'search_' . $sanitizedIp . '_' . $user_id;
        
        if (!$throttler->check($rateLimitKey, 30, MINUTE)) {
            $tokenTime = $throttler->getTokenTime();
            $message = 'Too many search requests. Please wait ' . $tokenTime . ' seconds before trying again.';
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $message,
                    'retry_after' => $tokenTime
                ])->setStatusCode(429);
            }
            
            return redirect()->back()->with('error', $message);
        }

        // Get search term from GET or POST request
        $searchTerm = $this->request->getGet('search_term') ?? $this->request->getPost('search_term');

        // Security: Sanitize and validate input
        $originalSearchTerm = $searchTerm;
        if ($searchTerm !== null) {
            // Remove potentially dangerous characters
            $searchTerm = trim($searchTerm);
            $searchTerm = strip_tags($searchTerm);
            
            // Remove null bytes and other control characters
            $searchTerm = str_replace(["\0", "\r", "\n", "\t"], '', $searchTerm);
            
            // Limit search term length to prevent abuse
            if (strlen($searchTerm) > 255) {
                $searchTerm = substr($searchTerm, 0, 255);
            }
            
            // Additional validation: reject if only special characters
            if (preg_match('/^[^\w\s]+$/', $searchTerm)) {
                $searchTerm = '';
            }
        }

        // Create a fresh model instance to avoid query builder state issues
        $courseModel = new CourseModel();

        // Build query with search conditions
        // CodeIgniter's like() method automatically handles SQL injection prevention through parameter binding
        try {
            // Start building the query with join
            $courseModel->select('courses.*, users.name as teacher_name')
                       ->join('users', 'users.id = courses.teacher_id');

            // Add search conditions if search term is provided
            if (!empty($searchTerm)) {
                $courseModel->groupStart()
                    ->like('courses.title', $searchTerm, 'both')
                    ->orLike('courses.description', $searchTerm, 'both')
                    ->groupEnd();
            }

            // Get courses with teacher information
            $courses = $courseModel->findAll();
        } catch (\Exception $e) {
            // Log error for debugging (in production, don't expose error details)
            log_message('error', 'Search error: ' . $e->getMessage());
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'An error occurred while searching. Please try again later.'
                ])->setStatusCode(500);
            }
            
            return redirect()->back()->with('error', 'An error occurred while searching. Please try again.');
        }

        // Get enrolled course IDs
        $enrolledCourseIds = $this->enrollmentModel
            ->select('course_id')
            ->where('user_id', $user_id)
            ->findAll();
        $enrolledIds = array_column($enrolledCourseIds, 'course_id');

        // Mark which courses are enrolled
        foreach ($courses as &$course) {
            $course['is_enrolled'] = in_array($course['id'], $enrolledIds);
        }

        // Handle AJAX requests
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'courses' => $courses,
                'search_term' => $originalSearchTerm ?? '',
                'count' => count($courses)
            ]);
        }

        // Regular request - render view
        $data = [
            'courses' => $courses,
            'searchTerm' => $originalSearchTerm ?? '',
            'user_id' => $user_id,
            'user_name' => $session->get('user_name'),
            'role' => $session->get('role')
        ];

        return view('courses/index', $data);
    }

    /**
     * Display course details
     *
     * @param int $course_id Course ID
     * @return mixed
     */
    public function view($course_id)
    {
        $session = session();

        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $courseModel = new CourseModel();
        $materialModel = new MaterialModel();
        $enrollmentModel = new EnrollmentModel();

        $course = $courseModel->find($course_id);
        if (!$course) {
            return redirect()->to('dashboard')->with('error', 'Course not found');
        }

        // Check if user is enrolled or has permission to view
        $user_id = $session->get('user_id');
        $role = $session->get('role');
        $enrolled = $enrollmentModel->where(['user_id' => $user_id, 'course_id' => $course_id])->first();

        if (!$enrolled && !in_array($role, ['admin', 'teacher'])) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. You are not enrolled in this course.');
        }

        $materials = $materialModel->getMaterialsByCourse($course_id);

        $data = [
            'course' => $course,
            'materials' => $materials,
            'enrolled' => $enrolled,
            'user_name' => $session->get('user_name')
        ];

        return view('course_view', $data);
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
            // Create notification for enrollment
            $notificationModel = new NotificationModel();
            $notificationModel->insert([
                'user_id' => $user_id,
                'message' => 'You have been enrolled in ' . $course['title'],
                'is_read' => 0
            ]);

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
