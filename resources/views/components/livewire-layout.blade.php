<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Space Xplorer') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Share+Tech+Mono&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 dark:bg-space-black antialiased scanlines grain">
    <div class="pb-16">
        {{ $slot }}
    </div>

    <!-- Terminal Command Bar -->
    <div class="fixed bottom-0 left-0 right-0 bg-surface-dark dark:bg-surface-dark border-t border-border-dark dark:border-border-dark font-mono z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <span class="text-gray-500 dark:text-gray-500 text-sm">
                        @auth
                            @php
                                $user = auth()->user();
                                if (!$user->relationLoaded('homePlanet')) {
                                    $user->load('homePlanet');
                                }
                                $planetName = $user->homePlanet?->name ?? 'SPACE-XPLORER';
                                $userName = str_replace(' ', '_', strtoupper($user->name));
                                $planetNameUpper = str_replace(' ', '_', strtoupper($planetName));
                            @endphp
                            <span class="text-warning dark:text-warning">{{ $userName }}</span><span class="text-gray-500 dark:text-gray-500">@</span><span class="text-space-secondary dark:text-space-secondary">{{ $planetNameUpper }}</span><span class="text-gray-500 dark:text-gray-500">:~$</span>
                        @else
                            SYSTEM@SPACE-XPLORER:~$
                        @endauth
                    </span>
                    <div class="flex items-center gap-4 text-sm">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-space-primary dark:text-space-primary hover:text-space-primary-light dark:hover:text-space-primary-light transition-colors">
                                > DASHBOARD
                            </a>
                            <a href="{{ route('profile') }}" class="text-space-primary dark:text-space-primary hover:text-space-primary-light dark:hover:text-space-primary-light transition-colors">
                                > PROFILE
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-space-primary dark:text-space-primary hover:text-space-primary-light dark:hover:text-space-primary-light transition-colors">
                                > LOGIN
                            </a>
                            <a href="{{ route('register') }}" class="text-space-primary dark:text-space-primary hover:text-space-primary-light dark:hover:text-space-primary-light transition-colors">
                                > REGISTER
                            </a>
                        @endauth
                    </div>
                </div>
                <div class="flex items-center gap-4 text-sm">
                    @isset($bottomBarActions)
                        {{ $bottomBarActions }}
                    @else
                        @auth
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-error dark:text-error hover:text-error-light dark:hover:text-error-light transition-colors">
                                    > LOGOUT
                                </button>
                            </form>
                        @endauth
                    @endisset
                </div>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>

