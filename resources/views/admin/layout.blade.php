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

    <title>Admin Panel - {{ config('app.name', 'Stellar') }}</title>

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

<body class="dark:bg-space-black bg-gray-50 antialiased">
    <div class="flex min-h-screen">
        <!-- Sidebar Navigation -->
        <aside class="w-64 flex-shrink-0 bg-surface-dark dark:bg-surface-dark border-r border-border-dark dark:border-border-dark">
            <div class="flex h-full flex-col">
                <!-- Logo/Header -->
                <div class="flex h-16 items-center border-b border-border-dark dark:border-border-dark px-6">
                    <span class="text-space-primary dark:text-space-primary font-mono text-lg font-bold">
                        ADMIN PANEL
                    </span>
                </div>

                <!-- Navigation Menu -->
                <nav class="flex-1 overflow-y-auto px-4 py-6">
                    <div class="space-y-1 font-mono">
                        <a
                            href="{{ route('admin.dashboard') }}"
                            wire:navigate
                            class="block px-4 py-2 rounded text-sm transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-space-primary text-space-black font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary hover:bg-gray-800 dark:hover:bg-gray-800' }}"
                            @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif
                        >
                            > DASHBOARD
                        </a>
                        <a
                            href="{{ route('admin.users.index') }}"
                            wire:navigate
                            class="block px-4 py-2 rounded text-sm transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-space-primary text-space-black font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary hover:bg-gray-800 dark:hover:bg-gray-800' }}"
                            @if(request()->routeIs('admin.users.*')) aria-current="page" @endif
                        >
                            > USERS
                        </a>
                        <a
                            href="{{ route('admin.resources.index') }}"
                            wire:navigate
                            class="block px-4 py-2 rounded text-sm transition-colors {{ request()->routeIs('admin.resources.*') ? 'bg-space-primary text-space-black font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary hover:bg-gray-800 dark:hover:bg-gray-800' }}"
                            @if(request()->routeIs('admin.resources.*')) aria-current="page" @endif
                        >
                            > RESOURCES
                        </a>
                        <a
                            href="{{ route('admin.map') }}"
                            wire:navigate
                            class="block px-4 py-2 rounded text-sm transition-colors {{ request()->routeIs('admin.map') ? 'bg-space-primary text-space-black font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary hover:bg-gray-800 dark:hover:bg-gray-800' }}"
                            @if(request()->routeIs('admin.map')) aria-current="page" @endif
                        >
                            > MAP
                        </a>
                        <a
                            href="{{ route('admin.scheduled-tasks.index') }}"
                            wire:navigate
                            class="block px-4 py-2 rounded text-sm transition-colors {{ request()->routeIs('admin.scheduled-tasks.*') ? 'bg-space-primary text-space-black font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary hover:bg-gray-800 dark:hover:bg-gray-800' }}"
                            @if(request()->routeIs('admin.scheduled-tasks.*')) aria-current="page" @endif
                        >
                            > SCHEDULED TASKS
                        </a>
                    </div>
                </nav>

                <!-- User Info & Logout -->
                <div class="border-t border-border-dark dark:border-border-dark px-4 py-4">
                    <div class="mb-3">
                        <span class="text-xs text-gray-500 dark:text-gray-400 font-mono">
                            {{ auth()->guard('admin')->user()->email }}
                        </span>
                    </div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <x-button
                            type="submit"
                            variant="danger"
                            size="sm"
                            class="w-full"
                        >
                            LOGOUT
                        </x-button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                @if (session('success'))
                    <x-alert
                        type="success"
                        :message="session('success')"
                        :showPrompt="false"
                    />
                @endif

                @if (isset($errors) && $errors->any())
                    <x-alert
                        type="error"
                        :message="implode(' ', $errors->all())"
                        :showPrompt="false"
                    />
                @endif

                @yield('content')
            </div>
        </main>
    </div>
    @livewireScripts
    @stack('scripts')
</body>

</html>
