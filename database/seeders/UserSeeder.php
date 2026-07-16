<?php

namespace Database\Seeders;

use App\Domains\Auth\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Super Admin', 'email' => 'admin@hris.test', 'role' => 'super_admin'],
            ['name' => 'HR Manager', 'email' => 'hr@hris.test', 'role' => 'hr_manager'],
            ['name' => 'Employee User', 'email' => 'employee@hris.test', 'role' => 'employee'],
            ['name' => 'Rina Susanti', 'email' => 'rina@hris.test', 'role' => 'employee'],
            ['name' => 'Budi Santoso', 'email' => 'budi@hris.test', 'role' => 'employee'],
            ['name' => 'Manager User', 'email' => 'manager@hris.test', 'role' => 'manager'],
            ['name' => 'Payroll Specialist', 'email' => 'payroll@hris.test', 'role' => 'payroll_specialist'],
            ['name' => 'Executive User', 'email' => 'executive@hris.test', 'role' => 'executive'],
            ['name' => 'Recruiter User', 'email' => 'recruiter@hris.test', 'role' => 'recruiter'],
            ['name' => 'IT Admin', 'email' => 'itadmin@hris.test', 'role' => 'it_admin'],
        ];

        foreach ($users as $data) {
            $user = User::create(array_merge($data, [
                'password' => Hash::make('password'),
            ]));

            $role = Role::where('name', $data['role'])->first();
            if ($role) {
                $user->userRoles()->attach($role->id);
            }
        }
    }
}
