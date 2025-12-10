<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeletedToUsersStatus extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('users', [
            'status' => [
                'type' => "ENUM('active','inactive','deleted')",
                'default' => 'active',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('users', [
            'status' => [
                'type' => "ENUM('active','inactive')",
                'default' => 'active',
            ],
        ]);
    }
}
