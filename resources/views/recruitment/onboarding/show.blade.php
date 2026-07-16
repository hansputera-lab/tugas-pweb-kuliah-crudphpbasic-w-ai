@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <span class="text-gray-900">Onboarding</span>
            </div>
            <div class="mt-2 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Onboarding - {{ $onboarding->employee->full_name }}</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ $onboarding->employee->department->name ?? '-' }}</p>
                </div>
                <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium
                    {{ $onboarding->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $onboarding->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $onboarding->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}">
                    {{ ucfirst(str_replace('_', ' ', $onboarding->status)) }}
                </span>
            </div>
        </div>

        <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Checklist</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Task Name</th>
                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Completed</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($onboarding->checklistItems ?? [] as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item->task_name }}</td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('recruitment.onboarding.toggle', $item) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="inline-flex items-center justify-center">
                                            @if($item->is_completed)
                                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @else
                                                <svg class="h-6 w-6 text-gray-300 hover:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    @if($item->is_completed)
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800">Done</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-800">Not Done</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500">No checklist items found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($onboarding->status !== 'completed')
            <div class="mt-6 flex justify-end">
                <form action="{{ route('recruitment.onboarding.complete', $onboarding) }}" method="POST" onsubmit="return confirm('Mark onboarding as complete?')">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="inline-flex items-center rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Mark Complete
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
