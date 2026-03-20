<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\SeatModel;
use App\Models\StudentModel;

class Enrollments extends BaseController
{
    public function index()
    {
        try {
            $enrollmentModel = new EnrollmentModel();

        $status = strtoupper(trim((string) $this->request->getGet('status')));
        if ($status === '') {
            $status = 'ACTIVE';
        }
        if (! in_array($status, ['ACTIVE', 'ENDED'], true)) {
            $status = 'ACTIVE';
        }

        $rows = $enrollmentModel
            ->select('enrollments.*, students.full_name, students.phone, seats.seat_no, seats.floor')
            ->join('students', 'students.id = enrollments.student_id')
            ->join('seats', 'seats.id = enrollments.seat_id')
            ->where('enrollments.status', $status)
            ->orderBy('enrollments.id', 'DESC')
            ->paginate(25);

        $library = config('Library');

            return view('admin/enrollments/index', [
                'enrollments' => $rows,
                'pager'       => $enrollmentModel->pager,
                'status'      => $status,
                'library'     => $library,
            ]);
        } catch (\Throwable $e) {
            return view('admin/setup', ['error' => $e->getMessage()]);
        }
    }

    public function new()
    {
        try {
            $studentModel = new StudentModel();
            $seatModel = new SeatModel();

        $students = $studentModel->orderBy('full_name', 'ASC')->findAll();
        $seats = $seatModel->orderBy('seat_no', 'ASC')->findAll();

        $library = config('Library');

            return view('admin/enrollments/form', [
                'students' => $students,
                'seats'    => $seats,
                'library'  => $library,
                'errors'   => session()->getFlashdata('errors') ?? [],
            ]);
        } catch (\Throwable $e) {
            return view('admin/setup', ['error' => $e->getMessage()]);
        }
    }

    public function create()
    {
        try {
        $library = config('Library');

        $rules = [
            'student_id'    => 'required|is_natural_no_zero',
            'seat_id'       => 'required|is_natural_no_zero',
            'plan'          => 'required|in_list[' . implode(',', $library->plans) . ']',
            'half_day_slot' => 'permit_empty|in_list[' . implode(',', $library->halfDaySlots) . ']',
            'start_date'    => 'required|valid_date[Y-m-d]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $studentId = (int) $this->request->getPost('student_id');
        $seatId = (int) $this->request->getPost('seat_id');
        $plan = strtoupper(trim((string) $this->request->getPost('plan')));
        $halfDaySlot = strtoupper(trim((string) $this->request->getPost('half_day_slot')));
        $startDate = (string) $this->request->getPost('start_date');

        if ($plan === 'HALF_DAY' && $halfDaySlot === '') {
            return redirect()->back()->withInput()->with('errors', ['half_day_slot' => 'Half-day slot is required for Half Day plan.']);
        }
        if ($plan === 'FULL_DAY') {
            $halfDaySlot = null;
        }

        $studentModel = new StudentModel();
        $seatModel = new SeatModel();
        $enrollmentModel = new EnrollmentModel();

        $student = $studentModel->find($studentId);
        if (! $student) {
            return redirect()->back()->withInput()->with('errors', ['student_id' => 'Student not found.']);
        }

        $seat = $seatModel->find($seatId);
        if (! $seat) {
            return redirect()->back()->withInput()->with('errors', ['seat_id' => 'Seat not found.']);
        }

        if ($seat['seat_no'] < 1 || $seat['seat_no'] > 100) {
            return redirect()->back()->withInput()->with('errors', ['seat_id' => 'Seat number must be between 1 and 100.']);
        }

        if ($enrollmentModel->getSeatConflict($seatId, $plan, $halfDaySlot)) {
            if ($plan === 'FULL_DAY') {
                return redirect()->back()->withInput()->with('errors', ['seat_id' => 'This seat already has an active enrollment (Full/Half day).']);
            }
            return redirect()->back()->withInput()->with('errors', ['seat_id' => 'This seat is already occupied for the selected half-day slot, or is occupied for Full Day.']);
        }

        if ($enrollmentModel->getActiveByStudentId($studentId)) {
            return redirect()->back()->withInput()->with('errors', ['student_id' => 'This student already has an active seat. End the current enrollment first.']);
        }

        $fee = 0;
        if ($plan === 'FULL_DAY') {
            $floor = strtoupper((string) $seat['floor']);
            $fee = (int) ($library->fees['FULL_DAY'][$floor] ?? 0);
        } else {
            $fee = (int) $library->fees['HALF_DAY'];
        }

        $id = $enrollmentModel->insert([
            'student_id'    => $studentId,
            'seat_id'       => $seatId,
            'plan'          => $plan,
            'half_day_slot' => $halfDaySlot,
            'fee'           => $fee,
            'start_date'    => $startDate,
            'end_date'      => null,
            'status'        => 'ACTIVE',
        ]);

        return redirect()->to(site_url('admin/enrollments'))->with('success', 'Seat allotted (Enrollment #' . $id . ').');
    } catch (\Throwable $e) {
        return redirect()->back()->withInput()->with('error', $e->getMessage());
    }
}

    public function end(int $id)
    {
        try {
            $enrollmentModel = new EnrollmentModel();
            $row = $enrollmentModel->find($id);
            if (! $row) {
                return redirect()->back()->with('error', 'Enrollment not found.');
            }

            if ($row['status'] !== 'ACTIVE') {
                return redirect()->back()->with('error', 'Enrollment is already ended.');
            }

            $enrollmentModel->update($id, [
                'status'   => 'ENDED',
                'end_date' => date('Y-m-d'),
            ]);

            return redirect()->back()->with('success', 'Enrollment ended. Seat is now available.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
