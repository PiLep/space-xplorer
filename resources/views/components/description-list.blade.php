@props([
    'columns' => 1, // 1, 2, 3
])

@php
    $gridClasses = [
        1 => 'grid-cols-1',
        2 => 'grid-cols-1 gap-x-4 sm:grid-cols-2',
        3 => 'grid-cols-1 gap-x-4 sm:grid-cols-2 lg:grid-cols-3',
    ];
    
    $gridClass = $gridClasses[$columns] ?? $gridClasses[1];
@endphp

<dl class="grid {{ $gridClass }} gap-y-6">
    {{ $slot }}
</dl>


