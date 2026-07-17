@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('payroll.index') }}" class="hover:text-gray-700">Payroll</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-900">Components</span>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">Payroll Components</h1>
            <p class="mt-1 text-sm text-gray-500">Configure allowance and deduction components applied to every payroll period (auto-calculated).</p>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Add form --}}
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5">
                <h2 class="text-base font-semibold text-gray-900">Add Component</h2>
                <form action="{{ route('payroll.components.store') }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" required maxlength="255" placeholder="e.g. Tunjangan Transport"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" required
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">Select Type</option>
                            <option value="allowance">Allowance</option>
                            <option value="deduction">Deduction</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Calculation</label>
                        <select name="calculation" required
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">Select Calculation</option>
                            <option value="fixed">Fixed Amount</option>
                            <option value="percentage">Percentage of Base Salary</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Value <span class="text-gray-400">(amount or %)</span>
                        </label>
                        <input type="number" step="0.01" name="value" required min="0"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sort Order</label>
                        <input type="number" name="sort_order" min="0" value="0"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Add Component</button>
                </form>
            </div>

            {{-- List --}}
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 lg:col-span-2">
                <h2 class="text-base font-semibold text-gray-900">Current Components</h2>
                <div class="mt-4 space-y-3">
                    @forelse($components as $component)
                        <form action="{{ route('payroll.components.update', $component) }}" method="POST" class="flex flex-wrap items-end gap-3 rounded-lg border border-gray-100 p-3">
                            @csrf
                            @method('PUT')
                            <div class="w-40">
                                <label class="text-xs font-medium text-gray-500">Name</label>
                                <input type="text" name="name" value="{{ $component->name }}" required maxlength="255" class="mt-1 block w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500">Type</label>
                                <select name="type" class="mt-1 block rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="allowance" {{ $component->type === 'allowance' ? 'selected' : '' }}>Allowance</option>
                                    <option value="deduction" {{ $component->type === 'deduction' ? 'selected' : '' }}>Deduction</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500">Calc</label>
                                <select name="calculation" class="mt-1 block rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="fixed" {{ $component->calculation === 'fixed' ? 'selected' : '' }}>Fixed</option>
                                    <option value="percentage" {{ $component->calculation === 'percentage' ? 'selected' : '' }}>%</option>
                                </select>
                            </div>
                            <div class="w-24">
                                <label class="text-xs font-medium text-gray-500">Value</label>
                                <input type="number" step="0.01" name="value" value="{{ $component->value }}" required min="0" class="mt-1 block w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                            <label class="flex items-center gap-1 text-xs text-gray-600">
                                <input type="checkbox" name="is_active" value="1" {{ $component->is_active ? 'checked' : '' }}> Active
                            </label>
                            <div class="flex gap-1">
                                <button type="submit" class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-500">Save</button>
                                <form action="{{ route('payroll.components.destroy', $component) }}" method="POST" onsubmit="return confirm('Delete this component?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-500">Del</button>
                                </form>
                            </div>
                        </form>
                    @empty
                        <p class="py-8 text-center text-sm text-gray-500">No components yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
