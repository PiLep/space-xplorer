@props([
    'command' => '',
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

<div class="text-sm text-space-primary dark:text-space-primary mb-2">
    <span class="text-gray-500 dark:text-gray-500">{{ $prompt }}</span>
    @if($command)
        <span class="text-space-primary dark:text-space-primary">{{ $command }}</span>
    @endif
</div>

