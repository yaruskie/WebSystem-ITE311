<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title' => 'Welcome to the New Academic Year',
                'content' => 'We are excited to welcome all students, teachers, and staff to the new academic year. This year promises to be filled with learning opportunities, growth, and success. Please make sure to check this announcements page regularly for important updates and information.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'System Maintenance Scheduled',
                'content' => 'Please be informed that our system will undergo scheduled maintenance on Sunday, October 20th, from 2:00 AM to 6:00 AM. During this time, the portal may be temporarily unavailable. We apologize for any inconvenience this may cause.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
            ],
            [
                'title' => 'Important: Course Registration Deadline',
                'content' => 'This is a reminder that the course registration deadline is approaching. All students must complete their course registration by the end of this week. Late registrations will not be accepted without prior approval from the academic office.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
            ]
        ];

        $this->db->table('announcements')->insertBatch($data);
    }
}
