<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLockoutToAdminUsers extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('admin_users', [
            'failed_attempts' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'last_login_at',
            ],
            'locked_until' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'after'   => 'failed_attempts',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('admin_users', ['failed_attempts', 'locked_until']);
    }
}
