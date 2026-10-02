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
                ->occupying()
                ->select('seat_id, plan, half_day_slot')
                ->findAll();

        $fullDayCount = 0;
        $amCount = 0;
        $pmCount = 0;

        foreach ($active as $e) {
            $batch = EnrollmentModel::planToBatch($e['plan'] ?? null, $e['half_day_slot'] ?? null);
            if ($batch === 'FULL') {
                $fullDayCount++;
            } elseif ($batch === 'AM') {
                $amCount++;
            } else {
                $pmCount++;
            }
        }

        // Free-seat counts come from the same model method the Allot Seat
        // dropdown uses, so the numbers always match.
        $normFloor = static function ($floor): string {
            $floor = strtoupper((string) ($floor ?? 'GROUND'));
            return $floor === 'FIRST' ? 'FIRST' : 'GROUND';
        };

        $totalByFloor = ['GROUND' => 0, 'FIRST' => 0];
        foreach ($seats as $s) {
            $totalByFloor[$normFloor($s['floor'] ?? null)]++;
        }

        $availableByBatch = [];
        $availableByFloor = [];
        foreach (['FULL', 'AM', 'PM'] as $batch) {
            $free = $enrollmentModel->getAvailableSeats($batch);
            $availableByBatch[$batch] = count($free);
            $availableByFloor[$batch] = ['GROUND' => 0, 'FIRST' => 0];
            foreach ($free as $s) {
                $availableByFloor[$batch][$normFloor($s['floor'] ?? null)]++;
            }
        }

        $availableForFullDay  = $availableByBatch['FULL'];
        $availableForAm       = $availableByBatch['AM'];
        $availableForPm       = $availableByBatch['PM'];
        $availableFullByFloor = $availableByFloor['FULL'];
        $availableAmByFloor   = $availableByFloor['AM'];
        $availablePmByFloor   = $availableByFloor['PM'];

            $library = config('Library');

            // Student status counts
            $db = \Config\Database::connect();
            $activeStudents  = (int) $db->table('students')->where('status', 'active')->countAllResults();
            $dormantStudents = (int) $db->table('students')->where('status', 'dormant')->countAllResults();
            $alumniCount     = $db->tableExists('library_alumni')
                ? (int) $db->table('library_alumni')->countAllResults()
                : 0;

            // Today's and this month's collection
            $todayCollection = (int) ($db->query(
                "SELECT IFNULL(SUM(amount),0) AS t FROM payments WHERE DATE(paid_on) = CURDATE()"
            )->getRow()->t ?? 0);

            $monthCollection = (int) ($db->query(
                "SELECT IFNULL(SUM(amount),0) AS t FROM payments
                 WHERE YEAR(paid_on) = YEAR(CURDATE()) AND MONTH(paid_on) = MONTH(CURDATE())"
            )->getRow()->t ?? 0);

            // Students with pending fees this month (seat-sorted)
            $dueStudents = $db->query("
                SELECT e.id, e.fee, e.start_date,
                       s.full_name, s.phone, se.seat_no, se.floor,
                       IFNULL((SELECT SUM(p.amount) FROM payments p
                               WHERE p.enrollment_id = e.id AND p.type='MONTHLY'), 0) AS paid_monthly,
                       (PERIOD_DIFF(DATE_FORMAT(NOW(),'%Y%m'),
                                    DATE_FORMAT(e.start_date,'%Y%m')) + 1) AS months_due
                FROM enrollments e
                JOIN students s  ON s.id  = e.student_id
                JOIN seats se    ON se.id = e.seat_id
                WHERE e.status = 'ACTIVE'
                HAVING paid_monthly < (months_due * e.fee)
                ORDER BY se.seat_no ASC
            ")->getResultArray();

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
                'todayCollection' => $todayCollection,
                'monthCollection' => $monthCollection,
                'dueStudents'     => $dueStudents,
            ]);
        } catch (\Throwable $e) {
            return view('admin/setup', ['error' => $e->getMessage()]);
        }
    }
}
