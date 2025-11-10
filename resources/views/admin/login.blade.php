<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Login - {{ config('app.name', 'Space Xplorer') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Share+Tech+Mono&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-space-black antialiased">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                    Admin Panel
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                    Sign in to access the admin panel
                </p>
            </div>
            <form class="mt-8 space-y-6" action="{{ route('admin.login') }}" method="POST">
                @csrf

                @if(isset($errors) && $errors->any())
                    <x-alert type="error" :message="implode(' ', $errors->all())" :showPrompt="false" />
                @endif

                <div class="space-y-4">
                    <x-form-input
                        type="email"
                        name="email"
                        label="Email address"
                        placeholder="Email address"
                        :value="old('email')"
                        required
                        autofocus
                    />

                    <x-form-input
                        type="password"
                        name="password"
                        label="Password"
                        placeholder="Password"
                        required
                    />
                </div>

                <div>
                    <x-button type="submit" variant="primary" size="lg" class="w-full">
                        Sign in
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

