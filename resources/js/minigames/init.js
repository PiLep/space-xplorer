/**
 * Initialize Scanning Minigame
 * This file is compiled by Vite and can be imported in Blade views
 */

import { ScanningMinigame } from './scanning.js';

/**
 * Check if we're in development mode
 * @returns {boolean} True if in development mode
 */
function isDevelopmentMode() {
  // Check for Vite dev mode (import.meta.env.DEV)
  if (typeof import.meta !== 'undefined' && import.meta.env && import.meta.env.DEV) {
    return true;
  }
  // Check for localhost or development hostname
  if (typeof window !== 'undefined' && window.location) {
    const hostname = window.location.hostname;
    return hostname === 'localhost' || hostname === '127.0.0.1' || hostname.startsWith('192.168.');
  }
  return false;
}

/**
 * Expose game instance globally for debugging (dev only)
 * @param {ScanningMinigame} game - Game instance to expose
 */
function exposeGameForDebugging(game) {
  if (isDevelopmentMode()) {
    window.minigame = game;
  }
}

/**
 * Initialize the scanning minigame in a container
 * @param {HTMLElement} container - Container element for the game
 * @param {Object} options - Configuration options
 */
export function initScanningMinigame(container, options = {}) {
    const game = new ScanningMinigame({
        container: container,
        config: {
            signalCount: options.signalCount || 8,
            signalDurationMin: options.signalDurationMin || 4000,
            signalDurationMax: options.signalDurationMax || 8000,
            totalDuration: options.totalDuration || 60000,
            radarSize: options.radarSize || 700,
            radiusMin: options.radiusMin || 210,
            radiusMax: options.radiusMax || 280,
        },
        onComplete: options.onComplete || (() => {}),
        onProgress: options.onProgress || (() => {})
    });
    
    // Make game available globally for debugging (dev only)
    exposeGameForDebugging(game);
    
    return game;
}

// Track if initialization is in progress to prevent multiple instances
let initializationInProgress = false;
let initializedGame = null;

// Auto-initialize if container exists
function tryInitialize() {
    // Prevent multiple simultaneous initializations
    if (initializationInProgress) {
        return;
    }
    
    const container = document.getElementById('minigame-container');
    if (!container) {
        return;
    }
    
    // If we already have a game instance and it's still valid, reuse it
    const existingGame = isDevelopmentMode() ? window.minigame : initializedGame;
    if (initializedGame && existingGame === initializedGame) {
        // Check if the game is still in a valid state
        // state is now a GameState object, use isDestroyed() method
        if (!initializedGame.state.isDestroyed() && initializedGame.container === container) {
            return;
        }
    }
    
    initializationInProgress = true;
    
    // Destroy existing game if it exists
    if (existingGame && typeof existingGame.destroy === 'function') {
        try {
            existingGame.destroy();
        } catch (e) {
            // Log error but don't break initialization
            if (isDevelopmentMode() && typeof console !== 'undefined' && console.error) {
                console.error('[ScanningMinigame] Error destroying existing game:', e);
            }
        }
    }
    
    // Clear global reference if in dev mode
    if (isDevelopmentMode()) {
        window.minigame = null;
    }
    initializedGame = null;
    
    // Check if container is empty or needs initialization
    if (!container.querySelector('.scanning-minigame__container')) {
        const game = initScanningMinigame(container);
        initializedGame = game;
    }
    
    initializationInProgress = false;
}

// Try on DOMContentLoaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', tryInitialize);
} else {
    // DOM already loaded
    tryInitialize();
}

// Also try after a short delay (for Livewire navigate) - but only once
let delayedInitDone = false;
setTimeout(() => {
    if (!delayedInitDone) {
        tryInitialize();
        delayedInitDone = true;
    }
}, 100);

// Listen for Livewire navigation events
document.addEventListener('livewire:navigated', () => {
    setTimeout(() => {
        initializedGame = null;
        if (isDevelopmentMode()) {
            window.minigame = null;
        }
        tryInitialize();
    }, 100);
});

