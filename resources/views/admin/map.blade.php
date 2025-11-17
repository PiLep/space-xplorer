@extends('admin.layout')

@section('content')
    <div class="mb-8 flex items-center justify-between">
        <x-page-header title="Universe Map" />
        <x-button
            href="{{ route('admin.systems.index') }}"
            variant="ghost"
            size="sm"
        >
            View Star Systems List
        </x-button>
    </div>

    <x-admin.universe-map-controls :systemCount="count($systems)" />

    <x-admin.universe-map-legend />

    <x-admin.universe-map-scale-info />

    <!-- Help text -->
    <div
        id="help-text-2d"
        class="mb-4 font-mono text-xs text-gray-500 dark:text-gray-400"
    >
        <span class="mr-4">🖱️ Scroll: Zoom | Drag: Pan | Click: Select system | Double-click: Zoom on system &
            connections</span>
    </div>
    <!-- Map Canvas 2D -->
    <div
        id="map-container-2d"
        class="bg-surface-dark dark:bg-surface-dark border-border-dark dark:border-border-dark rounded-lg border p-4 shadow"
    >
        <canvas
            id="universe-map-canvas"
            class="w-full"
            style="min-height: 600px; background: radial-gradient(circle, #0a0e27 0%, #000000 100%); user-select: none; cursor: grab;"
        ></canvas>
    </div>

    <x-admin.universe-map-system-info />

    @push('scripts')
        <script>
            // Make systems data available to JavaScript module
            window.universeMapSystems = @json($systems);

            // Initialize universe map after module is loaded
            function initializeUniverseMap() {
                const canvas = document.getElementById('universe-map-canvas');
                if (window.UniverseMap && window.universeMapSystems && canvas) {
                    try {
                        window.universeMap = new window.UniverseMap(
                            'universe-map-canvas',
                            window.universeMapSystems
                        );

                        // Initialize control functions after map is created
                        window.zoomIn = () => window.universeMap?.zoomIn();
                        window.zoomOut = () => window.universeMap?.zoomOut();
                        window.resetView = () => window.universeMap?.resetView();
                        window.toggleConnections = () => window.universeMap?.toggleConnections();
                        window.toggleDistances = () => window.universeMap?.toggleDistances();
                        window.toggleShowOnlyDiscovered = () => window.universeMap?.toggleShowOnlyDiscovered();
                        window.updateMaxDistance = (value) => window.universeMap?.updateMaxDistance(value);
                        window.toggleGodMode = function() {
                            const godModeEnabled = document.getElementById('god-mode')?.checked;
                            if (window.universeMap) {
                                window.universeMap.setGodMode(godModeEnabled);
                            }
                        };
                    } catch (error) {
                        console.error('Error initializing universe map:', error);
                        // Retry after a short delay
                        setTimeout(initializeUniverseMap, 100);
                    }
                } else {
                    // Retry if not ready yet (max 20 attempts = 2 seconds)
                    if (!window.universeMapInitAttempts) {
                        window.universeMapInitAttempts = 0;
                    }
                    if (window.universeMapInitAttempts < 20) {
                        window.universeMapInitAttempts++;
                        setTimeout(initializeUniverseMap, 100);
                    } else {
                        console.error('Failed to initialize universe map: module or data not available');
                    }
                }
            }

            // Wait for DOM and module to be ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    // Give Vite a moment to load the module
                    setTimeout(initializeUniverseMap, 100);
                });
            } else {
                // DOM is already ready, wait for module
                setTimeout(initializeUniverseMap, 100);
            }
        </script>
        @vite(['resources/js/admin/universe-map.js'])
    @endpush
@endsection
