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
     * Batch (AM / PM / FULL) => stored plan + half_day_slot.
     */
    public static function batchToPlan(string $batch): ?array
    {
        return match (strtoupper(trim($batch))) {
            'FULL'  => ['plan' => 'FULL_DAY', 'half_day_slot' => null],
            'AM'    => ['plan' => 'HALF_DAY', 'half_day_slot' => 'AM'],
            'PM'    => ['plan' => 'HALF_DAY', 'half_day_slot' => 'PM'],
            default => null,
        };
    }

    /**
     * Stored plan + half_day_slot => batch (AM / PM / FULL).
     */
    public static function planToBatch(?string $plan, ?string $halfDaySlot): string
    {
        if (strtoupper((string) $plan) === 'HALF_DAY') {
            return strtoupper((string) $halfDaySlot) === 'PM' ? 'PM' : 'AM';
        }
        return 'FULL';
    }

    /**
     * Limit the query to enrollments that currently occupy a seat:
     * ACTIVE and not past their end_date. Chainable.
     */
    public function occupying()
    {
        return $this->where('enrollments.status', 'ACTIVE')
            ->groupStart()
                ->where('enrollments.end_date', null)
                ->orWhere('enrollments.end_date >=', date('Y-m-d'))
            ->groupEnd();
    }

    /**
     * Seats that can be allotted for a batch (single source of truth).
     *
     * Only ACTIVE enrollments occupy a seat.
     * - AM / PM blocked if the seat has an active enrollment in the same slot OR FULL.
     * - FULL blocked if the seat has ANY active enrollment.
     *
     * Each returned seat has extra keys: am_booked, pm_booked, full_booked (bool), label.
     *
     * @param int|null $excludeEnrollmentId Enrollment to ignore (e.g. the one being moved).
     */
    public function getAvailableSeats(string $batch, ?int $excludeEnrollmentId = null): array
    {
        $batch = strtoupper(trim($batch));
        if (self::batchToPlan($batch) === null) {
            return [];
        }

        $qb = $this->occupying()->select('seat_id, plan, half_day_slot');
        if ($excludeEnrollmentId !== null && $excludeEnrollmentId > 0) {
            $qb->where('enrollments.id !=', $excludeEnrollmentId);
        }

        // seat_id => ['AM' => bool, 'PM' => bool, 'FULL' => bool]
        $booked = [];
        foreach ($qb->findAll() as $e) {
            $booked[(int) $e['seat_id']][self::planToBatch($e['plan'], $e['half_day_slot'])] = true;
        }

        $seats = $this->db->table('seats')->orderBy('seat_no', 'ASC')->get()->getResultArray();

        $available = [];
        foreach ($seats as $seat) {
            $b    = $booked[(int) $seat['id']] ?? [];
            $full = ! empty($b['FULL']);
            $am   = $full || ! empty($b['AM']);
            $pm   = $full || ! empty($b['PM']);

            $ok = match ($batch) {
                'AM'   => ! $am,
                'PM'   => ! $pm,
                'FULL' => ! $am && ! $pm,
            };
            if (! $ok) {
                continue;
            }

            if (! $am && ! $pm) {
                $state = 'fully free';
            } elseif ($am) {
                $state = 'PM free (AM booked)';
            } else {
                $state = 'AM free (PM booked)';
            }

            $seat['am_booked']   = $am;
            $seat['pm_booked']   = $pm;
            $seat['full_booked'] = $full;
            $seat['label']       = '#' . $seat['seat_no'] . ' (' . $seat['floor'] . ') — ' . $state;
            $available[]         = $seat;
        }

        return $available;
    }

    public function isSeatAvailable(int $seatId, string $batch, ?int $excludeEnrollmentId = null): bool
    {
        foreach ($this->getAvailableSeats($batch, $excludeEnrollmentId) as $seat) {
            if ((int) $seat['id'] === $seatId) {
                return true;
            }
        }
        return false;
    }

    public function getActiveByStudentId(int $studentId): ?array
    {
        $row = $this->where('student_id', $studentId)->where('status', 'ACTIVE')->first();
        return $row ?: null;
    }
}
