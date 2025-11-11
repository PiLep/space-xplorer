@props([
    'percentage' => 0,
    'color' => 'blue', // red, orange, green, blue
    'height' => 'h-3', // Tailwind height class
])

@php
    $percentage = max(0, min(100, (float)$percentage));
    $colorClasses = [
        'red' => 'bg-red-600',
        'orange' => 'bg-orange-600',
        'green' => 'bg-green-600',
        'blue' => 'bg-blue-600',
    ];
    $progressColor = $colorClasses[$color] ?? 'bg-blue-600';
@endphp

<div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full {{ $height }} overflow-hidden">
    <div 
        class="{{ $progressColor }} rounded-full" 
        style="width: {{ $percentage }}%; height: 100%;"
    ></div>
</div>

