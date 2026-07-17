@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('payroll.index') }}" class="hover:text-gray-700">Payroll</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-900">BPJS Settings</span>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">BPJS Ketenagakerjaan & Kesehatan</h1>
            <p class="mt-1 text-sm text-gray-500">Configure BPJS contribution rates for each component. Changes apply to payroll runs using these settings.</p>
            <div class="mt-4 rounded-lg bg-blue-50 p-4 text-sm text-blue-700">
                <p class="font-medium">BPJS Components:</p>
                <ul class="mt-1 list-inside list-disc space-y-1">
                    <li><strong>kes</strong> — BPJS Kesehatan: health insurance, max wage cap Rp 12,000,000 (4% employer + 1% employee)</li>
                    <li><strong>jkk</strong> — Jaminan Kecelakaan Kerja: work accident insurance, employer-only, rate depends on risk level (0.24%–1.74%)</li>
                    <li><strong>jkm</strong> — Jaminan Kematian: death insurance, employer-only at 0.30%</li>
                    <li><strong>jht</strong> — Jaminan Hari Tua: old-age savings, max wage cap Rp 10,000,000 (3.7% employer + 2% employee)</li>
                    <li><strong>jp</strong> — Jaminan Pensiun: pension insurance, max wage cap Rp 10,000,000 (2% employer + 1% employee)</li>
                </ul>
            </div>
        </div>

        @foreach($settings as $component => $items)
            <div class="mb-6 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
                <div class="border-b border-gray-100 px-5 py-4">
                    <h2 class="text-base font-semibold text-gray-900 uppercase">{{ $component }}</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($items as $item)
                        <form method="POST" action="{{ route('payroll.bpjs.update', $component) }}" class="flex flex-wrap items-end gap-3 p-4">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="risk_level" value="{{ $item->risk_level ?? '' }}">
                            <div class="w-28">
                                <label class="text-xs font-medium text-gray-500">Risk</label>
                                <p class="mt-0.5 text-sm text-gray-900">{{ $item->risk_level ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500">Employer %</label>
                                <input type="number" step="0.01" name="rate_employer" value="{{ $item->rate_employer }}" required min="0" max="100"
                                       class="mt-1 block w-24 rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500">Employee %</label>
                                <input type="number" step="0.01" name="rate_employee" value="{{ $item->rate_employee }}" required min="0" max="100"
                                       class="mt-1 block w-24 rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500">Max Wage</label>
                                <input type="text" name="max_wage" value="{{ $item->max_wage }}" data-currency
                                       class="mt-1 block w-28 rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500">Min Wage</label>
                                <input type="text" name="min_wage" value="{{ $item->min_wage ?? '' }}" data-currency
                                       class="mt-1 block w-28 rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-medium text-white hover:bg-indigo-500">Save</button>
                        </form>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
