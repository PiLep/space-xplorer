@props(['planet', 'showImage' => true, 'imageUrl' => null])

@if ($planet)
    <div
        class="dark:bg-surface-dark terminal-border-simple scan-effect hologram mb-8 overflow-hidden rounded-lg bg-white shadow-lg">
        <div class="flex flex-col md:flex-row md:items-stretch">
            @if ($showImage)
                <!-- Planet Video/Image -->
                <div class="relative z-0 flex min-h-0 flex-shrink-0 overflow-hidden md:w-1/3 lg:w-1/3 xl:w-2/5">
                    @php
                        // Priority: video > generated image > provided imageUrl > default
                        $videoUrl = $planet->video_url;
                        $finalImageUrl =
                            $planet->image_url ??
                            ($imageUrl ??
                                'https://images.unsplash.com/photo-1446776653964-20c1d3a81b06?w=800&h=600&fit=crop&q=80');
                    @endphp

                    @if ($videoUrl)
                        <!-- Video with fallback to image -->
                        <video
                            id="planet-video-{{ $planet->id }}"
                            src="{{ $videoUrl }}"
                            class="h-64 w-full object-cover md:h-full md:min-h-0 md:flex-1"
                            autoplay
                            loop
                            muted
                            playsinline
                            onerror="this.style.display='none'; document.getElementById('planet-image-{{ $planet->id }}').style.display='block';"
                        ></video>
                        <!-- Fallback image (hidden, shown if video fails) -->
                        <img
                            id="planet-image-{{ $planet->id }}"
                            src="{{ $finalImageUrl }}"
                            alt="{{ $planet->name }}"
                            class="h-64 w-full object-cover md:h-full md:min-h-0 md:flex-1"
                            style="display: none;"
                            onerror="this.src='https://via.placeholder.com/800x600/1a1a1a/00ff88?text={{ urlencode($planet->name) }}'"
                        >
                    @else
                        <!-- Image only (no video available) -->
                        <img
                            src="{{ $finalImageUrl }}"
                            alt="{{ $planet->name }}"
                            class="h-64 w-full object-cover md:h-full md:min-h-0 md:flex-1"
                            onerror="this.src='https://via.placeholder.com/800x600/1a1a1a/00ff88?text={{ urlencode($planet->name) }}'"
                        >
                    @endif
                </div>
            @endif

            <!-- Planet Content -->
            <div class="relative z-10 flex min-w-0 flex-1 flex-col">
                <!-- Planet Header -->
                <div class="dark:border-border-dark border-b border-gray-200 px-8 py-6">
                    <h2 class="dark:text-glow-subtle mb-2 font-mono text-3xl font-bold text-gray-900 dark:text-white">
                        {{ strtoupper($planet->name) }}</h2>
                    <p class="font-mono text-lg uppercase tracking-wider text-gray-600 dark:text-gray-400">
                        {{ $planet->type }}</p>
                </div>

                <!-- Planet Description -->
                <div class="dark:border-border-dark flex-1 border-b border-gray-200 px-8 py-6 font-mono">
                    <div class="mb-3 text-sm text-gray-500 dark:text-gray-500">
                        [INFO] Planetary description retrieved
                    </div>
                    <p class="text-base leading-relaxed text-gray-700 dark:text-white">
                        {{ $planet->description }}
                    </p>
                </div>

                <!-- Planet Characteristics -->
                <div class="dark:border-border-dark border-t border-gray-200 px-8 py-6">
                    <h3
                        class="dark:text-glow-subtle mb-6 font-mono text-xl font-semibold text-gray-900 dark:text-white">
                        PLANET_DATA</h3>
                    <div class="space-y-3 font-mono">
                        <div class="dark:border-border-dark flex items-baseline border-b border-gray-300 pb-2">
                            <span
                                class="w-32 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
                            >SIZE</span>
                            <span
                                class="text-space-primary dark:text-space-primary flex-1 capitalize">{{ $planet->size }}</span>
                        </div>
                        <div class="dark:border-border-dark flex items-baseline border-b border-gray-300 pb-2">
                            <span
                                class="w-32 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
                            >TEMP</span>
                            <span
                                class="text-space-primary dark:text-space-primary flex-1 capitalize">{{ $planet->temperature }}</span>
                        </div>
                        <div class="dark:border-border-dark flex items-baseline border-b border-gray-300 pb-2">
                            <span
                                class="w-32 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
                            >ATMOS</span>
                            <span
                                class="text-space-primary dark:text-space-primary flex-1 capitalize">{{ $planet->atmosphere }}</span>
                        </div>
                        <div class="dark:border-border-dark flex items-baseline border-b border-gray-300 pb-2">
                            <span
                                class="w-32 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
                            >TERRAIN</span>
                            <span
                                class="text-space-primary dark:text-space-primary flex-1 capitalize">{{ $planet->terrain }}</span>
                        </div>
                        <div class="dark:border-border-dark flex items-baseline border-b border-gray-300 pb-2">
                            <span
                                class="w-32 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
                            >RESOURCES</span>
                            <span
                                class="text-space-primary dark:text-space-primary flex-1 capitalize">{{ $planet->resources }}</span>
                        </div>
                        <div class="dark:border-border-dark flex items-baseline border-b border-gray-300 pb-2">
                            <span
                                class="w-32 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
                            >TYPE</span>
                            <span
                                class="text-space-primary dark:text-space-primary flex-1 capitalize">{{ $planet->type }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
