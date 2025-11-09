@props([
    'message', // Message à afficher (ex: "[OK] System ready")
    'marginBottom' => 'mb-2', // Margin bottom personnalisable
])

@php
    // Détection automatique du type de message basée sur le préfixe
    $type = 'default';
    $colorClass = 'text-gray-500 dark:text-gray-500';
    
    if (str_contains($message, '[OK]') || str_contains($message, '[SUCCESS]') || str_contains($message, '[READY]')) {
        $type = 'success';
        $colorClass = 'text-space-primary dark:text-space-primary';
    } elseif (str_contains($message, '[ERROR]')) {
        $type = 'error';
        $colorClass = 'text-error dark:text-error';
    } elseif (str_contains($message, '[INFO]')) {
        $type = 'info';
        $colorClass = 'text-space-secondary dark:text-space-secondary';
    } elseif (str_contains($message, '[WAIT]') || str_contains($message, '[LOADING]')) {
        $type = 'wait';
        $colorClass = 'text-gray-500 dark:text-gray-500';
    }
@endphp

<div class="text-sm {{ $colorClass }} {{ $marginBottom ?: '' }}">
    {{ $message }}
</div>

