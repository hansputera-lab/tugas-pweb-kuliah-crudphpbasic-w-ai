<?php

namespace Database\Seeders;

use App\Domains\Settings\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::create(['key' => 'work_start_time', 'value' => '08:00', 'type' => 'string', 'description' => 'Office start time']);
        Setting::create(['key' => 'work_end_time', 'value' => '17:00', 'type' => 'string', 'description' => 'Office end time']);
        Setting::create(['key' => 'grace_period_minutes', 'value' => '15', 'type' => 'integer', 'description' => 'Minutes after start time before marked late']);
        Setting::create(['key' => 'company_name', 'value' => 'HRIS System', 'type' => 'string', 'description' => 'Company name']);
        Setting::create(['key' => 'default_annual_leave_days', 'value' => '12', 'type' => 'integer', 'description' => 'Default annual leave days per year']);
        Setting::create(['key' => 'default_sick_leave_days', 'value' => '12', 'type' => 'integer', 'description' => 'Default sick leave days per year']);

        // Payroll configuration
        Setting::create(['key' => 'payroll_working_days', 'value' => '22', 'type' => 'integer', 'description' => 'Working days per month used to compute daily rate']);
        Setting::create(['key' => 'payroll_late_deduction_rate', 'value' => '0.5', 'type' => 'string', 'description' => 'Late deduction rate (fraction of daily rate per late day)']);
        Setting::create(['key' => 'payroll_absent_deduction_rate', 'value' => '1.0', 'type' => 'string', 'description' => 'Absent deduction rate (fraction of daily rate per absent day)']);
        Setting::create(['key' => 'payroll_ot_hourly_multiplier', 'value' => '1.5', 'type' => 'string', 'description' => 'Overtime hourly rate multiplier of regular hourly rate']);

        // KPI grade thresholds
        Setting::create(['key' => 'kpi_grade_a_min', 'value' => '90', 'type' => 'integer', 'description' => 'Minimum score for grade A']);
        Setting::create(['key' => 'kpi_grade_b_min', 'value' => '80', 'type' => 'integer', 'description' => 'Minimum score for grade B']);
        Setting::create(['key' => 'kpi_grade_c_min', 'value' => '70', 'type' => 'integer', 'description' => 'Minimum score for grade C']);
        Setting::create(['key' => 'kpi_grade_d_min', 'value' => '60', 'type' => 'integer', 'description' => 'Minimum score for grade D']);
    }
}
