@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">My Payslips</h1>
            <p class="mt-1 text-sm text-gray-500">View your salary slips per period</p>
        </div>

        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Period</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Base Salary</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Net Salary</th>
                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($items as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ $item->period->label ?? '-' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm tabular-nums text-gray-500">{{ currency($item->base_salary) }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm tabular-nums font-semibold text-indigo-600">{{ currency($item->net_salary) }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ $item->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $item->status === 'finalized' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $item->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    @if($item->payslip)
                                        <a href="{{ route('my.payslip', $item->period) }}" class="text-indigo-600 hover:text-indigo-900">View Slip</a>
                                    @else
                                        <span class="text-gray-400">Not available</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">No payslips available yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
