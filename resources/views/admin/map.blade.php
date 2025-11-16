@extends('admin.layout')

@section('content')
    <x-page-header title="Universe Map" />

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

            // Initialize toggleShowOnlyDiscovered function early
            window.toggleShowOnlyDiscovered = function() {
                if (window.universeMap) {
                    window.universeMap.toggleShowOnlyDiscovered();
                }
            };

            // Initialize toggleGodMode function early
            window.toggleGodMode = function() {
                const godModeEnabled = document.getElementById('god-mode').checked;
                if (window.universeMap) {
                    window.universeMap.setGodMode(godModeEnabled);
                }
            };

            // Initialize 2D map control functions
            if (window.universeMap) {
                window.zoomIn = () => window.universeMap.zoomIn();
                window.zoomOut = () => window.universeMap.zoomOut();
                window.resetView = () => window.universeMap.resetView();
                window.toggleConnections = () => window.universeMap.toggleConnections();
                window.toggleDistances = () => window.universeMap.toggleDistances();
                window.toggleShowOnlyDiscovered = () => window.universeMap.toggleShowOnlyDiscovered();
                window.updateMaxDistance = (value) => window.universeMap.updateMaxDistance(value);
            }
        </script>
        @vite(['resources/js/admin/universe-map.js'])
    @endpush
@endsection
