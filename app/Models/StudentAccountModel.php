<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentAccountModel extends Model
{
    protected $table         = 'student_accounts';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'student_id',
        'username',
        'password_hash',
        'status',
        'last_login_at',
    ];
}