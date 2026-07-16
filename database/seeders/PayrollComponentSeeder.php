<?php

namespace Database\Seeders;

use App\Domains\Payroll\Models\PayrollComponent;
use Illuminate\Database\Seeder;

class PayrollComponentSeeder extends Seeder
{
    public function run(): void
    {
        $components = [
            ['name' => 'Tunjangan Transport', 'type' => 'allowance', 'calculation' => 'fixed', 'value' => 500000, 'is_active' => true, 'sort_order' => 1],
            ['name' => 'Tunjangan Makan', 'type' => 'allowance', 'calculation' => 'fixed', 'value' => 500000, 'is_active' => true, 'sort_order' => 2],
            ['name' => 'Tunjangan Jabatan', 'type' => 'allowance', 'calculation' => 'percentage', 'value' => 5, 'is_active' => false, 'sort_order' => 3],
            ['name' => 'Pajak Penghasilan', 'type' => 'deduction', 'calculation' => 'percentage', 'value' => 5, 'is_active' => true, 'sort_order' => 1],
            ['name' => 'BPJS Kesehatan', 'type' => 'deduction', 'calculation' => 'percentage', 'value' => 1, 'is_active' => true, 'sort_order' => 2],
            ['name' => 'BPJS JHT', 'type' => 'deduction', 'calculation' => 'percentage', 'value' => 2, 'is_active' => true, 'sort_order' => 3],
        ];

        foreach ($components as $component) {
            PayrollComponent::updateOrCreate(
                ['name' => $component['name']],
                $component
            );
        }
    }
}
