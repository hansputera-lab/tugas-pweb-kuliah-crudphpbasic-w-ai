<?php

namespace Database\Seeders;

use App\Domains\Department\Models\Department;
use App\Domains\Employee\Models\Employee;
use App\Domains\Position\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $it = Department::where('code', 'IT')->first();
        $hr = Department::where('code', 'HR')->first();
        $finance = Department::where('code', 'FIN')->first() ?? Department::where('code', 'HR')->first();

        $itDev = Position::where('code', 'IT-JRD')->first();
        $itMgr = Position::where('code', 'IT-MGR')->first();
        $hrStf = Position::where('code', 'HR-STF')->first();

        $empUser = User::where('email', 'employee@hris.test')->first();
        $rina = User::where('email', 'rina@hris.test')->first();
        $budi = User::where('email', 'budi@hris.test')->first();
        $managerUser = User::where('email', 'manager@hris.test')->first();
        $payrollUser = User::where('email', 'payroll@hris.test')->first();
        $execUser = User::where('email', 'executive@hris.test')->first();
        $recruiterUser = User::where('email', 'recruiter@hris.test')->first();
        $itAdminUser = User::where('email', 'itadmin@hris.test')->first();
        $hrUser = User::where('email', 'hr@hris.test')->first();

        $budiEmp = Employee::create([
            'user_id' => $budi->id,
            'nip' => 'EMP003',
            'full_name' => 'Budi Santoso',
            'gender' => 'L',
            'date_of_birth' => '1990-03-10',
            'phone' => '081234567892',
            'address' => 'Jl. Buah Batu No. 50, Bandung',
            'join_date' => '2021-03-01',
            'status' => 'active',
            'department_id' => $it->id,
        ]);
        $budiEmp->positions()->attach($itMgr->id, ['start_date' => '2021-03-01', 'is_current' => true]);

        $emp = Employee::create([
            'user_id' => $empUser->id,
            'nip' => 'EMP001',
            'full_name' => 'Employee User',
            'gender' => 'L',
            'date_of_birth' => '1995-06-15',
            'phone' => '081234567890',
            'address' => 'Jl. Merdeka No. 10, Bandung',
            'join_date' => '2023-01-15',
            'status' => 'active',
            'department_id' => $it->id,
            'manager_id' => $budiEmp->id,
        ]);
        $emp->positions()->attach($itDev->id, ['start_date' => '2023-01-15', 'is_current' => true]);

        $emp2 = Employee::create([
            'user_id' => $rina->id,
            'nip' => 'EMP002',
            'full_name' => 'Rina Susanti',
            'gender' => 'P',
            'date_of_birth' => '1993-08-20',
            'phone' => '081234567891',
            'address' => 'Jl. Dago No. 25, Bandung',
            'join_date' => '2022-06-01',
            'status' => 'active',
            'department_id' => $hr->id,
        ]);
        $emp2->positions()->attach($hrStf->id, ['start_date' => '2022-06-01', 'is_current' => true]);

        $managerEmp = Employee::create([
            'user_id' => $managerUser->id,
            'nip' => 'EMP004',
            'full_name' => 'Manager User',
            'gender' => 'L',
            'date_of_birth' => '1988-11-20',
            'phone' => '081234567893',
            'address' => 'Jl. Setiabudi No. 15, Bandung',
            'join_date' => '2020-01-01',
            'status' => 'active',
            'department_id' => $it->id,
            'manager_id' => $budiEmp->id,
        ]);
        $managerEmp->positions()->attach($itMgr->id, ['start_date' => '2020-01-01', 'is_current' => true]);

        Employee::create([
            'user_id' => $hrUser->id,
            'nip' => 'EMP005',
            'full_name' => 'HR Manager',
            'gender' => 'P',
            'date_of_birth' => '1985-05-15',
            'phone' => '081234567894',
            'address' => 'Jl. Asia Afrika No. 8, Bandung',
            'join_date' => '2019-06-01',
            'status' => 'active',
            'department_id' => $hr->id,
        ])->positions()->attach($hrStf->id, ['start_date' => '2019-06-01', 'is_current' => true]);

        Employee::create([
            'user_id' => $payrollUser->id,
            'nip' => 'EMP006',
            'full_name' => 'Payroll Specialist',
            'gender' => 'P',
            'date_of_birth' => '1992-07-22',
            'phone' => '081234567895',
            'address' => 'Jl. Cihampelas No. 30, Bandung',
            'join_date' => '2022-03-01',
            'status' => 'active',
            'department_id' => $finance->id,
        ]);

        Employee::create([
            'user_id' => $execUser->id,
            'nip' => 'EMP007',
            'full_name' => 'Executive User',
            'gender' => 'L',
            'date_of_birth' => '1978-01-10',
            'phone' => '081234567896',
            'address' => 'Jl. Braga No. 5, Bandung',
            'join_date' => '2018-01-01',
            'status' => 'active',
            'department_id' => $hr->id,
        ]);

        Employee::create([
            'user_id' => $recruiterUser->id,
            'nip' => 'EMP008',
            'full_name' => 'Recruiter User',
            'gender' => 'P',
            'date_of_birth' => '1994-09-05',
            'phone' => '081234567897',
            'address' => 'Jl. Riau No. 12, Bandung',
            'join_date' => '2023-06-01',
            'status' => 'active',
            'department_id' => $hr->id,
        ]);

        Employee::create([
            'user_id' => $itAdminUser->id,
            'nip' => 'EMP009',
            'full_name' => 'IT Admin',
            'gender' => 'L',
            'date_of_birth' => '1987-04-18',
            'phone' => '081234567898',
            'address' => 'Jl. Sukajadi No. 22, Bandung',
            'join_date' => '2021-01-15',
            'status' => 'active',
            'department_id' => $it->id,
        ]);
    }
}
