@extends('admin.layout')

@section('content')
    <x-page-header title="Universe Map" />

    <div class="mb-6 flex flex-wrap items-center gap-4 font-mono">
        <div class="flex items-center gap-2">
            <span class="text-sm uppercase text-gray-500 dark:text-gray-400">View Plane:</span>
            <button
                id="view-xy"
                onclick="changeViewPlane('xy')"
                class="rounded bg-blue-600 px-3 py-1 text-sm text-white transition"
            >
                XY
            </button>
            <button
                id="view-xz"
                onclick="changeViewPlane('xz')"
                class="rounded bg-gray-200 px-3 py-1 text-sm text-gray-700 transition hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
            >
                XZ
            </button>
            <button
                id="view-yz"
                onclick="changeViewPlane('yz')"
                class="rounded bg-gray-200 px-3 py-1 text-sm text-gray-700 transition hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
            >
                YZ
            </button>
        </div>

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
                title="Reset view to fit all systems"
            >
                Reset View
            </button>
        </div>

        <div class="flex items-center gap-2">
            <label class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <input
                    type="checkbox"
                    id="show-connections"
                    onchange="toggleConnections()"
                    class="rounded"
                >
                <span>Show Connections</span>
            </label>
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <label for="max-distance">Max Distance:</label>
                <input
                    type="range"
                    id="max-distance"
                    min="50"
                    max="500"
                    value="200"
                    step="50"
                    oninput="updateMaxDistance(this.value)"
                    class="w-24"
                >
                <span
                    id="max-distance-value"
                    class="w-16 text-left"
                >200 AU</span>
            </div>
        </div>

        <div class="ml-auto text-sm text-gray-500 dark:text-gray-400">
            Systems: {{ count($systems) }}
        </div>
    </div>

    <!-- Legend -->
    <div
        class="bg-surface-dark dark:bg-surface-dark border-border-dark dark:border-border-dark mb-4 rounded-lg border p-4 shadow">
        <h3 class="mb-3 font-mono text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Star Type
            Legend</h3>
        <div class="flex flex-wrap gap-4 font-mono text-sm">
            <div class="flex items-center gap-2">
                <div
                    class="h-4 w-4 rounded-full"
                    style="background-color: #FFD700;"
                ></div>
                <span class="text-gray-700 dark:text-gray-300">Yellow Dwarf</span>
            </div>
            <div class="flex items-center gap-2">
                <div
                    class="h-4 w-4 rounded-full"
                    style="background-color: #FF6B6B;"
                ></div>
                <span class="text-gray-700 dark:text-gray-300">Red Dwarf</span>
            </div>
            <div class="flex items-center gap-2">
                <div
                    class="h-4 w-4 rounded-full"
                    style="background-color: #FF8C42;"
                ></div>
                <span class="text-gray-700 dark:text-gray-300">Orange Dwarf</span>
            </div>
            <div class="flex items-center gap-2">
                <div
                    class="h-4 w-4 rounded-full"
                    style="background-color: #FF4500;"
                ></div>
                <span class="text-gray-700 dark:text-gray-300">Red Giant</span>
            </div>
            <div class="flex items-center gap-2">
                <div
                    class="h-4 w-4 rounded-full"
                    style="background-color: #4169E1;"
                ></div>
                <span class="text-gray-700 dark:text-gray-300">Blue Giant</span>
            </div>
            <div class="flex items-center gap-2">
                <div
                    class="h-4 w-4 rounded-full"
                    style="background-color: #F0F0F0;"
                ></div>
                <span class="text-gray-700 dark:text-gray-300">White Dwarf</span>
            </div>
            <div class="flex items-center gap-2">
                <div
                    class="h-4 w-4 rounded-full border border-gray-400"
                    style="background-color: #FFFFFF;"
                ></div>
                <span class="text-gray-700 dark:text-gray-300">Unknown</span>
            </div>
        </div>
    </div>

    <!-- Scale Info -->
    <div
        class="bg-surface-dark dark:bg-surface-dark border-border-dark dark:border-border-dark mb-4 rounded-lg border p-3 shadow">
        <div class="flex flex-wrap items-center gap-4 font-mono text-xs text-gray-500 dark:text-gray-400">
            <span><strong class="text-gray-700 dark:text-gray-300">Scale:</strong> 1 unit = 1 AU</span>
            <span>1 ly = 63,241 AU</span>
            <span>1 pc = 206,265 AU</span>
        </div>
    </div>

    <!-- Help text -->
    <div class="mb-4 font-mono text-xs text-gray-500 dark:text-gray-400">
        <span class="mr-4">🖱️ Scroll: Zoom | Drag: Pan | Click: Select system</span>
    </div>

    <!-- Map Canvas -->
    <div
        class="bg-surface-dark dark:bg-surface-dark border-border-dark dark:border-border-dark rounded-lg border p-4 shadow">
        <canvas
            id="universe-map-canvas"
            class="w-full"
            style="min-height: 600px; background: radial-gradient(circle, #0a0e27 0%, #000000 100%); user-select: none; cursor: grab;"
        ></canvas>
    </div>

    <!-- Selected System Info -->
    <div
        id="system-info"
        class="bg-surface-dark dark:bg-surface-dark border-border-dark dark:border-border-dark mt-6 hidden rounded-lg border p-6 font-mono shadow"
    >
        <h3
            id="system-name"
            class="mb-4 text-xl font-bold text-gray-900 dark:text-white"
        ></h3>
        <div class="mb-6 space-y-2 text-sm">
            <div class="flex">
                <span class="w-32 text-gray-500 dark:text-gray-400">Star Type:</span>
                <span
                    id="system-star-type"
                    class="text-gray-900 dark:text-white"
                ></span>
            </div>
            <div class="flex">
                <span class="w-32 text-gray-500 dark:text-gray-400">Planets:</span>
                <span
                    id="system-planet-count"
                    class="text-gray-900 dark:text-white"
                ></span>
            </div>
            <div class="flex">
                <span class="w-32 text-gray-500 dark:text-gray-400">Coordinates:</span>
                <span
                    id="system-coordinates"
                    class="text-gray-900 dark:text-white"
                ></span>
            </div>
            <div class="flex">
                <span class="w-32 text-gray-500 dark:text-gray-400">Status:</span>
                <span
                    id="system-status"
                    class="text-gray-900 dark:text-white"
                ></span>
            </div>
        </div>

        <!-- Nearby Systems -->
        <div
            id="nearby-systems-section"
            class="hidden"
        >
            <h4
                class="mb-3 border-b border-gray-300 pb-2 text-lg font-semibold text-gray-900 dark:border-gray-700 dark:text-white">
                Nearby Systems
            </h4>
            <div
                id="nearby-systems-list"
                class="space-y-2 text-sm"
            >
                <!-- Will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('universe-map-canvas');
            const ctx = canvas.getContext('2d');

            // Star type colors
            const starColors = {
                'yellow_dwarf': '#FFD700',
                'red_dwarf': '#FF6B6B',
                'orange_dwarf': '#FF8C42',
                'red_giant': '#FF4500',
                'blue_giant': '#4169E1',
                'white_dwarf': '#F0F0F0',
            };

            let systems = @json($systems);
            let viewPlane = 'xy';
            let zoom = 1.0;
            let centerX = 0;
            let centerY = 0;
            let selectedSystemId = null;
            let isDragging = false;
            let lastMouseX = 0;
            let lastMouseY = 0;
            let showConnections = false;
            let maxConnectionDistance = 200; // Distance maximale en AU pour afficher les connexions

            // Convert 3D to 2D based on view plane (needed before calculateInitialView)
            function projectTo2D(system) {
                switch (viewPlane) {
                    case 'xy':
                        return {
                            x: system.x, y: system.y
                        };
                    case 'xz':
                        return {
                            x: system.x, y: system.z
                        };
                    case 'yz':
                        return {
                            x: system.y, y: system.z
                        };
                    default:
                        return {
                            x: system.x, y: system.y
                        };
                }
            }

            // Calculate initial zoom and center to fit all systems
            function calculateInitialView() {
                if (systems.length === 0) {
                    return;
                }

                // Get all 2D positions for current view plane
                const positions = systems.map(system => {
                    const pos2d = projectTo2D(system);
                    return {
                        x: pos2d.x,
                        y: pos2d.y
                    };
                });

                // Calculate bounding box
                const xs = positions.map(p => p.x);
                const ys = positions.map(p => p.y);
                const minX = Math.min(...xs);
                const maxX = Math.max(...xs);
                const minY = Math.min(...ys);
                const maxY = Math.max(...ys);

                // Calculate center
                centerX = (minX + maxX) / 2;
                centerY = (minY + maxY) / 2;

                // Calculate dimensions
                const width = maxX - minX;
                const height = maxY - minY;
                const maxDimension = Math.max(width, height);

                // Calculate zoom to fit with some padding (80% of canvas)
                if (maxDimension > 0) {
                    const padding = 0.1; // 10% padding on each side
                    const scaleX = (canvas.width * (1 - padding * 2)) / width;
                    const scaleY = (canvas.height * (1 - padding * 2)) / height;
                    zoom = Math.min(scaleX, scaleY);

                    // Ensure zoom is reasonable (not too zoomed in or out)
                    zoom = Math.max(0.00001, Math.min(zoom, 1.0));
                } else {
                    // Fallback if all systems are at same position
                    zoom = 0.001;
                }

                // Update zoom display
                document.getElementById('zoom-level').textContent = zoom.toFixed(5) + 'x';
            }

            // Resize canvas
            function resizeCanvas() {
                const container = canvas.parentElement;
                canvas.width = container.clientWidth - 32; // padding
                canvas.height = Math.max(600, window.innerHeight * 0.6);
            }
            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);

            // Calculate initial view after canvas is resized
            calculateInitialView();

            // Convert world coordinates to screen coordinates
            function worldToScreen(worldX, worldY) {
                const screenX = (worldX - centerX) * zoom + canvas.width / 2;
                const screenY = (worldY - centerY) * zoom + canvas.height / 2;
                return {
                    x: screenX,
                    y: screenY
                };
            }

            // Convert screen coordinates to world coordinates
            function screenToWorld(screenX, screenY) {
                const worldX = (screenX - canvas.width / 2) / zoom + centerX;
                const worldY = (screenY - canvas.height / 2) / zoom + centerY;
                return {
                    x: worldX,
                    y: worldY
                };
            }

            // Draw grid with adaptive size based on zoom
            function drawGrid() {
                ctx.strokeStyle = 'rgba(100, 100, 150, 0.2)';
                ctx.lineWidth = 1;

                // Calculate adaptive grid size based on zoom
                // Target: approximately 8-12 grid lines visible on screen
                const worldWidth = canvas.width / zoom;
                const worldHeight = canvas.height / zoom;
                const targetGridLines = 10;

                // Calculate ideal grid size
                const idealGridSize = Math.max(worldWidth, worldHeight) / targetGridLines;

                // Round to a nice round value (1, 2, 5, 10, 20, 50, 100, 200, 500, 1000, etc.)
                let finalGridSize = 1;
                if (idealGridSize > 0 && isFinite(idealGridSize)) {
                    const magnitude = Math.pow(10, Math.floor(Math.log10(idealGridSize)));
                    const normalized = idealGridSize / magnitude;

                    // Round to 1, 2, or 5
                    let multiplier = 1;
                    if (normalized >= 5) {
                        multiplier = 5;
                    } else if (normalized >= 2) {
                        multiplier = 2;
                    } else {
                        multiplier = 1;
                    }

                    finalGridSize = multiplier * magnitude;
                }

                // Ensure minimum grid size
                finalGridSize = Math.max(1, finalGridSize);

                const startX = Math.floor((centerX - canvas.width / 2 / zoom) / finalGridSize) * finalGridSize;
                const endX = Math.ceil((centerX + canvas.width / 2 / zoom) / finalGridSize) * finalGridSize;
                const startY = Math.floor((centerY - canvas.height / 2 / zoom) / finalGridSize) * finalGridSize;
                const endY = Math.ceil((centerY + canvas.height / 2 / zoom) / finalGridSize) * finalGridSize;

                for (let x = startX; x <= endX; x += finalGridSize) {
                    const screen = worldToScreen(x, centerY);
                    ctx.beginPath();
                    ctx.moveTo(screen.x, 0);
                    ctx.lineTo(screen.x, canvas.height);
                    ctx.stroke();
                }

                for (let y = startY; y <= endY; y += finalGridSize) {
                    const screen = worldToScreen(centerX, y);
                    ctx.beginPath();
                    ctx.moveTo(0, screen.y);
                    ctx.lineTo(canvas.width, screen.y);
                    ctx.stroke();
                }
            }

            // Calculate intersection of a line segment with canvas bounds
            // Returns the visible segment of the line, or null if no intersection
            function clipLineToCanvas(x1, y1, x2, y2) {
                // Check if both points are inside canvas
                const p1Inside = x1 >= 0 && x1 <= canvas.width && y1 >= 0 && y1 <= canvas.height;
                const p2Inside = x2 >= 0 && x2 <= canvas.width && y2 >= 0 && y2 <= canvas.height;

                if (p1Inside && p2Inside) {
                    // Both points inside, return as-is
                    return {
                        x1,
                        y1,
                        x2,
                        y2
                    };
                }

                // Calculate line equation: y = mx + b
                const dx = x2 - x1;
                const dy = y2 - y1;

                // Handle vertical line
                if (Math.abs(dx) < 0.001) {
                    if (x1 < 0 || x1 > canvas.width) {
                        return null; // Line is outside canvas horizontally
                    }
                    const yMin = Math.max(0, Math.min(y1, y2));
                    const yMax = Math.min(canvas.height, Math.max(y1, y2));
                    if (yMin > yMax) return null;
                    return {
                        x1: x1,
                        y1: yMin,
                        x2: x1,
                        y2: yMax
                    };
                }

                // Handle horizontal line
                if (Math.abs(dy) < 0.001) {
                    if (y1 < 0 || y1 > canvas.height) {
                        return null; // Line is outside canvas vertically
                    }
                    const xMin = Math.max(0, Math.min(x1, x2));
                    const xMax = Math.min(canvas.width, Math.max(x1, x2));
                    if (xMin > xMax) return null;
                    return {
                        x1: xMin,
                        y1: y1,
                        x2: xMax,
                        y2: y1
                    };
                }

                const m = dy / dx;
                const b = y1 - m * x1;

                // Find intersections with canvas edges
                const intersections = [];

                // Left edge (x = 0)
                const yLeft = m * 0 + b;
                if (yLeft >= 0 && yLeft <= canvas.height) {
                    intersections.push({
                        x: 0,
                        y: yLeft,
                        t: (0 - x1) / dx
                    });
                }

                // Right edge (x = canvas.width)
                const yRight = m * canvas.width + b;
                if (yRight >= 0 && yRight <= canvas.height) {
                    intersections.push({
                        x: canvas.width,
                        y: yRight,
                        t: (canvas.width - x1) / dx
                    });
                }

                // Top edge (y = 0)
                const xTop = (0 - b) / m;
                if (xTop >= 0 && xTop <= canvas.width) {
                    intersections.push({
                        x: xTop,
                        y: 0,
                        t: (xTop - x1) / dx
                    });
                }

                // Bottom edge (y = canvas.height)
                const xBottom = (canvas.height - b) / m;
                if (xBottom >= 0 && xBottom <= canvas.width) {
                    intersections.push({
                        x: xBottom,
                        y: canvas.height,
                        t: (xBottom - x1) / dx
                    });
                }

                // Add points that are inside canvas
                if (p1Inside) intersections.push({
                    x: x1,
                    y: y1,
                    t: 0
                });
                if (p2Inside) intersections.push({
                    x: x2,
                    y: y2,
                    t: 1
                });

                if (intersections.length < 2) {
                    return null; // No visible segment
                }

                // Filter intersections that are on the line segment (t between 0 and 1)
                const validIntersections = intersections.filter(p => p.t >= 0 && p.t <= 1);

                if (validIntersections.length < 2) {
                    return null; // No valid segment
                }

                // Sort by parameter t to get the start and end of visible segment
                validIntersections.sort((a, b) => a.t - b.t);

                return {
                    x1: validIntersections[0].x,
                    y1: validIntersections[0].y,
                    x2: validIntersections[validIntersections.length - 1].x,
                    y2: validIntersections[validIntersections.length - 1].y
                };
            }

            // Draw connections to nearby systems
            function drawConnections() {
                if (!showConnections) {
                    // If connections are disabled, only show connections from selected system
                    if (!selectedSystemId) {
                        return;
                    }

                    const selectedSystem = systems.find(s => s.id === selectedSystemId);
                    if (!selectedSystem) {
                        return;
                    }

                    // Get nearby systems
                    const nearbySystems = findNearbySystems(selectedSystem, 5);

                    if (nearbySystems.length === 0) {
                        return;
                    }

                    const selectedPos2d = projectTo2D(selectedSystem);
                    const selectedScreen = worldToScreen(selectedPos2d.x, selectedPos2d.y);

                    // Draw connection lines
                    ctx.strokeStyle = 'rgba(0, 255, 255, 0.5)';
                    ctx.lineWidth = 1.5;
                    ctx.setLineDash([5, 5]);

                    nearbySystems.forEach(({
                        system: nearbySystem
                    }) => {
                        const nearbyPos2d = projectTo2D(nearbySystem);
                        const nearbyScreen = worldToScreen(nearbyPos2d.x, nearbyPos2d.y);

                        // Clip line to canvas bounds
                        const visibleSegment = clipLineToCanvas(
                            selectedScreen.x, selectedScreen.y,
                            nearbyScreen.x, nearbyScreen.y
                        );

                        if (visibleSegment) {
                            ctx.beginPath();
                            ctx.moveTo(visibleSegment.x1, visibleSegment.y1);
                            ctx.lineTo(visibleSegment.x2, visibleSegment.y2);
                            ctx.stroke();
                        }
                    });

                    ctx.setLineDash([]);
                    return;
                }

                // Draw connections between all nearby systems
                ctx.strokeStyle = 'rgba(100, 150, 255, 0.2)';
                ctx.lineWidth = 1;
                ctx.setLineDash([3, 3]);

                // Track drawn connections to avoid duplicates
                const drawnConnections = new Set();

                systems.forEach((system1, index1) => {
                    // Check connections to other systems
                    systems.forEach((system2, index2) => {
                        if (index1 >= index2) return; // Avoid duplicate connections

                        const distance = calculateDistance3D(system1, system2);
                        if (distance > maxConnectionDistance) {
                            return;
                        }

                        // Create unique key for this connection
                        const connectionKey = [system1.id, system2.id].sort().join('-');
                        if (drawnConnections.has(connectionKey)) {
                            return;
                        }
                        drawnConnections.add(connectionKey);

                        const pos1_2d = projectTo2D(system1);
                        const pos2_2d = projectTo2D(system2);
                        const screen1 = worldToScreen(pos1_2d.x, pos1_2d.y);
                        const screen2 = worldToScreen(pos2_2d.x, pos2_2d.y);

                        // Clip line to canvas bounds to show visible segment
                        const visibleSegment = clipLineToCanvas(
                            screen1.x, screen1.y,
                            screen2.x, screen2.y
                        );

                        if (visibleSegment) {
                            // Make connections brighter if one of the systems is selected
                            if (system1.id === selectedSystemId || system2.id ===
                                selectedSystemId) {
                                ctx.strokeStyle = 'rgba(0, 255, 255, 0.5)';
                                ctx.lineWidth = 1.5;
                            } else {
                                ctx.strokeStyle = 'rgba(100, 150, 255, 0.2)';
                                ctx.lineWidth = 1;
                            }

                            ctx.beginPath();
                            ctx.moveTo(visibleSegment.x1, visibleSegment.y1);
                            ctx.lineTo(visibleSegment.x2, visibleSegment.y2);
                            ctx.stroke();
                        }
                    });
                });

                ctx.setLineDash([]);
            }

            // Draw systems
            function drawSystems() {
                // Get linked systems if a system is selected
                let linkedSystemIds = new Set();
                if (selectedSystemId) {
                    const selectedSystem = systems.find(s => s.id === selectedSystemId);
                    if (selectedSystem) {
                        let nearbySystems;
                        if (showConnections) {
                            // When connections are enabled, find all systems within max distance
                            nearbySystems = systems
                                .filter(s => {
                                    if (s.id === selectedSystemId) return false;
                                    const distance = calculateDistance3D(selectedSystem, s);
                                    return distance <= maxConnectionDistance;
                                })
                                .map(s => ({
                                    system: s,
                                    distance: calculateDistance3D(selectedSystem, s)
                                }));
                        } else {
                            // When connections are disabled, use the existing function
                            nearbySystems = findNearbySystems(selectedSystem, 5);
                        }

                        nearbySystems.forEach(({
                            system: linkedSystem
                        }) => {
                            linkedSystemIds.add(linkedSystem.id);
                        });
                    }
                }

                systems.forEach(system => {
                    const pos2d = projectTo2D(system);
                    const screen = worldToScreen(pos2d.x, pos2d.y);

                    // Skip if outside view
                    if (screen.x < -10 || screen.x > canvas.width + 10 ||
                        screen.y < -10 || screen.y > canvas.height + 10) {
                        return;
                    }

                    const isSelected = system.id === selectedSystemId;
                    const isLinked = linkedSystemIds.has(system.id);
                    const color = starColors[system.star_type] || '#FFFFFF';
                    const size = isSelected ? 6 : 4;

                    // Draw star
                    ctx.fillStyle = color;
                    ctx.beginPath();
                    ctx.arc(screen.x, screen.y, size, 0, Math.PI * 2);
                    ctx.fill();

                    // Selection ring
                    if (isSelected) {
                        ctx.strokeStyle = '#00FFFF';
                        ctx.lineWidth = 2;
                        ctx.beginPath();
                        ctx.arc(screen.x, screen.y, size + 4, 0, Math.PI * 2);
                        ctx.stroke();

                        // Draw system name below the point
                        const name = system.name.toUpperCase();
                        const fontSize = 14;
                        const padding = 8;

                        ctx.font = `bold ${fontSize}px monospace`;
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'top';

                        // Measure text width
                        const textMetrics = ctx.measureText(name);
                        const textWidth = textMetrics.width;
                        const boxWidth = textWidth + padding * 2;
                        const boxHeight = fontSize + padding;

                        // Draw background box
                        const boxX = screen.x - boxWidth / 2;
                        const boxY = screen.y + size + 4;

                        // Background with semi-transparent black
                        ctx.fillStyle = 'rgba(0, 0, 0, 0.75)';
                        ctx.fillRect(boxX, boxY, boxWidth, boxHeight);

                        // Border
                        ctx.strokeStyle = '#00FFFF';
                        ctx.lineWidth = 1;
                        ctx.strokeRect(boxX, boxY, boxWidth, boxHeight);

                        // Draw text
                        ctx.fillStyle = '#FFFFFF';
                        ctx.fillText(name, screen.x, boxY + padding / 2);
                    } else if (isLinked) {
                        // Draw linked system name below the point with different color
                        const name = system.name.toUpperCase();
                        const fontSize = 12;
                        const padding = 6;

                        ctx.font = `${fontSize}px monospace`;
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'top';

                        // Measure text width
                        const textMetrics = ctx.measureText(name);
                        const textWidth = textMetrics.width;
                        const boxWidth = textWidth + padding * 2;
                        const boxHeight = fontSize + padding;

                        // Draw background box
                        const boxX = screen.x - boxWidth / 2;
                        const boxY = screen.y + size + 2;

                        // Background with semi-transparent black (lighter)
                        ctx.fillStyle = 'rgba(0, 0, 0, 0.6)';
                        ctx.fillRect(boxX, boxY, boxWidth, boxHeight);

                        // Border with different color (lighter blue/gray)
                        ctx.strokeStyle = 'rgba(150, 200, 255, 0.6)';
                        ctx.lineWidth = 1;
                        ctx.strokeRect(boxX, boxY, boxWidth, boxHeight);

                        // Draw text with lighter color
                        ctx.fillStyle = 'rgba(200, 220, 255, 0.9)';
                        ctx.fillText(name, screen.x, boxY + padding / 2);
                    }
                });
            }

            // Draw scale with adaptive units (AU, kAU, ly, pc)
            function drawScale() {
                // Conversion: 1 unit = 1 AU (base unit)
                // Conversions for display:
                // 1 light-year = 63,241 AU
                // 1 parsec = 3.26 light-years = 206,265 AU
                const unitsPerAU = 1.0;
                const auPerLightYear = 63241.0;
                const auPerParsec = 206265.0;

                // Calculate a nice round value to display based on zoom
                // We want the scale to be about 15% of canvas width
                const targetPixels = canvas.width * 0.15;
                const worldDistance = targetPixels / zoom;
                const auDistance = worldDistance / unitsPerAU;

                // Determine best unit and round value
                let roundedValue, unit, scalePixels;

                if (auDistance <= 0 || !isFinite(auDistance)) {
                    // Fallback to 1 AU
                    roundedValue = 1;
                    unit = 'AU';
                    scalePixels = (roundedValue * unitsPerAU) * zoom;
                } else if (auDistance >= auPerParsec) {
                    // Use parsecs for very large distances
                    const parsecDistance = auDistance / auPerParsec;
                    const magnitude = Math.pow(10, Math.floor(Math.log10(parsecDistance)));
                    roundedValue = Math.max(0.1, Math.round(parsecDistance / magnitude) * magnitude);
                    unit = 'pc';
                    scalePixels = (roundedValue * auPerParsec * unitsPerAU) * zoom;
                } else if (auDistance >= auPerLightYear) {
                    // Use light-years for large distances
                    const lightYearDistance = auDistance / auPerLightYear;
                    const magnitude = Math.pow(10, Math.floor(Math.log10(lightYearDistance)));
                    roundedValue = Math.max(0.1, Math.round(lightYearDistance / magnitude) * magnitude);
                    unit = 'ly';
                    scalePixels = (roundedValue * auPerLightYear * unitsPerAU) * zoom;
                } else if (auDistance >= 1000) {
                    // Use kAU for medium distances
                    const kauDistance = auDistance / 1000;
                    const magnitude = Math.pow(10, Math.floor(Math.log10(kauDistance)));
                    roundedValue = Math.max(0.1, Math.round(kauDistance / magnitude) * magnitude);
                    unit = 'kAU';
                    scalePixels = (roundedValue * 1000 * unitsPerAU) * zoom;
                } else {
                    // Use AU for small distances
                    const magnitude = Math.pow(10, Math.floor(Math.log10(auDistance)));
                    roundedValue = Math.max(0.1, Math.round(auDistance / magnitude) * magnitude);
                    unit = 'AU';
                    scalePixels = (roundedValue * unitsPerAU) * zoom;
                }

                // Position: bottom left with some padding
                const padding = 20;
                const x = padding;
                const y = canvas.height - padding;

                // Draw scale line
                ctx.strokeStyle = 'rgba(255, 255, 255, 0.8)';
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.moveTo(x, y);
                ctx.lineTo(x + scalePixels, y);
                ctx.stroke();

                // Draw tick marks
                ctx.beginPath();
                ctx.moveTo(x, y - 5);
                ctx.lineTo(x, y + 5);
                ctx.moveTo(x + scalePixels, y - 5);
                ctx.lineTo(x + scalePixels, y + 5);
                ctx.stroke();

                // Format label based on unit
                let label = '';
                if (unit === 'pc') {
                    label = roundedValue.toFixed(roundedValue >= 10 ? 0 : 1) + ' pc';
                } else if (unit === 'ly') {
                    label = roundedValue.toFixed(roundedValue >= 10 ? 0 : 1) + ' ly';
                } else if (unit === 'kAU') {
                    label = roundedValue.toFixed(roundedValue >= 10 ? 0 : 1) + ' kAU';
                } else {
                    label = roundedValue.toFixed(roundedValue >= 10 ? 0 : 1) + ' AU';
                }

                // Draw text with background
                ctx.fillStyle = 'rgba(255, 255, 255, 0.9)';
                ctx.font = '12px monospace';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'top';

                const textMetrics = ctx.measureText(label);
                const boxWidth = textMetrics.width + 10;
                const boxHeight = 20;

                // Background box for better visibility
                ctx.fillStyle = 'rgba(0, 0, 0, 0.6)';
                ctx.fillRect(x + scalePixels / 2 - boxWidth / 2, y + 5, boxWidth, boxHeight);

                // Text on top
                ctx.fillStyle = 'rgba(255, 255, 255, 0.9)';
                ctx.fillText(label, x + scalePixels / 2, y + 8);
            }

            // Render loop
            function render() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                drawGrid();
                drawConnections();
                drawSystems();
                drawScale();
            }

            // Handle clicks
            canvas.addEventListener('click', function(e) {
                // Only handle left button clicks for system selection
                if (e.button !== 0) {
                    return;
                }

                if (isDragging) {
                    isDragging = false;
                    return;
                }

                const rect = canvas.getBoundingClientRect();
                const clickX = e.clientX - rect.left;
                const clickY = e.clientY - rect.top;

                // Find closest system
                let closestSystem = null;
                let minDistance = Infinity;

                systems.forEach(system => {
                    const pos2d = projectTo2D(system);
                    const screen = worldToScreen(pos2d.x, pos2d.y);
                    const distance = Math.sqrt(
                        Math.pow(clickX - screen.x, 2) + Math.pow(clickY - screen.y, 2)
                    );

                    if (distance < 20 && distance < minDistance) {
                        minDistance = distance;
                        closestSystem = system;
                    }
                });

                if (closestSystem) {
                    selectedSystemId = closestSystem.id;
                    showSystemInfo(closestSystem);
                    render();
                }

                // Reset cursor after click
                canvas.style.cursor = 'grab';
            });

            // Handle mouse wheel zoom
            canvas.addEventListener('wheel', function(e) {
                e.preventDefault();

                const rect = canvas.getBoundingClientRect();
                const mouseX = e.clientX - rect.left;
                const mouseY = e.clientY - rect.top;

                // Get world coordinates of mouse position before zoom
                const worldBefore = screenToWorld(mouseX, mouseY);

                // Adjust zoom with smaller increments for smoother control
                const zoomFactor = e.deltaY > 0 ? 0.95 : 1.05;
                const newZoom = Math.max(0.00001, Math.min(10.0, zoom * zoomFactor));

                // Adjust center to zoom towards mouse position
                // Keep the world point under the mouse fixed in screen space
                centerX = worldBefore.x - (mouseX - canvas.width / 2) / newZoom;
                centerY = worldBefore.y - (mouseY - canvas.height / 2) / newZoom;

                zoom = newZoom;

                document.getElementById('zoom-level').textContent = zoom.toFixed(5) + 'x';
                render();
            });

            // Handle mouse drag for panning
            canvas.addEventListener('mousedown', function(e) {
                if (e.button === 0 || e.button === 1) { // Left mouse button or middle button (wheel)
                    e.preventDefault(); // Prevent default behavior (scrolling) when using middle button
                    isDragging = true;
                    const rect = canvas.getBoundingClientRect();
                    lastMouseX = e.clientX - rect.left;
                    lastMouseY = e.clientY - rect.top;
                    canvas.style.cursor = 'grabbing';
                }
            });

            canvas.addEventListener('mousemove', function(e) {
                if (isDragging) {
                    const rect = canvas.getBoundingClientRect();
                    const mouseX = e.clientX - rect.left;
                    const mouseY = e.clientY - rect.top;

                    const deltaX = mouseX - lastMouseX;
                    const deltaY = mouseY - lastMouseY;

                    // Convert screen delta to world delta
                    centerX -= deltaX / zoom;
                    centerY -= deltaY / zoom;

                    lastMouseX = mouseX;
                    lastMouseY = mouseY;

                    render();
                }
            });

            canvas.addEventListener('mouseup', function(e) {
                if (e.button === 0 || e.button === 1) { // Left mouse button or middle button (wheel)
                    isDragging = false;
                    canvas.style.cursor = 'crosshair';
                }
            });

            // Prevent context menu when using middle button
            canvas.addEventListener('contextmenu', function(e) {
                e.preventDefault();
            });

            canvas.addEventListener('mouseleave', function() {
                isDragging = false;
                canvas.style.cursor = 'crosshair';
            });

            // Change cursor on hover to indicate draggable
            canvas.addEventListener('mouseenter', function() {
                if (!isDragging) {
                    canvas.style.cursor = 'grab';
                }
            });

            // Calculate distance between two systems in 3D
            function calculateDistance3D(system1, system2) {
                return Math.sqrt(
                    Math.pow(system1.x - system2.x, 2) +
                    Math.pow(system1.y - system2.y, 2) +
                    Math.pow(system1.z - system2.z, 2)
                );
            }

            // Format distance in appropriate units
            function formatDistance(distanceAU) {
                const auPerLightYear = 63241.0;
                const auPerParsec = 206265.0;

                if (distanceAU >= auPerParsec) {
                    const parsecs = distanceAU / auPerParsec;
                    return parsecs.toFixed(2) + ' pc';
                } else if (distanceAU >= auPerLightYear) {
                    const lightYears = distanceAU / auPerLightYear;
                    return lightYears.toFixed(2) + ' ly';
                } else if (distanceAU >= 1000) {
                    const kau = distanceAU / 1000;
                    return kau.toFixed(2) + ' kAU';
                } else {
                    return distanceAU.toFixed(2) + ' AU';
                }
            }

            // Find nearby systems
            function findNearbySystems(selectedSystem, maxCount = 5) {
                const distances = systems
                    .filter(s => s.id !== selectedSystem.id)
                    .map(otherSystem => ({
                        system: otherSystem,
                        distance: calculateDistance3D(selectedSystem, otherSystem)
                    }))
                    .sort((a, b) => a.distance - b.distance)
                    .slice(0, maxCount);

                return distances;
            }

            // Show system info
            function showSystemInfo(system) {
                document.getElementById('system-info').classList.remove('hidden');
                document.getElementById('system-name').textContent = system.name.toUpperCase();
                document.getElementById('system-star-type').textContent = system.star_type ?
                    system.star_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) :
                    'Unknown';
                document.getElementById('system-planet-count').textContent = system.planet_count;
                document.getElementById('system-coordinates').textContent =
                    `X: ${system.x.toFixed(2)}, Y: ${system.y.toFixed(2)}, Z: ${system.z.toFixed(2)}`;
                document.getElementById('system-status').textContent = system.discovered ? 'Discovered' :
                    'Undiscovered';

                // Find and display nearby systems
                const nearbySystems = findNearbySystems(system, 5);
                const nearbyList = document.getElementById('nearby-systems-list');
                const nearbySection = document.getElementById('nearby-systems-section');

                if (nearbySystems.length > 0) {
                    nearbySection.classList.remove('hidden');
                    nearbyList.innerHTML = '';

                    nearbySystems.forEach(({
                        system: nearbySystem,
                        distance
                    }) => {
                        const item = document.createElement('div');
                        item.className =
                            'flex items-center justify-between p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 transition cursor-pointer';
                        item.onclick = () => {
                            // Center view on nearby system (adjust for current view plane)
                            const pos2d = projectTo2D(nearbySystem);
                            centerX = pos2d.x;
                            centerY = pos2d.y;
                            selectedSystemId = nearbySystem.id;
                            showSystemInfo(nearbySystem);
                            render();
                        };

                        const nameDiv = document.createElement('div');
                        nameDiv.className = 'flex items-center gap-2';
                        nameDiv.innerHTML = `
                        <span class="text-gray-900 dark:text-white font-medium">${nearbySystem.name}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">(${nearbySystem.planet_count} planets)</span>
                    `;

                        const distanceDiv = document.createElement('div');
                        distanceDiv.className = 'text-gray-600 dark:text-gray-400';
                        distanceDiv.textContent = formatDistance(distance);

                        item.appendChild(nameDiv);
                        item.appendChild(distanceDiv);
                        nearbyList.appendChild(item);
                    });
                } else {
                    nearbySection.classList.add('hidden');
                }
            }

            // Global functions for buttons
            window.changeViewPlane = function(plane) {
                viewPlane = plane;

                // Recalculate view for new plane
                calculateInitialView();

                // Update button styles
                document.querySelectorAll('[id^="view-"]').forEach(btn => {
                    btn.classList.remove('bg-blue-600', 'text-white');
                    btn.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700',
                        'dark:text-gray-300');
                });
                document.getElementById('view-' + plane).classList.remove('bg-gray-200', 'dark:bg-gray-700',
                    'text-gray-700', 'dark:text-gray-300');
                document.getElementById('view-' + plane).classList.add('bg-blue-600', 'text-white');

                render();
            };

            window.zoomIn = function() {
                // Zoom towards center
                const zoomFactor = 1.5;
                zoom = Math.min(zoom * zoomFactor, 10.0);
                document.getElementById('zoom-level').textContent = zoom.toFixed(5) + 'x';
                render();
            };

            window.zoomOut = function() {
                // Zoom out from center
                const zoomFactor = 1.5;
                zoom = Math.max(zoom / zoomFactor, 0.01);
                document.getElementById('zoom-level').textContent = zoom.toFixed(5) + 'x';
                render();
            };

            window.resetView = function() {
                // Recalculate view to fit all systems
                calculateInitialView();
                render();
            };

            window.toggleConnections = function() {
                showConnections = document.getElementById('show-connections').checked;
                render();
            };

            window.updateMaxDistance = function(value) {
                maxConnectionDistance = parseFloat(value);
                document.getElementById('max-distance-value').textContent = value + ' AU';
                if (showConnections) {
                    render();
                }
            };

            // Initial render
            render();
        });
    </script>
@endsection
