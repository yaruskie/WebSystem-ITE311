<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialModel extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'course_id',
        'file_name',
        'file_path',
        'created_at',
    ];

    protected $useTimestamps = false;

    /**
     * Insert a new material record
     *
     * @param array $data Data to insert
     * @return int|string|bool Insert ID or false on failure
     */
    public function insertMaterial($data)
    {
        return $this->insert($data);
    }

    /**
     * Get all materials for a specific course
     *
     * @param int $course_id Course ID
     * @return array Array of materials
     */
    public function getMaterialsByCourse($course_id)
    {
        return $this->where('course_id', $course_id)->findAll();
    }
}
