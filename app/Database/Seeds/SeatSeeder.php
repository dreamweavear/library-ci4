<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SeatSeeder extends Seeder
{
    public function run()
    {
        $builder = $this->db->table('seats');

        // Avoid duplicates if seeder is re-run.
        $existing = $builder->countAllResults();
        if ($existing > 0) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $rows = [];
        for ($i = 1; $i <= 100; $i++) {
            $rows[] = [
                'seat_no'    => $i,
                // Assumption: first 50 seats are Ground, next 50 are First.
                // You can change per seat from Admin -> Seats.
                'floor'      => $i <= 50 ? 'GROUND' : 'FIRST',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $builder->insertBatch($rows);
    }
}

