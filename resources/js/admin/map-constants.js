/**
 * Map Constants - Shared constants for universe and system maps
 */

export const STAR_COLORS = {
    'yellow_dwarf': '#FFD700',
    'red_dwarf': '#FF6B6B',
    'orange_dwarf': '#FF8C42',
    'red_giant': '#FF4500',
    'blue_giant': '#4169E1',
    'white_dwarf': '#F0F0F0',
};

export const STAR_SIZES = {
    'white_dwarf': 3,
    'red_dwarf': 4,
    'yellow_dwarf': 5,
    'orange_dwarf': 5,
    'red_giant': 7,
    'blue_giant': 9,
};

export const PLANET_COLORS = {
    // Français
    'tellurique': '#9B7A4A',
    'gazeuse': '#C8A2C8',
    'glacée': '#87CEEB',
    'désertique': '#CD853F',
    'océanique': '#4169E1',
    // Anglais
    'terrestrial': '#9B7A4A',
    'gaseous': '#C8A2C8',
    'icy': '#87CEEB',
    'desert': '#CD853F',
    'oceanic': '#4169E1',
};

export const DISTANCE_CONSTANTS = {
    AU_PER_LIGHT_YEAR: 63241.0,
    AU_PER_PARSEC: 206265.0,
    UNITS_PER_AU: 1.0,
};

export const VIEW_PLANES = {
    XY: 'xy',
    XZ: 'xz',
    YZ: 'yz',
};

export const DEFAULT_MAP_CONFIG = {
    initialZoom: 1.0,
    minZoom: 0.00001,
    maxZoom: 10.0,
    zoomFactor: 1.5,
    wheelZoomFactor: 0.05,
    gridTargetLines: 10,
    scaleTargetPixels: 0.15,
    clickRadius: 20,
    padding: 0.1,
    maxZoomOutFactor: 0.5,
    zoomAnimationDuration: 600,
};

export const DEFAULT_SYSTEM_MAP_CONFIG = {
    initialZoom: 1.0,
    minZoom: 0.1,
    maxZoom: 50.0,
    zoomFactor: 1.5,
    wheelZoomFactor: 0.05,
    starSize: 5,
    planetSize: 4,
    selectedPlanetSize: 6,
    clickRadius: 20,
    padding: 0.15,
    gridTargetLines: 10,
    scaleTargetPixels: 0.15,
    orbitAnimationSpeed: 5.0,
    orbitAnimationEnabled: false,
    zoomAnimationDuration: 600, // Duration of zoom animation in milliseconds
};

