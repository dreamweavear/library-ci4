<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLibraryAlumni extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('library_alumni')) {
            return;
        }

        $this->forge->addField([
            'id'             => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'student_id'     => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true, 'default' => null],
            'full_name'      => ['type' => 'VARCHAR', 'constraint' => 120],
            'phone'          => ['type' => 'VARCHAR', 'constraint' => 20,  'null' => true, 'default' => null],
            'guardian_name'  => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true, 'default' => null],
            'email'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null],
            'address'        => ['type' => 'TEXT',    'null' => true, 'default' => null],
            'preparing_for'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null],
            'photo'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'default' => null],
            'admission_date' => ['type' => 'DATE',    'null' => true, 'default' => null],
            'left_date'      => ['type' => 'DATE',    'null' => true, 'default' => null],
            'notes'          => ['type' => 'TEXT',    'null' => true, 'default' => null],
            'created_at'     => ['type' => 'DATETIME','null' => true, 'default' => null],
            'updated_at'     => ['type' => 'DATETIME','null' => true, 'default' => null],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('student_id');
        $this->forge->createTable('library_alumni');
    }

    public function down()
    {
        $this->forge->dropTable('library_alumni', true);
    }
}
