@extends('layouts.app')

@section('title', 'Stellar')

@section('content')
<div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-textured">
    <!-- Background Effects -->
    <div class="absolute inset-0 scan-effect"></div>
    
    <!-- Main Content -->
    <div class="relative z-10 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto w-full">
        <!-- Terminal Prompt - Always visible, typed first -->
        <div class="mb-12 font-mono text-left w-full" id="terminal-prompt-container">
            <div id="terminal-prompt" class="text-sm text-space-primary dark:text-space-primary mb-2 w-full"></div>
            <div id="loading-messages" class="space-y-1 mt-4 opacity-0 w-full"></div>
        </div>
        
        <!-- Main Content - Hidden until loading completes, appears in same flow -->
        <div id="main-content" class="opacity-0 w-full">
            <!-- Corporate Header - Left aligned -->
            <div class="mb-12 font-mono text-left text-reveal" style="animation-delay: 0s;">
                <x-terminal-prompt command="access_recruitment_portal" prompt="HR@STELLAR:~$" />
                
                <div class="space-y-1 mt-4">
                    <div class="text-xs text-gray-500 dark:text-gray-500 text-reveal" style="animation-delay: 0.3s;">
                        <span data-typewriter="classified"></span>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-500 text-reveal" style="animation-delay: 0.6s;">
                        <span data-typewriter="ref">[REF] STL-REC-{{ date('Y') }}-{{ strtoupper(substr(md5(time()), 0, 6)) }}</span>
                    </div>
                    <div class="text-sm text-space-primary dark:text-space-primary text-reveal glitch-subtle" style="animation-delay: 0.9s;" data-status="active">
                        <span data-typewriter="status"></span>
                    </div>
                </div>
            </div>

            <!-- Main Title - Centered -->
            <div class="mb-8 text-reveal text-center" style="animation-delay: 1.2s;">
                <x-logo size="lg" :showScanlines="true" />
            </div>

            <!-- Corporate Tagline - Centered -->
            <div class="mb-12 font-mono text-reveal text-center" style="animation-delay: 1.5s;">
                <div class="text-sm text-gray-500 dark:text-gray-500 mb-4">
                    EXPLORATION DIVISION
                </div>
                <div class="text-xs text-gray-600 dark:text-gray-600 max-w-sm mx-auto leading-relaxed" id="tagline-container">
                    <span data-typewriter="tagline"></span>
                </div>
            </div>

            <!-- CTA Button - Centered -->
            <div class="text-reveal mb-6 text-center" style="animation-delay: 1.8s;">
                <a 
                    href="{{ route('register') }}" 
                    wire:navigate
                    class="inline-block bg-transparent border-2 border-space-primary hover:border-space-primary-dark text-space-primary hover:text-space-primary-light font-bold py-4 rounded-lg transition-all duration-300 hover:glow-primary font-mono text-base tracking-widest uppercase relative"
                    style="padding-left: 4rem; padding-right: 4rem;"
                >
                    <span class="relative z-10">APPLY_NOW</span>
                </a>
            </div>

            <!-- Corporate Footer - Centered -->
            <div class="mt-16 font-mono text-xs text-gray-600 dark:text-gray-600 text-reveal text-center" style="animation-delay: 2.1s;">
                <div class="space-y-1 mb-4">
                    <div class="text-gray-500 dark:text-gray-500">
                        [AUTHORIZED PERSONNEL ONLY]
                    </div>
                    <div class="text-gray-600 dark:text-gray-600">
                        <a href="{{ route('login') }}" wire:navigate class="text-space-secondary dark:text-space-secondary hover:text-space-secondary-light transition-colors">existing_employee_login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    @vite(['resources/js/landing.js'])
@endpush
@endsection

