@props(['variant' => 'default', 'size' => 'default'])

@php
    $logoPath = \App\Domains\Settings\Models\Setting::where('key', 'logo_light')->value('value');
    $imageUrl = $logoPath ? \Illuminate\Support\Facades\Storage::url($logoPath) : null;

    $classes = match ($size) {
        'sm' => 'h-8 w-auto',
        'lg' => 'h-12 w-auto',
        'xl' => 'h-16 w-auto',
        default => 'h-9 w-auto',
    };
@endphp

@if($imageUrl)
    <img src="{{ $imageUrl }}" alt="{{ config('app.name') }}" {{ $attributes->merge(['class' => $classes]) }}>
@else
    <x-application-logo {{ $attributes->merge(['class' => $classes . ' fill-current text-gray-500']) }} />
@endif
