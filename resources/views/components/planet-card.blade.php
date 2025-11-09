@props([
    'planet',
    'showImage' => true,
    'imageUrl' => null,
])

@if($planet)
    <div class="bg-white dark:bg-surface-dark shadow-lg rounded-lg overflow-hidden mb-8 terminal-border-simple scan-effect hologram">
        <div class="flex flex-col md:flex-row">
            @if($showImage)
                <!-- Planet Image -->
                <div class="md:w-1/3 lg:w-2/5 flex-shrink-0">
                    <img 
                        src="{{ $imageUrl ?? 'https://images.unsplash.com/photo-1446776653964-20c1d3a81b06?w=800&h=600&fit=crop&q=80' }}" 
                        alt="{{ $planet->name }}"
                        class="w-full h-64 md:h-full object-cover"
                        onerror="this.src='https://via.placeholder.com/800x600/1a1a1a/00ff88?text={{ urlencode($planet->name) }}'"
                    >
                </div>
            @endif

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
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 dark:text-glow-subtle font-mono">PLANET_DATA</h3>
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
@endif

