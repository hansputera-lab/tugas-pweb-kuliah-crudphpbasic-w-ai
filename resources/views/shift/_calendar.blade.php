@php
    $start = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
    $end = $start->copy()->endOfMonth();
    $firstDayOfWeek = $start->dayOfWeekIso - 1; // 0 = Monday
    $weeks = ceil(($firstDayOfWeek + $end->day) / 7);
@endphp

<div class="grid grid-cols-7 gap-px overflow-hidden rounded-lg bg-gray-200 text-center">
    @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $d)
        <div class="bg-gray-50 py-2 text-xs font-semibold uppercase tracking-wider text-gray-500">{{ $d }}</div>
    @endforeach

    @for($w = 0; $w < $weeks; $w++)
        @for($i = 0; $i < 7; $i++)
            @php
                $dayIndex = $w * 7 + $i;
                $dayNum = $dayIndex - $firstDayOfWeek + 1;
                $inMonth = $dayNum >= 1 && $dayNum <= $end->day;
                $dateStr = $inMonth ? \Carbon\Carbon::createFromDate($year, $month, $dayNum)->toDateString() : null;
                $shift = $dateStr && isset($calendar[$dateStr]) ? $calendar[$dateStr] : null;
                $isToday = $dateStr === now()->toDateString();
            @endphp
            <div class="min-h-[88px] bg-white p-2 text-left {{ $inMonth ? '' : 'bg-gray-50' }} {{ $isToday ? 'ring-2 ring-inset ring-indigo-400' : '' }}">
                @if($inMonth)
                    <span class="text-xs font-medium {{ $isToday ? 'text-indigo-600' : 'text-gray-400' }}">{{ $dayNum }}</span>
                    @if($shift)
                        <div class="mt-1 rounded-md px-2 py-1 text-xs font-medium text-white" style="background-color: {{ $shift->color }}">
                            {{ $shift->name }}
                        </div>
                        <p class="mt-0.5 text-[10px] text-gray-500">{{ substr($shift->start_time,0,5) }}-{{ substr($shift->end_time,0,5) }}</p>
                    @else
                        <p class="mt-1 text-[10px] text-gray-300">Off</p>
                    @endif
                @endif
            </div>
        @endfor
    @endfor
</div>
