<?php

namespace Database\Seeders;

use App\Domains\Employee\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::where('status', 'active')->get();

        for ($i = 1; $i <= 10; $i++) {
            $date = now()->subDays($i)->toDateString();

            foreach ($employees as $employee) {
                $rand = rand(1, 10);
                $status = $rand <= 6 ? 'present' : ($rand <= 8 ? 'late' : 'absent');

                $checkIn = $status === 'absent' ? null : ($status === 'late' ? '08:20:00' : '07:45:00');
                $checkOut = $status === 'absent' ? null : '17:00:00';

                DB::table('attendances')->insert([
                    'employee_id' => $employee->id,
                    'date' => $date,
                    'check_in_time' => $checkIn,
                    'check_out_time' => $checkOut,
                    'status' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
