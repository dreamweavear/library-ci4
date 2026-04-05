<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReceiptNumberToPayments extends Migration
{
    public function up(): void
    {
        if (! $this->db->fieldExists('receipt_number', 'payments')) {
            $this->forge->addColumn('payments', [
                'receipt_number' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'receipt_no',
                ],
            ]);
        }
    }

    public function down(): void
    {
        if ($this->db->fieldExists('receipt_number', 'payments')) {
            $this->forge->dropColumn('payments', 'receipt_number');
        }
    }
}
