<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'title',
        'description',
        'teacher_id',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = false;

    /**
     * Get all courses with teacher information
     *
     * @return array Array of courses with teacher details
     */
    public function getAllCoursesWithTeachers()
    {
        return $this->select('courses.*, users.name as teacher_name')
                    ->join('users', 'users.id = courses.teacher_id')
                    ->findAll();
    }

    /**
     * Get courses by teacher ID
     *
     * @param int $teacher_id Teacher ID
     * @return array Array of courses taught by the teacher
     */
    public function getCoursesByTeacher($teacher_id)
    {
        return $this->where('teacher_id', $teacher_id)->findAll();
    }
}
