<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Terminal Boot Messages (always visible at top) -->
    <div class="mb-8 font-mono">
        <div class="text-sm text-space-primary dark:text-space-primary mb-2 {{ $terminalBooted && count($bootMessages) > 3 ? 'fade-out-boot-message' : '' }}">
            <span class="text-gray-500 dark:text-gray-500">SYSTEM@SPACE-XPLORER:~$</span> <span class="text-space-primary dark:text-space-primary">boot_terminal</span>
        </div>
        
        <!-- Boot Messages -->
        <div class="space-y-1 mb-4">
            @foreach($bootMessages as $index => $message)
                @php
                    $shouldFadeOut = $terminalBooted && $index < max(0, count($bootMessages) - 4);
                @endphp
                <div class="text-sm {{ str_contains($message, '[OK]') ? 'text-space-primary dark:text-space-primary' : (str_contains($message, '[ERROR]') ? 'text-error dark:text-error' : 'text-gray-500 dark:text-gray-500') }} {{ $shouldFadeOut ? 'fade-out-boot-message' : '' }}" style="animation-delay: {{ $index * 0.1 }}s;">
                    {{ $message }}
                </div>
            @endforeach
            @if(!$terminalBooted)
                <div class="text-sm text-space-primary dark:text-space-primary animate-pulse" wire:poll.400ms="nextBootStep">
                    <span class="inline-block w-2 h-4 bg-space-primary dark:bg-space-primary">_</span>
                </div>
            @endif
        </div>
    </div>

    @if (!$terminalBooted)
        <!-- Still booting -->
        <div class="font-mono">
            <div class="text-sm text-gray-500 dark:text-gray-500">
                [WAIT] Initializing dashboard...
            </div>
        </div>
    @else
        <!-- Dashboard Content -->
        <div class="animate-fade-in">
        <div class="mb-8 font-mono">
            <div class="text-sm text-space-primary dark:text-space-primary mb-2">
                <span class="text-gray-500 dark:text-gray-500">SYSTEM@SPACE-XPLORER:~$</span> <span class="text-space-primary dark:text-space-primary">load_user_session</span>
            </div>
            @if($user)
                <div class="text-sm text-gray-500 dark:text-gray-500 mb-4">
                    [OK] Session loaded for user: <span class="text-space-primary dark:text-space-primary">{{ $user->name ?? 'UNKNOWN' }}</span>
                </div>
            @endif
            <div class="text-sm text-space-primary dark:text-space-primary mb-2">
                <span class="text-gray-500 dark:text-gray-500">SYSTEM@SPACE-XPLORER:~$</span> <span class="text-space-primary dark:text-space-primary">display_home_planet</span>
            </div>
        </div>

        @if ($loading)
        <div class="flex justify-center items-center py-12 font-mono">
            <div class="text-center">
                <div class="text-sm text-gray-500 dark:text-gray-500 mb-4">[LOADING] Accessing planetary database...</div>
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-space-primary mx-auto"></div>
            </div>
        </div>
    @elseif ($error)
        <div class="font-mono mb-4">
            <div class="text-sm text-error dark:text-error mb-2">
                <span class="text-gray-500 dark:text-gray-500">SYSTEM@SPACE-XPLORER:~$</span> <span class="text-error dark:text-error">ERROR</span>
            </div>
            <div class="bg-red-100 dark:bg-error-dark border border-red-400 dark:border-error text-red-700 dark:text-error-light px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">[ERROR] {{ $error }}</span>
            </div>
        </div>
    @elseif ($planet)
        <!-- Planet Card -->
        <div class="bg-white dark:bg-surface-dark shadow-lg rounded-lg overflow-hidden mb-8 terminal-border-simple scan-effect hologram">
            <div class="flex flex-col md:flex-row">
                <!-- Planet Image -->
                <div class="md:w-1/3 lg:w-2/5 flex-shrink-0">
                    <img 
                        src="https://images.unsplash.com/photo-1446776653964-20c1d3a81b06?w=800&h=600&fit=crop&q=80" 
                        alt="{{ $planet->name }}"
                        class="w-full h-64 md:h-full object-cover"
                        onerror="this.src='https://via.placeholder.com/800x600/1a1a1a/00ff88?text={{ urlencode($planet->name) }}'"
                    >
                </div>

                <!-- Planet Content -->
                <div class="flex-1 flex flex-col">
                    <!-- Planet Header -->
                    <div class="px-8 py-6 border-b border-gray-200 dark:border-border-dark">
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 dark:text-glow-subtle font-mono">{{ strtoupper($planet->name) }}</h2>
                        <p class="text-gray-600 dark:text-gray-400 text-lg uppercase tracking-wider font-mono">{{ $planet->type }}</p>
                    </div>

                    <!-- Planet Description -->
                    <div class="px-8 py-6 border-b border-gray-200 dark:border-border-dark flex-1 font-mono">
                        <div class="text-sm text-gray-500 dark:text-gray-500 mb-3">
                            [INFO] Planetary description retrieved
                        </div>
                        <p class="text-gray-700 dark:text-white text-base leading-relaxed">
                            {{ $planet->description }}
                        </p>
                    </div>

                    <!-- Planet Characteristics -->
                    <div class="px-8 py-6 border-t border-gray-200 dark:border-border-dark">
                        <div class="text-sm text-gray-500 dark:text-gray-500 mb-2 font-mono">
                            <span class="text-space-primary dark:text-space-primary">SYSTEM@SPACE-XPLORER:~$</span> <span class="text-space-secondary dark:text-space-secondary">query_planet_data</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 dark:text-glow-subtle font-mono">SYSTEM_DATA</h3>
                        <div class="space-y-3 font-mono">
                            <div class="flex items-baseline border-b border-gray-300 dark:border-border-dark pb-2">
                                <span class="text-sm text-gray-500 dark:text-gray-500 uppercase tracking-wider w-32 flex-shrink-0">SIZE</span>
                                <span class="text-space-primary dark:text-space-primary flex-1 capitalize">{{ $planet->size }}</span>
                            </div>
                            <div class="flex items-baseline border-b border-gray-300 dark:border-border-dark pb-2">
                                <span class="text-sm text-gray-500 dark:text-gray-500 uppercase tracking-wider w-32 flex-shrink-0">TEMP</span>
                                <span class="text-space-primary dark:text-space-primary flex-1 capitalize">{{ $planet->temperature }}</span>
                            </div>
                            <div class="flex items-baseline border-b border-gray-300 dark:border-border-dark pb-2">
                                <span class="text-sm text-gray-500 dark:text-gray-500 uppercase tracking-wider w-32 flex-shrink-0">ATMOS</span>
                                <span class="text-space-primary dark:text-space-primary flex-1 capitalize">{{ $planet->atmosphere }}</span>
                            </div>
                            <div class="flex items-baseline border-b border-gray-300 dark:border-border-dark pb-2">
                                <span class="text-sm text-gray-500 dark:text-gray-500 uppercase tracking-wider w-32 flex-shrink-0">TERRAIN</span>
                                <span class="text-space-primary dark:text-space-primary flex-1 capitalize">{{ $planet->terrain }}</span>
                            </div>
                            <div class="flex items-baseline border-b border-gray-300 dark:border-border-dark pb-2">
                                <span class="text-sm text-gray-500 dark:text-gray-500 uppercase tracking-wider w-32 flex-shrink-0">RESOURCES</span>
                                <span class="text-space-primary dark:text-space-primary flex-1 capitalize">{{ $planet->resources }}</span>
                            </div>
                            <div class="flex items-baseline border-b border-gray-300 dark:border-border-dark pb-2">
                                <span class="text-sm text-gray-500 dark:text-gray-500 uppercase tracking-wider w-32 flex-shrink-0">TYPE</span>
                                <span class="text-space-primary dark:text-space-primary flex-1 capitalize">{{ $planet->type }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Commands -->
        <div class="mt-8 font-mono">
            <div class="text-sm text-gray-500 dark:text-gray-500 mb-4">
                [READY] System ready for commands
            </div>
            <div class="flex flex-wrap justify-center gap-4">
                <button class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold py-3 px-6 rounded-lg transition-colors glow-primary hover:glow-primary font-mono text-sm">
                    > EXPLORE_PLANETS
                </button>
                <a href="{{ route('profile') }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-surface-medium dark:hover:bg-surface-dark text-gray-900 dark:text-white font-bold py-3 px-6 rounded-lg transition-colors border border-gray-300 dark:border-border-dark dark:hover:glow-border-primary font-mono text-sm">
                    > VIEW_PROFILE
                </a>
            </div>
        </div>
        @endif
        </div>
    @endif
</div>

