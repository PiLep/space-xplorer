@extends('layouts.app')

@section('title', 'Stellar')

@section('content')
<div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-textured">
    <!-- Background Effects -->
    <div class="absolute inset-0 scan-effect"></div>
    
    <!-- Main Content -->
    <div class="relative z-10 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
        <!-- Terminal Boot Sequence - Left aligned -->
        <div class="mb-16 font-mono text-left">
            <x-terminal-prompt command="init_stellar" prompt="SYSTEM@STELLAR:~$" />
            
            <div class="space-y-1 mt-4">
                <div class="text-sm text-gray-500 dark:text-gray-500 animate-fade-in" style="animation-delay: 0.2s;">
                    [INIT] Scanning deep space...
                </div>
                <div class="text-sm text-space-primary dark:text-space-primary animate-fade-in" style="animation-delay: 0.4s;">
                    [OK] Signal detected
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-500 animate-fade-in" style="animation-delay: 0.6s;">
                    [LOAD] Decrypting coordinates...
                </div>
                <div class="text-sm text-space-primary dark:text-space-primary animate-fade-in" style="animation-delay: 0.8s;">
                    [OK] Access granted
                </div>
            </div>
        </div>

        <!-- Main Title - Centered -->
        <div class="mb-12 animate-fade-in text-center" style="animation-delay: 1s;">
            <x-logo size="lg" :showScanlines="true" />
        </div>

        <!-- Subtitle - Centered -->
        <div class="mb-16 font-mono animate-fade-in text-center" style="animation-delay: 1.3s;">
            <div class="text-sm text-gray-500 dark:text-gray-500 mb-2">
                SYSTEM@STELLAR:~$ status
            </div>
            <div class="text-lg text-space-secondary dark:text-space-secondary text-glow-subtle">
                [READY]
            </div>
        </div>

        <!-- CTA Button - Centered -->
        <div class="animate-fade-in mb-4 text-center" style="animation-delay: 1.6s;">
            <a 
                href="{{ route('register') }}" 
                wire:navigate
                class="inline-block bg-transparent border-2 border-space-primary hover:border-space-primary-dark text-space-primary hover:text-space-primary-light font-bold py-4 rounded-lg transition-all duration-300 hover:glow-primary font-mono text-base tracking-widest uppercase"
                style="padding-left: 4rem; padding-right: 4rem;"
            >
                BEGIN_EXPLORATION
            </a>
        </div>

        <!-- Minimal Footer - Centered -->
        <div class="mt-20 font-mono text-sm text-gray-600 dark:text-gray-600 animate-fade-in text-center" style="animation-delay: 1.9s;">
            <div class="text-gray-500 dark:text-gray-500">
                <a href="{{ route('login') }}" wire:navigate class="text-space-secondary dark:text-space-secondary hover:text-space-secondary-light transition-colors">login</a>
            </div>
        </div>
    </div>
</div>
@endsection

