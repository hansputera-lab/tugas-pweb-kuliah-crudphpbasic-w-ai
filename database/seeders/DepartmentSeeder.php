<?php

namespace Database\Seeders;

use App\Domains\Department\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        Department::create(['name' => 'Human Resources', 'code' => 'HR', 'description' => 'Human Resources Department']);
        Department::create(['name' => 'Information Technology', 'code' => 'IT', 'description' => 'IT Department']);
        Department::create(['name' => 'Finance', 'code' => 'FIN', 'description' => 'Finance and Accounting']);
        Department::create(['name' => 'Marketing', 'code' => 'MKT', 'description' => 'Marketing and Sales']);
        Department::create(['name' => 'Operations', 'code' => 'OPS', 'description' => 'Operations Department']);
    }
}
