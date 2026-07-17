<?php

namespace Database\Seeders;

use App\Domains\Settings\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(['key' => 'work_start_time'], ['value' => '08:00', 'type' => 'string', 'description' => 'Office start time']);
        Setting::updateOrCreate(['key' => 'work_end_time'], ['value' => '17:00', 'type' => 'string', 'description' => 'Office end time']);
        Setting::updateOrCreate(['key' => 'grace_period_minutes'], ['value' => '15', 'type' => 'integer', 'description' => 'Minutes after start time before marked late']);
        Setting::updateOrCreate(['key' => 'company_name'], ['value' => 'HRIS System', 'type' => 'string', 'description' => 'Company name']);
        Setting::updateOrCreate(['key' => 'default_annual_leave_days'], ['value' => '12', 'type' => 'integer', 'description' => 'Default annual leave days per year']);
        Setting::updateOrCreate(['key' => 'default_sick_leave_days'], ['value' => '12', 'type' => 'integer', 'description' => 'Default sick leave days per year']);

        // Payroll configuration
        Setting::updateOrCreate(['key' => 'payroll_working_days'], ['value' => '22', 'type' => 'integer', 'description' => 'Working days per month used to compute daily rate']);
        Setting::updateOrCreate(['key' => 'payroll_late_deduction_rate'], ['value' => '0.5', 'type' => 'string', 'description' => 'Late deduction rate (fraction of daily rate per late day)']);
        Setting::updateOrCreate(['key' => 'payroll_absent_deduction_rate'], ['value' => '1.0', 'type' => 'string', 'description' => 'Absent deduction rate (fraction of daily rate per absent day)']);
        Setting::updateOrCreate(['key' => 'payroll_ot_hourly_multiplier'], ['value' => '1.5', 'type' => 'string', 'description' => 'Overtime hourly rate multiplier of regular hourly rate']);

        // KPI grade thresholds
        Setting::updateOrCreate(['key' => 'kpi_grade_a_min'], ['value' => '90', 'type' => 'integer', 'description' => 'Minimum score for grade A']);
        Setting::updateOrCreate(['key' => 'kpi_grade_b_min'], ['value' => '80', 'type' => 'integer', 'description' => 'Minimum score for grade B']);
        Setting::updateOrCreate(['key' => 'kpi_grade_c_min'], ['value' => '70', 'type' => 'integer', 'description' => 'Minimum score for grade C']);
        Setting::updateOrCreate(['key' => 'kpi_grade_d_min'], ['value' => '60', 'type' => 'integer', 'description' => 'Minimum score for grade D']);

        // Branding
        Setting::updateOrCreate(['key' => 'logo_light'], ['value' => '', 'type' => 'string', 'description' => 'Logo for light backgrounds (PNG/SVG)']);
        Setting::updateOrCreate(['key' => 'logo_dark'], ['value' => '', 'type' => 'string', 'description' => 'Logo for dark backgrounds (PNG/SVG)']);
        Setting::updateOrCreate(['key' => 'favicon'], ['value' => '', 'type' => 'string', 'description' => 'Favicon (ICO/PNG/SVG)']);
    }
}
