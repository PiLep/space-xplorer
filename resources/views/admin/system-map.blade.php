@extends('admin.layout')

@section('content')
    <x-page-header :title="'Star System Map: ' . $starSystem['name']" />

    <div class="mb-4 flex items-center gap-4">
        <a
            href="{{ route('admin.map') }}"
            class="hover:text-space-primary dark:hover:text-space-primary inline-flex items-center gap-2 font-mono text-sm text-gray-500 transition dark:text-gray-400"
        >
            ← Back to Universe Map
        </a>
        <a
            href="{{ route('admin.systems.index') }}"
            class="hover:text-space-primary dark:hover:text-space-primary inline-flex items-center gap-2 font-mono text-sm text-gray-500 transition dark:text-gray-400"
        >
            View All Star Systems
        </a>
    </div>

    <!-- System Info -->
    <div
        class="border-border-dark bg-surface-dark dark:border-border-dark dark:bg-surface-dark mb-6 rounded-lg border p-6 font-mono shadow">
        <h3 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">
            {{ strtoupper($starSystem['name']) }}
        </h3>
        <div class="space-y-2 text-sm">
            <div class="flex">
                <span class="w-32 text-gray-500 dark:text-gray-400">Star Type:</span>
                <span class="text-gray-900 dark:text-white">{{ $starSystem['star_type'] ?? 'Unknown' }}</span>
            </div>
            <div class="flex">
                <span class="w-32 text-gray-500 dark:text-gray-400">Planets:</span>
                <span class="text-gray-900 dark:text-white">{{ $starSystem['planet_count'] }}</span>
            </div>
            <div class="flex">
                <span class="w-32 text-gray-500 dark:text-gray-400">Coordinates:</span>
                <span class="text-gray-900 dark:text-white">
                    X: {{ number_format($starSystem['x'], 2) }},
                    Y: {{ number_format($starSystem['y'], 2) }},
                    Z: {{ number_format($starSystem['z'], 2) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Controls -->
    <div class="mb-6 flex flex-wrap items-center gap-4 font-mono">
        <div class="flex items-center gap-2">
            <button
                onclick="zoomIn()"
                class="rounded bg-gray-200 px-3 py-1 text-sm text-gray-700 transition hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
            >
                Zoom +
            </button>
            <span
                id="zoom-level"
                class="text-sm text-gray-500 dark:text-gray-400"
            >1.00x</span>
            <button
                onclick="zoomOut()"
                class="rounded bg-gray-200 px-3 py-1 text-sm text-gray-700 transition hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
            >
                Zoom -
            </button>
            <button
                onclick="resetView()"
                class="rounded bg-blue-600 px-3 py-1 text-sm text-white transition hover:bg-blue-700"
                title="Reset view to fit all planets"
            >
                Reset View
            </button>
        </div>

        <div class="flex items-center gap-2">
            <label class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <input
                    type="checkbox"
                    id="show-orbits"
                    checked
                    onchange="toggleOrbits()"
                    class="rounded"
                >
                <span>Show Orbits</span>
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <input
                    type="checkbox"
                    id="show-labels"
                    checked
                    onchange="toggleLabels()"
                    class="rounded"
                >
                <span>Show Labels</span>
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <input
                    type="checkbox"
                    id="show-animation"
                    onchange="toggleAnimation()"
                    class="rounded"
                >
                <span>Orbit Animation</span>
            </label>
        </div>

        <div class="ml-auto text-sm text-gray-500 dark:text-gray-400">
            Planets: {{ count($planets) }}
        </div>
    </div>

    <!-- Help text -->
    <div
        id="help-text"
        class="mb-4 font-mono text-xs text-gray-500 dark:text-gray-400"
    >
        <span class="mr-4">🖱️ Scroll: Zoom | Drag: Pan | Click: Select planet | Double-click: Zoom on planet</span>
    </div>

    <!-- Map Canvas -->
    <div
        id="map-container"
        class="bg-surface-dark dark:bg-surface-dark border-border-dark dark:border-border-dark rounded-lg border p-4 shadow"
    >
        <canvas
            id="system-map-canvas"
            class="w-full"
            style="min-height: 600px; background: radial-gradient(circle, #0a0e27 0%, #000000 100%); user-select: none; cursor: grab;"
        ></canvas>
    </div>

    <!-- Planet Info Panel -->
    <div
        id="planet-info"
        class="bg-surface-dark dark:bg-surface-dark border-border-dark dark:border-border-dark mt-6 hidden rounded-lg border p-6 font-mono shadow"
    >
        <h3
            id="planet-name"
            class="mb-4 text-xl font-bold text-gray-900 dark:text-white"
        ></h3>
        <div class="mb-6 space-y-2 text-sm">
            <div class="flex">
                <span class="w-32 text-gray-500 dark:text-gray-400">Type:</span>
                <span
                    id="planet-type"
                    class="text-gray-900 dark:text-white"
                ></span>
            </div>
            <div class="flex">
                <span class="w-32 text-gray-500 dark:text-gray-400">Size:</span>
                <span
                    id="planet-size"
                    class="text-gray-900 dark:text-white"
                ></span>
            </div>
            <div class="flex">
                <span class="w-32 text-gray-500 dark:text-gray-400">Orbital Distance:</span>
                <span
                    id="planet-orbital-distance"
                    class="text-gray-900 dark:text-white"
                ></span>
            </div>
            <div class="flex">
                <span class="w-32 text-gray-500 dark:text-gray-400">Orbital Angle:</span>
                <span
                    id="planet-orbital-angle"
                    class="text-gray-900 dark:text-white"
                ></span>
            </div>
            <div class="flex">
                <span class="w-32 text-gray-500 dark:text-gray-400">Has Image:</span>
                <span
                    id="planet-has-image"
                    class="text-gray-900 dark:text-white"
                ></span>
            </div>
            <div class="flex">
                <span class="w-32 text-gray-500 dark:text-gray-400">Has Video:</span>
                <span
                    id="planet-has-video"
                    class="text-gray-900 dark:text-white"
                ></span>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Make system and planets data available to JavaScript module
            window.systemMapData = {
                starSystem: @json($starSystem),
                planets: @json($planets),
            };

            // Initialize map after module is loaded
            function initializeSystemMap() {
                const canvas = document.getElementById('system-map-canvas');
                if (window.SystemMap && window.systemMapData && canvas) {
                    try {
                        window.systemMap = new window.SystemMap(
                            'system-map-canvas',
                            window.systemMapData.starSystem,
                            window.systemMapData.planets
                        );

                        // Initialize control functions
                        window.zoomIn = () => window.systemMap?.zoomIn();
                        window.zoomOut = () => window.systemMap?.zoomOut();
                        window.resetView = () => window.systemMap?.resetView();
                        window.toggleOrbits = () => window.systemMap?.toggleOrbits();
                        window.toggleLabels = () => window.systemMap?.toggleLabels();
                        window.toggleAnimation = () => {
                            window.systemMap?.toggleAnimation();
                            // Sync checkbox state
                            const checkbox = document.getElementById('show-animation');
                            if (checkbox && window.systemMap) {
                                checkbox.checked = window.systemMap.animationEnabled;
                            }
                        };
                    } catch (error) {
                        console.error('Error initializing system map:', error);
                        // Retry after a short delay
                        setTimeout(initializeSystemMap, 100);
                    }
                } else {
                    // Retry if not ready yet (max 20 attempts = 1 second)
                    if (!window.systemMapInitAttempts) {
                        window.systemMapInitAttempts = 0;
                    }
                    if (window.systemMapInitAttempts < 20) {
                        window.systemMapInitAttempts++;
                        setTimeout(initializeSystemMap, 50);
                    } else {
                        console.error('Failed to initialize system map: module or data not available');
                    }
                }
            }

            // Wait for DOM and module to be ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    // Give Vite a moment to load the module
                    setTimeout(initializeSystemMap, 100);
                });
            } else {
                // DOM is already ready, wait for module
                setTimeout(initializeSystemMap, 100);
            }
        </script>
        @vite(['resources/js/admin/system-map.js'])
    @endpush
@endsection
