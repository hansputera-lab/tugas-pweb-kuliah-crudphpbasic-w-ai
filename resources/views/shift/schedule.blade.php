@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Shift Schedule</h1>
                <p class="mt-1 text-sm text-gray-500">Monthly shift calendar per employee</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('shifts.assign') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Assign Shift</a>
                <a href="{{ route('overtime.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Overtime</a>
                <a href="{{ route('shifts.definitions') }}" class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Shift Definitions</a>
            </div>
        </div>

        <div class="mb-6 rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
            <form method="GET" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Employee</label>
                    <select name="employee_id" onchange="this.form.submit()" class="mt-1 block rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ $employee && $employee->id == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Month</label>
                    <input type="month" name="year" value="{{ $year }}-{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}" onchange="this.form.submit()" class="mt-1 block rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
            </form>
        </div>

        @if($employee)
            <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $employee->full_name }} &middot; {{ $monthLabel }}</h2>
                </div>
                <div class="p-4">
                    @include('shift._calendar', ['calendar' => $calendar, 'year' => $year, 'month' => $month])
                </div>
            </div>
        @else
            <p class="rounded-xl bg-white p-12 text-center text-sm text-gray-500 shadow-sm ring-1 ring-gray-950/5">No employees found.</p>
        @endif
    </div>
</div>
@endsection
