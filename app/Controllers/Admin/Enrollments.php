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

    /**
     * GET admin/enrollments/change-seat/(:num)
     * Show the change-seat form for a student.
     */
    public function changeSeatForm(int $studentId)
    {
        try {
            $studentModel    = new StudentModel();
            $enrollmentModel = new EnrollmentModel();
            $seatModel       = new SeatModel();

            $student = $studentModel->find($studentId);
            if (! $student) {
                return redirect()->to(site_url('admin/students'))->with('error', 'Student not found.');
            }

            $current = $enrollmentModel
                ->select('enrollments.*, seats.seat_no, seats.floor')
                ->join('seats', 'seats.id = enrollments.seat_id')
                ->where('enrollments.student_id', $studentId)
                ->where('enrollments.status', 'ACTIVE')
                ->first();

            if (! $current) {
                return redirect()->to(site_url('admin/students/' . $studentId))
                    ->with('error', 'This student has no active seat. Use "Allot Seat" first.');
            }

            // Build list of seats available for the same plan/slot (excluding current seat)
            $allSeats     = $seatModel->orderBy('seat_no', 'ASC')->findAll();
            $plan         = $current['plan'];
            $halfDaySlot  = $current['half_day_slot'] ?? null;
            $availableSeats = [];

            foreach ($allSeats as $seat) {
                if ((int) $seat['id'] === (int) $current['seat_id']) {
                    continue; // skip current seat
                }
                if (! $enrollmentModel->getSeatConflict((int) $seat['id'], $plan, $halfDaySlot)) {
                    $availableSeats[] = $seat;
                }
            }

            return view('admin/enrollments/change_seat', [
                'student'        => $student,
                'current'        => $current,
                'availableSeats' => $availableSeats,
                'errors'         => session()->getFlashdata('errors') ?? [],
            ]);
        } catch (\Throwable $e) {
            return view('admin/setup', ['error' => $e->getMessage()]);
        }
    }

    /**
     * POST admin/enrollments/change-seat/(:num)
     * Execute the seat change inside a DB transaction.
     */
    public function changeSeat(int $studentId)
    {
        try {
            $studentModel    = new StudentModel();
            $enrollmentModel = new EnrollmentModel();
            $seatModel       = new SeatModel();

            $student = $studentModel->find($studentId);
            if (! $student) {
                return redirect()->to(site_url('admin/students'))->with('error', 'Student not found.');
            }

            $current = $enrollmentModel
                ->where('student_id', $studentId)
                ->where('status', 'ACTIVE')
                ->first();

            if (! $current) {
                return redirect()->to(site_url('admin/students/' . $studentId))
                    ->with('error', 'No active enrollment found for this student.');
            }

            $newSeatId  = (int) $this->request->getPost('new_seat_id');
            $changeDate = trim((string) $this->request->getPost('change_date')) ?: date('Y-m-d');
            $reason     = trim((string) $this->request->getPost('reason'));

            // Validate
            if ($newSeatId <= 0) {
                return redirect()->back()->withInput()->with('errors', ['new_seat_id' => 'Please select a new seat.']);
            }
            if ($newSeatId === (int) $current['seat_id']) {
                return redirect()->back()->withInput()->with('errors', ['new_seat_id' => 'New seat must be different from the current seat.']);
            }

            $newSeat = $seatModel->find($newSeatId);
            if (! $newSeat) {
                return redirect()->back()->withInput()->with('errors', ['new_seat_id' => 'Selected seat not found.']);
            }

            // Check seat availability for the same plan
            $plan        = $current['plan'];
            $halfDaySlot = $current['half_day_slot'] ?? null;

            if ($enrollmentModel->getSeatConflict($newSeatId, $plan, $halfDaySlot)) {
                return redirect()->back()->withInput()->with('errors', ['new_seat_id' => 'Selected seat is no longer available. Please choose another.']);
            }

            // Transaction: end current → create new
            $db = \Config\Database::connect();
            $db->transBegin();

            try {
                $enrollmentModel->update((int) $current['id'], [
                    'status'   => 'ENDED',
                    'end_date' => $changeDate,
                ]);

                $enrollmentModel->insert([
                    'student_id'    => $studentId,
                    'seat_id'       => $newSeatId,
                    'plan'          => $plan,
                    'half_day_slot' => $halfDaySlot,
                    'fee'           => $current['fee'],
                    'start_date'    => $changeDate,
                    'end_date'      => null,
                    'status'        => 'ACTIVE',
                ]);

                $db->transCommit();
            } catch (\Throwable $inner) {
                $db->transRollback();
                throw $inner;
            }

            $oldSeatNo = $current['seat_no'] ?? $current['seat_id'];
            $newSeatNo = $newSeat['seat_no'];

            return redirect()->to(site_url('admin/students/' . $studentId))
                ->with('success', "Seat successfully changed from #$oldSeatNo to #$newSeatNo." . ($reason !== '' ? " Reason: $reason" : ''));
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
