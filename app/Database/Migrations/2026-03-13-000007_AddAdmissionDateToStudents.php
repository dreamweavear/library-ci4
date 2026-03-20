<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAdmissionDateToStudents extends Migration
{
    public function up()
    {
        $fields = [
            'admission_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'notes',
            ],
        ];

        $this->forge->addColumn('students', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('students', 'admission_date');
    }
}