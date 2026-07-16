@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Overtime Requests</h1>
                <p class="mt-1 text-sm text-gray-500">Approve or reject employee overtime requests</p>
            </div>
            <div>
                <a href="?status=pending" class="rounded-lg px-3 py-2 text-sm font-medium {{ request('status','pending') === 'pending' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">Pending</a>
                <a href="?status=all" class="rounded-lg px-3 py-2 text-sm font-medium {{ request('status') === 'all' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">All</a>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Time</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Hours</th>
                            <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($requests as $ot)
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ $ot->employee->full_name ?? '-' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $ot->date->format('d M Y') }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ substr($ot->start_time,0,5) }}-{{ substr($ot->end_time,0,5) }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm text-gray-900">{{ number_format($ot->hours, 1) }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ $ot->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $ot->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $ot->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($ot->status) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                    <a href="{{ route('overtime.show', $ot) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">No overtime requests found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
