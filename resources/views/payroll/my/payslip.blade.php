@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <div class="mb-4 flex items-center justify-between print:hidden">
            <a href="{{ route('my.payslips') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">&larr; Back</a>
            <button onclick="window.print()" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Print / PDF</button>
        </div>

        <div class="rounded-xl bg-white p-8 shadow-sm ring-1 ring-gray-950/5" id="payslip">
            <div class="flex items-center justify-between border-b border-gray-200 pb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ config('app.name', 'HRIS') }}</h1>
                    <p class="text-sm text-gray-500">Payslip / Slip Gaji</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-900">{{ $item->payslip->payslip_number ?? '-' }}</p>
                    <p class="text-sm text-gray-500">{{ $item->period->label }}</p>
                    <p class="text-xs text-gray-400">Generated: {{ $item->payslip->generated_at?->format('d M Y H:i') ?? '-' }}</p>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs uppercase tracking-wider text-gray-500">Employee</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $item->employee->full_name ?? '-' }}</p>
                    <p class="text-sm text-gray-500">NIP: {{ $item->employee->nip ?? '-' }}</p>
                    <p class="text-sm text-gray-500">{{ $item->employee->department->name ?? '-' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs uppercase tracking-wider text-gray-500">Status</p>
                    <p class="text-sm font-semibold capitalize text-gray-900">{{ $item->status }}</p>
                    @if($item->notes)
                        <p class="mt-1 text-xs text-gray-500">{{ $item->notes }}</p>
                    @endif
                </div>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <h2 class="mb-2 text-sm font-semibold text-green-700">Earnings</h2>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            <tr><td class="py-1.5 text-gray-600">Base Salary</td><td class="py-1.5 text-right tabular-nums text-gray-900">{{ currency($item->base_salary) }}</td></tr>
                            <tr><td class="py-1.5 text-gray-600">Transport Allowance</td><td class="py-1.5 text-right tabular-nums text-gray-900">{{ currency($item->allowance_transport) }}</td></tr>
                            <tr><td class="py-1.5 text-gray-600">Meal Allowance</td><td class="py-1.5 text-right tabular-nums text-gray-900">{{ currency($item->allowance_meal) }}</td></tr>
                            <tr><td class="py-1.5 text-gray-600">Other Allowance</td><td class="py-1.5 text-right tabular-nums text-gray-900">{{ currency($item->allowance_other) }}</td></tr>
                            <tr><td class="py-1.5 text-gray-600">Overtime ({{ number_format($item->overtime_hours, 1) }}h)</td><td class="py-1.5 text-right tabular-nums text-gray-900">{{ currency($item->overtime_pay) }}</td></tr>
                            <tr class="font-semibold"><td class="py-1.5 text-gray-800">Total Earnings</td><td class="py-1.5 text-right tabular-nums text-gray-900">{{ currency($item->base_salary + $item->total_allowance + $item->overtime_pay) }}</td></tr>
                        </tbody>
                    </table>
                </div>
                <div>
                    <h2 class="mb-2 text-sm font-semibold text-red-700">Deductions</h2>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            <tr><td class="py-1.5 text-gray-600">Late Deduction</td><td class="py-1.5 text-right tabular-nums text-gray-900">{{ currency($item->deduction_late) }}</td></tr>
                            <tr><td class="py-1.5 text-gray-600">Absent Deduction</td><td class="py-1.5 text-right tabular-nums text-gray-900">{{ currency($item->deduction_absent) }}</td></tr>
                            <tr><td class="py-1.5 text-gray-600">Other Deduction</td><td class="py-1.5 text-right tabular-nums text-gray-900">{{ currency($item->deduction_other) }}</td></tr>
                            <tr class="font-semibold"><td class="py-1.5 text-gray-800">Total Deductions</td><td class="py-1.5 text-right tabular-nums text-gray-900">{{ currency($item->total_deduction) }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-between rounded-lg bg-indigo-50 px-6 py-4">
                <span class="text-base font-semibold text-indigo-900">Net Salary</span>
                <span class="text-xl font-bold text-indigo-900 tabular-nums">{{ currency($item->net_salary) }}</span>
            </div>

            <p class="mt-8 text-center text-xs text-gray-400">This payslip is generated automatically by the HRIS system.</p>
        </div>
    </div>
</div>

<style>
    @media print {
        body { background: white; }
        .print\:hidden { display: none !important; }
        #payslip { box-shadow: none; border: none; }
    }
</style>
@endsection
