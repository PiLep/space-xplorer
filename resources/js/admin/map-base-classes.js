/**
 * Map Base Classes - Shared base classes for map components
 */

import { DEFAULT_MAP_CONFIG, DISTANCE_CONSTANTS } from './map-constants.js';

/**
 * Base ViewTransformer class
 */
export class BaseViewTransformer {
    constructor(canvas, config = DEFAULT_MAP_CONFIG) {
        this.canvas = canvas;
        this.config = config;
        this.zoom = config.initialZoom;
        this.initialZoom = config.initialZoom;
        this.centerX = 0;
        this.centerY = 0;
        this.screenCache = new Map();
    }

    invalidateCache() {
        this.screenCache.clear();
    }

    worldToScreen(worldX, worldY) {
        if (!this.canvas || this.canvas.width === 0 || this.canvas.height === 0) {
            return { x: 0, y: 0 };
        }
        
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
        if (!this.canvas || this.canvas.width === 0 || this.canvas.height === 0 || this.zoom === 0) {
            return { x: 0, y: 0 };
        }
        
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
}

/**
 * Base GridRenderer class
 */
export class BaseGridRenderer {
    constructor(ctx, transformer, config = DEFAULT_MAP_CONFIG) {
        this.ctx = ctx;
        this.transformer = transformer;
        this.config = config;
    }

    drawGrid() {
        const { canvas, zoom, centerX, centerY } = this.transformer;
        this.ctx.strokeStyle = 'rgba(100, 100, 150, 0.2)';
        this.ctx.lineWidth = 1;

        const worldWidth = canvas.width / zoom;
        const worldHeight = canvas.height / zoom;
        const targetGridLines = this.config.gridTargetLines;

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
 * Base ScaleRenderer class
 */
export class BaseScaleRenderer {
    constructor(ctx, canvas, transformer, config = DEFAULT_MAP_CONFIG) {
        this.ctx = ctx;
        this.canvas = canvas;
        this.transformer = transformer;
        this.config = config;
    }

    drawScale() {
        const { canvas, zoom } = this.transformer;
        const { AU_PER_LIGHT_YEAR, AU_PER_PARSEC, UNITS_PER_AU } = DISTANCE_CONSTANTS;

        const targetPixels = canvas.width * this.config.scaleTargetPixels;
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

