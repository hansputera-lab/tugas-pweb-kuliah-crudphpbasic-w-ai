@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Employee Report</h1>
            <p class="mt-1 text-sm text-gray-500">Comprehensive employee listing</p>
        </div>

        {{-- Filters --}}
        <div class="mb-6 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
            <form action="{{ route('reports.employees') }}" method="GET" class="flex flex-col gap-4 sm:flex-row sm:items-end">
                <div class="w-full sm:w-48">
                    <label for="department_id" class="block text-sm font-medium text-gray-700">Department</label>
                    <select name="department_id" id="department_id" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">All Departments</option>
                        @foreach($departments ?? [] as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-40">
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="probation" {{ request('status') === 'probation' ? 'selected' : '' }}>Probation</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Filter</button>
                    <a href="{{ route('reports.employees') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Reset</a>
                </div>
            </form>
        </div>

        {{-- Summary --}}
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-4">
            <x-stats-card title="Total Employees" value="{{ $totalEmployees ?? 0 }}" icon="users" color="blue" />
            <x-stats-card title="Active" value="{{ $activeCount ?? 0 }}" icon="check-circle" color="green" />
            <x-stats-card title="Suspended" value="{{ $suspendedCount ?? 0 }}" icon="clock" color="red" />
            <x-stats-card title="Inactive" value="{{ $inactiveCount ?? 0 }}" icon="document" color="red" />
        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">NIP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Position</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Join Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($employees ?? [] as $employee)
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ $employee->nip }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ $employee->full_name }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $employee->department->name ?? '-' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $employee->currentPosition()?->name ?? '-' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('d M Y') : '-' }}</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ $employee->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $employee->status === 'inactive' ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $employee->status === 'probation' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                        {{ ucfirst($employee->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                                    No employees found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($employees) && $employees instanceof \Illuminate\Pagination\LengthAwarePaginator && $employees->hasPages())
                <div class="border-t border-gray-200 bg-white px-6 py-3">
                    {{ $employees->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
