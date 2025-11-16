/**
 * Universe Map - Main Module
 * Handles the 3D star system map visualization
 */

// Constants
const STAR_COLORS = {
    'yellow_dwarf': '#FFD700',
    'red_dwarf': '#FF6B6B',
    'orange_dwarf': '#FF8C42',
    'red_giant': '#FF4500',
    'blue_giant': '#4169E1',
    'white_dwarf': '#F0F0F0',
};

const DISTANCE_CONSTANTS = {
    AU_PER_LIGHT_YEAR: 63241.0,
    AU_PER_PARSEC: 206265.0,
    UNITS_PER_AU: 1.0,
};

const VIEW_PLANES = {
    XY: 'xy',
    XZ: 'xz',
    YZ: 'yz',
};

const DEFAULT_CONFIG = {
    initialZoom: 1.0,
    minZoom: 0.00001, // Fallback minimum zoom
    maxZoom: 10.0,
    zoomFactor: 1.5,
    wheelZoomFactor: 0.05,
    maxConnectionDistance: 200,
    gridTargetLines: 10,
    scaleTargetPixels: 0.15,
    clickRadius: 20,
    padding: 0.1,
    maxZoomOutFactor: 0.5, // Maximum zoom out: 50% of initial zoom (2x wider view)
    zoomAnimationDuration: 600, // Duration of zoom animation in milliseconds
};

/**
 * Coordinate projection utilities
 */
class CoordinateProjector {
    constructor(viewPlane) {
        this.viewPlane = viewPlane;
    }

    setViewPlane(plane) {
        this.viewPlane = plane;
    }

    projectTo2D(system) {
        switch (this.viewPlane) {
            case VIEW_PLANES.XY:
                return { x: system.x, y: system.y };
            case VIEW_PLANES.XZ:
                return { x: system.x, y: system.z };
            case VIEW_PLANES.YZ:
                return { x: system.y, y: system.z };
            default:
                return { x: system.x, y: system.y };
        }
    }

    calculateDistance3D(system1, system2) {
        return Math.sqrt(
            Math.pow(system1.x - system2.x, 2) +
            Math.pow(system1.y - system2.y, 2) +
            Math.pow(system1.z - system2.z, 2)
        );
    }
}

/**
 * View transformation utilities
 */
class ViewTransformer {
    constructor(canvas) {
        this.canvas = canvas;
        this.zoom = DEFAULT_CONFIG.initialZoom;
        this.initialZoom = DEFAULT_CONFIG.initialZoom;
        this.dynamicMinZoom = DEFAULT_CONFIG.minZoom; // Will be calculated based on initial zoom
        this.centerX = 0;
        this.centerY = 0;
        this.screenCache = new Map();
    }

    invalidateCache() {
        this.screenCache.clear();
    }

    worldToScreen(worldX, worldY) {
        const cacheKey = `${worldX.toFixed(2)},${worldY.toFixed(2)},${this.zoom.toFixed(5)},${this.centerX.toFixed(2)},${this.centerY.toFixed(2)}`;
        if (this.screenCache.has(cacheKey)) {
            return this.screenCache.get(cacheKey);
        }
        const screenX = (worldX - this.centerX) * this.zoom + this.canvas.width / 2;
        const screenY = (worldY - this.centerY) * this.zoom + this.canvas.height / 2;
        const result = { x: screenX, y: screenY };
        this.screenCache.set(cacheKey, result);
        return result;
    }

    screenToWorld(screenX, screenY) {
        const worldX = (screenX - this.canvas.width / 2) / this.zoom + this.centerX;
        const worldY = (screenY - this.canvas.height / 2) / this.zoom + this.centerY;
        return { x: worldX, y: worldY };
    }

    formatZoom(zoomValue) {
        if (this.initialZoom <= 0) {
            return zoomValue.toFixed(2) + 'x';
        }

        const relativeZoom = (zoomValue / this.initialZoom) * 100;

        if (relativeZoom >= 1000) {
            return Math.round(relativeZoom) + '%';
        } else if (relativeZoom >= 100) {
            return relativeZoom.toFixed(1) + '%';
        } else if (relativeZoom >= 10) {
            return relativeZoom.toFixed(1) + '%';
        } else if (relativeZoom >= 1) {
            return relativeZoom.toFixed(2) + '%';
        } else {
            return relativeZoom.toFixed(2) + '%';
        }
    }

    calculateInitialView(systems, projector) {
        if (systems.length === 0) {
            return;
        }

        const positions = systems.map(system => {
            const pos2d = projector.projectTo2D(system);
            return { x: pos2d.x, y: pos2d.y };
        });

        const xs = positions.map(p => p.x);
        const ys = positions.map(p => p.y);
        const minX = Math.min(...xs);
        const maxX = Math.max(...xs);
        const minY = Math.min(...ys);
        const maxY = Math.max(...ys);

        this.centerX = (minX + maxX) / 2;
        this.centerY = (minY + maxY) / 2;

        const width = maxX - minX;
        const height = maxY - minY;
        const maxDimension = Math.max(width, height);

        if (maxDimension > 0) {
            const padding = DEFAULT_CONFIG.padding;
            const scaleX = (this.canvas.width * (1 - padding * 2)) / width;
            const scaleY = (this.canvas.height * (1 - padding * 2)) / height;
            this.zoom = Math.min(scaleX, scaleY);
            this.zoom = Math.max(DEFAULT_CONFIG.minZoom, Math.min(this.zoom, DEFAULT_CONFIG.maxZoom));
        } else {
            this.zoom = 0.001;
        }

        this.initialZoom = this.zoom;
        // Calculate dynamic minimum zoom: don't allow zooming out more than maxZoomOutFactor
        this.dynamicMinZoom = Math.max(
            DEFAULT_CONFIG.minZoom,
            this.initialZoom * DEFAULT_CONFIG.maxZoomOutFactor
        );
        this.invalidateCache();
    }
}

/**
 * Distance formatting utilities
 */
class DistanceFormatter {
    static formatDistance(distanceAU) {
        const { AU_PER_LIGHT_YEAR, AU_PER_PARSEC } = DISTANCE_CONSTANTS;

        if (distanceAU >= AU_PER_PARSEC) {
            const parsecs = distanceAU / AU_PER_PARSEC;
            return parsecs.toFixed(2) + ' pc';
        } else if (distanceAU >= AU_PER_LIGHT_YEAR) {
            const lightYears = distanceAU / AU_PER_LIGHT_YEAR;
            return lightYears.toFixed(2) + ' ly';
        } else if (distanceAU >= 1000) {
            const kau = distanceAU / 1000;
            return kau.toFixed(2) + ' kAU';
        } else {
            return distanceAU.toFixed(2) + ' AU';
        }
    }

    static findNearbySystems(systems, selectedSystem, maxCount = 5, projector) {
        const distances = systems
            .filter(s => s.id !== selectedSystem.id)
            .map(otherSystem => ({
                system: otherSystem,
                distance: projector.calculateDistance3D(selectedSystem, otherSystem)
            }))
            .sort((a, b) => a.distance - b.distance)
            .slice(0, maxCount);

        return distances;
    }
}

/**
 * Grid renderer
 */
class GridRenderer {
    constructor(ctx, transformer) {
        this.ctx = ctx;
        this.transformer = transformer;
    }

    drawGrid() {
        const { canvas, zoom, centerX, centerY } = this.transformer;
        this.ctx.strokeStyle = 'rgba(100, 100, 150, 0.2)';
        this.ctx.lineWidth = 1;

        const worldWidth = canvas.width / zoom;
        const worldHeight = canvas.height / zoom;
        const targetGridLines = DEFAULT_CONFIG.gridTargetLines;

        const idealGridSize = Math.max(worldWidth, worldHeight) / targetGridLines;
        let finalGridSize = 1;

        if (idealGridSize > 0 && isFinite(idealGridSize)) {
            const magnitude = Math.pow(10, Math.floor(Math.log10(idealGridSize)));
            const normalized = idealGridSize / magnitude;

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

        finalGridSize = Math.max(1, finalGridSize);

        const startX = Math.floor((centerX - canvas.width / 2 / zoom) / finalGridSize) * finalGridSize;
        const endX = Math.ceil((centerX + canvas.width / 2 / zoom) / finalGridSize) * finalGridSize;
        const startY = Math.floor((centerY - canvas.height / 2 / zoom) / finalGridSize) * finalGridSize;
        const endY = Math.ceil((centerY + canvas.height / 2 / zoom) / finalGridSize) * finalGridSize;

        for (let x = startX; x <= endX; x += finalGridSize) {
            const screen = this.transformer.worldToScreen(x, centerY);
            this.ctx.beginPath();
            this.ctx.moveTo(screen.x, 0);
            this.ctx.lineTo(screen.x, canvas.height);
            this.ctx.stroke();
        }

        for (let y = startY; y <= endY; y += finalGridSize) {
            const screen = this.transformer.worldToScreen(centerX, y);
            this.ctx.beginPath();
            this.ctx.moveTo(0, screen.y);
            this.ctx.lineTo(canvas.width, screen.y);
            this.ctx.stroke();
        }
    }
}

/**
 * Line clipping utilities
 */
class LineClipper {
    constructor(canvas) {
        this.canvas = canvas;
    }

    clipLineToCanvas(x1, y1, x2, y2) {
        const p1Inside = x1 >= 0 && x1 <= this.canvas.width && y1 >= 0 && y1 <= this.canvas.height;
        const p2Inside = x2 >= 0 && x2 <= this.canvas.width && y2 >= 0 && y2 <= this.canvas.height;

        if (p1Inside && p2Inside) {
            return { x1, y1, x2, y2 };
        }

        const dx = x2 - x1;
        const dy = y2 - y1;

        if (Math.abs(dx) < 0.001) {
            if (x1 < 0 || x1 > this.canvas.width) {
                return null;
            }
            const yMin = Math.max(0, Math.min(y1, y2));
            const yMax = Math.min(this.canvas.height, Math.max(y1, y2));
            if (yMin > yMax) return null;
            return { x1: x1, y1: yMin, x2: x1, y2: yMax };
        }

        if (Math.abs(dy) < 0.001) {
            if (y1 < 0 || y1 > this.canvas.height) {
                return null;
            }
            const xMin = Math.max(0, Math.min(x1, x2));
            const xMax = Math.min(this.canvas.width, Math.max(x1, x2));
            if (xMin > xMax) return null;
            return { x1: xMin, y1: y1, x2: xMax, y2: y1 };
        }

        const m = dy / dx;
        const b = y1 - m * x1;

        const intersections = [];

        const yLeft = m * 0 + b;
        if (yLeft >= 0 && yLeft <= this.canvas.height) {
            intersections.push({ x: 0, y: yLeft, t: (0 - x1) / dx });
        }

        const yRight = m * this.canvas.width + b;
        if (yRight >= 0 && yRight <= this.canvas.height) {
            intersections.push({ x: this.canvas.width, y: yRight, t: (this.canvas.width - x1) / dx });
        }

        const xTop = (0 - b) / m;
        if (xTop >= 0 && xTop <= this.canvas.width) {
            intersections.push({ x: xTop, y: 0, t: (xTop - x1) / dx });
        }

        const xBottom = (this.canvas.height - b) / m;
        if (xBottom >= 0 && xBottom <= this.canvas.width) {
            intersections.push({ x: xBottom, y: this.canvas.height, t: (xBottom - x1) / dx });
        }

        if (p1Inside) intersections.push({ x: x1, y: y1, t: 0 });
        if (p2Inside) intersections.push({ x: x2, y: y2, t: 1 });

        if (intersections.length < 2) {
            return null;
        }

        const validIntersections = intersections.filter(p => p.t >= 0 && p.t <= 1);

        if (validIntersections.length < 2) {
            return null;
        }

        validIntersections.sort((a, b) => a.t - b.t);

        return {
            x1: validIntersections[0].x,
            y1: validIntersections[0].y,
            x2: validIntersections[validIntersections.length - 1].x,
            y2: validIntersections[validIntersections.length - 1].y
        };
    }
}

/**
 * Connections renderer
 */
class ConnectionsRenderer {
    constructor(ctx, canvas, transformer, projector, clipper) {
        this.ctx = ctx;
        this.canvas = canvas;
        this.transformer = transformer;
        this.projector = projector;
        this.clipper = clipper;
    }

    drawConnections(systems, selectedSystemId, showConnections, maxConnectionDistance) {
        if (!showConnections) {
            if (!selectedSystemId) {
                return;
            }

            const selectedSystem = systems.find(s => s.id === selectedSystemId);
            if (!selectedSystem) {
                return;
            }

            const nearbySystems = DistanceFormatter.findNearbySystems(systems, selectedSystem, 5, this.projector);

            if (nearbySystems.length === 0) {
                return;
            }

            const selectedPos2d = this.projector.projectTo2D(selectedSystem);
            const selectedScreen = this.transformer.worldToScreen(selectedPos2d.x, selectedPos2d.y);

            this.ctx.strokeStyle = 'rgba(0, 255, 255, 0.5)';
            this.ctx.lineWidth = 1.5;
            this.ctx.setLineDash([5, 5]);

            nearbySystems.forEach(({ system: nearbySystem }) => {
                const nearbyPos2d = this.projector.projectTo2D(nearbySystem);
                const nearbyScreen = this.transformer.worldToScreen(nearbyPos2d.x, nearbyPos2d.y);

                const visibleSegment = this.clipper.clipLineToCanvas(
                    selectedScreen.x, selectedScreen.y,
                    nearbyScreen.x, nearbyScreen.y
                );

                if (visibleSegment) {
                    this.ctx.beginPath();
                    this.ctx.moveTo(visibleSegment.x1, visibleSegment.y1);
                    this.ctx.lineTo(visibleSegment.x2, visibleSegment.y2);
                    this.ctx.stroke();
                }
            });

            this.ctx.setLineDash([]);
            return;
        }

        this.ctx.strokeStyle = 'rgba(100, 150, 255, 0.2)';
        this.ctx.lineWidth = 1;
        this.ctx.setLineDash([3, 3]);

        const drawnConnections = new Set();

        systems.forEach((system1, index1) => {
            systems.forEach((system2, index2) => {
                if (index1 >= index2) return;

                const distance = this.projector.calculateDistance3D(system1, system2);
                if (distance > maxConnectionDistance) {
                    return;
                }

                const connectionKey = [system1.id, system2.id].sort().join('-');
                if (drawnConnections.has(connectionKey)) {
                    return;
                }
                drawnConnections.add(connectionKey);

                const pos1_2d = this.projector.projectTo2D(system1);
                const pos2_2d = this.projector.projectTo2D(system2);
                const screen1 = this.transformer.worldToScreen(pos1_2d.x, pos1_2d.y);
                const screen2 = this.transformer.worldToScreen(pos2_2d.x, pos2_2d.y);

                const visibleSegment = this.clipper.clipLineToCanvas(
                    screen1.x, screen1.y,
                    screen2.x, screen2.y
                );

                if (visibleSegment) {
                    if (system1.id === selectedSystemId || system2.id === selectedSystemId) {
                        this.ctx.strokeStyle = 'rgba(0, 255, 255, 0.5)';
                        this.ctx.lineWidth = 1.5;
                    } else {
                        this.ctx.strokeStyle = 'rgba(100, 150, 255, 0.2)';
                        this.ctx.lineWidth = 1;
                    }

                    this.ctx.beginPath();
                    this.ctx.moveTo(visibleSegment.x1, visibleSegment.y1);
                    this.ctx.lineTo(visibleSegment.x2, visibleSegment.y2);
                    this.ctx.stroke();
                }
            });
        });

        this.ctx.setLineDash([]);
    }
}

/**
 * Systems renderer
 */
class SystemsRenderer {
    constructor(ctx, canvas, transformer, projector) {
        this.ctx = ctx;
        this.canvas = canvas;
        this.transformer = transformer;
        this.projector = projector;
    }

    drawSystems(systems, selectedSystemId, linkedSystemIds) {
        systems.forEach(system => {
            const pos2d = this.projector.projectTo2D(system);
            const screen = this.transformer.worldToScreen(pos2d.x, pos2d.y);

            if (screen.x < -10 || screen.x > this.canvas.width + 10 ||
                screen.y < -10 || screen.y > this.canvas.height + 10) {
                return;
            }

            const isSelected = system.id === selectedSystemId;
            const isLinked = linkedSystemIds.has(system.id);
            const color = STAR_COLORS[system.star_type] || '#FFFFFF';
            const size = isSelected ? 6 : 4;

            this.ctx.fillStyle = color;
            this.ctx.beginPath();
            this.ctx.arc(screen.x, screen.y, size, 0, Math.PI * 2);
            this.ctx.fill();

            if (isSelected) {
                this._drawSelectedSystem(system, screen, size);
            } else if (isLinked) {
                this._drawLinkedSystem(system, screen, size);
            }
        });
    }

    _drawSelectedSystem(system, screen, size) {
        this.ctx.strokeStyle = '#00FFFF';
        this.ctx.lineWidth = 2;
        this.ctx.beginPath();
        this.ctx.arc(screen.x, screen.y, size + 4, 0, Math.PI * 2);
        this.ctx.stroke();

        const name = system.name.toUpperCase();
        const fontSize = 14;
        const padding = 8;

        this.ctx.font = `bold ${fontSize}px monospace`;
        this.ctx.textAlign = 'center';
        this.ctx.textBaseline = 'top';

        const textMetrics = this.ctx.measureText(name);
        const textWidth = textMetrics.width;
        const boxWidth = textWidth + padding * 2;
        const boxHeight = fontSize + padding;

        const boxX = screen.x - boxWidth / 2;
        const boxY = screen.y + size + 4;

        this.ctx.fillStyle = 'rgba(0, 0, 0, 0.75)';
        this.ctx.fillRect(boxX, boxY, boxWidth, boxHeight);

        this.ctx.strokeStyle = '#00FFFF';
        this.ctx.lineWidth = 1;
        this.ctx.strokeRect(boxX, boxY, boxWidth, boxHeight);

        this.ctx.fillStyle = '#FFFFFF';
        this.ctx.fillText(name, screen.x, boxY + padding / 2);
    }

    _drawLinkedSystem(system, screen, size) {
        const name = system.name.toUpperCase();
        const fontSize = 12;
        const padding = 6;

        this.ctx.font = `${fontSize}px monospace`;
        this.ctx.textAlign = 'center';
        this.ctx.textBaseline = 'top';

        const textMetrics = this.ctx.measureText(name);
        const textWidth = textMetrics.width;
        const boxWidth = textWidth + padding * 2;
        const boxHeight = fontSize + padding;

        const boxX = screen.x - boxWidth / 2;
        const boxY = screen.y + size + 2;

        this.ctx.fillStyle = 'rgba(0, 0, 0, 0.6)';
        this.ctx.fillRect(boxX, boxY, boxWidth, boxHeight);

        this.ctx.strokeStyle = 'rgba(150, 200, 255, 0.6)';
        this.ctx.lineWidth = 1;
        this.ctx.strokeRect(boxX, boxY, boxWidth, boxHeight);

        this.ctx.fillStyle = 'rgba(200, 220, 255, 0.9)';
        this.ctx.fillText(name, screen.x, boxY + padding / 2);
    }
}

/**
 * Scale renderer
 */
class ScaleRenderer {
    constructor(ctx, canvas, transformer) {
        this.ctx = ctx;
        this.canvas = canvas;
        this.transformer = transformer;
    }

    drawScale() {
        const { canvas, zoom } = this.transformer;
        const { AU_PER_LIGHT_YEAR, AU_PER_PARSEC, UNITS_PER_AU } = DISTANCE_CONSTANTS;

        const targetPixels = canvas.width * DEFAULT_CONFIG.scaleTargetPixels;
        const worldDistance = targetPixels / zoom;
        const auDistance = worldDistance / UNITS_PER_AU;

        let roundedValue, unit, scalePixels;

        if (auDistance <= 0 || !isFinite(auDistance)) {
            roundedValue = 1;
            unit = 'AU';
            scalePixels = (roundedValue * UNITS_PER_AU) * zoom;
        } else if (auDistance >= AU_PER_PARSEC) {
            const parsecDistance = auDistance / AU_PER_PARSEC;
            const magnitude = Math.pow(10, Math.floor(Math.log10(parsecDistance)));
            roundedValue = Math.max(0.1, Math.round(parsecDistance / magnitude) * magnitude);
            unit = 'pc';
            scalePixels = (roundedValue * AU_PER_PARSEC * UNITS_PER_AU) * zoom;
        } else if (auDistance >= AU_PER_LIGHT_YEAR) {
            const lightYearDistance = auDistance / AU_PER_LIGHT_YEAR;
            const magnitude = Math.pow(10, Math.floor(Math.log10(lightYearDistance)));
            roundedValue = Math.max(0.1, Math.round(lightYearDistance / magnitude) * magnitude);
            unit = 'ly';
            scalePixels = (roundedValue * AU_PER_LIGHT_YEAR * UNITS_PER_AU) * zoom;
        } else if (auDistance >= 1000) {
            const kauDistance = auDistance / 1000;
            const magnitude = Math.pow(10, Math.floor(Math.log10(kauDistance)));
            roundedValue = Math.max(0.1, Math.round(kauDistance / magnitude) * magnitude);
            unit = 'kAU';
            scalePixels = (roundedValue * 1000 * UNITS_PER_AU) * zoom;
        } else {
            const magnitude = Math.pow(10, Math.floor(Math.log10(auDistance)));
            roundedValue = Math.max(0.1, Math.round(auDistance / magnitude) * magnitude);
            unit = 'AU';
            scalePixels = (roundedValue * UNITS_PER_AU) * zoom;
        }

        const padding = 20;
        const x = padding;
        const y = canvas.height - padding;

        this.ctx.strokeStyle = 'rgba(255, 255, 255, 0.8)';
        this.ctx.lineWidth = 2;
        this.ctx.beginPath();
        this.ctx.moveTo(x, y);
        this.ctx.lineTo(x + scalePixels, y);
        this.ctx.stroke();

        this.ctx.beginPath();
        this.ctx.moveTo(x, y - 5);
        this.ctx.lineTo(x, y + 5);
        this.ctx.moveTo(x + scalePixels, y - 5);
        this.ctx.lineTo(x + scalePixels, y + 5);
        this.ctx.stroke();

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

        this.ctx.fillStyle = 'rgba(255, 255, 255, 0.9)';
        this.ctx.font = '12px monospace';
        this.ctx.textAlign = 'center';
        this.ctx.textBaseline = 'top';

        const textMetrics = this.ctx.measureText(label);
        const boxWidth = textMetrics.width + 10;
        const boxHeight = 20;

        this.ctx.fillStyle = 'rgba(0, 0, 0, 0.6)';
        this.ctx.fillRect(x + scalePixels / 2 - boxWidth / 2, y + 5, boxWidth, boxHeight);

        this.ctx.fillStyle = 'rgba(255, 255, 255, 0.9)';
        this.ctx.fillText(label, x + scalePixels / 2, y + 8);
    }
}

/**
 * System info manager
 */
class SystemInfoManager {
    constructor() {
        this.infoElement = document.getElementById('system-info');
        this.nameElement = document.getElementById('system-name');
        this.starTypeElement = document.getElementById('system-star-type');
        this.planetCountElement = document.getElementById('system-planet-count');
        this.coordinatesElement = document.getElementById('system-coordinates');
        this.statusElement = document.getElementById('system-status');
        this.nearbySection = document.getElementById('nearby-systems-section');
        this.nearbyList = document.getElementById('nearby-systems-list');
    }

    showSystemInfo(system, systems, projector, onSystemClick) {
        this.infoElement.classList.remove('hidden');
        this.nameElement.textContent = system.name.toUpperCase();
        this.starTypeElement.textContent = system.star_type ?
            system.star_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) :
            'Unknown';
        this.planetCountElement.textContent = system.planet_count;
        this.coordinatesElement.textContent =
            `X: ${system.x.toFixed(2)}, Y: ${system.y.toFixed(2)}, Z: ${system.z.toFixed(2)}`;
        this.statusElement.textContent = system.discovered ? 'Discovered' : 'Undiscovered';

        const nearbySystems = DistanceFormatter.findNearbySystems(systems, system, 5, projector);

        if (nearbySystems.length > 0) {
            this.nearbySection.classList.remove('hidden');
            this.nearbyList.innerHTML = '';

            nearbySystems.forEach(({ system: nearbySystem, distance }) => {
                const item = document.createElement('div');
                item.className =
                    'flex items-center justify-between p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 transition cursor-pointer';
                item.onclick = () => onSystemClick(nearbySystem);

                const nameDiv = document.createElement('div');
                nameDiv.className = 'flex items-center gap-2';
                nameDiv.innerHTML = `
                    <span class="text-gray-900 dark:text-white font-medium">${nearbySystem.name}</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">(${nearbySystem.planet_count} planets)</span>
                `;

                const distanceDiv = document.createElement('div');
                distanceDiv.className = 'text-gray-600 dark:text-gray-400';
                distanceDiv.textContent = DistanceFormatter.formatDistance(distance);

                item.appendChild(nameDiv);
                item.appendChild(distanceDiv);
                this.nearbyList.appendChild(item);
            });
        } else {
            this.nearbySection.classList.add('hidden');
        }
    }
}

/**
 * Main Universe Map class
 */
class UniverseMap {
    constructor(canvasId, systems) {
        this.canvas = document.getElementById(canvasId);
        this.ctx = this.canvas.getContext('2d');
        this.systems = systems;

        this.viewPlane = VIEW_PLANES.XY;
        this.selectedSystemId = null;
        this.isDragging = false;
        this.lastMouseX = 0;
        this.lastMouseY = 0;
        this.showConnections = false;
        this.maxConnectionDistance = DEFAULT_CONFIG.maxConnectionDistance;
        this.lastClickTime = 0;
        this.lastClickSystem = null;
        this.doubleClickDelay = 300; // milliseconds
        this.isAnimating = false;
        this.animationRequestId = null;

        this.projector = new CoordinateProjector(this.viewPlane);
        this.transformer = new ViewTransformer(this.canvas);
        this.gridRenderer = new GridRenderer(this.ctx, this.transformer);
        this.clipper = new LineClipper(this.canvas);
        this.connectionsRenderer = new ConnectionsRenderer(
            this.ctx, this.canvas, this.transformer, this.projector, this.clipper
        );
        this.systemsRenderer = new SystemsRenderer(
            this.ctx, this.canvas, this.transformer, this.projector
        );
        this.scaleRenderer = new ScaleRenderer(this.ctx, this.canvas, this.transformer);
        this.infoManager = new SystemInfoManager();

        this.linkedSystemsCache = null;
        this.cacheInvalidated = true;
        this.renderRequestId = null;

        this._setupCanvas();
        this._setupEventListeners();
        this._setupGlobalFunctions();
    }

    _setupCanvas() {
        const resizeCanvas = () => {
            const container = this.canvas.parentElement;
            this.canvas.width = container.clientWidth - 32;
            this.canvas.height = Math.max(600, window.innerHeight * 0.6);
            this.transformer.invalidateCache();
            this.render();
        };

        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);
        this.transformer.calculateInitialView(this.systems, this.projector);
        this._updateZoomDisplay();
    }

    _setupEventListeners() {
        this.canvas.addEventListener('click', (e) => this._handleClick(e));
        this.canvas.addEventListener('wheel', (e) => this._handleWheel(e));
        this.canvas.addEventListener('mousedown', (e) => this._handleMouseDown(e));
        this.canvas.addEventListener('mousemove', (e) => this._handleMouseMove(e));
        this.canvas.addEventListener('mouseup', (e) => this._handleMouseUp(e));
        this.canvas.addEventListener('mouseleave', () => this._handleMouseLeave());
        this.canvas.addEventListener('mouseenter', () => this._handleMouseEnter());
        this.canvas.addEventListener('contextmenu', (e) => e.preventDefault());
    }

    _setupGlobalFunctions() {
        window.changeViewPlane = (plane) => this.changeViewPlane(plane);
        window.zoomIn = () => this.zoomIn();
        window.zoomOut = () => this.zoomOut();
        window.resetView = () => this.resetView();
        window.toggleConnections = () => this.toggleConnections();
        window.updateMaxDistance = (value) => this.updateMaxDistance(value);
    }

    _handleClick(e) {
        if (e.button !== 0) {
            return;
        }

        if (this.isDragging) {
            this.isDragging = false;
            return;
        }

        const rect = this.canvas.getBoundingClientRect();
        const clickX = e.clientX - rect.left;
        const clickY = e.clientY - rect.top;

        let closestSystem = null;
        let minDistance = Infinity;

        this.systems.forEach(system => {
            const pos2d = this.projector.projectTo2D(system);
            const screen = this.transformer.worldToScreen(pos2d.x, pos2d.y);
            const distance = Math.sqrt(
                Math.pow(clickX - screen.x, 2) + Math.pow(clickY - screen.y, 2)
            );

            if (distance < DEFAULT_CONFIG.clickRadius && distance < minDistance) {
                minDistance = distance;
                closestSystem = system;
            }
        });

        if (closestSystem) {
            const currentTime = Date.now();
            const isDoubleClick = 
                currentTime - this.lastClickTime < this.doubleClickDelay &&
                this.lastClickSystem &&
                this.lastClickSystem.id === closestSystem.id;

            if (isDoubleClick) {
                // Double click: zoom on system and connected systems
                this._zoomOnSystemAndConnections(closestSystem);
                this.lastClickTime = 0; // Reset to prevent triple-click issues
                this.lastClickSystem = null;
            } else {
                // Single click: select system
                this.selectedSystemId = closestSystem.id;
                this._invalidateCache();
                this.infoManager.showSystemInfo(closestSystem, this.systems, this.projector, (system) => {
                    const pos2d = this.projector.projectTo2D(system);
                    this.transformer.centerX = pos2d.x;
                    this.transformer.centerY = pos2d.y;
                    this.selectedSystemId = system.id;
                    this._invalidateCache();
                    this.infoManager.showSystemInfo(system, this.systems, this.projector, () => {});
                    this.render();
                });
                this.render();
                
                // Store click info for double-click detection
                this.lastClickTime = currentTime;
                this.lastClickSystem = closestSystem;
            }
        }

        this.canvas.style.cursor = 'grab';
    }

    _handleWheel(e) {
        e.preventDefault();

        // Cancel any ongoing animation when user zooms with wheel
        if (this.isAnimating && this.animationRequestId) {
            cancelAnimationFrame(this.animationRequestId);
            this.isAnimating = false;
            this.animationRequestId = null;
        }

        const rect = this.canvas.getBoundingClientRect();
        const mouseX = e.clientX - rect.left;
        const mouseY = e.clientY - rect.top;

        const worldBefore = this.transformer.screenToWorld(mouseX, mouseY);

        const zoomFactor = e.deltaY > 0 ? 1 - DEFAULT_CONFIG.wheelZoomFactor : 1 + DEFAULT_CONFIG.wheelZoomFactor;
        const newZoom = Math.max(
            this.transformer.dynamicMinZoom,
            Math.min(DEFAULT_CONFIG.maxZoom, this.transformer.zoom * zoomFactor)
        );

        this.transformer.centerX = worldBefore.x - (mouseX - this.canvas.width / 2) / newZoom;
        this.transformer.centerY = worldBefore.y - (mouseY - this.canvas.height / 2) / newZoom;
        this.transformer.zoom = newZoom;
        this._invalidateCache();

        this._updateZoomDisplay();
        this.render();
    }

    _handleMouseDown(e) {
        if (e.button === 0 || e.button === 1) {
            // Cancel any ongoing animation when user starts dragging
            if (this.isAnimating && this.animationRequestId) {
                cancelAnimationFrame(this.animationRequestId);
                this.isAnimating = false;
                this.animationRequestId = null;
            }
            e.preventDefault();
            this.isDragging = true;
            const rect = this.canvas.getBoundingClientRect();
            this.lastMouseX = e.clientX - rect.left;
            this.lastMouseY = e.clientY - rect.top;
            this.canvas.style.cursor = 'grabbing';
        }
    }

    _handleMouseMove(e) {
        if (this.isDragging) {
            const rect = this.canvas.getBoundingClientRect();
            const mouseX = e.clientX - rect.left;
            const mouseY = e.clientY - rect.top;

            const deltaX = mouseX - this.lastMouseX;
            const deltaY = mouseY - this.lastMouseY;

            this.transformer.centerX -= deltaX / this.transformer.zoom;
            this.transformer.centerY -= deltaY / this.transformer.zoom;
            this._invalidateCache();

            this.lastMouseX = mouseX;
            this.lastMouseY = mouseY;

            this.render();
        }
    }

    _handleMouseUp(e) {
        if (e.button === 0 || e.button === 1) {
            this.isDragging = false;
            this.canvas.style.cursor = 'crosshair';
        }
    }

    _handleMouseLeave() {
        this.isDragging = false;
        this.canvas.style.cursor = 'crosshair';
    }

    _handleMouseEnter() {
        if (!this.isDragging) {
            this.canvas.style.cursor = 'grab';
        }
    }

    _invalidateCache() {
        this.cacheInvalidated = true;
        this.transformer.invalidateCache();
        this.linkedSystemsCache = null;
    }

    _getLinkedSystemIds() {
        if (!this.selectedSystemId) {
            return new Set();
        }

        if (!this.linkedSystemsCache || this.cacheInvalidated) {
            const selectedSystem = this.systems.find(s => s.id === this.selectedSystemId);
            if (!selectedSystem) {
                return new Set();
            }

            let nearbySystems;
            if (this.showConnections) {
                nearbySystems = this.systems
                    .filter(s => {
                        if (s.id === this.selectedSystemId) return false;
                        const distance = this.projector.calculateDistance3D(selectedSystem, s);
                        return distance <= this.maxConnectionDistance;
                    })
                    .map(s => ({
                        system: s,
                        distance: this.projector.calculateDistance3D(selectedSystem, s)
                    }));
            } else {
                nearbySystems = DistanceFormatter.findNearbySystems(this.systems, selectedSystem, 5, this.projector);
            }

            const linkedSystemIds = new Set(nearbySystems.map(({ system }) => system.id));
            this.linkedSystemsCache = linkedSystemIds;
            return linkedSystemIds;
        }

        return this.linkedSystemsCache;
    }

    _getConnectedSystems(system) {
        // Get systems connected to the given system (same logic as _getLinkedSystemIds but for any system)
        let nearbySystems;
        if (this.showConnections) {
            nearbySystems = this.systems
                .filter(s => {
                    if (s.id === system.id) return false;
                    const distance = this.projector.calculateDistance3D(system, s);
                    return distance <= this.maxConnectionDistance;
                })
                .map(s => ({
                    system: s,
                    distance: this.projector.calculateDistance3D(system, s)
                }));
        } else {
            nearbySystems = DistanceFormatter.findNearbySystems(this.systems, system, 5, this.projector);
        }
        return nearbySystems.map(({ system }) => system);
    }

    _zoomOnSystemAndConnections(system) {
        // Get connected systems
        const connectedSystems = this._getConnectedSystems(system);
        
        // Include the selected system itself
        const systemsToShow = [system, ...connectedSystems];
        
        if (systemsToShow.length === 0) {
            return;
        }

        // Calculate bounding box for all systems
        const positions = systemsToShow.map(s => {
            const pos2d = this.projector.projectTo2D(s);
            return { x: pos2d.x, y: pos2d.y };
        });

        const xs = positions.map(p => p.x);
        const ys = positions.map(p => p.y);
        const minX = Math.min(...xs);
        const maxX = Math.max(...xs);
        const minY = Math.min(...ys);
        const maxY = Math.max(...ys);

        const width = maxX - minX;
        const height = maxY - minY;
        const maxDimension = Math.max(width, height);

        // Calculate target center
        const targetCenterX = (minX + maxX) / 2;
        const targetCenterY = (minY + maxY) / 2;

        // Calculate target zoom to fit with padding
        let targetZoom;
        if (maxDimension > 0) {
            const padding = DEFAULT_CONFIG.padding;
            const scaleX = (this.canvas.width * (1 - padding * 2)) / width;
            const scaleY = (this.canvas.height * (1 - padding * 2)) / height;
            targetZoom = Math.min(scaleX, scaleY);
            // Ensure zoom doesn't exceed maxZoom
            targetZoom = Math.min(targetZoom, DEFAULT_CONFIG.maxZoom);
        } else {
            // If all systems are at same position, zoom in a bit
            targetZoom = this.transformer.zoom * 2;
            targetZoom = Math.min(targetZoom, DEFAULT_CONFIG.maxZoom);
        }

        // Store initial values for animation
        const startCenterX = this.transformer.centerX;
        const startCenterY = this.transformer.centerY;
        const startZoom = this.transformer.zoom;

        // Select the system immediately
        this.selectedSystemId = system.id;
        
        // Show system info immediately
        this.infoManager.showSystemInfo(system, this.systems, this.projector, (clickedSystem) => {
            const pos2d = this.projector.projectTo2D(clickedSystem);
            this.transformer.centerX = pos2d.x;
            this.transformer.centerY = pos2d.y;
            this.selectedSystemId = clickedSystem.id;
            this._invalidateCache();
            this.infoManager.showSystemInfo(clickedSystem, this.systems, this.projector, () => {});
            this.render();
        });

        // Animate to target view
        this._animateZoom(startCenterX, startCenterY, startZoom, targetCenterX, targetCenterY, targetZoom);
    }

    _easeInOutCubic(t) {
        // Easing function for smooth animation: ease-in-out cubic
        return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
    }

    _animateZoom(startX, startY, startZoom, targetX, targetY, targetZoom) {
        // Cancel any existing animation
        if (this.animationRequestId) {
            cancelAnimationFrame(this.animationRequestId);
        }

        if (this.isAnimating) {
            return;
        }

        this.isAnimating = true;
        const startTime = performance.now();
        const duration = DEFAULT_CONFIG.zoomAnimationDuration;

        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Apply easing function
            const easedProgress = this._easeInOutCubic(progress);

            // Interpolate values
            this.transformer.centerX = startX + (targetX - startX) * easedProgress;
            this.transformer.centerY = startY + (targetY - startY) * easedProgress;
            this.transformer.zoom = startZoom + (targetZoom - startZoom) * easedProgress;

            // Invalidate cache during animation
            this._invalidateCache();
            this._updateZoomDisplay();
            this.render();

            if (progress < 1) {
                this.animationRequestId = requestAnimationFrame(animate);
            } else {
                // Animation complete
                this.isAnimating = false;
                this.animationRequestId = null;
                // Ensure final values are exact
                this.transformer.centerX = targetX;
                this.transformer.centerY = targetY;
                this.transformer.zoom = targetZoom;
                this._invalidateCache();
                this._updateZoomDisplay();
                this.render();
            }
        };

        this.animationRequestId = requestAnimationFrame(animate);
    }

    _updateZoomDisplay() {
        const zoomElement = document.getElementById('zoom-level');
        if (zoomElement) {
            zoomElement.textContent = this.transformer.formatZoom(this.transformer.zoom);
        }
    }

    render() {
        if (this.renderRequestId) {
            cancelAnimationFrame(this.renderRequestId);
        }

        this.renderRequestId = requestAnimationFrame(() => {
            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
            this.gridRenderer.drawGrid();
            this.connectionsRenderer.drawConnections(
                this.systems,
                this.selectedSystemId,
                this.showConnections,
                this.maxConnectionDistance
            );
            this.systemsRenderer.drawSystems(
                this.systems,
                this.selectedSystemId,
                this._getLinkedSystemIds()
            );
            this.scaleRenderer.drawScale();
            this.cacheInvalidated = false;
            this.renderRequestId = null;
        });
    }

    changeViewPlane(plane) {
        this.viewPlane = plane;
        this.projector.setViewPlane(plane);
        this._invalidateCache();
        this.transformer.calculateInitialView(this.systems, this.projector);
        this._updateZoomDisplay();

        document.querySelectorAll('[id^="view-"]').forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white');
            btn.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
        });
        const activeBtn = document.getElementById('view-' + plane);
        if (activeBtn) {
            activeBtn.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
            activeBtn.classList.add('bg-blue-600', 'text-white');
        }

        this.render();
    }

    zoomIn() {
        // Cancel any ongoing animation when user zooms with button
        if (this.isAnimating && this.animationRequestId) {
            cancelAnimationFrame(this.animationRequestId);
            this.isAnimating = false;
            this.animationRequestId = null;
        }

        const zoomFactor = DEFAULT_CONFIG.zoomFactor;
        this.transformer.zoom = Math.min(
            DEFAULT_CONFIG.maxZoom,
            this.transformer.zoom * zoomFactor
        );
        this._invalidateCache();
        this._updateZoomDisplay();
        this.render();
    }

    zoomOut() {
        // Cancel any ongoing animation when user zooms with button
        if (this.isAnimating && this.animationRequestId) {
            cancelAnimationFrame(this.animationRequestId);
            this.isAnimating = false;
            this.animationRequestId = null;
        }

        const zoomFactor = DEFAULT_CONFIG.zoomFactor;
        this.transformer.zoom = Math.max(
            this.transformer.dynamicMinZoom,
            this.transformer.zoom / zoomFactor
        );
        this._invalidateCache();
        this._updateZoomDisplay();
        this.render();
    }

    resetView() {
        // Cancel any ongoing animation when user resets view
        if (this.isAnimating && this.animationRequestId) {
            cancelAnimationFrame(this.animationRequestId);
            this.isAnimating = false;
            this.animationRequestId = null;
        }

        this.transformer.calculateInitialView(this.systems, this.projector);
        this._updateZoomDisplay();
        this.render();
    }

    toggleConnections() {
        this.showConnections = document.getElementById('show-connections').checked;
        this._invalidateCache();
        this.render();
    }

    updateMaxDistance(value) {
        this.maxConnectionDistance = parseFloat(value);
        this._invalidateCache();
        const valueElement = document.getElementById('max-distance-value');
        if (valueElement) {
            valueElement.textContent = value + ' AU';
        }
        if (this.showConnections) {
            this.render();
        }
    }
}

// Export for use in other modules
export { UniverseMap, STAR_COLORS, DISTANCE_CONSTANTS, VIEW_PLANES };

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const systemsData = window.universeMapSystems;
    if (systemsData && document.getElementById('universe-map-canvas')) {
        window.universeMap = new UniverseMap('universe-map-canvas', systemsData);
    }
});

