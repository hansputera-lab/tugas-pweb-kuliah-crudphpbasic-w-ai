@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">KPI / Competency Library</h1>
            <p class="mt-1 text-sm text-gray-500">Define KPIs used in performance appraisals. Weights determine each KPI's contribution to the final score.</p>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5">
                <h2 class="text-base font-semibold text-gray-900">Add KPI</h2>
                <form action="{{ route('performance.kpis.store') }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" name="title" required placeholder="e.g. Code Quality"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Category</label>
                        <select name="category" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="competency">Competency</option>
                            <option value="goal">Goal</option>
                            <option value="behavior">Behavior</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Weight (%)</label>
                            <input type="number" name="weight" required min="0" max="100" value="0"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Target</label>
                            <input type="number" step="0.01" name="target_value"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Unit</label>
                        <input type="text" name="measurement_unit" placeholder="e.g. %" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" rows="2" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Add KPI</button>
                </form>
            </div>

            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 lg:col-span-2">
                <h2 class="text-base font-semibold text-gray-900">KPIs</h2>
                <div class="mt-4 space-y-3">
                    @forelse($kpis as $kpi)
                        <form action="{{ route('performance.kpis.update', $kpi) }}" method="POST" class="flex flex-wrap items-end gap-3 rounded-lg border border-gray-100 p-3">
                            @csrf
                            @method('PUT')
                            <div class="w-44">
                                <label class="text-xs font-medium text-gray-500">Title</label>
                                <input type="text" name="title" value="{{ $kpi->title }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500">Category</label>
                                <select name="category" class="mt-1 block rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="competency" {{ $kpi->category === 'competency' ? 'selected' : '' }}>Competency</option>
                                    <option value="goal" {{ $kpi->category === 'goal' ? 'selected' : '' }}>Goal</option>
                                    <option value="behavior" {{ $kpi->category === 'behavior' ? 'selected' : '' }}>Behavior</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500">Weight %</label>
                                <input type="number" name="weight" min="0" max="100" value="{{ $kpi->weight }}" class="mt-1 block w-20 rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                            <label class="flex items-center gap-1 text-xs text-gray-600">
                                <input type="checkbox" name="is_active" value="1" {{ $kpi->is_active ? 'checked' : '' }}> Active
                            </label>
                            <div class="flex gap-1">
                                <button type="submit" class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-500">Save</button>
                                <form action="{{ route('performance.kpis.destroy', $kpi) }}" method="POST" onsubmit="return confirm('Delete this KPI?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-500">Del</button>
                                </form>
                            </div>
                        </form>
                    @empty
                        <p class="py-8 text-center text-sm text-gray-500">No KPIs yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
