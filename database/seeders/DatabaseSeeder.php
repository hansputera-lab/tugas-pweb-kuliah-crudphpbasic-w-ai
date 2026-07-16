<?php

namespace Database\Seeders;

use App\Domains\Department\Models\Department;
use App\Domains\Employee\Models\Employee;
use App\Domains\Leave\Models\LeaveBalance;
use App\Domains\Leave\Models\LeaveType;
use App\Domains\Position\Models\Position;
use App\Domains\Settings\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            UserSeeder::class,
            DepartmentSeeder::class,
            PositionSeeder::class,
            EmployeeSeeder::class,
            LeaveTypeSeeder::class,
            LeaveBalanceSeeder::class,
            AttendanceSeeder::class,
            SettingSeeder::class,
            PayrollComponentSeeder::class,
            ExpenseCategorySeeder::class,
            ShiftSeeder::class,
            KpiSeeder::class,
        ]);
    }
}
