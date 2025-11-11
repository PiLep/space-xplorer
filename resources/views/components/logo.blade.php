@props([
    'size' => 'lg', // xs, sm, md, lg, xl
    'showScanlines' => true,
])

@php
    $sizeClasses = [
        'xs' => 'text-base sm:text-lg',
        'sm' => 'text-2xl sm:text-3xl',
        'md' => 'text-3xl sm:text-4xl',
        'lg' => 'text-4xl sm:text-5xl lg:text-6xl',
        'xl' => 'text-5xl sm:text-6xl lg:text-7xl',
    ];
    
    $classes = $sizeClasses[$size] ?? $sizeClasses['lg'];
    $scanlinesClass = $showScanlines ? 'scanlines-title' : '';
@endphp

<h1 class="{{ $classes }} font-bold font-mono text-space-primary dark:text-space-primary text-glow-primary pulse-glow {{ $scanlinesClass }}" style="letter-spacing: 0.15em; line-height: 1;" @if($showScanlines) data-text="STELLAR" @endif>
    STELLAR
</h1>

