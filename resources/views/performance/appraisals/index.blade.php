@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Performance Appraisals</h1>
                <p class="mt-1 text-sm text-gray-500">Grade scale: A&ge;{{ $thresholds['A'] }}, B&ge;{{ $thresholds['B'] }}, C&ge;{{ $thresholds['C'] }}, D&ge;{{ $thresholds['D'] }}, E&lt;{{ $thresholds['D'] }}</p>
            </div>
            <a href="{{ route('performance.appraisals.create') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                New Appraisal
            </a>
        </div>

        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Period</th>
                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Score</th>
                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Grade</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($appraisals as $appraisal)
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ $appraisal->employee->full_name ?? '-' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $appraisal->period }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ $appraisal->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($appraisal->status) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm text-gray-900">{{ number_format($appraisal->total_score, 1) }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    @if($appraisal->final_grade)
                                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full text-sm font-bold
                                            {{ $appraisal->final_grade == 'A' ? 'bg-green-600 text-white' : '' }}
                                            {{ $appraisal->final_grade == 'B' ? 'bg-blue-600 text-white' : '' }}
                                            {{ $appraisal->final_grade == 'C' ? 'bg-yellow-500 text-white' : '' }}
                                            {{ $appraisal->final_grade == 'D' ? 'bg-orange-500 text-white' : '' }}
                                            {{ $appraisal->final_grade == 'E' ? 'bg-red-600 text-white' : '' }}">
                                            {{ $appraisal->final_grade }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    <a href="{{ route('performance.appraisals.show', $appraisal) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">No appraisals yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
