<?php
// Bootstrap CI4 just enough for models
define('FCPATH', __DIR__ . '/public/');
chdir(__DIR__);

require 'vendor/autoload.php';

$app = require_once 'app/Config/Boot/development.php';

define('ENVIRONMENT', 'development');

use Config\Database;

$db = Database::connect();

$studentId = 11;

echo "=== Student 11 current state ===\n";
$student = $db->table('students')->where('id', $studentId)->get()->getRowArray();
print_r($student);

echo "\n=== Enrollments for student 11 ===\n";
$enrollments = $db->table('enrollments')->where('student_id', $studentId)->orderBy('id')->get()->getResultArray();
foreach ($enrollments as $e) {
    echo "ID:{$e['id']} status:{$e['status']} seat:{$e['seat_id']} end:{$e['end_date']}\n";
}

echo "\n=== Simulating CI4 EnrollmentModel query ===\n";
$enrollmentModel = new App\Models\EnrollmentModel();
$activeEnrollment = $enrollmentModel
    ->where('student_id', $studentId)
    ->where('status', 'ACTIVE')
    ->first();
echo "Active enrollment found: ";
var_dump($activeEnrollment ? $activeEnrollment['id'] : null);

if ($activeEnrollment) {
    echo "Calling enrollmentModel->update({$activeEnrollment['id']}, ...)\n";
    $result = $enrollmentModel->update((int) $activeEnrollment['id'], [
        'status'   => 'ENDED',
        'end_date' => date('Y-m-d'),
    ]);
    echo "Update result: "; var_dump($result);

    // Check what SQL was actually run
    $lastQ = $db->getLastQuery();
    echo "Last SQL: $lastQ\n";
}

echo "\n=== Simulating StudentModel update ===\n";
$studentModel = new App\Models\StudentModel();
$student = $studentModel->find($studentId);
echo "Student found: " . ($student ? $student['full_name'] : 'NOT FOUND') . "\n";
$r2 = $studentModel->update($studentId, ['status' => 'dormant']);
echo "Student update result: "; var_dump($r2);
$lastQ2 = $db->getLastQuery();
echo "Last SQL: $lastQ2\n";

echo "\n=== DB state after ===\n";
$s2 = $db->table('students')->where('id', $studentId)->get()->getRowArray();
echo "Student status: {$s2['status']}\n";
$e2 = $db->table('enrollments')->where('student_id', $studentId)->orderBy('id')->get()->getResultArray();
foreach ($e2 as $e) {
    echo "ID:{$e['id']} status:{$e['status']}\n";
}

// Reset
$db->table('enrollments')->where('id', 14)->update(['status' => 'ACTIVE', 'end_date' => null]);
$db->table('students')->where('id', $studentId)->update(['status' => 'active']);
echo "\n=== Reset done ===\n";
