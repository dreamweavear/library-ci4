<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\SeatModel;

class Seats extends BaseController
{
    public function index()
    {
        try {
            $seatModel = new SeatModel();
            $enrollmentModel = new EnrollmentModel();

            $seats = $seatModel->orderBy('seat_no', 'ASC')->findAll();

            $activeEnrollments = $enrollmentModel
                ->occupying()
                ->select('enrollments.*, students.full_name AS student_name')
                ->join('students', 'students.id = enrollments.student_id')
                ->findAll();

        // seat_id => ['FULL_DAY' => row, 'AM' => row, 'PM' => row]
        $seatAssignments = [];
        foreach ($activeEnrollments as $e) {
            $seatId = (int) $e['seat_id'];
            if (! isset($seatAssignments[$seatId])) {
                $seatAssignments[$seatId] = [];
            }

            if (($e['plan'] ?? '') === 'FULL_DAY') {
                $seatAssignments[$seatId]['FULL_DAY'] = $e;
            } elseif (($e['plan'] ?? '') === 'HALF_DAY') {
                $slot = strtoupper((string) ($e['half_day_slot'] ?? ''));
                if ($slot === 'AM' || $slot === 'PM') {
                    $seatAssignments[$seatId][$slot] = $e;
                }
            }
        }

            $library = config('Library');

            // Fully booked = FULL_DAY, or both AM + PM. Half booked = only AM or only PM.
            $totalSeats    = count($seats);
            $occupiedCount = 0;
            $halfCount     = 0;
            foreach ($seatAssignments as $a) {
                if (isset($a['FULL_DAY']) || (isset($a['AM']) && isset($a['PM']))) {
                    $occupiedCount++;
                } elseif (isset($a['AM']) || isset($a['PM'])) {
                    $halfCount++;
                }
            }
            $availableCount = $totalSeats - $occupiedCount - $halfCount;

            return view('admin/seats/index', [
                'seats'          => $seats,
                'seatAssignments'=> $seatAssignments,
                'library'        => $library,
                'totalSeats'     => $totalSeats,
                'occupiedCount'  => $occupiedCount,
                'halfCount'      => $halfCount,
                'availableCount' => $availableCount,
            ]);
        } catch (\Throwable $e) {
            return view('admin/setup', ['error' => $e->getMessage()]);
        }
    }

    public function updateFloor(int $seatId)
    {
        $library = config('Library');
        $floor = strtoupper(trim((string) $this->request->getPost('floor')));

        if (! in_array($floor, $library->floors, true)) {
            return redirect()->back()->with('error', 'Invalid floor.');
        }

        $seatModel = new SeatModel();
        $seat = $seatModel->find($seatId);
        if (! $seat) {
            return redirect()->back()->with('error', 'Seat not found.');
        }

        $seatModel->update($seatId, ['floor' => $floor]);
        return redirect()->back()->with('success', 'Seat floor updated.');
    }
}
