@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('my.overtime') }}" class="hover:text-gray-700">My Overtime</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-900">Request Overtime</span>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">Request Overtime</h1>
        </div>

        <form action="{{ route('my.overtime.store') }}" method="POST" class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
            @csrf
            <div class="space-y-5">
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}"
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                    @error('date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                        <input type="time" name="start_time" id="start_time" value="{{ old('start_time', '17:00') }}"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                        @error('start_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                        <input type="time" name="end_time" id="end_time" value="{{ old('end_time', '20:00') }}"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500" required>
                        @error('end_time') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                    <textarea name="reason" id="reason" rows="3"
                              class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">{{ old('reason') }}</textarea>
                </div>
            </div>
            <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
                <a href="{{ route('my.overtime') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Submit</button>
            </div>
        </form>
    </div>
</div>
@endsection
