/**
 * Map Utilities - Shared utility functions for maps
 */

import { DISTANCE_CONSTANTS } from './map-constants.js';

/**
 * Format distance in appropriate units
 */
export function formatDistance(distanceAU) {
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

/**
 * Find nearby systems/planets within a certain distance
 */
export function findNearby(items, selectedItem, maxCount = 5, distanceCalculator) {
    const distances = items
        .filter(item => item.id !== selectedItem.id)
        .map(otherItem => ({
            item: otherItem,
            distance: distanceCalculator(selectedItem, otherItem)
        }))
        .sort((a, b) => a.distance - b.distance)
        .slice(0, maxCount);

    return distances;
}

/**
 * Normalize discovered status to boolean
 */
export function isDiscovered(item) {
    return item.discovered === true || item.discovered === 'true' || item.discovered === 1;
}

/**
 * Convert orbital coordinates to 2D position
 */
export function orbitalTo2D(orbitalDistance, orbitalAngle, animationTime = 0, animationSpeed = 0) {
    if (orbitalDistance === null || orbitalAngle === null) {
        return { x: 0, y: 0 };
    }
    
    // Calculate angle with animation (counter-clockwise rotation)
    // Closer planets orbit faster (simplified Kepler's law)
    const speedMultiplier = 1 / Math.sqrt(orbitalDistance);
    const currentAngle = orbitalAngle + (animationTime * animationSpeed * speedMultiplier);
    const angleRad = (currentAngle * Math.PI) / 180;
    
    return {
        x: orbitalDistance * Math.cos(angleRad),
        y: orbitalDistance * Math.sin(angleRad),
    };
}

