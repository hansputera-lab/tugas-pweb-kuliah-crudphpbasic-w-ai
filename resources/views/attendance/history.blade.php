@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">My Attendance History</h1>
            <p class="mt-1 text-sm text-gray-500">View your personal attendance records</p>
        </div>

        {{-- Filters --}}
        <div class="mb-6 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
            <form action="{{ route('my.attendance') }}" method="GET" class="flex flex-col gap-4 sm:flex-row sm:items-end">
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
                <div class="flex gap-2">
                    <button type="submit" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">Filter</button>
                    <a href="{{ route('my.attendance') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Reset</a>
                </div>
            </form>
        </div>

        {{-- Summary Cards --}}
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-4">
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
                <p class="text-sm text-gray-500">Total Working Days</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $totalDays ?? 0 }}</p>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
                <p class="text-sm text-gray-500">Present</p>
                <p class="mt-1 text-2xl font-bold text-green-600">{{ $presentDays ?? 0 }}</p>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
                <p class="text-sm text-gray-500">Late</p>
                <p class="mt-1 text-2xl font-bold text-yellow-600">{{ $lateDays ?? 0 }}</p>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
                <p class="text-sm text-gray-500">Absent</p>
                <p class="mt-1 text-2xl font-bold text-red-600">{{ $absentDays ?? 0 }}</p>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Day</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Check In</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Check Out</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Hours</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($attendances ?? [] as $att)
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::parse($att->date)->format('l') }}</td>
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
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                                    No attendance records found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($attendances) && $attendances instanceof \Illuminate\Pagination\LengthAwarePaginator && $attendances->hasPages())
                <div class="border-t border-gray-200 bg-white px-6 py-3">
                    {{ $attendances->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
