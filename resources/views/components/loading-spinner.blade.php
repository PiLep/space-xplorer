@props([
    'message' => '[LOADING] Loading...',
    'size' => 'md', // sm, md, lg
    'showMessage' => true,
    'variant' => 'terminal', // terminal, simple
])

@php
    $sizeConfig = [
        'sm' => 'h-8 w-8 border-b-2',
        'md' => 'h-12 w-12 border-b-2',
        'lg' => 'h-14 w-14 border-b-3',
    ];
    
    $spinnerClasses = $sizeConfig[$size] ?? $sizeConfig['md'];
    $containerClasses = $variant === 'simple' ? 'flex justify-center items-center py-12' : 'flex justify-center items-center py-12 font-mono';
@endphp

<div class="{{ $containerClasses }}">
    @if($variant === 'simple')
        <div class="animate-spin rounded-full {{ $spinnerClasses }} border-space-primary"></div>
    @else
        <div class="text-center">
            @if($showMessage)
                <div class="text-sm text-gray-500 dark:text-gray-500 mb-4">
                    {{ $message }}
                </div>
            @endif
            <div class="animate-spin rounded-full {{ $spinnerClasses }} border-space-primary mx-auto"></div>
        </div>
    @endif
</div>

