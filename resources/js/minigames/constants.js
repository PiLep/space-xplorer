/**
 * Constants for Scanning Minigame
 * Centralized configuration values
 */

export const COLORS = {
  PRIMARY: '#00ff88',
  SECONDARY: '#00aaff',
  ACCENT: '#ffaa00',
  ERROR: '#ff4444',
  BACKGROUND: '#0a0a0a',
  
  // Signal states
  SIGNAL_LOCKED: '#00ff88',
  SIGNAL_OPTIMAL: '#00aaff',
  SIGNAL_ACTIVE: '#ffaa00',
  SIGNAL_INACTIVE: '#ff4444',
  
  // Glow colors
  GLOW_LOCKED: 'rgba(0, 255, 136, 0.5)',
  GLOW_OPTIMAL: 'rgba(0, 170, 255, 0.4)',
  GLOW_ACTIVE: 'rgba(255, 170, 0, 0.3)',
  GLOW_INACTIVE: 'rgba(255, 68, 68, 0.3)',
  
  // UI colors
  UI_BORDER: 'rgba(0, 255, 136, 0.3)',
  UI_PROGRESS: 'rgba(0, 170, 255, 0.8)',
  UI_SUCCESS: '#00ff88',
  UI_ERROR: '#ff4444',
};

export const SIZES = {
  SIGNAL_MIN: 2,
  SIGNAL_DEFAULT: 8,
  SIGNAL_OPTIMAL: 10,
  SIGNAL_LOCKED: 12,
  CLICK_RADIUS: 20,
};

export const TIMING = {
  LOCK_REQUIRED_DURATION: 400,
  END_ANIMATION_DURATION: 2000,
  SIGNAL_TOLERANCE: 500,
  TERMINAL_TYPING_SPEED: 30,
};

export const ZONES = {
  OPTIMAL_MIN: 0.25,
  OPTIMAL_MAX: 0.75,
  OPTIMAL_CENTER: 0.5,
  INTENSITY_MAX_DISTANCE: 0.25, // Maximum distance from center for intensity calculation
};

export const INTENSITY = {
  MIN: 50,
  MAX: 100,
  BASE_PENALTY: 50, // Penalty applied based on distance from center
  HOLD_BONUS_MAX: 20, // Maximum bonus percentage for holding longer
  HOLD_BONUS_DIVISOR: 10, // Divisor for calculating hold bonus
};

export const ANIMATIONS = {
  SCANLINE_SPEED: 8, // seconds for full cycle
  SCAN_ROTATION_SPEED: Math.PI / 2, // radians per second
  SCAN_ROTATION_PERIOD: 4000, // milliseconds for full rotation
  PULSE_SPEED: 100,
};

export const INTERFERENCE = {
  STATIC_PARTICLES_COUNT: 30,
  NOISE_DOT_COUNT: 15,
  GRAIN_DOT_COUNT: 50,
  SCANLINE_SPACING: 4,
  CRT_SCANLINE_SPACING: 2,
};

export const SCORING = {
  ACQUISITION_RATE_WEIGHT: 0.6, // Weight for acquisition rate in quality score
  AVG_INTENSITY_WEIGHT: 0.4, // Weight for average intensity in quality score
};

export const DEFAULT_CONFIG = {
  signalCount: 8,
  signalDurationMin: 4000,
  signalDurationMax: 8000,
  totalDuration: 60000,
  radarSize: 700,
  radiusMin: 210,
  radiusMax: 280,
};

