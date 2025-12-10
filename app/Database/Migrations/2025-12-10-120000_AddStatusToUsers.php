<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'status' => [
                'type' => "ENUM('active','inactive')",
                'default' => 'active',
            ],
        ];

        // Add column if it doesn't already exist
        $forge = $this->forge;
        if (! $this->db->fieldExists('status', 'users')) {
            $forge->addColumn('users', $fields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('status', 'users')) {
            $this->forge->dropColumn('users', 'status');
        }
    }
}
