@props([
    'variant' => 'standard', // 'standard', 'compact', 'full'
    'class' => '',
])

@php
    $maxWidthClasses = [
        'standard' => 'max-w-7xl md:max-w-5xl',
        'compact' => 'max-w-4xl md:max-w-3xl',
        'full' => '',
    ];
    
    $maxWidthClass = $maxWidthClasses[$variant] ?? $maxWidthClasses['standard'];
    
    // Padding horizontal responsive standardisé
    $paddingClass = 'px-4 sm:px-6 lg:px-8';
@endphp

<div class="mx-auto {{ $maxWidthClass }} {{ $paddingClass }} {{ $class }}">
    {{ $slot }}
</div>

