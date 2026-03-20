<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Library extends BaseConfig
{
    public array $floors = ['GROUND', 'FIRST'];
    public array $plans = ['FULL_DAY', 'HALF_DAY'];
    public array $halfDaySlots = ['AM', 'PM'];

    public array $fees = [
        'FULL_DAY' => [
            'GROUND' => 900,
            'FIRST'  => 700,
        ],
        'HALF_DAY' => 600,
    ];

    public array $timings = [
        'FULL_DAY' => ['07:00', '21:00'],
        'HALF_DAY' => [
            'AM' => ['07:00', '14:00'],
            'PM' => ['14:00', '21:00'],
        ],
    ];
}

