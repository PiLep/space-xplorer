@extends('layouts.app')

@section('title', 'Welcome to Space Xplorer')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="mb-16 text-center">
            <h1 class="mb-4 text-5xl font-bold text-gray-900 dark:text-white">
                Welcome to Space Xplorer
            </h1>
            <p class="mx-auto mb-8 max-w-3xl text-xl text-gray-600 dark:text-gray-400">
                Embark on an epic journey through the cosmos. Discover unique planets, explore star systems,
                and build your interstellar empire in this immersive space exploration game.
            </p>
            <div class="flex justify-center space-x-4">
                @auth
                    <a
                        href="{{ route('dashboard') }}"
                        class="rounded-lg bg-blue-600 px-8 py-3 text-lg font-semibold text-white transition-colors hover:bg-blue-700"
                    >
                        Go to Dashboard
                    </a>
                @else
                    <a
                        href="{{ route('register') }}"
                        class="rounded-lg bg-blue-600 px-8 py-3 text-lg font-semibold text-white transition-colors hover:bg-blue-700"
                    >
                        Start Your Journey
                    </a>
                    <a
                        href="{{ route('login') }}"
                        class="rounded-lg bg-gray-200 px-8 py-3 text-lg font-semibold text-gray-900 transition-colors hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600"
                    >
                        Sign In
                    </a>
                @endauth
            </div>
        </div>

        <!-- Features Section -->
        <div class="mb-16 grid gap-8 md:grid-cols-3">
            <div class="rounded-lg bg-white p-6 shadow-md dark:bg-gray-800">
                <h3 class="mb-2 text-xl font-semibold text-gray-900 dark:text-white">
                    Explore the Universe
                </h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Discover countless star systems, each with unique planets waiting to be explored.
                </p>
            </div>

            <div class="rounded-lg bg-white p-6 shadow-md dark:bg-gray-800">
                <h3 class="mb-2 text-xl font-semibold text-gray-900 dark:text-white">
                    Your Home Planet
                </h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Every explorer starts with a unique home planet, procedurally generated just for you.
                </p>
            </div>

            <div class="rounded-lg bg-white p-6 shadow-md dark:bg-gray-800">
                <h3 class="mb-2 text-xl font-semibold text-gray-900 dark:text-white">
                    Build Your Empire
                </h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Expand your influence across the galaxy and become the ultimate space explorer.
                </p>
            </div>
        </div>

        <!-- Call to Action -->
        @guest
            <div class="rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 p-12 text-center text-white">
                <h2 class="mb-4 text-3xl font-bold">Ready to Begin Your Adventure?</h2>
                <p class="mb-8 text-xl opacity-90">
                    Join thousands of explorers discovering the mysteries of the universe.
                </p>
                <a
                    href="{{ route('register') }}"
                    class="inline-block rounded-lg bg-white px-8 py-3 text-lg font-semibold text-blue-600 transition-colors hover:bg-gray-100"
                >
                    Create Your Account
                </a>
            </div>
        @endguest
    </div>
@endsection
