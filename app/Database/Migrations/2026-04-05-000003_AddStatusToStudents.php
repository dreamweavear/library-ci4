<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToStudents extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('status', 'students')) {
            $this->forge->addColumn('students', [
                'status' => [
                    'type'    => "ENUM('active','dormant','alumni')",
                    'null'    => false,
                    'default' => 'active',
                    'after'   => 'admission_date',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('status', 'students')) {
            $this->forge->dropColumn('students', 'status');
        }
    }
}
