@props([
    'align' => 'center', // center, left, right
    'spacing' => 'md', // sm, md, lg
    'fullWidth' => false,
])

@php
    $alignClasses = [
        'center' => 'justify-center',
        'left' => 'justify-start',
        'right' => 'justify-end',
    ];
    
    $spacingClasses = [
        'sm' => 'gap-2',
        'md' => 'gap-4',
        'lg' => 'gap-6',
    ];
    
    $baseClasses = 'flex flex-wrap ' . $alignClasses[$align] . ' ' . $spacingClasses[$spacing];
    
    if ($fullWidth) {
        $baseClasses .= ' w-full';
    }
@endphp

<div {{ $attributes->merge(['class' => $baseClasses]) }}>
    {{ $slot }}
</div>




