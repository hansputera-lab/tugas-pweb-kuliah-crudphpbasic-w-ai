@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('performance.appraisals') }}" class="hover:text-gray-700">Performance</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-900">{{ $appraisal->employee->full_name ?? '' }} &middot; {{ $appraisal->period }}</span>
            </div>
            <div class="mt-2 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Appraisal Detail</h1>
                @if($appraisal->isDraft())
                    <a href="{{ route('performance.appraisals.evaluate', $appraisal) }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Score KPIs</a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 lg:col-span-2">
                <h2 class="text-lg font-semibold text-gray-900">KPI Scores</h2>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">KPI</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Weight</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Score</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Comment</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($appraisal->details as $detail)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $detail->kpi->title ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm text-gray-500">{{ $detail->weight }}%</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-gray-900">{{ number_format($detail->score, 1) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $detail->comment ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">No KPI details.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($appraisal->notes)
                    <div class="mt-4 rounded-lg bg-gray-50 p-4 text-sm text-gray-700">
                        <span class="font-medium">Reviewer notes:</span> {{ $appraisal->notes }}
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="rounded-xl bg-indigo-50 p-6 text-center ring-1 ring-indigo-100">
                    <p class="text-xs font-medium uppercase tracking-wider text-indigo-500">Final Grade</p>
                    @if($appraisal->final_grade)
                        <div class="mx-auto mt-2 flex h-16 w-16 items-center justify-center rounded-full text-3xl font-bold text-white
                            {{ $appraisal->final_grade == 'A' ? 'bg-green-600' : '' }}
                            {{ $appraisal->final_grade == 'B' ? 'bg-blue-600' : '' }}
                            {{ $appraisal->final_grade == 'C' ? 'bg-yellow-500' : '' }}
                            {{ $appraisal->final_grade == 'D' ? 'bg-orange-500' : '' }}
                            {{ $appraisal->final_grade == 'E' ? 'bg-red-600' : '' }}">
                            {{ $appraisal->final_grade }}
                        </div>
                        <p class="mt-2 text-lg font-bold text-indigo-900">{{ number_format($appraisal->total_score, 1) }}</p>
                    @else
                        <p class="mt-2 text-gray-400">Not evaluated</p>
                    @endif
                    <p class="mt-1 text-xs text-indigo-500">Scale: A&ge;{{ $thresholds['A'] }}, B&ge;{{ $thresholds['B'] }}, C&ge;{{ $thresholds['C'] }}, D&ge;{{ $thresholds['D'] }}</p>
                </div>

                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5">
                    <h3 class="text-sm font-semibold text-gray-900">360&deg; Feedback</h3>
                    <div class="mt-3 space-y-3">
                        @forelse($appraisal->feedback as $fb)
                            <div class="rounded-lg border border-gray-100 p-3 text-sm">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900">{{ $fb->reviewer_name }}</span>
                                    <span class="text-xs text-gray-400 capitalize">{{ $fb->relationship }}</span>
                                </div>
                                @if($fb->rating)
                                    <p class="text-xs text-gray-500">Rating: {{ number_format($fb->rating, 1) }}</p>
                                @endif
                                @if($fb->comment)
                                    <p class="mt-1 text-gray-600">{{ $fb->comment }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-400">No feedback yet.</p>
                        @endforelse
                    </div>

                    <form action="{{ route('performance.appraisals.feedback', $appraisal) }}" method="POST" class="mt-4 space-y-3 border-t border-gray-100 pt-4">
                        @csrf
                        <div>
                            <label class="text-xs font-medium text-gray-500">Reviewer Name</label>
                            <input type="text" name="reviewer_name" required value="{{ old('reviewer_name', Auth::user()->name) }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500">Relationship</label>
                            <select name="relationship" class="mt-1 block w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <option value="manager">Manager</option>
                                <option value="peer">Peer</option>
                                <option value="subordinate">Subordinate</option>
                                <option value="self">Self</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500">Rating (0-100)</label>
                            <input type="number" step="0.1" name="rating" min="0" max="100" class="mt-1 block w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-500">Comment</label>
                            <textarea name="comment" rows="2" class="mt-1 block w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500">Add Feedback</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
