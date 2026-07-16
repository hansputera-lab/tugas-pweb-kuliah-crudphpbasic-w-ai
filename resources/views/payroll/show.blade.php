@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('payroll.index') }}" class="hover:text-gray-700">Payroll</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-900">{{ $period->label }}</span>
            </div>
            <div class="mt-2 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Payroll {{ $period->label }}</h1>
                <div class="flex flex-wrap gap-2">
                    @if(!$period->isPaid())
                        <form action="{{ route('payroll.regenerate', $period) }}" method="POST">
                            @csrf
                            <button type="submit" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Recalculate</button>
                        </form>
                    @endif
                    @if($period->isDraft())
                        <form action="{{ route('payroll.finalize', $period) }}" method="POST">
                            @csrf
                            <button type="submit" class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white hover:bg-blue-500">Finalize</button>
                        </form>
                    @endif
                    @if($period->isFinalized())
                        <form action="{{ route('payroll.pay', $period) }}" method="POST" onsubmit="return confirm('Mark this period as paid and generate payslips?')">
                            @csrf
                            <button type="submit" class="rounded-lg bg-green-600 px-3 py-2 text-sm font-medium text-white hover:bg-green-500">Mark as Paid</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Summary --}}
        <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-5">
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Employees</p>
                <p class="mt-1 text-xl font-bold text-gray-900">{{ $summary['employees'] }}</p>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Base Salary</p>
                <p class="mt-1 text-lg font-bold text-gray-900 tabular-nums">{{ currency($summary['total_base']) }}</p>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Allowances</p>
                <p class="mt-1 text-lg font-bold text-green-600 tabular-nums">{{ currency($summary['total_allowance'] + $summary['total_overtime']) }}</p>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Deductions</p>
                <p class="mt-1 text-lg font-bold text-red-600 tabular-nums">{{ currency($summary['total_deduction']) }}</p>
            </div>
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Net Salary</p>
                <p class="mt-1 text-lg font-bold text-indigo-600 tabular-nums">{{ currency($summary['total_net']) }}</p>
            </div>
        </div>

        {{-- Items table --}}
        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Employee</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Base</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Allowance</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">OT Pay</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Deduction</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Net</th>
                            <th class="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($items as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $item->employee->full_name ?? '-' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm tabular-nums text-gray-500">{{ currency($item->base_salary) }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm tabular-nums text-gray-500">{{ currency($item->total_allowance) }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm tabular-nums text-gray-500">{{ currency($item->overtime_pay) }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm tabular-nums text-red-600">{{ currency($item->total_deduction) }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm tabular-nums font-semibold text-indigo-600">{{ currency($item->net_salary) }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-center">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                        {{ $item->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $item->status === 'finalized' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $item->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        @if(!$period->isPaid())
                                            <a href="{{ route('payroll.items.edit', $item) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        @endif
                                        @if($item->payslip)
                                            <a href="{{ route('payroll.payslip', $item) }}" class="text-green-600 hover:text-green-900">Slip</a>
                                        @endif
                                        <a href="{{ route('payroll.documents', $item) }}" class="text-blue-600 hover:text-blue-900">Docs</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-sm text-gray-500">No payroll items. Click "Recalculate" to generate.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
