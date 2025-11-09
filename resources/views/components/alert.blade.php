@props([
    'type' => 'error', // error, warning, success, info
    'message' => '',
    'showPrompt' => true,
    'prompt' => null, // Si null, utilise le prompt dynamique selon l'état de connexion
])

@php
    if ($prompt === null) {
        if (auth()->check()) {
            $user = auth()->user();
            if (!$user->relationLoaded('homePlanet')) {
                $user->load('homePlanet');
            }
            $planetName = $user->homePlanet?->name ?? 'SPACE-XPLORER';
            $userName = str_replace(' ', '_', strtoupper($user->name));
            $planetNameUpper = str_replace(' ', '_', strtoupper($planetName));
            $prompt = $userName . '@' . $planetNameUpper . ':~$';
        } else {
            $prompt = 'SYSTEM@SPACE-XPLORER:~$';
        }
    }
@endphp

@php
    $typeConfig = [
        'error' => [
            'promptColor' => 'text-error dark:text-error',
            'bgColor' => 'bg-red-200 dark:bg-red-900/50',
            'borderColor' => 'border-red-400 dark:border-error',
            'textColor' => 'text-red-800 dark:text-error',
            'prefix' => '[ERROR]',
        ],
        'warning' => [
            'promptColor' => 'text-warning dark:text-warning',
            'bgColor' => 'bg-yellow-200 dark:bg-yellow-900/50',
            'borderColor' => 'border-yellow-400 dark:border-warning',
            'textColor' => 'text-yellow-800 dark:text-warning',
            'prefix' => '[WARNING]',
        ],
        'success' => [
            'promptColor' => 'text-space-primary dark:text-space-primary',
            'bgColor' => 'bg-green-200 dark:bg-green-900/50',
            'borderColor' => 'border-green-400 dark:border-space-primary',
            'textColor' => 'text-green-800 dark:text-space-primary',
            'prefix' => '[SUCCESS]',
        ],
        'info' => [
            'promptColor' => 'text-space-secondary dark:text-space-secondary',
            'bgColor' => 'bg-blue-200 dark:bg-blue-900/50',
            'borderColor' => 'border-blue-400 dark:border-space-secondary',
            'textColor' => 'text-blue-800 dark:text-space-secondary',
            'prefix' => '[INFO]',
        ],
    ];
    
    $config = $typeConfig[$type] ?? $typeConfig['error'];
@endphp

<div class="font-mono mb-4">
    @if($showPrompt)
        <div class="text-sm {{ $config['promptColor'] }} mb-2">
            <span class="text-gray-500 dark:text-gray-500">{{ $prompt }}</span>
            <span class="{{ $config['promptColor'] }}">{{ strtoupper($type) }}</span>
        </div>
    @endif
    <div class="{{ $config['bgColor'] }} border {{ $config['borderColor'] }} {{ $config['textColor'] }} px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ $config['prefix'] }} {{ $message }}</span>
    </div>
</div>

