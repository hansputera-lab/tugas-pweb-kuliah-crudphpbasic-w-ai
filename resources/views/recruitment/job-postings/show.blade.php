@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('recruitment.index') }}" class="hover:text-gray-700">Job Postings</a>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900">{{ $posting->title }}</span>
            </div>
            <div class="mt-2 flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">{{ $posting->title }}</h1>
                <div class="flex items-center gap-2">
                    <a href="{{ route('recruitment.edit', $posting) }}" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Department</p>
                <p class="mt-1 text-sm font-medium text-gray-900">{{ $posting->department->name ?? '-' }}</p>
            </div>
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Position</p>
                <p class="mt-1 text-sm font-medium text-gray-900">{{ $posting->position->name ?? '-' }}</p>
            </div>
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Employment Type</p>
                <p class="mt-1 text-sm font-medium text-gray-900">{{ $posting->employment_type ?? '-' }}</p>
            </div>
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Status</p>
                <p class="mt-1">
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                        {{ $posting->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                        {{ $posting->status === 'open' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $posting->status === 'closed' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ ucfirst($posting->status) }}
                    </span>
                </p>
            </div>
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Salary Range</p>
                <p class="mt-1 text-sm font-medium text-gray-900">
                    @if($posting->salary_min || $posting->salary_max)
                        {{ $posting->salary_min ? number_format($posting->salary_min) : 'Unspecified' }} - {{ $posting->salary_max ? number_format($posting->salary_max) : 'Unspecified' }}
                    @else
                        -
                    @endif
                </p>
            </div>
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Posted / Closed</p>
                <p class="mt-1 text-sm font-medium text-gray-900">
                    {{ $posting->created_at ? \Carbon\Carbon::parse($posting->created_at)->format('d M Y') : '-' }}
                    @if($posting->closed_at)
                        &rarr; {{ \Carbon\Carbon::parse($posting->closed_at)->format('d M Y') }}
                    @endif
                </p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5">
                <h2 class="text-base font-semibold text-gray-900">Description</h2>
                <div class="mt-3 text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $posting->description }}</div>
            </div>
            <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5">
                <h2 class="text-base font-semibold text-gray-900">Requirements</h2>
                <div class="mt-3 text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $posting->requirements ?? 'No specific requirements listed.' }}</div>
            </div>
        </div>

        <div class="mt-6 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Applications ({{ $posting->applications->count() }})</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Candidate Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Applied At</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($posting->applications as $application)
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ $application->candidate->full_name ?? '-' }}</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ $application->status === 'applied' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $application->status === 'screening' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $application->status === 'interview' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                        {{ $application->status === 'offer' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $application->status === 'hired' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $application->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $application->created_at ? \Carbon\Carbon::parse($application->created_at)->format('d M Y') : '-' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-1 flex-wrap">
                                        @if($application->status !== 'screening')
                                            <form action="{{ route('recruitment.applications.update-status', $application) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="screening">
                                                <button type="submit" class="rounded bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-700 hover:bg-yellow-100">Screening</button>
                                            </form>
                                        @endif
                                        @if($application->status !== 'interview')
                                            <form action="{{ route('recruitment.applications.update-status', $application) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="interview">
                                                <button type="submit" class="rounded bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-100">Interview</button>
                                            </form>
                                        @endif
                                        @if($application->status !== 'offer')
                                            <form action="{{ route('recruitment.applications.update-status', $application) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="offer">
                                                <button type="submit" class="rounded bg-purple-50 px-2 py-1 text-xs font-medium text-purple-700 hover:bg-purple-100">Offer</button>
                                            </form>
                                        @endif
                                        @if($application->status !== 'hired')
                                            <form action="{{ route('recruitment.applications.update-status', $application) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="hired">
                                                <button type="submit" class="rounded bg-green-50 px-2 py-1 text-xs font-medium text-green-700 hover:bg-green-100">Hired</button>
                                            </form>
                                        @endif
                                        @if($application->status !== 'rejected')
                                            <form action="{{ route('recruitment.applications.update-status', $application) }}" method="POST" class="inline" onsubmit="return confirm('Reject this application?')">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="rounded bg-red-50 px-2 py-1 text-xs font-medium text-red-700 hover:bg-red-100">Rejected</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">No applications yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @foreach($posting->applications as $application)
        @if($application->interviews->count() > 0)
        <div class="mt-6 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="border-b border-gray-100 px-6 py-4">
                <h3 class="text-base font-semibold text-gray-900">Interviews - {{ $application->candidate->full_name ?? 'Candidate' }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Interviewer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Scheduled At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Feedback</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Rating</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($application->interviews as $interview)
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ $interview->interviewer->full_name ?? '-' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $interview->scheduled_at ? \Carbon\Carbon::parse($interview->scheduled_at)->format('d M Y H:i') : '-' }}</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ $interview->status === 'scheduled' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $interview->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $interview->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($interview->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $interview->feedback ?? '-' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $interview->rating ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="mt-4 rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5">
            <h3 class="text-base font-semibold text-gray-900">Schedule Interview - {{ $application->candidate->full_name ?? 'Candidate' }}</h3>
            <form action="{{ route('recruitment.interviews.store') }}" method="POST" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @csrf
                <input type="hidden" name="job_application_id" value="{{ $application->id }}">
                <div>
                    <label for="interviewer_id_{{ $application->id }}" class="block text-sm font-medium text-gray-700">Interviewer</label>
                    <select name="interviewer_id" id="interviewer_id_{{ $application->id }}"
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <option value="">Select Interviewer</option>
                        @foreach($users ?? [] as $user)
                            <option value="{{ $user->id }}">{{ $user->full_name ?? $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="scheduled_at_{{ $application->id }}" class="block text-sm font-medium text-gray-700">Scheduled At</label>
                    <input type="datetime-local" name="scheduled_at" id="scheduled_at_{{ $application->id }}"
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="duration_minutes_{{ $application->id }}" class="block text-sm font-medium text-gray-700">Duration (minutes)</label>
                    <input type="number" name="duration_minutes" id="duration_minutes_{{ $application->id }}" value="60"
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                </div>
                <div>
                    <label for="location_{{ $application->id }}" class="block text-sm font-medium text-gray-700">Location</label>
                    <input type="text" name="location" id="location_{{ $application->id }}"
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                           placeholder="Office room / address">
                </div>
                <div>
                    <label for="meeting_link_{{ $application->id }}" class="block text-sm font-medium text-gray-700">Meeting Link</label>
                    <input type="url" name="meeting_link" id="meeting_link_{{ $application->id }}"
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                           placeholder="https://meet.google.com/...">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Schedule</button>
                </div>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endsection
