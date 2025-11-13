@props(['planet', 'showImage' => true, 'imageUrl' => null])

@if ($planet)
    <div
        class="dark:bg-surface-dark terminal-border-simple scan-effect hologram mb-8 overflow-hidden rounded-lg bg-white shadow-lg">
        <div class="flex flex-col md:flex-row md:items-stretch">
            @if ($showImage)
                <!-- Planet Video/Image -->
                <div class="relative z-0 flex min-h-0 flex-shrink-0 overflow-hidden md:w-1/3 lg:w-1/3 xl:w-2/5">
                    @php
                        // Priority: video > generated image > provided imageUrl > scan placeholder
                        $videoUrl = $planet->video_url;
                        $finalImageUrl = $planet->image_url ?? $imageUrl;
                        $hasImage = !empty($finalImageUrl);
                        // Generate fallback JavaScript for video error
                        $videoErrorFallback = $hasImage
                            ? "document.getElementById('planet-image-{$planet->id}').style.display='block';"
                            : "document.getElementById('planet-scan-{$planet->id}').style.display='block';";
                    @endphp

                    @if ($planet->isVideoGenerating())
                        <!-- Video is being generated -->
                        <x-scan-placeholder
                            type="video"
                            :label="'SCANNING_PLANETARY_SYSTEM: ' . strtoupper($planet->name)"
                            class="h-64 w-full md:h-full md:min-h-0 md:flex-1"
                        />
                    @elseif ($planet->isImageGenerating() && !$videoUrl)
                        <!-- Image is being generated (and no video available) -->
                        <x-scan-placeholder
                            type="image"
                            :label="'SCANNING_PLANETARY_SYSTEM: ' . strtoupper($planet->name)"
                            class="h-64 w-full md:h-full md:min-h-0 md:flex-1"
                        />
                    @elseif ($videoUrl)
                        <!-- Video with fallback to image or scan placeholder -->
                        <video
                            id="planet-video-{{ $planet->id }}"
                            src="{{ $videoUrl }}"
                            class="h-64 w-full object-cover md:h-full md:min-h-0 md:flex-1"
                            autoplay
                            loop
                            muted
                            playsinline
                            onerror="this.style.display='none'; {{ $videoErrorFallback }}"
                        ></video>
                        <!-- Fallback image (hidden, shown if video fails) -->
                        @if ($hasImage)
                            <img
                                id="planet-image-{{ $planet->id }}"
                                src="{{ $finalImageUrl }}"
                                alt="{{ $planet->name }}"
                                class="h-64 w-full object-cover md:h-full md:min-h-0 md:flex-1"
                                style="display: none;"
                                onerror="this.style.display='none'; document.getElementById('planet-scan-{{ $planet->id }}').style.display='block';"
                            >
                        @endif
                        <!-- Fallback scan placeholder (hidden, shown if video and/or image fail) -->
                        <div
                            id="planet-scan-{{ $planet->id }}"
                            style="display: none;"
                            class="h-64 w-full md:h-full md:min-h-0 md:flex-1"
                        >
                            <x-scan-placeholder
                                type="image"
                                :label="'SCANNING_PLANETARY_SYSTEM: ' . strtoupper($planet->name)"
                                class="h-full w-full"
                            />
                        </div>
                    @elseif ($planet->isImageGenerating())
                        <!-- Image is being generated (video not available) -->
                        <x-scan-placeholder
                            type="image"
                            :label="'SCANNING_PLANETARY_SYSTEM: ' . strtoupper($planet->name)"
                            class="h-64 w-full md:h-full md:min-h-0 md:flex-1"
                        />
                    @elseif ($hasImage)
                        <!-- Image only (no video available) -->
                        <img
                            src="{{ $finalImageUrl }}"
                            alt="{{ $planet->name }}"
                            class="h-64 w-full object-cover md:h-full md:min-h-0 md:flex-1"
                            onerror="this.style.display='none'; document.getElementById('planet-scan-fallback-{{ $planet->id }}').style.display='block';"
                        >
                        <!-- Fallback scan placeholder (hidden, shown if image fails) -->
                        <div
                            id="planet-scan-fallback-{{ $planet->id }}"
                            style="display: none;"
                            class="h-64 w-full md:h-full md:min-h-0 md:flex-1"
                        >
                            <x-scan-placeholder
                                type="image"
                                :label="'SCANNING_PLANETARY_SYSTEM: ' . strtoupper($planet->name)"
                                class="h-full w-full"
                            />
                        </div>
                    @else
                        <!-- No image available, show scan placeholder -->
                        <x-scan-placeholder
                            type="image"
                            :label="'SCANNING_PLANETARY_SYSTEM: ' . strtoupper($planet->name)"
                            class="h-64 w-full md:h-full md:min-h-0 md:flex-1"
                        />
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
                        {{ $planet->starSystem ? $planet->starSystem->name : $planet->type }}</p>
                </div>

                <!-- Star System Information -->
                @if ($planet->starSystem)
                    <div class="dark:border-border-dark border-b border-gray-200 px-8 py-6 font-mono">
                        <div class="mb-3 text-sm text-gray-500 dark:text-gray-500">
                            [INFO] Star system retrieved
                        </div>
                        <h3
                            class="dark:text-glow-subtle mb-6 font-mono text-xl font-semibold text-gray-900 dark:text-white">
                            STAR_SYSTEM</h3>
                        <div class="space-y-3 font-mono">
                            <div class="dark:border-border-dark flex items-baseline border-b border-gray-300 pb-2">
                                <span
                                    class="w-40 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
                                >SYSTEM</span>
                                <span
                                    class="text-space-primary dark:text-space-primary flex-1 font-semibold">{{ $planet->starSystem->name }}</span>
                            </div>
                            @if ($planet->starSystem->star_type)
                                <div class="dark:border-border-dark flex items-baseline border-b border-gray-300 pb-2">
                                    <span
                                        class="w-40 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
                                    >STAR_TYPE</span>
                                    <span
                                        class="text-space-primary dark:text-space-primary flex-1 capitalize">{{ str_replace('_', ' ', $planet->starSystem->star_type) }}</span>
                                </div>
                            @endif
                            @if ($planet->starSystem->planet_count)
                                <div class="dark:border-border-dark flex items-baseline border-b border-gray-300 pb-2">
                                    <span
                                        class="w-40 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
                                    >PLANETS</span>
                                    <span
                                        class="text-space-primary dark:text-space-primary flex-1">{{ $planet->starSystem->planet_count }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Planet Coordinates -->
                @if (
                    ($planet->x !== null && $planet->y !== null && $planet->z !== null) ||
                        $planet->orbital_distance !== null ||
                        $planet->orbital_angle !== null ||
                        $planet->orbital_inclination !== null)
                    <div class="dark:border-border-dark border-b border-gray-200 px-8 py-6 font-mono">
                        <div class="mb-3 text-sm text-gray-500 dark:text-gray-500">
                            [INFO] Planet coordinates retrieved
                        </div>
                        <h3
                            class="dark:text-glow-subtle mb-6 font-mono text-xl font-semibold text-gray-900 dark:text-white">
                            PLANET_COORDINATES</h3>
                        <div class="space-y-3 font-mono">
                            @if ($planet->x !== null && $planet->y !== null && $planet->z !== null)
                                <div class="dark:border-border-dark flex items-baseline border-b border-gray-300 pb-2">
                                    <span
                                        class="w-40 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
                                    >COORDS</span>
                                    <span class="text-space-primary dark:text-space-primary flex-1 font-mono">
                                        {{ number_format($planet->x, 2) }}, {{ number_format($planet->y, 2) }},
                                        {{ number_format($planet->z, 2) }}
                                    </span>
                                </div>
                            @endif
                            @if ($planet->orbital_distance !== null)
                                <div class="dark:border-border-dark flex items-baseline border-b border-gray-300 pb-2">
                                    <span
                                        class="w-40 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
                                    >ORB_DIST</span>
                                    <span
                                        class="text-space-primary dark:text-space-primary flex-1 font-mono">{{ number_format($planet->orbital_distance, 2) }}
                                        AU</span>
                                </div>
                            @endif
                            @if ($planet->orbital_angle !== null)
                                <div class="dark:border-border-dark flex items-baseline border-b border-gray-300 pb-2">
                                    <span
                                        class="w-40 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
                                    >ORB_ANGLE</span>
                                    <span
                                        class="text-space-primary dark:text-space-primary flex-1 font-mono">{{ number_format($planet->orbital_angle, 2) }}°</span>
                                </div>
                            @endif
                            @if ($planet->orbital_inclination !== null)
                                <div class="dark:border-border-dark flex items-baseline border-b border-gray-300 pb-2">
                                    <span
                                        class="w-40 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
                                    >ORB_INCL</span>
                                    <span
                                        class="text-space-primary dark:text-space-primary flex-1 font-mono">{{ number_format($planet->orbital_inclination, 2) }}°</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Planet Characteristics -->
                <div class="dark:border-border-dark border-t border-gray-200 px-8 py-6">
                    <div class="mb-3 font-mono text-sm text-gray-500 dark:text-gray-500">
                        [INFO] Planet properties retrieved
                    </div>
                    <h3
                        class="dark:text-glow-subtle mb-6 font-mono text-xl font-semibold text-gray-900 dark:text-white">
                        PLANET_PROPERTIES</h3>
                    <div class="space-y-3 font-mono">
                        <div class="dark:border-border-dark flex items-baseline border-b border-gray-300 pb-2">
                            <span
                                class="w-40 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
                            >SIZE</span>
                            <span
                                class="text-space-primary dark:text-space-primary flex-1 capitalize">{{ $planet->size }}</span>
                        </div>
                        <div class="dark:border-border-dark flex items-baseline border-b border-gray-300 pb-2">
                            <span
                                class="w-40 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
                            >TEMP</span>
                            <span
                                class="text-space-primary dark:text-space-primary flex-1 capitalize">{{ $planet->temperature }}</span>
                        </div>
                        <div class="dark:border-border-dark flex items-baseline border-b border-gray-300 pb-2">
                            <span
                                class="w-40 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
                            >ATMOS</span>
                            <span
                                class="text-space-primary dark:text-space-primary flex-1 capitalize">{{ $planet->atmosphere }}</span>
                        </div>
                        <div class="dark:border-border-dark flex items-baseline border-b border-gray-300 pb-2">
                            <span
                                class="w-40 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
                            >TERRAIN</span>
                            <span
                                class="text-space-primary dark:text-space-primary flex-1 capitalize">{{ $planet->terrain }}</span>
                        </div>
                        <div class="dark:border-border-dark flex items-baseline border-b border-gray-300 pb-2">
                            <span
                                class="w-40 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
                            >RESOURCES</span>
                            <span
                                class="text-space-primary dark:text-space-primary flex-1 capitalize">{{ $planet->resources }}</span>
                        </div>
                        <div class="dark:border-border-dark flex items-baseline border-b border-gray-300 pb-2">
                            <span
                                class="w-40 flex-shrink-0 text-sm uppercase tracking-wider text-gray-500 dark:text-gray-500"
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
