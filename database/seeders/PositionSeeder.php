<?php

namespace Database\Seeders;

use App\Domains\Department\Models\Department;
use App\Domains\Position\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $hr = Department::where('code', 'HR')->first();
        $it = Department::where('code', 'IT')->first();
        $fin = Department::where('code', 'FIN')->first();
        $mkt = Department::where('code', 'MKT')->first();
        $ops = Department::where('code', 'OPS')->first();

        Position::create(['department_id' => $hr->id, 'name' => 'HR Director', 'code' => 'HR-DIR', 'base_salary' => 15000000, 'level' => 1, 'default_annual_leave_days' => 15, 'default_sick_leave_days' => 12]);
        Position::create(['department_id' => $hr->id, 'name' => 'HR Manager', 'code' => 'HR-MGR', 'base_salary' => 10000000, 'level' => 2, 'default_annual_leave_days' => 12, 'default_sick_leave_days' => 12]);
        Position::create(['department_id' => $hr->id, 'name' => 'HR Staff', 'code' => 'HR-STF', 'base_salary' => 5000000, 'level' => 3, 'default_annual_leave_days' => 12, 'default_sick_leave_days' => 12]);

        Position::create(['department_id' => $it->id, 'name' => 'IT Director', 'code' => 'IT-DIR', 'base_salary' => 18000000, 'level' => 1, 'default_annual_leave_days' => 15, 'default_sick_leave_days' => 12]);
        Position::create(['department_id' => $it->id, 'name' => 'IT Manager', 'code' => 'IT-MGR', 'base_salary' => 12000000, 'level' => 2, 'default_annual_leave_days' => 12, 'default_sick_leave_days' => 12]);
        Position::create(['department_id' => $it->id, 'name' => 'Senior Developer', 'code' => 'IT-SRD', 'base_salary' => 9000000, 'level' => 3, 'default_annual_leave_days' => 12, 'default_sick_leave_days' => 12]);
        Position::create(['department_id' => $it->id, 'name' => 'Junior Developer', 'code' => 'IT-JRD', 'base_salary' => 5500000, 'level' => 4, 'default_annual_leave_days' => 12, 'default_sick_leave_days' => 12]);

        Position::create(['department_id' => $fin->id, 'name' => 'Finance Director', 'code' => 'FN-DIR', 'base_salary' => 16000000, 'level' => 1, 'default_annual_leave_days' => 15, 'default_sick_leave_days' => 12]);
        Position::create(['department_id' => $fin->id, 'name' => 'Accountant', 'code' => 'FN-ACC', 'base_salary' => 6000000, 'level' => 3, 'default_annual_leave_days' => 12, 'default_sick_leave_days' => 12]);

        Position::create(['department_id' => $mkt->id, 'name' => 'Marketing Manager', 'code' => 'MK-MGR', 'base_salary' => 11000000, 'level' => 2, 'default_annual_leave_days' => 12, 'default_sick_leave_days' => 12]);
        Position::create(['department_id' => $mkt->id, 'name' => 'Marketing Staff', 'code' => 'MK-STF', 'base_salary' => 5500000, 'level' => 3, 'default_annual_leave_days' => 12, 'default_sick_leave_days' => 12]);

        Position::create(['department_id' => $ops->id, 'name' => 'Operations Manager', 'code' => 'OP-MGR', 'base_salary' => 10000000, 'level' => 2, 'default_annual_leave_days' => 12, 'default_sick_leave_days' => 12]);
        Position::create(['department_id' => $ops->id, 'name' => 'Operations Staff', 'code' => 'OP-STF', 'base_salary' => 5000000, 'level' => 3, 'default_annual_leave_days' => 12, 'default_sick_leave_days' => 12]);
    }
}
