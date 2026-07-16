@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Payroll</h1>
                <p class="mt-1 text-sm text-gray-500">Generate and manage employee payroll periods</p>
            </div>
        </div>

        {{-- Config summary --}}
        <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Working Days</p>
                <p class="mt-1 text-xl font-bold text-gray-900">{{ $config['working_days'] }}</p>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Late Deduction</p>
                <p class="mt-1 text-xl font-bold text-gray-900">{{ number_format($config['late_deduction_rate'] * 100, 0) }}%</p>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Absent Deduction</p>
                <p class="mt-1 text-xl font-bold text-gray-900">{{ number_format($config['absent_deduction_rate'] * 100, 0) }}%</p>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">OT Multiplier</p>
                <p class="mt-1 text-xl font-bold text-gray-900">{{ $config['ot_hourly_multiplier'] }}x</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Generate form --}}
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5">
                <h2 class="text-base font-semibold text-gray-900">Generate Payroll</h2>
                <p class="mt-1 text-sm text-gray-500">Create or recalculate a payroll period.</p>
                <form action="{{ route('payroll.generate') }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label for="month" class="block text-sm font-medium text-gray-700">Month</label>
                        <select name="month" id="month" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromDate(null, $m, 1)->translatedFormat('F') }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                        <select name="year" id="year" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                            @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                                <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        Generate
                    </button>
                </form>
            </div>

            {{-- Periods list --}}
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 lg:col-span-2">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900">Payroll Periods</h2>
                    <a href="{{ route('payroll.components') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">Components</a>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Period</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($periodList as $period)
                                <tr class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $period->label }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                            {{ $period->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $period->status === 'finalized' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $period->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                            {{ ucfirst($period->status) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-medium">
                                        <a href="{{ route('payroll.show', $period) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-10 text-center text-sm text-gray-500">No payroll periods yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
