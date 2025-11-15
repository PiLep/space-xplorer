<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Panel - {{ config('app.name', 'Stellar') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Share+Tech+Mono&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 dark:bg-space-black antialiased">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="bg-surface-dark dark:bg-surface-dark border-b border-border-dark dark:border-border-dark">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <span class="text-space-primary dark:text-space-primary font-mono font-bold text-lg">
                                ADMIN PANEL
                            </span>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="{{ route('admin.dashboard') }}" wire:navigate class="border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Dashboard
                            </a>
                            <a href="{{ route('admin.users.index') }}" wire:navigate class="border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Users
                            </a>
                            <a href="{{ route('admin.resources.index') }}" wire:navigate class="border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Resources
                            </a>
                            <a href="{{ route('admin.minigame.test') }}" wire:navigate class="border-transparent text-gray-500 dark:text-gray-400 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Minigame Test
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="text-gray-500 dark:text-gray-400 text-sm mr-4">
                            {{ auth()->guard('admin')->user()->email }}
                        </span>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <x-button type="submit" variant="danger" size="sm">
                                Logout
                            </x-button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                @if(session('success'))
                    <x-alert type="success" :message="session('success')" :showPrompt="false" />
                @endif

                @if(isset($errors) && $errors->any())
                    <x-alert type="error" :message="implode(' ', $errors->all())" :showPrompt="false" />
                @endif

                @yield('content')
            </div>
        </main>
    </div>
    @livewireScripts
    @stack('scripts')
</body>
</html>

