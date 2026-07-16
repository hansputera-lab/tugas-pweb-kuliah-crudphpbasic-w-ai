@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('performance.appraisals') }}" class="hover:text-gray-700">Performance</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-900">New Appraisal</span>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">Create Appraisal</h1>
            <p class="mt-1 text-sm text-gray-500">Each active KPI will be added as a scoring line. You can score them next.</p>
        </div>

        <form action="{{ route('performance.appraisals.store') }}" method="POST" class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
            @csrf
            <div class="space-y-5">
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700">Employee</label>
                    <select name="employee_id" id="employee_id" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                        <option value="">Select Employee</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->department->name ?? '' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="period" class="block text-sm font-medium text-gray-700">Period</label>
                    <input type="text" name="period" id="period" value="{{ old('period', now()->year . '-Q' . ceil(now()->month/3)) }}" placeholder="e.g. 2026-Q1"
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                    <p class="mt-1 text-xs text-gray-400">Use a unique period per employee, e.g. 2026-Q1</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">{{ $kpis->count() }} active KPI(s) will be scored.</p>
                </div>
            </div>
            <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
                <a href="{{ route('performance.appraisals') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Create & Score</button>
            </div>
        </form>
    </div>
</div>
@endsection
