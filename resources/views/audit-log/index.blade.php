@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <x-alert />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Audit Log</h1>
            <p class="mt-1 text-sm text-gray-500">System activity log showing all changes made across the application</p>
        </div>

        <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Action</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Subject</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Changes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500" title="{{ $log->created_at->format('d M Y H:i:s') }}">
                                    {{ $log->created_at->diffForHumans() }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                    @if($log->user)
                                        {{ $log->user->name }}
                                        <span class="text-gray-400 text-xs block">{{ $log->user->email }}</span>
                                    @else
                                        <span class="text-gray-400">System</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ $log->action === 'created' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $log->action === 'updated' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $log->action === 'deleted' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ !in_array($log->action, ['created','updated','deleted']) ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                    @php
                                        $short = class_basename($log->subject_type) . '#' . $log->subject_id;
                                    @endphp
                                    {{ $short }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 max-w-xs">
                                    @if($log->new_values)
                                        @php $changed = []; @endphp
                                        @foreach($log->new_values as $key => $val)
                                            @php
                                                $old = $log->old_values[$key] ?? '';
                                                if ((string)$old !== (string)$val) {
                                                    $changed[] = "$key";
                                                }
                                            @endphp
                                        @endforeach
                                        <span class="text-xs text-gray-500" title="Changed: {{ implode(', ', $changed) }}">
                                            {{ implode(', ', array_slice($changed, 0, 3)) }}{{ count($changed) > 3 ? '...' : '' }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-xs text-gray-400 font-mono">
                                    {{ $log->ip_address ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">No activity logs recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
