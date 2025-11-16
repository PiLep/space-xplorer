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
    <div class="flex min-h-screen flex-col">
        <!-- Navigation -->
        <nav class="bg-surface-dark dark:bg-surface-dark border-border-dark dark:border-border-dark border-b">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between">
                    <div class="flex">
                        <div class="flex flex-shrink-0 items-center">
                            <span class="text-space-primary dark:text-space-primary font-mono text-lg font-bold">
                                ADMIN PANEL
                            </span>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a
                                href="{{ route('admin.dashboard') }}"
                                wire:navigate
                                class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                            >
                                Dashboard
                            </a>
                            <a
                                href="{{ route('admin.users.index') }}"
                                wire:navigate
                                class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                            >
                                Users
                            </a>
                            <a
                                href="{{ route('admin.resources.index') }}"
                                wire:navigate
                                class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                            >
                                Resources
                            </a>
                            <a
                                href="{{ route('admin.map') }}"
                                wire:navigate
                                class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                            >
                                Map
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="mr-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ auth()->guard('admin')->user()->email }}
                        </span>
                        <form
                            method="POST"
                            action="{{ route('admin.logout') }}"
                        >
                            @csrf
                            <x-button
                                type="submit"
                                variant="danger"
                                size="sm"
                            >
                                Logout
                            </x-button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1">
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
