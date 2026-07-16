@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('shifts.index') }}" class="hover:text-gray-700">Shifts</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-900">Definitions</span>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">Shift Definitions</h1>
            <p class="mt-1 text-sm text-gray-500">Create shift templates used when assigning employee schedules.</p>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5">
                <h2 class="text-base font-semibold text-gray-900">Add Shift</h2>
                <form action="{{ route('shifts.definitions.store') }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" required placeholder="e.g. Morning"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Start</label>
                            <input type="time" name="start_time" required class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">End</label>
                            <input type="time" name="end_time" required class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Late Threshold</label>
                        <input type="time" name="late_threshold" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-400">Defaults to start time</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Color</label>
                        <input type="color" name="color" value="#6366f1" class="mt-1 block h-10 w-full rounded-lg border border-gray-300">
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Add Shift</button>
                </form>
            </div>

            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 lg:col-span-2">
                <h2 class="text-base font-semibold text-gray-900">Shifts</h2>
                <div class="mt-4 space-y-3">
                    @forelse($shifts as $shift)
                        <form action="{{ route('shifts.definitions.update', $shift) }}" method="POST" class="flex flex-wrap items-end gap-3 rounded-lg border border-gray-100 p-3">
                            @csrf
                            @method('PUT')
                            <div class="w-32">
                                <label class="text-xs font-medium text-gray-500">Name</label>
                                <input type="text" name="name" value="{{ $shift->name }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500">Start</label>
                                <input type="time" name="start_time" value="{{ substr($shift->start_time,0,5) }}" class="mt-1 block rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500">End</label>
                                <input type="time" name="end_time" value="{{ substr($shift->end_time,0,5) }}" class="mt-1 block rounded-lg border border-gray-300 px-2 py-1.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500">Color</label>
                                <input type="color" name="color" value="{{ $shift->color }}" class="mt-1 block h-9 w-12 rounded-lg border border-gray-300">
                            </div>
                            <label class="flex items-center gap-1 text-xs text-gray-600">
                                <input type="checkbox" name="is_active" value="1" {{ $shift->is_active ? 'checked' : '' }}> Active
                            </label>
                            <div class="flex gap-1">
                                <button type="submit" class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-500">Save</button>
                                <form action="{{ route('shifts.definitions.destroy', $shift) }}" method="POST" onsubmit="return confirm('Delete this shift?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-500">Del</button>
                                </form>
                            </div>
                        </form>
                    @empty
                        <p class="py-8 text-center text-sm text-gray-500">No shifts yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
