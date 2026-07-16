@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500">Welcome back, {{ Auth::user()->name }}</p>
        </div>

        @if(auth()->user()->hasAnyPermission(['view_employees', 'view_reports', 'view_dashboard_admin']))
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <x-stats-card title="Total Employees" :value="$stats['total_employees']" />
                <x-stats-card title="Active Employees" :value="$stats['active_employees']" />
                <x-stats-card title="Present Today" :value="$stats['present_today']" />
                <x-stats-card title="Pending Leave" :value="$stats['pending_leave']" />
            </div>

            @if($payrollSummary)
                <div class="mt-5 rounded-xl bg-indigo-50 p-5 ring-1 ring-indigo-100">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-indigo-500">Latest Payroll &middot; {{ $payrollSummary['period'] }}</p>
                            <p class="mt-1 text-2xl font-bold text-indigo-900 tabular-nums">{{ currency($payrollSummary['total_net']) }}</p>
                            <p class="text-sm text-indigo-600">{{ $payrollSummary['employees'] }} employees &middot; {{ ucfirst($payrollSummary['status']) }}</p>
                        </div>
                        <a href="{{ route('payroll.show', \App\Domains\Payroll\Models\PayrollPeriod::orderByDesc('year')->orderByDesc('month')->first()) }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">View Payroll</a>
                    </div>
                </div>
            @endif

            <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
                    <h3 class="text-lg font-semibold text-gray-900">Monthly Attendance</h3>
                    <div class="mt-4" style="height: 300px;">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
                <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
                    <h3 class="text-lg font-semibold text-gray-900">Leave Requests</h3>
                    <div class="mt-4 flex items-center justify-center" style="height: 300px;">
                        <canvas id="leaveChart"></canvas>
                    </div>
                </div>
            </div>
        @endif

        @if($employee)
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <x-stats-card title="My Department" :value="$employee->department->name ?? 'N/A'" />
                <x-stats-card title="My Position" :value="$employee->currentPosition() ? $employee->currentPosition()->name : 'N/A'" />
                <x-stats-card title="Today Status" :value="$todayAttendance ? ucfirst($todayAttendance->status) : 'Not Checked In'" />
                <x-stats-card title="Leave Balance" :value="$leaveBalances->sum('remaining_days') . ' days'" />
            </div>

            <div class="mt-8 rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="flex flex-wrap gap-4">
                    @if(!$todayAttendance || !$todayAttendance->check_in_time)
                        <form method="POST" action="{{ route('attendance.check-in') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Check In
                            </button>
                        </form>
                    @elseif(!$todayAttendance->check_out_time)
                        <form method="POST" action="{{ route('attendance.check-out') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Check Out
                            </button>
                        </form>
                    @else
                        <span class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium">
                            Attendance completed for today
                        </span>
                    @endif
                    <a href="{{ route('leaves.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Request Leave
                    </a>
                </div>
            </div>

            @if($leaveBalances->count() > 0)
                <div class="mt-8 rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">My Leave Balances ({{ now()->year }})</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($leaveBalances as $balance)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">{{ $balance->leaveType->name }}</span>
                                    <span class="text-sm text-gray-500">{{ $balance->remaining_days }}/{{ $balance->total_days }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $balance->usage_percentage }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Used: {{ $balance->used_days }} days</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection

@push('scripts')
@php
    $attPresent = $chartData['attendance_present'][0] ?? 0;
    $attLate = $chartData['attendance_late'][0] ?? 0;
    $attAbsent = $chartData['attendance_absent'][0] ?? 0;
    $leaveLabels = $chartData['leave_labels'];
    $leaveData = $chartData['leave_data'];
    $attLabels = $chartData['attendance_labels'];
@endphp
<script>
document.addEventListener('DOMContentLoaded', function() {
    var attendanceCtx = document.getElementById('attendanceChart');
    if (attendanceCtx) {
        new Chart(attendanceCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($attLabels) !!},
                datasets: [{
                    label: 'Employees',
                    data: [{{ $attPresent }}, {{ $attLate }}, {{ $attAbsent }}],
                    backgroundColor: ['#22c55e', '#eab308', '#ef4444'],
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    }

    var leaveCtx = document.getElementById('leaveChart');
    if (leaveCtx) {
        new Chart(leaveCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($leaveLabels) !!},
                datasets: [{
                    data: {!! json_encode($leaveData) !!},
                    backgroundColor: ['#eab308', '#22c55e', '#ef4444'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
                cutout: '60%',
            }
        });
    }
});
</script>
@endpush
