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
                class="bg-surface-dark dark:bg-surface-dark border-border-dark dark:border-border-dark fixed bottom-0 left-0 right-0 z-50 border-t font-mono"
            >
                <div class="w-full px-4 py-3 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                            <a
                                href="{{ route('home') }}"
                                class="flex-shrink-0"
                            >
                                <x-logo
                                    size="xs"
                                    :showScanlines="false"
                                />
                            </a>
                            <!-- Prompt terminal - Masqué sur mobile -->
                            <span class="hidden md:inline text-sm text-gray-500 dark:text-gray-500 whitespace-nowrap">
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
                            <!-- Universe time - Masqué sur très petits écrans -->
                            <span class="hidden lg:inline">
                                <x-universe-time-status />
                            </span>
                            <!-- Navigation principale -->
                            <div class="flex items-center gap-2 sm:gap-4 text-sm ml-auto">
                                @auth
                                    <a
                                        href="{{ route('dashboard') }}"
                                        wire:navigate
                                        class="text-space-primary dark:text-space-primary hover:text-space-primary-light dark:hover:text-space-primary-light cursor-pointer transition-colors whitespace-nowrap"
                                    >
                                        > DASHBOARD
                                    </a>
                                    <a
                                        href="{{ route('inbox') }}"
                                        wire:navigate
                                        class="text-space-primary dark:text-space-primary hover:text-space-primary-light dark:hover:text-space-primary-light {{ ($unreadMessagesCount ?? 0) > 0 ? 'text-pulse inline-block' : '' }} cursor-pointer transition-colors whitespace-nowrap"
                                    >
                                        > INBOX
                                    </a>
                                    <livewire:notification-badge wire:key="notification-badge" />
                                @else
                                    <a
                                        href="{{ route('login') }}"
                                        wire:navigate
                                        class="text-space-primary dark:text-space-primary hover:text-space-primary-light dark:hover:text-space-primary-light cursor-pointer transition-colors whitespace-nowrap"
                                    >
                                        > LOGIN
                                    </a>
                                    <a
                                        href="{{ route('register') }}"
                                        wire:navigate
                                        class="text-space-primary dark:text-space-primary hover:text-space-primary-light dark:hover:text-space-primary-light cursor-pointer transition-colors whitespace-nowrap"
                                    >
                                        > REGISTER
                                    </a>
                                @endauth
                            </div>
                        </div>
                        <!-- Menu "More" pour les actions secondaires -->
                        <div class="flex items-center gap-2 sm:gap-4 text-sm flex-shrink-0">
                            @isset($bottomBarActions)
                                {{ $bottomBarActions }}
                            @else
                                @auth
                                    <div class="relative" id="more-menu-container">
                                        <button
                                            type="button"
                                            id="more-menu-button"
                                            class="text-space-primary dark:text-space-primary hover:text-space-primary-light dark:hover:text-space-primary-light cursor-pointer px-2 py-1 transition-colors whitespace-nowrap"
                                            aria-label="Plus d'options"
                                            aria-expanded="false"
                                            aria-haspopup="true"
                                        >
                                            > MORE
                                        </button>
                                        <!-- Dropdown menu -->
                                        <div
                                            id="more-menu-dropdown"
                                            class="absolute bottom-full right-0 mb-2 w-48 bg-surface-dark dark:bg-surface-dark border border-border-dark dark:border-border-dark rounded-lg shadow-lg z-50 opacity-0 invisible scale-95 transition-all duration-100"
                                        >
                                            <div class="py-1">
                                                <a
                                                    href="{{ route('profile') }}"
                                                    wire:navigate
                                                    class="block px-4 py-2 text-sm text-space-primary dark:text-space-primary hover:bg-surface-medium dark:hover:bg-surface-medium transition-colors"
                                                >
                                                    > EMPLOYEE_STATUS
                                                </a>
                                                @if (auth()->user()->is_super_admin)
                                                    <a
                                                        href="{{ route('admin.access') }}"
                                                        wire:navigate
                                                        class="block px-4 py-2 text-sm text-space-secondary dark:text-space-secondary hover:bg-surface-medium dark:hover:bg-surface-medium transition-colors"
                                                    >
                                                        > ADMIN
                                                    </a>
                                                @endif
                                                <form
                                                    method="POST"
                                                    action="{{ route('logout') }}"
                                                    class="block"
                                                >
                                                    @csrf
                                                    <button
                                                        type="submit"
                                                        class="w-full text-left px-4 py-2 text-sm text-error dark:text-error hover:bg-surface-medium dark:hover:bg-surface-medium transition-colors"
                                                    >
                                                        > LOGOUT
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                        (function() {
                                            const button = document.getElementById('more-menu-button');
                                            const dropdown = document.getElementById('more-menu-dropdown');
                                            const container = document.getElementById('more-menu-container');
                                            
                                            if (!button || !dropdown) return;
                                            
                                            function toggleMenu() {
                                                const isOpen = dropdown.classList.contains('opacity-100');
                                                if (isOpen) {
                                                    dropdown.classList.remove('opacity-100', 'visible', 'scale-100');
                                                    dropdown.classList.add('opacity-0', 'invisible', 'scale-95');
                                                    button.setAttribute('aria-expanded', 'false');
                                                } else {
                                                    dropdown.classList.remove('opacity-0', 'invisible', 'scale-95');
                                                    dropdown.classList.add('opacity-100', 'visible', 'scale-100');
                                                    button.setAttribute('aria-expanded', 'true');
                                                }
                                            }
                                            
                                            button.addEventListener('click', function(e) {
                                                e.stopPropagation();
                                                toggleMenu();
                                            });
                                            
                                            document.addEventListener('click', function(e) {
                                                if (!container.contains(e.target)) {
                                                    dropdown.classList.remove('opacity-100', 'visible', 'scale-100');
                                                    dropdown.classList.add('opacity-0', 'invisible', 'scale-95');
                                                    button.setAttribute('aria-expanded', 'false');
                                                }
                                            });
                                            
                                            // Fermer le menu lors de la navigation
                                            dropdown.querySelectorAll('a').forEach(link => {
                                                link.addEventListener('click', function() {
                                                    dropdown.classList.remove('opacity-100', 'visible', 'scale-100');
                                                    dropdown.classList.add('opacity-0', 'invisible', 'scale-95');
                                                    button.setAttribute('aria-expanded', 'false');
                                                });
                                            });
                                        })();
                                    </script>
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
