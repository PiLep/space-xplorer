<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Space Xplorer') }} - @yield('title', 'Explore the Universe')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Share+Tech+Mono&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 dark:bg-space-black antialiased scanlines grain">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="bg-white dark:bg-surface-dark shadow-sm border-b border-gray-200 dark:border-border-dark dark:glow-border-primary">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="text-xl font-bold text-gray-900 dark:text-white dark:text-glow-subtle">
                            Space Xplorer
                        </a>
                    </div>

                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                                Dashboard
                            </a>
                            <a href="{{ route('profile') }}" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                                Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                                    Logout
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                Register
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1 pb-16">
            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endisset
        </main>

        <!-- Terminal Command Bar -->
        <div class="fixed bottom-0 left-0 right-0 bg-surface-dark dark:bg-surface-dark border-t border-border-dark dark:border-border-dark font-mono z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                <div class="flex items-center gap-2">
                    <span class="text-gray-500 dark:text-gray-500 text-sm">SYSTEM@SPACE-XPLORER:~$</span>
                    <div class="flex-1 flex items-center gap-4 text-sm">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-space-primary dark:text-space-primary hover:text-space-primary-light dark:hover:text-space-primary-light transition-colors">
                                > DASHBOARD
                            </a>
                            <a href="{{ route('profile') }}" class="text-space-primary dark:text-space-primary hover:text-space-primary-light dark:hover:text-space-primary-light transition-colors">
                                > PROFILE
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-error dark:text-error hover:text-error-light dark:hover:text-error-light transition-colors">
                                    > LOGOUT
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-space-primary dark:text-space-primary hover:text-space-primary-light dark:hover:text-space-primary-light transition-colors">
                                > LOGIN
                            </a>
                            <a href="{{ route('register') }}" class="text-space-primary dark:text-space-primary hover:text-space-primary-light dark:hover:text-space-primary-light transition-colors">
                                > REGISTER
                            </a>
                        @endauth
                        <a href="{{ route('home') }}" class="text-gray-500 dark:text-gray-500 hover:text-gray-400 dark:hover:text-gray-400 transition-colors">
                            > HOME
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white dark:bg-surface-dark border-t border-gray-200 dark:border-border-dark mt-auto hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <p class="text-center text-sm text-gray-600 dark:text-gray-400">
                    &copy; {{ date('Y') }} Space Xplorer. Explore the universe.
                </p>
            </div>
        </footer>
    </div>

    @livewireScripts
</body>
</html>

