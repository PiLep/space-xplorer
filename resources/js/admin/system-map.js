/**
 * System Map - Main Module
 * Handles the visualization of planets orbiting a star system
 */

import { 
    STAR_COLORS, 
    STAR_SIZES, 
    PLANET_COLORS, 
    DISTANCE_CONSTANTS, 
    DEFAULT_SYSTEM_MAP_CONFIG 
} from './map-constants.js';
import { orbitalTo2D } from './map-utils.js';
import { BaseViewTransformer, BaseGridRenderer, BaseScaleRenderer } from './map-base-classes.js';

const DEFAULT_CONFIG = DEFAULT_SYSTEM_MAP_CONFIG;

/**
 * View transformation utilities
 */
class ViewTransformer extends BaseViewTransformer {
    constructor(canvas) {
        super(canvas, DEFAULT_CONFIG);
    }

    calculateInitialView(planets) {
        // Ensure canvas has valid dimensions
        if (!this.canvas || this.canvas.width === 0 || this.canvas.height === 0) {
            console.warn('Canvas dimensions not ready, using default zoom');
            this.centerX = 0;
            this.centerY = 0;
            this.zoom = DEFAULT_CONFIG.initialZoom;
            this.initialZoom = this.zoom;
            return;
        }

        if (planets.length === 0) {
            this.centerX = 0;
            this.centerY = 0;
            this.zoom = DEFAULT_CONFIG.initialZoom;
            this.initialZoom = this.zoom;
            return;
        }

        // Center is always at (0, 0) - the star
        this.centerX = 0;
        this.centerY = 0;

        // Find the maximum orbital distance (most reliable method)
        const validPlanets = planets.filter(p => p.orbital_distance !== null);
        
        if (validPlanets.length === 0) {
            this.zoom = DEFAULT_CONFIG.initialZoom;
            this.initialZoom = this.zoom;
            return;
        }

        const maxOrbitalDistance = Math.max(
            ...validPlanets.map(p => p.orbital_distance),
            10 // Minimum fallback
        );

        // Calculate the bounding box needed (diameter of the system)
        // Add some margin for planet sizes and labels
        const systemDiameter = maxOrbitalDistance * 2;
        
        // Add margin: 10% on each side (20% total) plus extra for planet sizes
        const margin = 0.10;
        const planetSizeMargin = 15; // pixels for planet sizes and labels
        
        // Calculate zoom to fit the system diameter with margins
        const availableWidth = this.canvas.width - (planetSizeMargin * 2);
        const availableHeight = this.canvas.height - (planetSizeMargin * 2);
        
        const scaleX = (availableWidth * (1 - margin * 2)) / systemDiameter;
        const scaleY = (availableHeight * (1 - margin * 2)) / systemDiameter;
        
        // Use the smaller scale to ensure everything fits
        this.zoom = Math.min(scaleX, scaleY);
        
        // Ensure zoom is within reasonable bounds
        this.zoom = Math.max(DEFAULT_CONFIG.minZoom, Math.min(this.zoom, DEFAULT_CONFIG.maxZoom));

        this.initialZoom = this.zoom;
    }
}

// orbitalTo2D is now imported from map-utils.js

/**
 * Planets renderer
 */
class PlanetsRenderer {
    constructor(ctx, canvas, transformer) {
        this.ctx = ctx;
        this.canvas = canvas;
        this.transformer = transformer;
    }

    drawStar(starType) {
        const center = this.transformer.worldToScreen(0, 0);
        const color = STAR_COLORS[starType] || '#FFFFFF';
        // Get star size based on type, fallback to default
        const baseSize = STAR_SIZES[starType] || DEFAULT_CONFIG.starSize;
        const size = baseSize * this.transformer.zoom;

        // Draw discrete halo (subtle glow around the star)
        const haloSize = size * 1.4;
        const haloGradient = this.ctx.createRadialGradient(
            center.x, center.y, size * 0.8,
            center.x, center.y, haloSize
        );
        haloGradient.addColorStop(0, color + '20'); // Very subtle at edge of star
        haloGradient.addColorStop(0.5, color + '10');
        haloGradient.addColorStop(1, color + '00'); // Transparent at edge
        this.ctx.fillStyle = haloGradient;
        this.ctx.beginPath();
        this.ctx.arc(center.x, center.y, haloSize, 0, Math.PI * 2);
        this.ctx.fill();

        // Draw star core with subtle gradient
        const coreGradient = this.ctx.createRadialGradient(
            center.x, center.y, 0,
            center.x, center.y, size
        );
        coreGradient.addColorStop(0, color + 'FF'); // Full color at center
        coreGradient.addColorStop(0.7, color + 'E6');
        coreGradient.addColorStop(1, color + 'CC'); // Slightly transparent at edge
        this.ctx.fillStyle = coreGradient;
        this.ctx.beginPath();
        this.ctx.arc(center.x, center.y, size, 0, Math.PI * 2);
        this.ctx.fill();
    }

    drawOrbits(planets, showOrbits) {
        if (!showOrbits) {
            return;
        }

        this.ctx.strokeStyle = 'rgba(255, 255, 255, 0.4)';
        this.ctx.lineWidth = 1.5;

        planets.forEach(planet => {
            if (planet.orbital_distance === null) {
                return;
            }

            const center = this.transformer.worldToScreen(0, 0);
            const radius = planet.orbital_distance * this.transformer.zoom;

            this.ctx.beginPath();
            this.ctx.arc(center.x, center.y, radius, 0, Math.PI * 2);
            this.ctx.stroke();
        });
    }

    drawPlanets(planets, selectedPlanetId, showLabels, animationTime = 0, animationSpeed = 0) {
        planets.forEach(planet => {
            const pos2d = orbitalTo2D(planet.orbital_distance, planet.orbital_angle, animationTime, animationSpeed);
            const screen = this.transformer.worldToScreen(pos2d.x, pos2d.y);

            if (screen.x < -50 || screen.x > this.canvas.width + 50 ||
                screen.y < -50 || screen.y > this.canvas.height + 50) {
                return;
            }

            const isSelected = planet.id === selectedPlanetId;
            const planetType = planet.type || 'tellurique';
            // Try French first, then English, then lowercase versions
            const color = PLANET_COLORS[planetType] || 
                         PLANET_COLORS[planetType?.toLowerCase()] || 
                         '#888888';
            
            // Calculate size based on planet size property
            const baseSize = this.getPlanetSize(planet.size);
            const size = isSelected ? baseSize + 2 : baseSize;

            // Draw planet
            this.ctx.fillStyle = color;
            this.ctx.beginPath();
            this.ctx.arc(screen.x, screen.y, size, 0, Math.PI * 2);
            this.ctx.fill();

            // Draw selection ring
            if (isSelected) {
                this.ctx.strokeStyle = '#00FFFF';
                this.ctx.lineWidth = 2;
                this.ctx.beginPath();
                this.ctx.arc(screen.x, screen.y, size + 3, 0, Math.PI * 2);
                this.ctx.stroke();
            }

            // Draw label
            if (showLabels) {
                this.ctx.fillStyle = '#FFFFFF';
                this.ctx.font = '12px monospace';
                this.ctx.textAlign = 'center';
                this.ctx.textBaseline = 'top';
                this.ctx.fillText(planet.name, screen.x, screen.y + size + 5);
            }
        });
    }

    /**
     * Get planet size based on size property
     */
    getPlanetSize(sizeProperty) {
        if (!sizeProperty) {
            return DEFAULT_CONFIG.planetSize;
        }
        
        const sizeLower = sizeProperty.toLowerCase();
        
        // Map size property to pixel size
        switch (sizeLower) {
            case 'petite':
            case 'small':
                return 3;
            case 'moyenne':
            case 'medium':
                return 5;
            case 'grande':
            case 'large':
                return 7;
            default:
                return DEFAULT_CONFIG.planetSize;
        }
    }
}

/**
 * Grid renderer
 */
class GridRenderer extends BaseGridRenderer {
    constructor(ctx, transformer) {
        super(ctx, transformer, DEFAULT_CONFIG);
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
 * Main System Map class
 */
class SystemMap {
    constructor(canvasId, starSystem, planets) {
        this.canvas = document.getElementById(canvasId);
        if (!this.canvas) {
            console.error(`Canvas with id "${canvasId}" not found`);
            return;
        }

        this.ctx = this.canvas.getContext('2d');
        this.starSystem = starSystem;
        this.planets = planets;
        this.selectedPlanetId = null;
        this.showOrbits = true;
        this.showLabels = true;
        this.isDragging = false;
        this.hasMoved = false;
        this.dragStart = null;
        this.lastPan = { x: 0, y: 0 };
        this.animationEnabled = DEFAULT_CONFIG.orbitAnimationEnabled;
        this.animationSpeed = DEFAULT_CONFIG.orbitAnimationSpeed;
        this.animationStartTime = performance.now();
        this.lastFrameTime = performance.now();
        this.animationFrameId = null;
        
        // Zoom animation state
        this.isZoomAnimating = false;
        this.zoomAnimationRequestId = null;

        // Initialize transformer
        this.transformer = new ViewTransformer(this.canvas);

        // Initialize renderers
        this.renderer = new PlanetsRenderer(this.ctx, this.canvas, this.transformer);
        this.gridRenderer = new GridRenderer(this.ctx, this.transformer);
        this.scaleRenderer = new ScaleRenderer(this.ctx, this.canvas, this.transformer);

        // Setup event listeners
        this.setupEventListeners();

        // Initial render - use requestAnimationFrame to ensure canvas is ready
        requestAnimationFrame(() => {
            // Resize canvas first to get correct dimensions
            this.resize();
            // Calculate initial view AFTER canvas is resized
            this.transformer.calculateInitialView(this.planets);
            // Small delay to ensure canvas is fully rendered
            setTimeout(() => {
                this.render();
                // Start animation loop if enabled
                if (this.animationEnabled) {
                    this.animationElapsedTime = 0;
                    this.startAnimation();
                }
            }, 10);
        });
    }

    setupEventListeners() {
        // Mouse events
        this.canvas.addEventListener('mousedown', (e) => this.onMouseDown(e));
        this.canvas.addEventListener('mousemove', (e) => this.onMouseMove(e));
        this.canvas.addEventListener('mouseup', (e) => this.onMouseUp(e));
        this.canvas.addEventListener('mouseleave', (e) => this.onMouseLeave(e));
        this.canvas.addEventListener('wheel', (e) => this.onWheel(e));
        this.canvas.addEventListener('dblclick', (e) => this.onDoubleClick(e));

        // Touch events
        this.canvas.addEventListener('touchstart', (e) => this.onTouchStart(e));
        this.canvas.addEventListener('touchmove', (e) => this.onTouchMove(e));
        this.canvas.addEventListener('touchend', (e) => this.onTouchEnd(e));

        // Window resize
        window.addEventListener('resize', () => this.resize());
    }

    resize() {
        const container = this.canvas.parentElement;
        if (!container) {
            return;
        }
        const rect = container.getBoundingClientRect();
        // Ensure minimum dimensions
        const width = Math.max(rect.width - 32, 800);
        const height = Math.max(rect.height - 32, 600);
        
        // Always set dimensions (canvas might not have been initialized yet)
        this.canvas.width = width;
        this.canvas.height = height;
    }

    onMouseDown(e) {
        // Cancel any ongoing zoom animation when user starts dragging
        if (this.isZoomAnimating && this.zoomAnimationRequestId) {
            cancelAnimationFrame(this.zoomAnimationRequestId);
            this.isZoomAnimating = false;
            this.zoomAnimationRequestId = null;
        }
        
        const rect = this.canvas.getBoundingClientRect();
        this.dragStart = {
            x: e.clientX - rect.left,
            y: e.clientY - rect.top,
        };
        this.lastPan = {
            x: this.transformer.centerX,
            y: this.transformer.centerY,
        };
        // Don't set isDragging immediately - wait to see if there's actual movement
        this.isDragging = false;
        this.hasMoved = false;
        this.canvas.style.cursor = 'grab';
    }

    onMouseMove(e) {
        const rect = this.canvas.getBoundingClientRect();
        const mouseX = e.clientX - rect.left;
        const mouseY = e.clientY - rect.top;

        // Check if mouse has moved significantly (more than 5 pixels) to consider it a drag
        if (this.dragStart !== null && !this.hasMoved) {
            const moveDistance = Math.sqrt(
                Math.pow(mouseX - this.dragStart.x, 2) + 
                Math.pow(mouseY - this.dragStart.y, 2)
            );
            if (moveDistance > 5) {
                this.isDragging = true;
                this.hasMoved = true;
                this.canvas.style.cursor = 'grabbing';
            }
        }

        if (this.isDragging && this.hasMoved && this.dragStart !== null) {
            const deltaX = (mouseX - this.dragStart.x) / this.transformer.zoom;
            const deltaY = (mouseY - this.dragStart.y) / this.transformer.zoom;

            this.transformer.centerX = this.lastPan.x - deltaX;
            this.transformer.centerY = this.lastPan.y - deltaY;

            // Render will be called by animation loop if enabled, otherwise render now
            if (!this.animationEnabled) {
                this.render();
            }
        } else {
            // Check if hovering over a planet
            const planet = this.getPlanetAt(mouseX, mouseY);
            this.canvas.style.cursor = planet ? 'pointer' : 'grab';
        }
    }

    onMouseUp(e) {
        // If it wasn't a drag (no significant movement), treat it as a click
        if (!this.hasMoved) {
            // Click to select planet
            const rect = this.canvas.getBoundingClientRect();
            const mouseX = e.clientX - rect.left;
            const mouseY = e.clientY - rect.top;
            const planet = this.getPlanetAt(mouseX, mouseY);
            if (planet) {
                this.selectPlanet(planet.id);
            } else {
                // Only deselect if clicking on empty space (not when leaving canvas)
                this.selectPlanet(null);
            }
        }
        this.isDragging = false;
        this.hasMoved = false;
        this.dragStart = null;
        this.canvas.style.cursor = 'grab';
    }

    onMouseLeave(e) {
        // When leaving canvas, just clean up drag state without deselecting planet
        this.isDragging = false;
        this.hasMoved = false;
        this.dragStart = null;
        this.canvas.style.cursor = 'grab';
    }

    onWheel(e) {
        e.preventDefault();
        
        // Cancel any ongoing zoom animation when user zooms with wheel
        if (this.isZoomAnimating && this.zoomAnimationRequestId) {
            cancelAnimationFrame(this.zoomAnimationRequestId);
            this.isZoomAnimating = false;
            this.zoomAnimationRequestId = null;
        }
        
        const rect = this.canvas.getBoundingClientRect();
        const mouseX = e.clientX - rect.left;
        const mouseY = e.clientY - rect.top;

        const worldPos = this.transformer.screenToWorld(mouseX, mouseY);
        const zoomFactor = e.deltaY > 0 ? 1 - DEFAULT_CONFIG.wheelZoomFactor : 1 + DEFAULT_CONFIG.wheelZoomFactor;

        const newZoom = Math.max(
            DEFAULT_CONFIG.minZoom,
            Math.min(DEFAULT_CONFIG.maxZoom, this.transformer.zoom * zoomFactor)
        );

        this.transformer.zoom = newZoom;

        // Adjust center to zoom towards mouse position
        const newWorldPos = this.transformer.screenToWorld(mouseX, mouseY);
        this.transformer.centerX += worldPos.x - newWorldPos.x;
        this.transformer.centerY += worldPos.y - newWorldPos.y;

        this.updateZoomDisplay();
        // Render will be called by animation loop if enabled, otherwise render now
        if (!this.animationEnabled) {
            this.render();
        }
    }

    onDoubleClick(e) {
        const rect = this.canvas.getBoundingClientRect();
        const mouseX = e.clientX - rect.left;
        const mouseY = e.clientY - rect.top;
        const planet = this.getPlanetAt(mouseX, mouseY);

        if (planet) {
            this.zoomToPlanet(planet.id);
        }
    }

    onTouchStart(e) {
        if (e.touches.length === 1) {
            const touch = e.touches[0];
            const rect = this.canvas.getBoundingClientRect();
            this.dragStart = {
                x: touch.clientX - rect.left,
                y: touch.clientY - rect.top,
            };
            this.lastPan = {
                x: this.transformer.centerX,
                y: this.transformer.centerY,
            };
            this.isDragging = true;
        }
    }

    onTouchMove(e) {
        if (this.isDragging && e.touches.length === 1) {
            e.preventDefault();
            const touch = e.touches[0];
            const rect = this.canvas.getBoundingClientRect();
            const mouseX = touch.clientX - rect.left;
            const mouseY = touch.clientY - rect.top;

            const deltaX = (mouseX - this.dragStart.x) / this.transformer.zoom;
            const deltaY = (mouseY - this.dragStart.y) / this.transformer.zoom;

            this.transformer.centerX = this.lastPan.x - deltaX;
            this.transformer.centerY = this.lastPan.y - deltaY;

            // Render will be called by animation loop if enabled, otherwise render now
            if (!this.animationEnabled) {
                this.render();
            }
        }
    }

    onTouchEnd(e) {
        this.isDragging = false;
    }

    getPlanetAt(screenX, screenY) {
        // Calculate current animation time for accurate planet position
        const animationTime = this.animationEnabled && this.animationElapsedTime !== undefined
            ? this.animationElapsedTime
            : (this.animationEnabled ? (performance.now() - this.animationStartTime) / 1000 : 0);
        const animationSpeed = this.animationEnabled ? this.animationSpeed : 0;

        for (const planet of this.planets) {
            if (planet.orbital_distance === null || planet.orbital_angle === null) {
                continue;
            }

            const pos2d = orbitalTo2D(planet.orbital_distance, planet.orbital_angle, animationTime, animationSpeed);
            const screen = this.transformer.worldToScreen(pos2d.x, pos2d.y);

            // Calculate planet size to adjust click radius
            const planetSize = this.renderer.getPlanetSize(planet.size);
            const clickRadius = Math.max(DEFAULT_CONFIG.clickRadius, planetSize + 5);

            const distance = Math.sqrt(
                Math.pow(screenX - screen.x, 2) + Math.pow(screenY - screen.y, 2)
            );

            if (distance <= clickRadius) {
                return planet;
            }
        }
        return null;
    }

    selectPlanet(planetId) {
        this.selectedPlanetId = planetId;
        this.updatePlanetInfo();
        // Render will be called by animation loop if enabled, otherwise render now
        if (!this.animationEnabled) {
            this.render();
        }
    }

    zoomToPlanet(planetId) {
        const planet = this.planets.find(p => p.id === planetId);
        if (!planet || planet.orbital_distance === null) {
            return;
        }

        // Use current animation position for zoom target
        const animationTime = this.animationEnabled && this.animationElapsedTime !== undefined
            ? this.animationElapsedTime
            : (this.animationEnabled ? (performance.now() - this.animationStartTime) / 1000 : 0);
        const animationSpeed = this.animationEnabled ? this.animationSpeed : 0;
        const pos2d = orbitalTo2D(planet.orbital_distance, planet.orbital_angle, animationTime, animationSpeed);
        
        // Calculate target zoom using a consistent approach:
        // Show the planet with a standard viewing area around it
        // The viewing area should be proportional to the planet's orbital distance
        // but with a minimum and maximum size for consistency
        
        // Target viewing radius: show area around planet (2x orbital distance, with min/max bounds)
        const minViewRadius = 2.0; // Minimum 2 AU radius around planet
        const maxViewRadius = 20.0; // Maximum 20 AU radius around planet
        const viewRadius = Math.max(minViewRadius, Math.min(maxViewRadius, planet.orbital_distance * 2));
        
        // Calculate zoom to fit the viewing area with padding
        const padding = DEFAULT_CONFIG.padding;
        const availableWidth = this.canvas.width * (1 - padding * 2);
        const availableHeight = this.canvas.height * (1 - padding * 2);
        
        // Diameter of viewing area
        const viewDiameter = viewRadius * 2;
        
        const scaleX = availableWidth / viewDiameter;
        const scaleY = availableHeight / viewDiameter;
        const targetZoom = Math.min(scaleX, scaleY);
        
        // Clamp to valid zoom range
        const targetZoomClamped = Math.max(
            DEFAULT_CONFIG.minZoom,
            Math.min(DEFAULT_CONFIG.maxZoom, targetZoom)
        );

        // Store initial values for animation
        const startCenterX = this.transformer.centerX;
        const startCenterY = this.transformer.centerY;
        const startZoom = this.transformer.zoom;
        
        const targetCenterX = pos2d.x;
        const targetCenterY = pos2d.y;

        // Select the planet immediately
        this.selectPlanet(planetId);

        // Animate to target view
        this._animateZoom(startCenterX, startCenterY, startZoom, targetCenterX, targetCenterY, targetZoomClamped);
    }

    _easeInOutCubic(t) {
        // Easing function for smooth animation: ease-in-out cubic
        return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
    }

    _animateZoom(startX, startY, startZoom, targetX, targetY, targetZoom) {
        // Cancel any existing zoom animation
        if (this.zoomAnimationRequestId) {
            cancelAnimationFrame(this.zoomAnimationRequestId);
        }

        if (this.isZoomAnimating) {
            return;
        }

        this.isZoomAnimating = true;
        const startTime = performance.now();
        const duration = DEFAULT_CONFIG.zoomAnimationDuration || 600; // Fallback to 600ms if not defined

        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Apply easing function
            const easedProgress = this._easeInOutCubic(progress);

            // Interpolate values
            this.transformer.centerX = startX + (targetX - startX) * easedProgress;
            this.transformer.centerY = startY + (targetY - startY) * easedProgress;
            this.transformer.zoom = startZoom + (targetZoom - startZoom) * easedProgress;

            this.updateZoomDisplay();
            
            // Always render during zoom animation to ensure smooth transition
            // (even if orbit animation is enabled, we need to render the zoom changes)
            this.render();

            if (progress < 1) {
                this.zoomAnimationRequestId = requestAnimationFrame(animate);
            } else {
                // Animation complete
                this.isZoomAnimating = false;
                this.zoomAnimationRequestId = null;
                // Ensure final values are exact
                this.transformer.centerX = targetX;
                this.transformer.centerY = targetY;
                this.transformer.zoom = targetZoom;
                this.updateZoomDisplay();
                // Final render
                this.render();
            }
        };

        this.zoomAnimationRequestId = requestAnimationFrame(animate);
    }

    updatePlanetInfo() {
        const infoPanel = document.getElementById('planet-info');
        if (!infoPanel) {
            return;
        }

        if (!this.selectedPlanetId) {
            infoPanel.classList.add('hidden');
            return;
        }

        const planet = this.planets.find(p => p.id === this.selectedPlanetId);
        if (!planet) {
            infoPanel.classList.add('hidden');
            return;
        }

        infoPanel.classList.remove('hidden');

        document.getElementById('planet-name').textContent = planet.name.toUpperCase();
        document.getElementById('planet-type').textContent = planet.type || 'Unknown';
        document.getElementById('planet-size').textContent = planet.size || 'Unknown';
        document.getElementById('planet-orbital-distance').textContent =
            planet.orbital_distance !== null ? `${planet.orbital_distance.toFixed(2)} AU` : 'N/A';
        document.getElementById('planet-orbital-angle').textContent =
            planet.orbital_angle !== null ? `${planet.orbital_angle.toFixed(2)}°` : 'N/A';
        document.getElementById('planet-has-image').textContent = planet.has_image ? 'Yes' : 'No';
        document.getElementById('planet-has-video').textContent = planet.has_video ? 'Yes' : 'No';
    }

    zoomIn() {
        // Cancel any ongoing zoom animation when user zooms with button
        if (this.isZoomAnimating && this.zoomAnimationRequestId) {
            cancelAnimationFrame(this.zoomAnimationRequestId);
            this.isZoomAnimating = false;
            this.zoomAnimationRequestId = null;
        }
        
        const newZoom = Math.min(
            DEFAULT_CONFIG.maxZoom,
            this.transformer.zoom * DEFAULT_CONFIG.zoomFactor
        );
        this.transformer.zoom = newZoom;
        this.updateZoomDisplay();
        // Render will be called by animation loop if enabled, otherwise render now
        if (!this.animationEnabled) {
            this.render();
        }
    }

    zoomOut() {
        // Cancel any ongoing zoom animation when user zooms with button
        if (this.isZoomAnimating && this.zoomAnimationRequestId) {
            cancelAnimationFrame(this.zoomAnimationRequestId);
            this.isZoomAnimating = false;
            this.zoomAnimationRequestId = null;
        }
        
        const newZoom = Math.max(
            DEFAULT_CONFIG.minZoom,
            this.transformer.zoom / DEFAULT_CONFIG.zoomFactor
        );
        this.transformer.zoom = newZoom;
        this.updateZoomDisplay();
        // Render will be called by animation loop if enabled, otherwise render now
        if (!this.animationEnabled) {
            this.render();
        }
    }

    resetView() {
        // Cancel any ongoing zoom animation when user resets view
        if (this.isZoomAnimating && this.zoomAnimationRequestId) {
            cancelAnimationFrame(this.zoomAnimationRequestId);
            this.isZoomAnimating = false;
            this.zoomAnimationRequestId = null;
        }
        
        // Ensure canvas is properly sized before recalculating
        this.resize();
        this.transformer.calculateInitialView(this.planets);
        this.transformer.centerX = 0;
        this.transformer.centerY = 0;
        this.updateZoomDisplay();
        // Render will be called by animation loop if enabled, otherwise render now
        if (!this.animationEnabled) {
            this.render();
        }
    }

    toggleOrbits() {
        this.showOrbits = document.getElementById('show-orbits').checked;
        // Render will be called by animation loop if enabled, otherwise render now
        if (!this.animationEnabled) {
            this.render();
        }
    }

    toggleLabels() {
        this.showLabels = document.getElementById('show-labels').checked;
        // Render will be called by animation loop if enabled, otherwise render now
        if (!this.animationEnabled) {
            this.render();
        }
    }

    updateZoomDisplay() {
        const zoomDisplay = document.getElementById('zoom-level');
        if (zoomDisplay) {
            const relativeZoom = (this.transformer.zoom / this.transformer.initialZoom) * 100;
            zoomDisplay.textContent = relativeZoom.toFixed(1) + '%';
        }
    }

    startAnimation() {
        if (this.animationFrameId !== null) {
            return; // Animation already running
        }

        this.lastFrameTime = performance.now();

        const animate = (currentTime) => {
            if (!this.animationEnabled) {
                this.animationFrameId = null;
                return;
            }

            // Calculate delta time for smooth animation
            const deltaTime = (currentTime - this.lastFrameTime) / 1000; // Convert to seconds
            this.lastFrameTime = currentTime;

            // Cap delta time to prevent large jumps
            const cappedDeltaTime = Math.min(deltaTime, 0.1); // Max 100ms per frame

            // Update animation time
            this.animationElapsedTime = (currentTime - this.animationStartTime) / 1000;

            this.render();
            this.animationFrameId = requestAnimationFrame(animate);
        };

        this.animationFrameId = requestAnimationFrame(animate);
    }

    stopAnimation() {
        if (this.animationFrameId !== null) {
            cancelAnimationFrame(this.animationFrameId);
            this.animationFrameId = null;
        }
    }

    toggleAnimation() {
        this.animationEnabled = !this.animationEnabled;
        
        // Sync checkbox state
        const checkbox = document.getElementById('show-animation');
        if (checkbox) {
            checkbox.checked = this.animationEnabled;
        }
        
        if (this.animationEnabled) {
            this.animationStartTime = performance.now();
            this.lastFrameTime = performance.now();
            this.animationElapsedTime = 0;
            this.startAnimation();
        } else {
            this.stopAnimation();
            // Render once more to show static state
            this.render();
        }
    }

    render() {
        // Clear canvas
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

        // Calculate animation time using performance.now() for smoother animation
        const animationTime = this.animationEnabled && this.animationElapsedTime !== undefined
            ? this.animationElapsedTime
            : (this.animationEnabled ? (performance.now() - this.animationStartTime) / 1000 : 0);
        const animationSpeed = this.animationEnabled ? this.animationSpeed : 0;

        // Draw grid (before other elements so it appears behind)
        this.gridRenderer.drawGrid();

        // Draw orbits
        this.renderer.drawOrbits(this.planets, this.showOrbits);

        // Draw star
        this.renderer.drawStar(this.starSystem.star_type);

        // Draw planets (with animation)
        this.renderer.drawPlanets(this.planets, this.selectedPlanetId, this.showLabels, animationTime, animationSpeed);

        // Draw scale (on top of everything)
        this.scaleRenderer.drawScale();
    }
}

// Export for global access
window.SystemMap = SystemMap;

// Note: Initialization is handled in the Blade template to ensure
// data is available before creating the map instance

