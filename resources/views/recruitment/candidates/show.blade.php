@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('recruitment.candidates.index') }}" class="hover:text-gray-700">Candidates</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900">{{ $candidate->full_name }}</span>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">Candidate Detail</h1>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
                <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                <div class="mt-4 space-y-4">
                    <div class="flex justify-between border-b border-gray-100 pb-3">
                        <span class="text-sm text-gray-500">Full Name</span>
                        <span class="text-sm font-medium text-gray-900">{{ $candidate->full_name }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-3">
                        <span class="text-sm text-gray-500">Email</span>
                        <span class="text-sm font-medium text-gray-900">{{ $candidate->email ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-3">
                        <span class="text-sm text-gray-500">Phone</span>
                        <span class="text-sm font-medium text-gray-900">{{ $candidate->phone ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-100 pb-3">
                        <span class="text-sm text-gray-500">Source</span>
                        <span class="text-sm font-medium text-gray-900">{{ $candidate->source ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Notes</span>
                        <span class="text-sm font-medium text-gray-900 text-right max-w-xs">{{ $candidate->notes ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5">
                <h3 class="text-lg font-semibold text-gray-900">Application History</h3>
                @forelse($candidate->applications as $application)
                    <div class="mt-4 space-y-4">
                        <div class="rounded-lg border border-gray-200 p-4">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-gray-900">{{ $application->jobPosting->title ?? 'Job Posting' }}</p>
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                    {{ $application->status === 'applied' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $application->status === 'screening' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $application->status === 'interview' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                    {{ $application->status === 'offer' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $application->status === 'hired' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $application->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Applied: {{ $application->created_at ? \Carbon\Carbon::parse($application->created_at)->format('d M Y') : '-' }}</p>
                        </div>
                    </div>
                @empty
                    <p class="mt-4 text-sm text-gray-500">No application history</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
