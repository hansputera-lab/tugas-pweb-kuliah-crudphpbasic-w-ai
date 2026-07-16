@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('performance.appraisals') }}" class="hover:text-gray-700">Performance</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-900">{{ $appraisal->employee->full_name ?? '' }} &middot; {{ $appraisal->period }}</span>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">Score KPIs</h1>
            <p class="mt-1 text-sm text-gray-500">Enter a score (0-100) per KPI. Final score is the weighted average.</p>
        </div>

        <form action="{{ route('performance.appraisals.evaluate.update', $appraisal) }}" method="POST" class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                @foreach($appraisal->details as $detail)
                    <div class="rounded-lg border border-gray-100 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $detail->kpi->title ?? '-' }}</p>
                                <p class="text-xs text-gray-500">{{ ucfirst($detail->kpi->category ?? '') }} &middot; weight {{ $detail->weight }}%</p>
                                @if($detail->kpi->description)
                                    <p class="mt-1 text-xs text-gray-400">{{ $detail->kpi->description }}</p>
                                @endif
                            </div>
                            <div class="w-28">
                                <label class="block text-xs font-medium text-gray-500">Score</label>
                                <input type="number" step="0.1" name="scores[{{ $detail->id }}]" min="0" max="100"
                                       value="{{ old('scores.' . $detail->id, $detail->score) }}"
                                       class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                        </div>
                        <div class="mt-2">
                            <input type="text" name="comments[{{ $detail->id }}]" placeholder="Comment (optional)"
                                   value="{{ old('comments.' . $detail->id, $detail->comment) }}"
                                   class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-5">
                <label for="notes" class="block text-sm font-medium text-gray-700">Reviewer Notes</label>
                <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('notes', $appraisal->notes) }}</textarea>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
                <a href="{{ route('performance.appraisals.show', $appraisal) }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Complete Appraisal</button>
            </div>
        </form>
    </div>
</div>
@endsection
