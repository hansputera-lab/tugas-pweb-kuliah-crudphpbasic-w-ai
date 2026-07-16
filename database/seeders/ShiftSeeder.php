<?php

namespace Database\Seeders;

use App\Domains\Shift\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = [
            ['name' => 'Morning', 'start_time' => '07:00:00', 'end_time' => '15:00:00', 'late_threshold' => '07:15:00', 'color' => '#6366f1', 'is_active' => true],
            ['name' => 'Afternoon', 'start_time' => '15:00:00', 'end_time' => '23:00:00', 'late_threshold' => '15:15:00', 'color' => '#f59e0b', 'is_active' => true],
            ['name' => 'Night', 'start_time' => '23:00:00', 'end_time' => '07:00:00', 'late_threshold' => '23:15:00', 'color' => '#0ea5e9', 'is_active' => true],
        ];

        foreach ($shifts as $shift) {
            Shift::updateOrCreate(['name' => $shift['name']], $shift);
        }
    }
}
