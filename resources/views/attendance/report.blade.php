@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Attendance Report</h1>
            <p class="mt-1 text-sm text-gray-500">Summary of employee attendance</p>
        </div>

        {{-- Filters --}}
        <div class="mb-6 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
            <form action="{{ route('attendance.report') }}" method="GET" class="flex flex-col gap-4 sm:flex-row sm:items-end">
                <div class="w-full sm:w-40">
                    <label for="month" class="block text-sm font-medium text-gray-700">Month</label>
                    <select name="month" id="month" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>{{ Carbon\Carbon::create()->month($m)->format('F') }}</option>
                        @endfor
                    </select>
                </div>
                <div class="w-full sm:w-32">
                    <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                    <select name="year" id="year" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                            <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="w-full sm:w-48">
                    <label for="department_id" class="block text-sm font-medium text-gray-700">Department</label>
                    <select name="department_id" id="department_id" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">All Departments</option>
                        @foreach($departments ?? [] as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Filter</button>
                    <a href="{{ route('attendance.report') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Reset</a>
                </div>
            </form>
        </div>

        {{-- Summary Cards --}}
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-4">
            <x-stats-card title="Total Employees" value="{{ $totalEmployees ?? 0 }}" icon="users" color="blue" />
            <x-stats-card title="Avg. Attendance" value="{{ $avgAttendance ?? '0%' }}%" icon="chart-bar" color="green" />
            <x-stats-card title="Total Late" value="{{ $totalLate ?? 0 }}" icon="clock" color="yellow" />
            <x-stats-card title="Total Absent" value="{{ $totalAbsent ?? 0 }}" icon="document" color="red" />
        </div>

        {{-- Report Table --}}
        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="border-b border-gray-100 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">Attendance Summary by Employee</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Department</th>
                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Working Days</th>
                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Present</th>
                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Late</th>
                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Absent</th>
                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Attendance %</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($reportData ?? [] as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ $row['employee_name'] }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $row['department'] }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-center text-sm text-gray-500">{{ $row['working_days'] }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-center text-sm text-green-600 font-medium">{{ $row['present'] }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-center text-sm text-yellow-600 font-medium">{{ $row['late'] }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-center text-sm text-red-600 font-medium">{{ $row['absent'] }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    @php
                                        $percentage = $row['working_days'] > 0 ? round(($row['present'] / $row['working_days']) * 100) : 0;
                                    @endphp
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="h-2 w-16 overflow-hidden rounded-full bg-gray-200">
                                            <div class="h-full rounded-full {{ $percentage >= 80 ? 'bg-green-500' : ($percentage >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium text-gray-700">{{ $percentage }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">
                                    No data available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
