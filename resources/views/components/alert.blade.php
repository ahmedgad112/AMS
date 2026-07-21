@props(['type' => 'success', 'message'])

@php
    $styles = match($type) {
        'success' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
        'error' => 'bg-red-50 border-red-200 text-red-800',
        'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
        default => 'bg-blue-50 border-blue-200 text-blue-800',
    };
@endphp

<div {{ $attributes->merge(['class' => "mb-6 px-4 py-3 rounded-lg border text-sm font-medium {$styles}"]) }}>
    {{ $message }}
</div>
