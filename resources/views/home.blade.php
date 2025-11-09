@extends('layouts.app')

@section('title', 'Welcome to Space Xplorer')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Hero Section -->
    <div class="text-center mb-16">
        <h1 class="text-5xl font-bold text-gray-900 dark:text-white mb-4">
            Welcome to Space Xplorer
        </h1>
        <p class="text-xl text-gray-600 dark:text-gray-400 mb-8 max-w-3xl mx-auto">
            Embark on an epic journey through the cosmos. Discover unique planets, explore star systems, 
            and build your interstellar empire in this immersive space exploration game.
        </p>
        <div class="flex justify-center space-x-4">
            @auth
                <a href="{{ route('dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg text-lg font-semibold transition-colors">
                    Go to Dashboard
                </a>
            @else
                <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg text-lg font-semibold transition-colors">
                    Start Your Journey
                </a>
                <a href="{{ route('login') }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white px-8 py-3 rounded-lg text-lg font-semibold transition-colors">
                    Sign In
                </a>
            @endauth
        </div>
    </div>

    <!-- Features Section -->
    <div class="grid md:grid-cols-3 gap-8 mb-16">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                Explore the Universe
            </h3>
            <p class="text-gray-600 dark:text-gray-400">
                Discover countless star systems, each with unique planets waiting to be explored.
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                Your Home Planet
            </h3>
            <p class="text-gray-600 dark:text-gray-400">
                Every explorer starts with a unique home planet, procedurally generated just for you.
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                Build Your Empire
            </h3>
            <p class="text-gray-600 dark:text-gray-400">
                Expand your influence across the galaxy and become the ultimate space explorer.
            </p>
        </div>
    </div>

    <!-- Call to Action -->
    @guest
    <div class="text-center bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-12 text-white">
        <h2 class="text-3xl font-bold mb-4">Ready to Begin Your Adventure?</h2>
        <p class="text-xl mb-8 opacity-90">
            Join thousands of explorers discovering the mysteries of the universe.
        </p>
        <a href="{{ route('register') }}" class="bg-white text-blue-600 hover:bg-gray-100 px-8 py-3 rounded-lg text-lg font-semibold transition-colors inline-block">
            Create Your Account
        </a>
    </div>
    @endguest
</div>
@endsection

