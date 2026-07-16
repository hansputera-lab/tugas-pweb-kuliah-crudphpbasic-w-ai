@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('payroll.index') }}" class="hover:text-gray-700">Payroll</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('payroll.show', $item->period) }}" class="hover:text-gray-700">{{ $item->period->label }}</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-900">Edit {{ $item->employee->full_name ?? '' }}</span>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">Edit Payroll Item</h1>
            <p class="mt-1 text-sm text-gray-500">Monetary values shown as <span class="tabular-nums font-medium">≈ Rp 1.000.000</span> for readability</p>
        </div>

        <form action="{{ route('payroll.items.update', $item) }}" method="POST" class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Base Salary</label>
                    <input type="number" step="0.01" name="base_salary" value="{{ old('base_salary', $item->base_salary) }}"
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                    <p class="mt-0.5 text-xs text-gray-400 tabular-nums">≈ {{ currency($item->base_salary) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Overtime Hours</label>
                    <input type="number" step="0.01" name="overtime_hours" value="{{ old('overtime_hours', $item->overtime_hours) }}"
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Transport Allowance</label>
                    <input type="number" step="0.01" name="allowance_transport" value="{{ old('allowance_transport', $item->allowance_transport) }}"
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <p class="mt-0.5 text-xs text-gray-400 tabular-nums">≈ {{ currency($item->allowance_transport) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Meal Allowance</label>
                    <input type="number" step="0.01" name="allowance_meal" value="{{ old('allowance_meal', $item->allowance_meal) }}"
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <p class="mt-0.5 text-xs text-gray-400 tabular-nums">≈ {{ currency($item->allowance_meal) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Other Allowance</label>
                    <input type="number" step="0.01" name="allowance_other" value="{{ old('allowance_other', $item->allowance_other) }}"
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <p class="mt-0.5 text-xs text-gray-400 tabular-nums">≈ {{ currency($item->allowance_other) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Late Deduction</label>
                    <input type="number" step="0.01" name="deduction_late" value="{{ old('deduction_late', $item->deduction_late) }}"
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <p class="mt-0.5 text-xs text-gray-400 tabular-nums">≈ {{ currency($item->deduction_late) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Absent Deduction</label>
                    <input type="number" step="0.01" name="deduction_absent" value="{{ old('deduction_absent', $item->deduction_absent) }}"
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <p class="mt-0.5 text-xs text-gray-400 tabular-nums">≈ {{ currency($item->deduction_absent) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Other Deduction</label>
                    <input type="number" step="0.01" name="deduction_other" value="{{ old('deduction_other', $item->deduction_other) }}"
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <p class="mt-0.5 text-xs text-gray-400 tabular-nums">≈ {{ currency($item->deduction_other) }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Overtime Pay</label>
                    <input type="number" step="0.01" name="overtime_pay" value="{{ old('overtime_pay', $item->overtime_pay) }}"
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <p class="mt-0.5 text-xs text-gray-400 tabular-nums">≈ {{ currency($item->overtime_pay) }}</p>
                </div>
            </div>

            <div class="mt-5">
                <label class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea name="notes" rows="3" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('notes', $item->notes) }}</textarea>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                <a href="{{ route('payroll.show', $item->period) }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
