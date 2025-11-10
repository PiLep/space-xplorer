@props([
    'variant' => 'default', // success, warning, error, info, generating, default
    'size' => 'md', // sm, md, lg
    'pulse' => false, // Animation pulse (pour generating)
    'terminal' => false, // Style terminal avec bordure
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium border transition-all duration-150 whitespace-nowrap';
    
    // Style arrondi ou terminal selon la variante
    $roundedClass = $terminal ? 'rounded font-mono' : 'rounded-full';
    
    $variantClasses = [
        'success' => $terminal 
            ? 'bg-success text-space-black border-success shadow-[0_0_8px_rgba(0,255,136,0.3)]' 
            : 'bg-success text-space-black border-success/30',
        'warning' => $terminal 
            ? 'bg-warning text-space-black border-warning shadow-[0_0_8px_rgba(255,170,0,0.3)]' 
            : 'bg-warning text-space-black border-warning/30',
        'error' => $terminal 
            ? 'bg-error text-white border-error shadow-[0_0_8px_rgba(255,68,68,0.3)]' 
            : 'bg-error text-white border-error/30',
        'info' => $terminal 
            ? 'bg-info text-white border-info shadow-[0_0_8px_rgba(0,170,255,0.3)]' 
            : 'bg-info text-white border-info/30',
        'generating' => $terminal 
            ? 'bg-info text-white border-info shadow-[0_0_8px_rgba(0,170,255,0.3)]' 
            : 'bg-info text-white border-info/30',
        'default' => $terminal 
            ? 'bg-gray-200 dark:bg-surface-medium text-gray-900 dark:text-gray-200 border-gray-300 dark:border-border-dark font-mono' 
            : 'bg-gray-200 dark:bg-surface-medium text-gray-900 dark:text-gray-200 border-gray-300 dark:border-border-dark',
    ];
    
    $sizeClasses = [
        'sm' => $terminal ? 'px-3 py-0.5 text-xs' : 'px-3 py-0.5 text-xs',
        'md' => $terminal ? 'px-3 py-0.5 text-xs' : 'px-3 py-0.5 text-xs',
        'lg' => $terminal ? 'px-4 py-1 text-sm' : 'px-4 py-1 text-sm',
    ];
    
    $pulseClass = $pulse ? 'animate-pulse' : '';
    
    $classes = $baseClasses . ' ' . $roundedClass . ' ' . $variantClasses[$variant] . ' ' . $sizeClasses[$size] . ' ' . $pulseClass;
    
    $attributes = $attributes->merge(['class' => $classes]);
@endphp

<span {{ $attributes }}>
    {{ $slot }}
</span>

