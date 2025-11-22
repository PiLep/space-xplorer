/**
 * Universe Map - Main Module
 * Handles the 3D star system map visualization
 */

import { 
    STAR_COLORS, 
    DISTANCE_CONSTANTS, 
    VIEW_PLANES, 
    DEFAULT_MAP_CONFIG 
} from './map-constants.js';
import { formatDistance, findNearby, isDiscovered } from './map-utils.js';
import { BaseViewTransformer, BaseGridRenderer, BaseScaleRenderer } from './map-base-classes.js';

const DEFAULT_CONFIG = {
    ...DEFAULT_MAP_CONFIG,
    maxConnectionDistance: 200,
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
class ViewTransformer extends BaseViewTransformer {
    constructor(canvas) {
        super(canvas, DEFAULT_CONFIG);
        this.dynamicMinZoom = DEFAULT_CONFIG.minZoom; // Will be calculated based on initial zoom
    }

    calculateInitialView(systems, projector) {
        // Ensure canvas has valid dimensions before calculating view
        if (!this.canvas || this.canvas.width === 0 || this.canvas.height === 0) {
            console.warn('Canvas dimensions not ready for calculateInitialView');
            return;
        }

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
 * Distance formatting utilities (using shared functions)
 */
const DistanceFormatter = {
    formatDistance,
    findNearbySystems(systems, selectedSystem, maxCount = 5, projector) {
        return findNearby(systems, selectedSystem, maxCount, (s1, s2) => 
            projector.calculateDistance3D(s1, s2)
        ).map(({ item, distance }) => ({ system: item, distance }));
    }
};

/**
 * Grid renderer
 */
class GridRenderer extends BaseGridRenderer {
    constructor(ctx, transformer) {
        super(ctx, transformer, DEFAULT_CONFIG);
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

    drawConnections(systems, selectedSystemId, showConnections, maxConnectionDistance, showDistances = false, showOnlyDiscovered = false, godMode = false) {
        if (!showConnections) {
            if (!selectedSystemId) {
                return;
            }

            const selectedSystem = systems.find(s => s.id === selectedSystemId);
            if (!selectedSystem) {
                return;
            }

            // Filter systems if showOnlyDiscovered is enabled (unless god mode)
            let systemsToSearch = systems;
            if (showOnlyDiscovered && !godMode) {
                systemsToSearch = systems.filter(s => {
                    const isDiscovered = s.discovered === true || s.discovered === 'true' || s.discovered === 1;
                    return isDiscovered;
                });
            }

            const nearbySystems = DistanceFormatter.findNearbySystems(systemsToSearch, selectedSystem, 5, this.projector);

            if (nearbySystems.length === 0) {
                return;
            }

            const selectedPos2d = this.projector.projectTo2D(selectedSystem);
            const selectedScreen = this.transformer.worldToScreen(selectedPos2d.x, selectedPos2d.y);

            // For undiscovered systems, only show connections to discovered systems (unless god mode)
            const isSelectedDiscovered = isDiscovered(selectedSystem);
            const filteredNearbySystems = nearbySystems.filter(({ system: nearbySystem }) => {
                // Ensure discovered is a boolean
                const nearbyDiscovered = isDiscovered(nearbySystem);
                
                // In god mode, show all connections
                if (godMode) {
                    return true;
                }
                
                // If selected system is undiscovered, only show connections to discovered systems
                if (!isSelectedDiscovered) {
                    return nearbyDiscovered;
                }
                // If selected system is discovered, show all connections (unless filtering)
                if (showOnlyDiscovered) {
                    return nearbyDiscovered;
                }
                return true;
            });

            if (filteredNearbySystems.length === 0) {
                return;
            }

            filteredNearbySystems.forEach(({ system: nearbySystem, distance }) => {
                const nearbyPos2d = this.projector.projectTo2D(nearbySystem);
                const nearbyScreen = this.transformer.worldToScreen(nearbyPos2d.x, nearbyPos2d.y);

                const visibleSegment = this.clipper.clipLineToCanvas(
                    selectedScreen.x, selectedScreen.y,
                    nearbyScreen.x, nearbyScreen.y
                );

                if (visibleSegment) {
                    // Check if destination system is discovered
                    const nearbyDiscovered = isDiscovered(nearbySystem);
                    const showAsDiscovered = godMode || nearbyDiscovered;
                    
                    // Apply gray discrete style for links to undiscovered systems (unless god mode)
                    let connectionStyle;
                    if (!showAsDiscovered) {
                        // Gray discrete style for links to undiscovered systems
                        connectionStyle = { color: 'rgba(140, 140, 140, 0.4)', width: 1.0, dash: [3, 3] };
                    } else if (!isSelectedDiscovered && !godMode) {
                        // Mysterious style for connections from undiscovered systems
                        connectionStyle = { color: 'rgba(150, 150, 200, 0.3)', width: 1, dash: [3, 3] };
                    } else {
                        // Normal style for connections between discovered systems (or god mode)
                        connectionStyle = { color: 'rgba(0, 255, 255, 0.5)', width: 1.5, dash: [5, 5] };
                    }

                    this.ctx.strokeStyle = connectionStyle.color;
                    this.ctx.lineWidth = connectionStyle.width;
                    this.ctx.setLineDash(connectionStyle.dash);

                    this.ctx.beginPath();
                    this.ctx.moveTo(visibleSegment.x1, visibleSegment.y1);
                    this.ctx.lineTo(visibleSegment.x2, visibleSegment.y2);
                    this.ctx.stroke();

                    // Draw distance label only if enabled and both systems are discovered (or god mode)
                    if (showDistances && (godMode || (isSelectedDiscovered && nearbyDiscovered))) {
                        this._drawDistanceLabel(
                            visibleSegment.x1, visibleSegment.y1,
                            visibleSegment.x2, visibleSegment.y2,
                            distance,
                            'rgba(0, 255, 255, 0.9)'
                        );
                    }
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
            // Ensure discovered is a boolean
            const system1Discovered = isDiscovered(system1);
            
            // Filter systems if showOnlyDiscovered is enabled (unless god mode)
            if (showOnlyDiscovered && !godMode && !system1Discovered) {
                return;
            }

            systems.forEach((system2, index2) => {
                if (index1 >= index2) return;

                // Ensure discovered is a boolean
                const system2Discovered = isDiscovered(system2);
                
                // Filter systems if showOnlyDiscovered is enabled (unless god mode)
                if (showOnlyDiscovered && !godMode && !system2Discovered) {
                    return;
                }

                const distance = this.projector.calculateDistance3D(system1, system2);
                if (distance > maxConnectionDistance) {
                    return;
                }

                // For undiscovered systems, only show connections to discovered systems (unless god mode)
                
                // Skip connection if both systems are undiscovered (unless god mode)
                if (!godMode && !system1Discovered && !system2Discovered) {
                    return;
                }

                // Skip connection if one is undiscovered and the other is not discovered
                // (we only show connections FROM undiscovered TO discovered)
                if (!system1Discovered && system2Discovered) {
                    // This is OK - undiscovered to discovered
                } else if (system1Discovered && !system2Discovered) {
                    // This is OK - discovered to undiscovered
                } else {
                    // Both discovered - always show
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
                    const isSelected = system1.id === selectedSystemId || system2.id === selectedSystemId;
                    
                    // Determine connection style based on discovery status
                    // Priority: links to undiscovered systems get gray discrete style (unless god mode)
                    const showAsDiscovered1 = godMode || system1Discovered;
                    const showAsDiscovered2 = godMode || system2Discovered;
                    let connectionColor, connectionWidth;
                    if (!showAsDiscovered1 && !showAsDiscovered2) {
                        // Both undiscovered - gray discrete style
                        connectionColor = 'rgba(140, 140, 140, 0.3)';
                        connectionWidth = 0.8;
                    } else if (!showAsDiscovered1 || !showAsDiscovered2) {
                        // One undiscovered - gray discrete style for links to undiscovered systems
                        connectionColor = isSelected 
                            ? 'rgba(140, 140, 140, 0.45)' 
                            : 'rgba(140, 140, 140, 0.35)';
                        connectionWidth = isSelected ? 1.0 : 0.9;
                    } else {
                        // Both discovered (or god mode) - normal style
                        connectionColor = isSelected 
                            ? 'rgba(0, 255, 255, 0.5)' 
                            : 'rgba(100, 150, 255, 0.2)';
                        connectionWidth = isSelected ? 1.5 : 1;
                    }

                    this.ctx.strokeStyle = connectionColor;
                    this.ctx.lineWidth = connectionWidth;

                    this.ctx.beginPath();
                    this.ctx.moveTo(visibleSegment.x1, visibleSegment.y1);
                    this.ctx.lineTo(visibleSegment.x2, visibleSegment.y2);
                    this.ctx.stroke();

                    // Draw distance label only for selected connections between discovered systems (or god mode) and if enabled
                    if (isSelected && showDistances && (godMode || (system1Discovered && system2Discovered))) {
                        this._drawDistanceLabel(
                            visibleSegment.x1, visibleSegment.y1,
                            visibleSegment.x2, visibleSegment.y2,
                            distance,
                            'rgba(0, 255, 255, 0.9)'
                        );
                    }
                }
            });
        });

        this.ctx.setLineDash([]);
    }

    _drawDistanceLabel(x1, y1, x2, y2, distance, textColor) {
        // Calculate midpoint
        const midX = (x1 + x2) / 2;
        const midY = (y1 + y2) / 2;

        // Calculate angle of the line
        const dx = x2 - x1;
        const dy = y2 - y1;
        let angle = Math.atan2(dy, dx);

        // Format distance
        const distanceText = DistanceFormatter.formatDistance(distance);

        // Calculate text dimensions
        this.ctx.save();
        this.ctx.font = '11px monospace';
        this.ctx.textAlign = 'center';
        this.ctx.textBaseline = 'middle';
        const textMetrics = this.ctx.measureText(distanceText);
        const textWidth = textMetrics.width;
        const textHeight = 14; // Approximate height for 11px font
        const padding = 4;

        // Adjust angle for readability: normalize to -90 to 90 degrees range
        // This ensures text is always readable from left to right
        let normalizedAngle = angle;
        const angleDegrees = (angle * 180) / Math.PI;
        let needsFlip = false;
        
        // Normalize angle to [-90, 90] range for readability
        if (angleDegrees > 90 || angleDegrees < -90) {
            normalizedAngle = angle + Math.PI;
            needsFlip = true;
        }

        // Calculate label position (above the line)
        // Offset perpendicular to the line direction
        const offsetDistance = 12; // pixels above the line
        const perpAngle = angle + Math.PI / 2; // Perpendicular angle
        
        // If we flipped the angle, also flip the offset position
        const finalPerpAngle = needsFlip ? perpAngle + Math.PI : perpAngle;
        const offsetX = Math.cos(finalPerpAngle) * offsetDistance;
        const offsetY = Math.sin(finalPerpAngle) * offsetDistance;

        const labelX = midX + offsetX;
        const labelY = midY + offsetY;

        // Draw background rectangle
        const boxWidth = textWidth + padding * 2;
        const boxHeight = textHeight + padding * 2;

        // Rotate context to match normalized line angle
        this.ctx.translate(labelX, labelY);
        this.ctx.rotate(normalizedAngle);

        // Draw background
        this.ctx.fillStyle = 'rgba(0, 0, 0, 0.7)';
        this.ctx.fillRect(-boxWidth / 2, -boxHeight / 2, boxWidth, boxHeight);

        // Draw border
        this.ctx.strokeStyle = textColor;
        this.ctx.lineWidth = 1;
        this.ctx.strokeRect(-boxWidth / 2, -boxHeight / 2, boxWidth, boxHeight);

        // Draw text
        this.ctx.fillStyle = textColor;
        this.ctx.fillText(distanceText, 0, 0);

        this.ctx.restore();
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

    drawSystems(systems, selectedSystemId, linkedSystemIds, showOnlyDiscovered = false, godMode = false) {
        systems.forEach(system => {
            // Ensure discovered is a boolean (handle string "true"/"false" from JSON)
            const discovered = isDiscovered(system);
            const showAsDiscovered = godMode || discovered;
            
            // Filter out undiscovered systems if showOnlyDiscovered is enabled (unless god mode)
            if (showOnlyDiscovered && !showAsDiscovered) {
                return;
            }

            const pos2d = this.projector.projectTo2D(system);
            const screen = this.transformer.worldToScreen(pos2d.x, pos2d.y);

            if (screen.x < -10 || screen.x > this.canvas.width + 10 ||
                screen.y < -10 || screen.y > this.canvas.height + 10) {
                return;
            }

            const isSelected = system.id === selectedSystemId;
            const isLinked = linkedSystemIds.has(system.id);

            // Mysterious style for undiscovered systems (unless god mode)
            if (!showAsDiscovered) {
                this._drawMysteriousSystem(system, screen, isSelected, isLinked);
                return;
            }

            // Normal style for discovered systems
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

    _drawMysteriousSystem(system, screen, isSelected, isLinked) {
        // Draw a simple gray point for undiscovered systems
        const size = isSelected ? 4 : 2;
        const grayColor = '#808080'; // Simple gray color

        // Draw the gray point
        this.ctx.fillStyle = grayColor;
        this.ctx.beginPath();
        this.ctx.arc(screen.x, screen.y, size, 0, Math.PI * 2);
        this.ctx.fill();

        // Draw a border for selected systems
        if (isSelected) {
            this.ctx.strokeStyle = '#CCCCCC';
            this.ctx.lineWidth = 1;
            this.ctx.setLineDash([2, 2]);
            this.ctx.beginPath();
            this.ctx.arc(screen.x, screen.y, size + 2, 0, Math.PI * 2);
            this.ctx.stroke();
            this.ctx.setLineDash([]);
        }
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
class ScaleRenderer extends BaseScaleRenderer {
    constructor(ctx, canvas, transformer) {
        super(ctx, canvas, transformer, DEFAULT_CONFIG);
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

    showSystemInfo(system, systems, projector, onSystemClick, godMode = false) {
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
                const discovered = isDiscovered(nearbySystem);
                const showInfo = godMode || discovered;
                
                const item = document.createElement('div');
                item.className =
                    'flex items-center justify-between p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-800 transition cursor-pointer';
                item.onclick = () => onSystemClick(nearbySystem);

                const nameDiv = document.createElement('div');
                nameDiv.className = 'flex items-center gap-2';
                
                if (showInfo) {
                    // Show full information (god mode or discovered systems)
                    nameDiv.innerHTML = `
                        <span class="text-gray-900 dark:text-white font-medium">${nearbySystem.name}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">(${nearbySystem.planet_count} planets)</span>
                    `;
                } else {
                    // Hide information for undiscovered systems
                    nameDiv.innerHTML = `
                        <span class="text-gray-500 dark:text-gray-500 font-medium italic">Unknown System</span>
                        <span class="text-xs text-gray-400 dark:text-gray-600">(?)</span>
                    `;
                }

                const distanceDiv = document.createElement('div');
                distanceDiv.className = showInfo ? 'text-gray-600 dark:text-gray-400' : 'text-gray-400 dark:text-gray-600';
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
        if (!this.canvas) {
            throw new Error(`Canvas with id "${canvasId}" not found`);
        }
        
        const ctx = this.canvas.getContext('2d');
        if (!ctx) {
            throw new Error(`Could not get 2d context from canvas "${canvasId}"`);
        }
        this.ctx = ctx;
        this.systems = systems;

        this.viewPlane = VIEW_PLANES.XY;
        this.selectedSystemId = null;
        this.isDragging = false;
        this.lastMouseX = 0;
        this.lastMouseY = 0;
        this.showConnections = false;
        this.showDistances = false; // Disabled by default
        this.showOnlyDiscovered = false; // Show all systems by default
        this.godMode = false; // God mode disabled by default
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

        // Use requestAnimationFrame to ensure DOM is ready
        requestAnimationFrame(() => {
            this._setupCanvas();
            this._setupEventListeners();
            this._setupGlobalFunctions();
        });
    }

    _setupCanvas() {
        // Ensure canvas and parent are available
        if (!this.canvas || !this.canvas.parentElement) {
            console.warn('Canvas or parent not ready, retrying...');
            setTimeout(() => this._setupCanvas(), 50);
            return;
        }

        const resizeCanvas = () => {
            const container = this.canvas.parentElement;
            if (!container) {
                return;
            }
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
        window.toggleDistances = () => this.toggleDistances();
        window.toggleShowOnlyDiscovered = () => this.toggleShowOnlyDiscovered();
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
            // Filter out undiscovered systems if showOnlyDiscovered is enabled
            if (this.showOnlyDiscovered && !system.discovered) {
                return;
            }

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
                    this.infoManager.showSystemInfo(system, this.systems, this.projector, () => {}, this.godMode);
                    this.render();
                }, this.godMode);
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
            this.infoManager.showSystemInfo(clickedSystem, this.systems, this.projector, () => {}, this.godMode);
            this.render();
        }, this.godMode);

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
                this.maxConnectionDistance,
                this.showDistances,
                this.showOnlyDiscovered,
                this.godMode
            );
            this.systemsRenderer.drawSystems(
                this.systems,
                this.selectedSystemId,
                this._getLinkedSystemIds(),
                this.showOnlyDiscovered,
                this.godMode
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

    toggleDistances() {
        this.showDistances = document.getElementById('show-distances').checked;
        this._invalidateCache();
        this.render();
    }

    toggleShowOnlyDiscovered() {
        const checkbox = document.getElementById('show-only-discovered');
        if (!checkbox) {
            return;
        }
        this.showOnlyDiscovered = checkbox.checked;
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

    setGodMode(enabled) {
        this.godMode = enabled;
        this._invalidateCache();
        // Update info display if a system is selected
        if (this.selectedSystemId) {
            const selectedSystem = this.systems.find(s => s.id === this.selectedSystemId);
            if (selectedSystem) {
                this.infoManager.showSystemInfo(selectedSystem, this.systems, this.projector, (system) => {
                    this.selectSystem(system.id);
                }, this.godMode);
            }
        }
        this.render();
    }
}

// Export for use in other modules
export { UniverseMap, STAR_COLORS, DISTANCE_CONSTANTS, VIEW_PLANES };
// Re-export shared constants and utilities for backward compatibility
export { formatDistance, isDiscovered } from './map-utils.js';

// Make UniverseMap available globally for initialization from Blade template
window.UniverseMap = UniverseMap;

