<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title'       => 'Introduction to Web Development',
                'description' => 'Learn the fundamentals of HTML, CSS, and JavaScript to build modern web applications. This course covers responsive design, basic programming concepts, and hands-on projects.',
                'teacher_id'  => 2, // Teacher User
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'Advanced PHP Programming',
                'description' => 'Master advanced PHP concepts including object-oriented programming, MVC architecture, database integration, and security best practices.',
                'teacher_id'  => 2, // Teacher User
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'Database Design and Management',
                'description' => 'Comprehensive course on relational database design, SQL queries, normalization, indexing, and performance optimization techniques.',
                'teacher_id'  => 2, // Teacher User
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'JavaScript Frameworks',
                'description' => 'Explore popular JavaScript frameworks including React, Vue.js, and Angular. Learn component-based architecture and modern development practices.',
                'teacher_id'  => 2, // Teacher User
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'Mobile App Development',
                'description' => 'Build native mobile applications for iOS and Android using React Native. Cover app deployment, API integration, and best practices.',
                'teacher_id'  => 2, // Teacher User
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'Data Science Fundamentals',
                'description' => 'Introduction to data analysis, visualization, and machine learning concepts using Python, pandas, and scikit-learn.',
                'teacher_id'  => 2, // Teacher User
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'Cybersecurity Basics',
                'description' => 'Learn essential cybersecurity concepts including threat detection, encryption, secure coding practices, and network security fundamentals.',
                'teacher_id'  => 2, // Teacher User
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'Cloud Computing with AWS',
                'description' => 'Comprehensive guide to Amazon Web Services including EC2, S3, Lambda, and other cloud services for scalable application deployment.',
                'teacher_id'  => 2, // Teacher User
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert multiple rows
        $this->db->table('courses')->insertBatch($data);
    }
}
