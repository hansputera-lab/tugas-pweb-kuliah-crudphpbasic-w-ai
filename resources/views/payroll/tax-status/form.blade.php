@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('payroll.index') }}" class="hover:text-gray-700">Payroll</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <a href="{{ route('payroll.tax-status.index') }}" class="hover:text-gray-700">Tax Status</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-900">{{ isset($taxStatus) ? 'Edit' : 'Add' }}</span>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">{{ isset($taxStatus) ? 'Edit Tax Status' : 'Add Tax Status' }}</h1>
        </div>

        <form method="POST" action="{{ isset($taxStatus) ? route('payroll.tax-status.update', $taxStatus) : route('payroll.tax-status.store') }}" class="space-y-6">
            @csrf
            @if(isset($taxStatus)) @method('PUT') @endif

            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5">
                @if(!isset($taxStatus))
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Employee</label>
                        <select name="employee_id" required
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->user?->name }} ({{ $emp->employee_id ?? $emp->id }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tax Status</label>
                        <select name="tax_status" required
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="TK" {{ (isset($taxStatus) && $taxStatus->tax_status === 'TK') ? 'selected' : '' }}>TK (Tidak Kawin)</option>
                            <option value="K" {{ (isset($taxStatus) && $taxStatus->tax_status === 'K') ? 'selected' : '' }}>K (Kawin)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jumlah Tanggungan</label>
                        <input type="number" name="jumlah_tanggungan" min="0" required
                               value="{{ old('jumlah_tanggungan', isset($taxStatus) ? $taxStatus->jumlah_tanggungan : 0) }}"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">NPWP</label>
                        <input type="text" name="npwp" value="{{ old('npwp', optional($taxStatus)->npwp ?? '') }}"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Effective Date</label>
                        <input type="date" name="effective_date" value="{{ old('effective_date', optional($taxStatus)->effective_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('payroll.tax-status.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection
