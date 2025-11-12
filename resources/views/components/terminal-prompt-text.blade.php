@php
    if (auth()->check()) {
        $user = auth()->user();
        if (!$user->relationLoaded('homePlanet')) {
            $user->load('homePlanet');
        }
        $planetName = $user->homePlanet?->name ?? 'STELLAR';
        $userName = str_replace(' ', '_', strtoupper($user->name));
        $planetNameUpper = str_replace(' ', '_', strtoupper($planetName));
        $prompt = $userName . '[' . $user->matricule . ']@' . $planetNameUpper . ':~$';
    } else {
        $prompt = 'SYSTEM@STELLAR:~$';
    }
@endphp

{{ $prompt }}

