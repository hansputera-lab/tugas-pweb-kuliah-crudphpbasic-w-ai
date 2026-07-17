@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6 flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <a href="{{ route('payroll.index') }}" class="hover:text-gray-700">Payroll</a>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="text-gray-900">Employee Tax Status</span>
                </div>
                <h1 class="mt-2 text-2xl font-bold text-gray-900">Employee Tax Status</h1>
                <p class="mt-1 text-sm text-gray-500">Manage PTKP status, NPWP, and tax category for each employee.</p>
            </div>
            @can('pph21.manage')
                <a href="{{ route('payroll.tax-status.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">+ Add</a>
            @endcan
        </div>

        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Employee</th>
                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">PTKP Status</th>
                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">NPWP</th>
                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">TER Category</th>
                        <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Effective</th>
                        @can('pph21.manage')
                            <th class="px-5 py-3"></th>
                        @endcan
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($statuses as $s)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 text-sm text-gray-900">{{ $s->employee?->user?->name ?? 'N/A' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-900">{{ $s->ptkp_label }}</td>
                            <td class="px-5 py-3 text-sm text-gray-500">{{ $s->npwp ?? '-' }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700">{{ $s->ter_category ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-3 text-sm text-gray-500">{{ $s->effective_date ? $s->effective_date->format('d/m/Y') : '-' }}</td>
                            @can('pph21.manage')
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('payroll.tax-status.edit', $s) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">Edit</a>
                                </td>
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-sm text-gray-500">No tax status records yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $statuses->links() }}
        </div>
    </div>
</div>
@endsection
