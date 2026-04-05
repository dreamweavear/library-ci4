<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\PaymentModel;
use App\Models\SeatModel;
use App\Models\StudentModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $studentId = (int) session()->get('student_id');

        $studentModel = new StudentModel();
        $enrollmentModel = new EnrollmentModel();

        $student = $studentModel->find($studentId);
        if (! $student) {
            session()->remove(['student_account_id', 'student_id', 'student_name']);
            return redirect()->to(site_url('student/login'))->with('error', 'Student not found.');
        }

        $active = $enrollmentModel
            ->select('enrollments.*, seats.seat_no, seats.floor')
            ->join('seats', 'seats.id = enrollments.seat_id')
            ->where('enrollments.student_id', $studentId)
            ->where('enrollments.status', 'ACTIVE')
            ->first();

        $paymentModel = new PaymentModel();
        $payments = $paymentModel
            ->where('student_id', $studentId)
            ->orderBy('id', 'DESC')
            ->findAll();

        return view('student/dashboard', [
            'title'    => 'Student Dashboard · Brilient Brains Library',
            'student'  => $student,
            'active'   => $active,
            'payments' => $payments,
        ]);
    }
}
