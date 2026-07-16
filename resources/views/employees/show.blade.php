@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('employees.index') }}" class="hover:text-gray-700">Employees</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900">{{ $employee->full_name }}</span>
            </div>
            <div class="mt-2 flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Employee Detail</h1>
                <div class="flex items-center gap-2">
                    @if(auth()->user()->hasPermissionTo('manage_employees'))
                        @if($employee->status !== 'suspended')
                            <form action="{{ route('employees.suspend', $employee) }}" method="POST" onsubmit="return confirm('Suspend {{ $employee->full_name }}? They will be unable to log in.')">
                                @csrf
                                <button type="submit" class="inline-flex items-center rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-orange-500">
                                    Suspend
                                </button>
                            </form>
                        @else
                            <form action="{{ route('employees.unsuspend', $employee) }}" method="POST" onsubmit="return confirm('Reactivate {{ $employee->full_name }}?')">
                                @csrf
                                <button type="submit" class="inline-flex items-center rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                                    Reactivate
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('employees.edit', $employee) }}" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Profile Header --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
            <div class="flex flex-col items-center gap-6 sm:flex-row">
                @if($employee->photo)
                    <img src="{{ Storage::url($employee->photo) }}" alt="{{ $employee->full_name }}" class="h-24 w-24 rounded-full object-cover ring-4 ring-gray-100">
                @else
                    <div class="flex h-24 w-24 items-center justify-center rounded-full bg-blue-100 text-2xl font-bold text-blue-600 ring-4 ring-gray-100">
                        {{ strtoupper(substr($employee->full_name, 0, 2)) }}
                    </div>
                @endif
                <div class="text-center sm:text-left">
                    <h2 class="text-xl font-bold text-gray-900">{{ $employee->full_name }}</h2>
                    <p class="text-sm text-gray-500">NIP: {{ $employee->nip }}</p>
                    <div class="mt-2 flex flex-wrap items-center justify-center gap-2 sm:justify-start">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                            {{ $employee->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $employee->status === 'inactive' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $employee->status === 'suspended' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($employee->status) }}
                        </span>
                        <span class="text-sm text-gray-500">{{ $employee->department->name ?? '-' }} / {{ $employee->currentPosition()?->name ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Personal Information --}}
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
                <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                <div class="mt-4 space-y-4">
                    <div class="flex justify-between border-b border-gray-100 pb-3">
                        <span class="text-sm text-gray-500">Full Name</span>
                        <span class="text-sm font-medium text-gray-900">{{ $employee->full_name }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-3">
                        <span class="text-sm text-gray-500">Email</span>
                        <span class="text-sm font-medium text-gray-900">{{ $employee->user->email ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-3">
                        <span class="text-sm text-gray-500">Gender</span>
                        <span class="text-sm font-medium text-gray-900">{{ ucfirst($employee->gender ?? '-') }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-3">
                        <span class="text-sm text-gray-500">Date of Birth</span>
                        <span class="text-sm font-medium text-gray-900">{{ $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('d M Y') : '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-3">
                        <span class="text-sm text-gray-500">Phone</span>
                        <span class="text-sm font-medium text-gray-900">{{ $employee->phone ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Address</span>
                        <span class="text-sm font-medium text-gray-900 text-right max-w-xs">{{ $employee->address ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Employment Information --}}
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
                <h3 class="text-lg font-semibold text-gray-900">Employment Information</h3>
                <div class="mt-4 space-y-4">
                    <div class="flex justify-between border-b border-gray-100 pb-3">
                        <span class="text-sm text-gray-500">NIP</span>
                        <span class="text-sm font-medium text-gray-900">{{ $employee->nip }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-3">
                        <span class="text-sm text-gray-500">Department</span>
                        <span class="text-sm font-medium text-gray-900">{{ $employee->department->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-3">
                        <span class="text-sm text-gray-500">Position</span>
                        <span class="text-sm font-medium text-gray-900">{{ $employee->currentPosition()?->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-3">
                        <span class="text-sm text-gray-500">Reports To</span>
                        <span class="text-sm font-medium text-gray-900">{{ $employee->manager->full_name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-3">
                        <span class="text-sm text-gray-500">Join Date</span>
                        <span class="text-sm font-medium text-gray-900">{{ $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('d M Y') : '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Status</span>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                            {{ $employee->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $employee->status === 'inactive' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $employee->status === 'suspended' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($employee->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Leave Balances --}}
        <div class="mt-6 rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
            <h3 class="text-lg font-semibold text-gray-900">Leave Balances</h3>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @forelse($employee->leaveBalances ?? [] as $balance)
                    <div class="rounded-lg border border-gray-200 p-4">
                        <p class="text-sm font-medium text-gray-500">{{ $balance->leaveType->name }}</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $balance->remaining }}</p>
                        <p class="text-xs text-gray-400">of {{ $balance->total }} days</p>
                        <div class="mt-2 h-2 overflow-hidden rounded-full bg-gray-100">
                            <div class="h-full rounded-full bg-blue-500" style="width: {{ $balance->total > 0 ? ($balance->remaining / $balance->total) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="col-span-4 text-sm text-gray-500">No leave balances found</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Attendance --}}
        <div class="mt-6 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="border-b border-gray-100 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Attendance</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Check In</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Check Out</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Hours</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($employee->attendances->take(7) ?? [] as $att)
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $att->check_in_time ? \Carbon\Carbon::parse($att->check_in_time)->format('H:i') : '-' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $att->check_out_time ? \Carbon\Carbon::parse($att->check_out_time)->format('H:i') : '-' }}</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ $att->status === 'present' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $att->status === 'late' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $att->status === 'absent' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($att->status) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $att->work_hours ?? '-' }}h</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">No attendance records</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
