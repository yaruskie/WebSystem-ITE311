<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToCoursesTable extends Migration
{
	public function up()
	{
		// If the courses table doesn't exist yet, skip this migration.
		if (! $this->db->tableExists('courses')) {
			return;
		}

		$this->forge->addColumn('courses', [
			'course_code' => [
				'type' => 'VARCHAR',
				'constraint' => 20,
				'null' => false,
				'default' => ''
			],
			'school_year' => [
				'type' => 'VARCHAR',
				'constraint' => 20,
				'null' => false,
				'default' => '',
				'after' => 'title'
			],
			'semester' => [
				'type' => 'ENUM',
				'constraint' => ['1st Semester', '2nd Semester', 'Summer'],
				'null' => false,
				'default' => '1st Semester',
				'after' => 'school_year'
			],
			'schedule' => [
				'type' => 'VARCHAR',
				'constraint' => 100,
				'null' => false,
				'default' => '',
				'after' => 'semester'
			],
			'status' => [
				'type' => 'ENUM',
				'constraint' => ['Active', 'Inactive'],
				'null' => false,
				'default' => 'Active',
				'after' => 'schedule'
			],
			'start_date' => [
				'type' => 'DATE',
				'null' => true,
				'after' => 'status'
			],
			'end_date' => [
				'type' => 'DATE',
				'null' => true,
				'after' => 'start_date'
			]
		]);
	}

	public function down()
	{
		if (! $this->db->tableExists('courses')) {
			return;
		}

		$this->forge->dropColumn('courses', [
			'course_code',
			'school_year',
			'semester',
			'schedule',
			'status',
			'start_date',
			'end_date'
		]);
	}
}
