<?php

namespace App\Controllers;

use App\Models\MaterialModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;

class Materials extends BaseController
{
    protected $materialModel;
    protected $courseModel;
    protected $enrollmentModel;

    public function __construct()
    {
        $this->materialModel = new MaterialModel();
        $this->courseModel = new CourseModel();
        $this->enrollmentModel = new EnrollmentModel();
    }

    public function upload($course_id)
    {
        $session = session();

        // Check if user is logged in and is admin/teacher
        if (!$session->get('isLoggedIn') || !in_array($session->get('role'), ['admin', 'teacher'])) {
            return redirect()->to('/login');
        }

        $course = $this->courseModel->find($course_id);
        if (!$course) {
            $session->setFlashdata('error', 'Course not found.');
            return redirect()->to('/dashboard');
        }

        $materials = $this->materialModel->getMaterialsByCourse($course_id);

        if ($this->request->is('post')) {
            // Handle file upload
            $file = $this->request->getFile('material');
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(WRITEPATH . 'uploads', $newName);

                $data = [
                    'course_id' => $course_id,
                    'file_name' => $file->getClientName(),
                    'file_path' => 'uploads/' . $newName,
                    'created_at' => date('Y-m-d H:i:s')
                ];

                if ($this->materialModel->insertMaterial($data)) {
                    $session->setFlashdata('success', 'Material uploaded successfully.');
                    return redirect()->to('/admin/course/' . $course_id . '/upload');
                } else {
                    $session->setFlashdata('error', 'Failed to save material.');
                }
            } else {
                $session->setFlashdata('error', 'Invalid file.');
            }
            return redirect()->back();
        }

        $data = [
            'course_id' => $course_id,
            'course_title' => $course['title'],
            'user_name' => $session->get('user_name'),
            'materials' => $materials
        ];

        return view('upload_material', $data);
    }

    public function delete($material_id)
    {
        $session = session();

        if (!$session->get('isLoggedIn') || !in_array($session->get('role'), ['admin', 'teacher'])) {
            return redirect()->to('/login');
        }

        $material = $this->materialModel->find($material_id);
        if (!$material) {
            $session->setFlashdata('error', 'Material not found.');
            return redirect()->back();
        }

        // If teacher, check if owns the course
        if ($session->get('role') === 'teacher') {
            $course = $this->courseModel->find($material['course_id']);
            if (!$course || $course['teacher_id'] != $session->get('user_id')) {
                $session->setFlashdata('error', 'Access denied. You can only delete materials from your courses.');
                return redirect()->back();
            }
        }

        // Delete file
        $file_path = WRITEPATH . $material['file_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // Delete record
        if ($this->materialModel->delete($material_id)) {
            $session->setFlashdata('success', 'Material deleted successfully.');
        } else {
            $session->setFlashdata('error', 'Failed to delete material.');
        }

        return redirect()->back();
    }

    public function download($material_id)
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $material = $this->materialModel->find($material_id);
        if (!$material) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Material not found');
        }

        // Check if user is enrolled in the course
        $user_id = $session->get('user_id');
        $enrolled = $this->enrollmentModel->where(['user_id' => $user_id, 'course_id' => $material['course_id']])->first();

        if (!$enrolled && !in_array($session->get('role'), ['admin', 'teacher'])) {
            $session->setFlashdata('error', 'Access denied. You are not enrolled in this course.');
            return redirect()->to('/dashboard');
        }

        $file_path = WRITEPATH . $material['file_path'];
        if (file_exists($file_path)) {
            return $this->response->download($file_path, null)->setFileName($material['file_name']);
        } else {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found');
        }
    }
}
