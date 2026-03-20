<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table         = 'payments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'student_id',
        'enrollment_id',
        'type',
        'for_month',
        'amount',
        'paid_on',
        'receipt_no',
        'notes',
    ];

    public function generateReceiptNo(): string
    {
        // Format: BB-YYYYMMDD-XXXX
        return 'BB-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
    }
}

