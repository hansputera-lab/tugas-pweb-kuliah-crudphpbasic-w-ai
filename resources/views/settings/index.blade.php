@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
            <p class="mt-1 text-sm text-gray-500">Configure application settings</p>
        </div>

        <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Company Settings --}}
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
                <h2 class="text-lg font-semibold text-gray-900">Company Settings</h2>
                <p class="mt-1 text-sm text-gray-500">Basic company information</p>

                <div class="mt-6 space-y-4">
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name</label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $settings['company_name'] ?? '') }}"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @error('company_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Work Schedule --}}
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
                <h2 class="text-lg font-semibold text-gray-900">Work Schedule</h2>
                <p class="mt-1 text-sm text-gray-500">Define working hours and attendance rules</p>

                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="work_start_time" class="block text-sm font-medium text-gray-700">Work Start Time</label>
                        <input type="time" name="work_start_time" id="work_start_time" value="{{ old('work_start_time', $settings['work_start_time'] ?? '08:00') }}"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @error('work_start_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="work_end_time" class="block text-sm font-medium text-gray-700">Work End Time</label>
                        <input type="time" name="work_end_time" id="work_end_time" value="{{ old('work_end_time', $settings['work_end_time'] ?? '17:00') }}"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @error('work_end_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="grace_period_minutes" class="block text-sm font-medium text-gray-700">Grace Period (minutes)</label>
                        <input type="number" name="grace_period_minutes" id="grace_period_minutes" value="{{ old('grace_period_minutes', $settings['grace_period_minutes'] ?? 15) }}"
                               min="0" max="60"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-400">Minutes after start time before marking as late</p>
                        @error('grace_period_minutes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Payroll Settings --}}
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
                <h2 class="text-lg font-semibold text-gray-900">Payroll Settings</h2>
                <p class="mt-1 text-sm text-gray-500">Configure payroll calculation rules (customizable per company)</p>

                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="payroll_working_days" class="block text-sm font-medium text-gray-700">Working Days / Month</label>
                        <input type="number" name="payroll_working_days" id="payroll_working_days" value="{{ old('payroll_working_days', $settings['payroll_working_days'] ?? 22) }}"
                               min="1" max="31"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @error('payroll_working_days') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="payroll_late_deduction_rate" class="block text-sm font-medium text-gray-700">Late Deduction Rate</label>
                        <input type="number" step="0.01" name="payroll_late_deduction_rate" id="payroll_late_deduction_rate" value="{{ old('payroll_late_deduction_rate', $settings['payroll_late_deduction_rate'] ?? 0.5) }}"
                               min="0" max="1"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-400">Fraction of daily rate deducted per late day (0.5 = 50%)</p>
                        @error('payroll_late_deduction_rate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="payroll_absent_deduction_rate" class="block text-sm font-medium text-gray-700">Absent Deduction Rate</label>
                        <input type="number" step="0.01" name="payroll_absent_deduction_rate" id="payroll_absent_deduction_rate" value="{{ old('payroll_absent_deduction_rate', $settings['payroll_absent_deduction_rate'] ?? 1) }}"
                               min="0" max="2"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-400">Fraction of daily rate deducted per absent day (1 = full day)</p>
                        @error('payroll_absent_deduction_rate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="payroll_ot_hourly_multiplier" class="block text-sm font-medium text-gray-700">Overtime Multiplier</label>
                        <input type="number" step="0.1" name="payroll_ot_hourly_multiplier" id="payroll_ot_hourly_multiplier" value="{{ old('payroll_ot_hourly_multiplier', $settings['payroll_ot_hourly_multiplier'] ?? 1.5) }}"
                               min="1" max="5"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-400">Hourly OT rate = (daily rate / 8) x multiplier</p>
                        @error('payroll_ot_hourly_multiplier') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Leave Settings --}}
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
                <h2 class="text-lg font-semibold text-gray-900">Leave Settings</h2>
                <p class="mt-1 text-sm text-gray-500">Configure leave policies</p>

                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="default_annual_leave_days" class="block text-sm font-medium text-gray-700">Annual Leave Days</label>
                        <input type="number" name="default_annual_leave_days" id="default_annual_leave_days" value="{{ old('default_annual_leave_days', $settings['default_annual_leave_days'] ?? 12) }}"
                               min="0" max="30"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @error('default_annual_leave_days') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="default_sick_leave_days" class="block text-sm font-medium text-gray-700">Sick Leave Days</label>
                        <input type="number" name="default_sick_leave_days" id="default_sick_leave_days" value="{{ old('default_sick_leave_days', $settings['default_sick_leave_days'] ?? 12) }}"
                               min="0" max="30"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @error('default_sick_leave_days') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Performance Settings --}}
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
                <h2 class="text-lg font-semibold text-gray-900">KPI Grade Thresholds</h2>
                <p class="mt-1 text-sm text-gray-500">Minimum score for each grade (admin-customizable). Grades: A &ge; value, then B, C, D, else E.</p>

                <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div>
                        <label for="kpi_grade_a_min" class="block text-sm font-medium text-gray-700">Grade A &ge;</label>
                        <input type="number" name="kpi_grade_a_min" id="kpi_grade_a_min" value="{{ old('kpi_grade_a_min', $settings['kpi_grade_a_min'] ?? 90) }}"
                               min="0" max="100"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @error('kpi_grade_a_min') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="kpi_grade_b_min" class="block text-sm font-medium text-gray-700">Grade B &ge;</label>
                        <input type="number" name="kpi_grade_b_min" id="kpi_grade_b_min" value="{{ old('kpi_grade_b_min', $settings['kpi_grade_b_min'] ?? 80) }}"
                               min="0" max="100"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @error('kpi_grade_b_min') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="kpi_grade_c_min" class="block text-sm font-medium text-gray-700">Grade C &ge;</label>
                        <input type="number" name="kpi_grade_c_min" id="kpi_grade_c_min" value="{{ old('kpi_grade_c_min', $settings['kpi_grade_c_min'] ?? 70) }}"
                               min="0" max="100"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @error('kpi_grade_c_min') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="kpi_grade_d_min" class="block text-sm font-medium text-gray-700">Grade D &ge;</label>
                        <input type="number" name="kpi_grade_d_min" id="kpi_grade_d_min" value="{{ old('kpi_grade_d_min', $settings['kpi_grade_d_min'] ?? 60) }}"
                               min="0" max="100"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @error('kpi_grade_d_min') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('dashboard') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">Save Settings</button>
            </div>
        </form>
    </div>
</div>
@endsection
