<?php

namespace Database\Seeders;

use App\Domains\Performance\Models\Kpi;
use Illuminate\Database\Seeder;

class KpiSeeder extends Seeder
{
    public function run(): void
    {
        $kpis = [
            ['title' => 'Code Quality', 'description' => 'Clean, maintainable, well-tested code', 'category' => 'competency', 'target_value' => 90, 'weight' => 25, 'measurement_unit' => '%', 'is_active' => true],
            ['title' => 'Productivity', 'description' => 'Tasks delivered per sprint', 'category' => 'goal', 'target_value' => 100, 'weight' => 25, 'measurement_unit' => 'tasks', 'is_active' => true],
            ['title' => 'Team Collaboration', 'description' => 'Communication and collaboration', 'category' => 'behavior', 'target_value' => 90, 'weight' => 20, 'measurement_unit' => '%', 'is_active' => true],
            ['title' => 'Initiative', 'description' => 'Proactiveness and problem solving', 'category' => 'behavior', 'target_value' => 85, 'weight' => 15, 'measurement_unit' => '%', 'is_active' => true],
            ['title' => 'Attendance & Discipline', 'description' => 'Punctuality and adherence to policy', 'category' => 'competency', 'target_value' => 95, 'weight' => 15, 'measurement_unit' => '%', 'is_active' => true],
        ];

        foreach ($kpis as $kpi) {
            Kpi::updateOrCreate(['title' => $kpi['title']], $kpi);
        }
    }
}
