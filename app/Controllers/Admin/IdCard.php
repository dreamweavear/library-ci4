<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class IdCard extends BaseController
{
    /**
     * Fetch student with current seat_no via a join.
     */
    private function getStudentWithSeat(int $id): ?array
    {
        $db = \Config\Database::connect();
        $row = $db->table('students s')
            ->select('s.*, seats.seat_no')
            ->join('enrollments e', 'e.student_id = s.id AND e.status = \'ACTIVE\'', 'left')
            ->join('seats', 'seats.id = e.seat_id', 'left')
            ->where('s.id', $id)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    /**
     * Print single student ID card.
     * Route: GET admin/idcard/print/{id}
     */
    public function print(int $id)
    {
        try {
            $student = $this->getStudentWithSeat($id);

            if (! $student) {
                return redirect()->to(site_url('admin/students'))->with('error', 'Student not found.');
            }

            return view('admin/idcard/print', [
                'title' => 'ID Card — ' . $student['full_name'],
                'cards' => [$student],
            ]);
        } catch (\Throwable $e) {
            return redirect()->to(site_url('admin/students'))->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk print selected student ID cards.
     * Route: POST admin/idcard/bulk
     */
    public function bulk()
    {
        try {
            $ids = $this->request->getPost('ids');

            if (empty($ids) || ! is_array($ids)) {
                return redirect()->to(site_url('admin/students'))->with('error', 'No students selected for ID card printing.');
            }

            $cards = [];
            foreach ($ids as $id) {
                $student = $this->getStudentWithSeat((int) $id);
                if ($student) {
                    $cards[] = $student;
                }
            }

            if (empty($cards)) {
                return redirect()->to(site_url('admin/students'))->with('error', 'No valid students found.');
            }

            return view('admin/idcard/print', [
                'title' => 'ID Cards — Bulk Print (' . count($cards) . ' students)',
                'cards' => $cards,
            ]);
        } catch (\Throwable $e) {
            return redirect()->to(site_url('admin/students'))->with('error', $e->getMessage());
        }
    }
}
