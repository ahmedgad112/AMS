@props(['size' => 'md'])

@php
    $sizes = [
        'sm' => 'h-10',
        'md' => 'h-14',
        'lg' => 'h-20',
        'xl' => 'h-24',
    ];
    $height = $sizes[$size] ?? $sizes['md'];
@endphp

<img src="{{ asset('images/logo.png') }}"
     alt="جامعة برج العرب التكنولوجية"
     {{ $attributes->merge(['class' => "{$height} w-auto object-contain"]) }}>
