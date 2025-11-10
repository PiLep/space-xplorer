@php
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
@endphp

{{ $prompt }}

