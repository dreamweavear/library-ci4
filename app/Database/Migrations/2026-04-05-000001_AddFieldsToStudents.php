<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToStudents extends Migration
{
    public function up(): void
    {
        $fields = [];

        if (! $this->db->fieldExists('photo', 'students')) {
            $fields['photo'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
            ];
        }
        if (! $this->db->fieldExists('aadhar_number', 'students')) {
            $fields['aadhar_number'] = [
                'type'       => 'VARCHAR',
                'constraint' => 12,
                'null'       => true,
                'default'    => null,
            ];
        }
        if (! $this->db->fieldExists('preparing_for', 'students')) {
            $fields['preparing_for'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
            ];
        }
        if (! $this->db->fieldExists('address', 'students')) {
            $fields['address'] = [
                'type' => 'TEXT',
                'null' => true,
            ];
        }
        if (! $this->db->fieldExists('email', 'students')) {
            $fields['email'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
            ];
        }

        if (! empty($fields)) {
            $this->forge->addColumn('students', $fields);
        }
    }

    public function down(): void
    {
        foreach (['photo', 'aadhar_number', 'preparing_for', 'address', 'email'] as $col) {
            if ($this->db->fieldExists($col, 'students')) {
                $this->forge->dropColumn('students', $col);
            }
        }
    }
}
