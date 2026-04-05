<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\SeatModel;

class Dashboard extends BaseController
{
    public function index()
    {
        try {
            $seatModel = new SeatModel();
            $enrollmentModel = new EnrollmentModel();

            $seats = $seatModel->select('id, floor')->findAll();
            $totalSeats = count($seats);

            $active = $enrollmentModel
                ->select('seat_id, plan, half_day_slot')
                ->where('status', 'ACTIVE')
                ->findAll();

        $anyOccupiedSeatIds = [];
        $fullDaySeatIds = [];
        $amSeatIds = [];
        $pmSeatIds = [];

        $fullDayCount = 0;
        $amCount = 0;
        $pmCount = 0;

        foreach ($active as $e) {
            $seatId = (int) $e['seat_id'];
            $anyOccupiedSeatIds[$seatId] = true;

            if (($e['plan'] ?? '') === 'FULL_DAY') {
                $fullDaySeatIds[$seatId] = true;
                $fullDayCount++;
                continue;
            }

            if (($e['plan'] ?? '') === 'HALF_DAY') {
                $slot = strtoupper((string) ($e['half_day_slot'] ?? ''));
                if ($slot === 'AM') {
                    $amSeatIds[$seatId] = true;
                    $amCount++;
                } elseif ($slot === 'PM') {
                    $pmSeatIds[$seatId] = true;
                    $pmCount++;
                }
            }
        }

        // Availability rules:
        // - Full day needs a completely free seat (no active enrollment).
        // - AM needs seat not used by FULL_DAY and not used by HALF_DAY (AM).
        // - PM needs seat not used by FULL_DAY and not used by HALF_DAY (PM).
        $availableForFullDay = max(0, $totalSeats - count($anyOccupiedSeatIds));
        $availableForAm = max(0, $totalSeats - count($fullDaySeatIds + $amSeatIds));
        $availableForPm = max(0, $totalSeats - count($fullDaySeatIds + $pmSeatIds));

        $floors = ['GROUND', 'FIRST'];
        $totalByFloor = ['GROUND' => 0, 'FIRST' => 0];
        $availableFullByFloor = ['GROUND' => 0, 'FIRST' => 0];
        $availableAmByFloor = ['GROUND' => 0, 'FIRST' => 0];
        $availablePmByFloor = ['GROUND' => 0, 'FIRST' => 0];

        foreach ($seats as $s) {
            $floor = strtoupper((string) ($s['floor'] ?? 'GROUND'));
            if (! in_array($floor, $floors, true)) {
                $floor = 'GROUND';
            }

            $totalByFloor[$floor]++;

            $seatId = (int) $s['id'];
            $isAny = isset($anyOccupiedSeatIds[$seatId]);
            $isFull = isset($fullDaySeatIds[$seatId]);
            $isAm = isset($amSeatIds[$seatId]);
            $isPm = isset($pmSeatIds[$seatId]);

            if (! $isAny) {
                $availableFullByFloor[$floor]++;
            }
            if (! $isFull && ! $isAm) {
                $availableAmByFloor[$floor]++;
            }
            if (! $isFull && ! $isPm) {
                $availablePmByFloor[$floor]++;
            }
        }

            $library = config('Library');

            // Student status counts
            $db = \Config\Database::connect();
            $activeStudents  = (int) $db->table('students')->where('status', 'active')->countAllResults();
            $dormantStudents = (int) $db->table('students')->where('status', 'dormant')->countAllResults();
            $alumniCount     = $db->tableExists('library_alumni')
                ? (int) $db->table('library_alumni')->countAllResults()
                : 0;

            return view('admin/dashboard', [
                'totalSeats'      => $totalSeats,
                'fullDayCount'    => $fullDayCount,
                'amCount'         => $amCount,
                'pmCount'         => $pmCount,
                'availableForFullDay' => $availableForFullDay,
                'availableForAm'  => $availableForAm,
                'availableForPm'  => $availableForPm,
                'totalByFloor'    => $totalByFloor,
                'availableFullByFloor' => $availableFullByFloor,
                'availableAmByFloor' => $availableAmByFloor,
                'availablePmByFloor' => $availablePmByFloor,
                'library'         => $library,
                'activeStudents'  => $activeStudents,
                'dormantStudents' => $dormantStudents,
                'alumniCount'     => $alumniCount,
            ]);
        } catch (\Throwable $e) {
            return view('admin/setup', ['error' => $e->getMessage()]);
        }
    }
}
