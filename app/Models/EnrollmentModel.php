<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table         = 'enrollments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'student_id',
        'seat_id',
        'plan',
        'half_day_slot',
        'fee',
        'start_date',
        'end_date',
        'status',
    ];

    public function getActiveBySeatId(int $seatId): ?array
    {
        $row = $this->where('seat_id', $seatId)->where('status', 'ACTIVE')->first();
        return $row ?: null;
    }

    /**
     * Returns a conflicting active enrollment for a seat, if any.
     *
     * Rules:
     * - FULL_DAY conflicts with any ACTIVE enrollment on the seat.
     * - HALF_DAY (AM) conflicts with ACTIVE FULL_DAY or ACTIVE HALF_DAY (AM).
     * - HALF_DAY (PM) conflicts with ACTIVE FULL_DAY or ACTIVE HALF_DAY (PM).
     */
    public function getSeatConflict(int $seatId, string $plan, ?string $halfDaySlot): ?array
    {
        $plan = strtoupper($plan);
        $halfDaySlot = $halfDaySlot !== null ? strtoupper($halfDaySlot) : null;

        $qb = $this->where('seat_id', $seatId)->where('status', 'ACTIVE');

        if ($plan === 'FULL_DAY') {
            $row = $qb->first();
            return $row ?: null;
        }

        // HALF_DAY: conflict only if FULL_DAY exists, or the same slot exists.
        $qb = $qb->groupStart()
            ->where('plan', 'FULL_DAY')
            ->orGroupStart()
                ->where('plan', 'HALF_DAY')
                ->where('half_day_slot', $halfDaySlot)
            ->groupEnd()
        ->groupEnd();

        $row = $qb->first();
        return $row ?: null;
    }

    public function getActiveByStudentId(int $studentId): ?array
    {
        $row = $this->where('student_id', $studentId)->where('status', 'ACTIVE')->first();
        return $row ?: null;
    }
}
