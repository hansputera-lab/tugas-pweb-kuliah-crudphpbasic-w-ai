@props(['title', 'value', 'icon' => 'users', 'color' => 'blue', 'subtitle' => null, 'trend' => null, 'trendDirection' => null])

@php
$colorClasses = match($color) {
    'blue' => 'bg-blue-50 text-blue-600',
    'green' => 'bg-green-50 text-green-600',
    'yellow' => 'bg-yellow-50 text-yellow-600',
    'red' => 'bg-red-50 text-red-600',
    'purple' => 'bg-purple-50 text-purple-600',
    'indigo' => 'bg-indigo-50 text-indigo-600',
    'pink' => 'bg-pink-50 text-pink-600',
    'cyan' => 'bg-cyan-50 text-cyan-600',
    default => 'bg-blue-50 text-blue-600',
};

$iconBg = match($color) {
    'blue' => 'bg-blue-100',
    'green' => 'bg-green-100',
    'yellow' => 'bg-yellow-100',
    'red' => 'bg-red-100',
    'purple' => 'bg-purple-100',
    'indigo' => 'bg-indigo-100',
    'pink' => 'bg-pink-100',
    'cyan' => 'bg-cyan-100',
    default => 'bg-blue-100',
};
@endphp

<div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
    <div class="p-6">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-500">{{ $title }}</p>
                <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $value }}</p>
                @if($subtitle)
                    <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
                @endif
                @if($trend)
                    <div class="mt-2 flex items-center text-sm">
                        @if($trendDirection === 'up')
                            <svg class="mr-1 h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-green-600">{{ $trend }}</span>
                        @elseif($trendDirection === 'down')
                            <svg class="mr-1 h-4 w-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-red-600">{{ $trend }}</span>
                        @else
                            <span class="text-gray-500">{{ $trend }}</span>
                        @endif
                    </div>
                @endif
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg {{ $iconBg }}">
                @if($icon === 'users')
                    <svg class="h-6 w-6 {{ $colorClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                @elseif($icon === 'check-circle')
                    <svg class="h-6 w-6 {{ $colorClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @elseif($icon === 'clock')
                    <svg class="h-6 w-6 {{ $colorClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @elseif($icon === 'calendar')
                    <svg class="h-6 w-6 {{ $colorClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                @elseif($icon === 'chart-bar')
                    <svg class="h-6 w-6 {{ $colorClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                @elseif($icon === 'document')
                    <svg class="h-6 w-6 {{ $colorClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                @else
                    <svg class="h-6 w-6 {{ $colorClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                @endif
            </div>
        </div>
    </div>
</div>
