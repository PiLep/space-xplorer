<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="dark"
>

<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>{{ config('app.name', 'Stellar') }} - @yield('title', 'Explore the Universe')</title>

    <!-- Favicons -->
    <link
        rel="apple-touch-icon"
        sizes="180x180"
        href="{{ asset('apple-touch-icon.png') }}"
    >
    <link
        rel="icon"
        type="image/png"
        sizes="32x32"
        href="{{ asset('favicon-32x32.png') }}"
    >
    <link
        rel="icon"
        type="image/png"
        sizes="16x16"
        href="{{ asset('favicon-16x16.png') }}"
    >
    <link
        rel="manifest"
        href="{{ asset('site.webmanifest') }}"
    >
    <link
        rel="icon"
        href="{{ asset('favicon.ico') }}"
    >

    <!-- Fonts -->
    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >
    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Share+Tech+Mono&display=swap"
        rel="stylesheet"
    >

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="dark:bg-space-black scanlines grain bg-gray-50 antialiased">
    <div class="flex min-h-screen flex-col">
        <!-- Main Content -->
        <main class="{{ request()->routeIs('home') ? '' : 'pb-16' }} flex-1">
            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endisset
        </main>

        <!-- Terminal Command Bar -->
        @unless (request()->routeIs('home'))
            <div
                class="bg-surface-dark dark:bg-surface-dark border-border-dark dark:border-border-dark fixed bottom-0 left-0 right-0 z-50 border-t font-mono">
                <div class="w-full px-4 py-3 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <a
                                href="{{ route('home') }}"
                                class="flex-shrink-0"
                            >
                                <x-logo
                                    size="xs"
                                    :showScanlines="false"
                                />
                            </a>
                            <span class="text-sm text-gray-500 dark:text-gray-500">
                                @auth
                                    @php
                                        $user = auth()->user();
                                        if (!$user->relationLoaded('homePlanet')) {
                                            $user->load('homePlanet');
                                        }
                                        $planetName = $user->homePlanet?->name ?? 'STELLAR';
                                        $userName = str_replace(' ', '_', strtoupper($user->name));
                                        $planetNameUpper = str_replace(' ', '_', strtoupper($planetName));
                                    @endphp
                                    <span class="text-warning dark:text-warning">{{ $userName }}</span><span
                                        class="text-gray-400 dark:text-gray-400"
                                    >[{{ $user->matricule }}]</span><span
                                        class="text-gray-500 dark:text-gray-500">@</span><span
                                        class="text-space-secondary dark:text-space-secondary"
                                    >{{ $planetNameUpper }}</span><span class="text-gray-500 dark:text-gray-500">:~$</span>
                                @else
                                    SYSTEM@STELLAR:~$
                                @endauth
                            </span>
                            <x-universe-time-status />
                            <div class="flex items-center gap-4 text-sm">
                                @auth
                                    <a
                                        href="{{ route('dashboard') }}"
                                        wire:navigate
                                        class="text-space-primary dark:text-space-primary hover:text-space-primary-light dark:hover:text-space-primary-light cursor-pointer transition-colors"
                                    >
                                        > DASHBOARD
                                    </a>
                                    <a
                                        href="{{ route('inbox') }}"
                                        wire:navigate
                                        class="text-space-primary dark:text-space-primary hover:text-space-primary-light dark:hover:text-space-primary-light {{ ($unreadMessagesCount ?? 0) > 0 ? 'text-pulse inline-block' : '' }} cursor-pointer transition-colors"
                                    >
                                        > INBOX
                                    </a>
                                    <a
                                        href="{{ route('profile') }}"
                                        wire:navigate
                                        class="text-space-primary dark:text-space-primary hover:text-space-primary-light dark:hover:text-space-primary-light cursor-pointer transition-colors"
                                    >
                                        > EMPLOYEE_STATUS
                                    </a>
                                @else
                                    <a
                                        href="{{ route('login') }}"
                                        wire:navigate
                                        class="text-space-primary dark:text-space-primary hover:text-space-primary-light dark:hover:text-space-primary-light cursor-pointer transition-colors"
                                    >
                                        > LOGIN
                                    </a>
                                    <a
                                        href="{{ route('register') }}"
                                        wire:navigate
                                        class="text-space-primary dark:text-space-primary hover:text-space-primary-light dark:hover:text-space-primary-light cursor-pointer transition-colors"
                                    >
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
                                    @if (auth()->user()->is_super_admin)
                                        <a
                                            href="{{ route('admin.access') }}"
                                            class="text-space-secondary dark:text-space-secondary hover:text-space-secondary-light dark:hover:text-space-secondary-light cursor-pointer px-2 py-1 transition-colors"
                                        >
                                            > ADMIN
                                        </a>
                                    @endif
                                    <form
                                        method="POST"
                                        action="{{ route('logout') }}"
                                        class="relative z-50 inline"
                                    >
                                        @csrf
                                        <button
                                            type="submit"
                                            class="text-error dark:text-error hover:text-error-light dark:hover:text-error-light relative z-50 cursor-pointer px-2 py-1 transition-colors"
                                        >
                                            > LOGOUT
                                        </button>
                                    </form>
                                @endauth
                            @endisset
                        </div>
                    </div>
                </div>
            </div>
        @endunless

        <!-- Footer -->
        <footer class="dark:bg-surface-dark dark:border-border-dark mt-auto hidden border-t border-gray-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-gray-600 dark:text-gray-400">
                    &copy; {{ date('Y') }} Stellar. Explore the universe.
                </p>
            </div>
        </footer>
    </div>

    @livewireScripts
    @stack('scripts')

    <!-- Screen Size Error Check -->
    <x-screen-size-error />
</body>

</html>
