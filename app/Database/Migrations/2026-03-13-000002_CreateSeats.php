<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSeats extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'seat_no' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'floor' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => 'GROUND',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('seat_no');
        $this->forge->createTable('seats');
    }

    public function down()
    {
        $this->forge->dropTable('seats');
    }
}

