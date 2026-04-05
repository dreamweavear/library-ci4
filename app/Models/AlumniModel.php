<?php

namespace App\Models;

use CodeIgniter\Model;

class AlumniModel extends Model
{
    protected $table         = 'library_alumni';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'student_id', 'full_name', 'phone', 'guardian_name',
        'email', 'address', 'preparing_for', 'photo',
        'admission_date', 'left_date', 'notes',
    ];
}
