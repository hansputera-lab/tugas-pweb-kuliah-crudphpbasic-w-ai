@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Schedule</h1>
                <p class="mt-1 text-sm text-gray-500">Your shift calendar for {{ $monthLabel }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('my.schedule', ['year' => $month == 1 ? $year - 1 : $year, 'month' => $month == 1 ? 12 : $month - 1]) }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">&larr; Prev</a>
                <a href="{{ route('my.schedule', ['year' => $month == 12 ? $year + 1 : $year, 'month' => $month == 12 ? 1 : $month + 1]) }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Next &rarr;</a>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="p-4">
                @include('shift._calendar', ['calendar' => $calendar, 'year' => $year, 'month' => $month])
            </div>
        </div>
    </div>
</div>
@endsection
