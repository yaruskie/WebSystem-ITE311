<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'user_id',
        'course_id',
        'enrollment_date',
    ];

    protected $useTimestamps = false;

    /**
     * Enroll a user in a course
     *
     * @param array $data Enrollment data (user_id, course_id, enrollment_date)
     * @return bool Success status
     */
    public function enrollUser($data)
    {
        $data['enrollment_date'] = date('Y-m-d H:i:s');

        return $this->insert($data);
    }

    /**
     * Get all enrollments for a specific user with course details
     *
     * @param int $user_id User ID
     * @return array Array of enrolled courses with details
     */
    public function getUserEnrollments($user_id)
    {
        return $this->select('enrollments.*, courses.title, courses.description, courses.teacher_id, users.name as teacher_name')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->join('users', 'users.id = courses.teacher_id')
                    ->where('enrollments.user_id', $user_id)
                    ->findAll();
    }

    /**
     * Check if a user is already enrolled in a specific course
     *
     * @param int $user_id User ID
     * @param int $course_id Course ID
     * @return bool True if already enrolled, false otherwise
     */
    public function isAlreadyEnrolled($user_id, $course_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('course_id', $course_id)
                    ->countAllResults() > 0;
    }

    /**
     * Get all available courses (not enrolled by user)
     *
     * @param int $user_id User ID
     * @return array Array of available courses
     */
    public function getAvailableCourses($user_id)
    {
        $enrolled_course_ids = $this->select('course_id')
                                   ->where('user_id', $user_id)
                                   ->findAll();

        $enrolled_ids = array_column($enrolled_course_ids, 'course_id');

        $courses = new CourseModel();

        if (empty($enrolled_ids)) {
            return $courses->findAll();
        }

        return $courses->whereNotIn('id', $enrolled_ids)->findAll();
    }
}
