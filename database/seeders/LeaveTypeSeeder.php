<?php

namespace Database\Seeders;

use App\Domains\Leave\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        LeaveType::create(['name' => 'Annual Leave', 'code' => 'ANNUAL', 'days_per_year' => 12, 'is_paid' => true, 'description' => 'Paid annual leave']);
        LeaveType::create(['name' => 'Sick Leave', 'code' => 'SICK', 'days_per_year' => 12, 'is_paid' => true, 'description' => 'Paid sick leave']);
        LeaveType::create(['name' => 'Unpaid Leave', 'code' => 'UNPAID', 'days_per_year' => 0, 'is_paid' => false, 'description' => 'Unpaid leave']);
        LeaveType::create(['name' => 'Maternity Leave', 'code' => 'MATERNITY', 'days_per_year' => 90, 'is_paid' => true, 'description' => 'Maternity leave']);
        LeaveType::create(['name' => 'Personal Leave', 'code' => 'PERSONAL', 'days_per_year' => 3, 'is_paid' => true, 'description' => 'Personal leave for urgent matters']);
    }
}
