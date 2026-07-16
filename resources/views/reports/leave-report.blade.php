@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Leave Report</h1>
            <p class="mt-1 text-sm text-gray-500">Annual leave request statistics</p>
        </div>

        {{-- Filters --}}
        <div class="mb-6 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
            <form action="{{ route('reports.leaves') }}" method="GET" class="flex flex-col gap-4 sm:flex-row sm:items-end">
                <div class="w-full sm:w-32">
                    <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                    <select name="year" id="year" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @for($y = now()->year - 3; $y <= now()->year; $y++)
                            <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="w-full sm:w-48">
                    <label for="leave_type_id" class="block text-sm font-medium text-gray-700">Leave Type</label>
                    <select name="leave_type_id" id="leave_type_id" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">All Types</option>
                        @foreach($leaveTypes ?? [] as $type)
                            <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
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
                    <a href="{{ route('reports.leaves') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Reset</a>
                </div>
            </form>
        </div>

        {{-- Summary Cards --}}
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <x-stats-card title="Total Requests" value="{{ $totalRequests ?? 0 }}" icon="document" color="blue" />
            <x-stats-card title="Approved" value="{{ $approvedCount ?? 0 }}" icon="check-circle" color="green" />
            <x-stats-card title="Pending" value="{{ $pendingCount ?? 0 }}" icon="clock" color="yellow" />
            <x-stats-card title="Rejected" value="{{ $rejectedCount ?? 0 }}" icon="document" color="red" />
            <x-stats-card title="Total Days" value="{{ $totalDays ?? 0 }}" icon="calendar" color="purple" />
        </div>

        {{-- Charts --}}
        <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- By Type --}}
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
                <h3 class="text-lg font-semibold text-gray-900">Leave by Type</h3>
                <div class="mt-4 flex items-center justify-center" style="height: 280px;">
                    <canvas id="leaveTypeChart"></canvas>
                </div>
            </div>

            {{-- Monthly Trend --}}
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
                <h3 class="text-lg font-semibold text-gray-900">Monthly Trend</h3>
                <div class="mt-4" style="height: 280px;">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>
        </div>

        {{-- By Department --}}
        <div class="mb-6 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="border-b border-gray-100 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">Leave by Department</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Department</th>
                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Total Requests</th>
                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Approved</th>
                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Pending</th>
                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Rejected</th>
                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Total Days</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($departmentLeaveData ?? [] as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ $row['department'] }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-center text-sm text-gray-500">{{ $row['total'] }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-center text-sm text-green-600 font-medium">{{ $row['approved'] }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-center text-sm text-yellow-600 font-medium">{{ $row['pending'] }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-center text-sm text-red-600 font-medium">{{ $row['rejected'] }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-center text-sm text-gray-500 font-medium">{{ $row['total_days'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Leave by Type Doughnut
        const typeCtx = document.getElementById('leaveTypeChart');
        if (typeCtx) {
            new Chart(typeCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($leaveTypeNames ?? []) !!},
                    datasets: [{
                        data: {!! json_encode($leaveTypeCounts ?? []) !!},
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(234, 179, 8, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(168, 85, 247, 0.8)',
                        ],
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

        // Monthly Trend Line
        const trendCtx = document.getElementById('monthlyTrendChart');
        if (trendCtx) {
            new Chart(trendCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlyLabels ?? ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']) !!},
                    datasets: [{
                        label: 'Approved',
                        data: {!! json_encode($monthlyApproved ?? []) !!},
                        borderColor: 'rgba(34, 197, 94, 1)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        fill: true,
                        tension: 0.4,
                    }, {
                        label: 'Rejected',
                        data: {!! json_encode($monthlyRejected ?? []) !!},
                        borderColor: 'rgba(239, 68, 68, 1)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        fill: true,
                        tension: 0.4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
