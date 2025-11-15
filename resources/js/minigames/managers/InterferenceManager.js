/**
 * Interference Manager
 * Handles all visual interference effects
 */

import { INTERFERENCE } from '../constants.js';

export class InterferenceManager {
  constructor() {
    this.interference = {
      // Continuous effects (always active)
      continuousScanlines: true,
      continuousNoise: true,
      continuousGrain: true,
      vignette: true,
      chromaticAberration: true,
      crtEffect: true,
      
      // Progressive effects (increase over time)
      baseDistortion: 0,
      progressiveDistortion: 0,
      glitchFrequency: 0,
      staticIntensity: 0,
      flickerIntensity: 0,
      
      // Acute effects (random spikes)
      glitch: false,
      glitchIntensity: 0,
      static: false,
      staticParticles: [],
      scanline: false,
      distortion: 0,
      flicker: false,
      noise: false,
      chromaticShift: 0,
      screenShake: 0,
    };
    
    this.manualOverrides = {
      baseDistortion: false,
      progressiveDistortion: false,
      glitchFrequency: false,
      staticIntensity: false,
      flickerIntensity: false,
    };
    
    this.progressiveIntensityMultiplier = 1.0;
    this.interferenceIntervals = [];
    this.updateProgressiveEffectsFn = null;
    this.progressiveEffectsFrameId = null;
  }

  /**
   * Initialize static particles
   */
  initStaticParticles(radarSize) {
    this.interference.staticParticles = [];
    for (let i = 0; i < INTERFERENCE.STATIC_PARTICLES_COUNT; i++) {
      this.interference.staticParticles.push({
        x: Math.random() * radarSize,
        y: Math.random() * radarSize,
        size: Math.random() * 2 + 1,
        opacity: Math.random() * 0.5 + 0.2,
        speed: Math.random() * 2 + 0.5,
        life: Math.random() * 1000 + 500,
        maxLife: Math.random() * 1000 + 500,
      });
    }
  }

  /**
   * Start interference effects
   */
  start(startTime, totalDuration) {
    // Reset interference state
    const preservedValues = {
      continuousScanlines: this.interference.continuousScanlines,
      continuousNoise: this.interference.continuousNoise,
      continuousGrain: this.interference.continuousGrain,
      vignette: this.interference.vignette,
      chromaticAberration: this.interference.chromaticAberration,
      crtEffect: this.interference.crtEffect,
    };
    
    // Preserve manual override values
    if (this.manualOverrides.baseDistortion) {
      preservedValues.baseDistortion = this.interference.baseDistortion;
    }
    if (this.manualOverrides.progressiveDistortion) {
      preservedValues.progressiveDistortion = this.interference.progressiveDistortion;
    }
    if (this.manualOverrides.glitchFrequency) {
      preservedValues.glitchFrequency = this.interference.glitchFrequency;
    }
    if (this.manualOverrides.staticIntensity) {
      preservedValues.staticIntensity = this.interference.staticIntensity;
    }
    if (this.manualOverrides.flickerIntensity) {
      preservedValues.flickerIntensity = this.interference.flickerIntensity;
    }
    
    // Reset acute effects
    this.interference.glitch = false;
    this.interference.glitchIntensity = 0;
    this.interference.static = false;
    this.interference.scanline = false;
    this.interference.distortion = 0;
    this.interference.flicker = false;
    this.interference.noise = false;
    this.interference.chromaticShift = 0;
    this.interference.screenShake = 0;
    
    // Restore preserved values
    Object.assign(this.interference, preservedValues);
    
    // Start progressive effects update loop
    this.startProgressiveEffects(startTime, totalDuration);
    
    // Start random acute effects
    this.startAcuteEffects();
  }

  /**
   * Start progressive effects update loop
   */
  startProgressiveEffects(startTime, totalDuration) {
    this.updateProgressiveEffectsFn = () => {
      const elapsed = Date.now() - startTime;
      const progress = Math.min(elapsed / totalDuration, 1);
      const multiplier = this.progressiveIntensityMultiplier || 1.0;
      
      // Update progressive effects (only if not manually overridden)
      if (!this.manualOverrides.progressiveDistortion) {
        this.interference.progressiveDistortion = progress * 3 * multiplier;
      }
      if (!this.manualOverrides.baseDistortion) {
        this.interference.baseDistortion = progress * 1.5 * multiplier;
      }
      if (!this.manualOverrides.glitchFrequency) {
        this.interference.glitchFrequency = progress * 0.5 * multiplier;
      }
      if (!this.manualOverrides.staticIntensity) {
        this.interference.staticIntensity = progress * 0.4 * multiplier;
      }
      if (!this.manualOverrides.flickerIntensity) {
        this.interference.flickerIntensity = progress * 0.3 * multiplier;
      }
      
      this.progressiveEffectsFrameId = requestAnimationFrame(this.updateProgressiveEffectsFn);
    };
    
    this.updateProgressiveEffectsFn();
  }

  /**
   * Start acute effects (random spikes)
   */
  startAcuteEffects() {
    // Random glitch effects
    const glitchInterval = setInterval(() => {
      const baseChance = 0.2;
      const progressiveChance = this.interference.glitchFrequency;
      if (Math.random() < (baseChance + progressiveChance)) {
        this.interference.glitch = true;
        this.interference.glitchIntensity = Math.random() * 3 + 1 + (this.interference.progressiveDistortion * 0.5);
        setTimeout(() => {
          this.interference.glitch = false;
        }, 50 + Math.random() * 100);
      }
    }, 2000);
    
    // Random static noise
    const staticInterval = setInterval(() => {
      const baseChance = 0.3;
      const progressiveChance = this.interference.staticIntensity;
      if (Math.random() < (baseChance + progressiveChance)) {
        this.interference.static = true;
        setTimeout(() => {
          this.interference.static = false;
        }, 100 + Math.random() * 200);
      }
    }, 1500);
    
    // Random scanline interference
    const scanlineInterval = setInterval(() => {
      const baseChance = 0.15;
      const progressiveChance = this.interference.glitchFrequency * 0.5;
      if (Math.random() < (baseChance + progressiveChance)) {
        this.interference.scanline = true;
        setTimeout(() => {
          this.interference.scanline = false;
        }, 80 + Math.random() * 120);
      }
    }, 3000);
    
    // Random distortion spikes
    const distortionInterval = setInterval(() => {
      const baseChance = 0.15;
      const progressiveChance = this.interference.glitchFrequency * 0.3;
      if (Math.random() < (baseChance + progressiveChance)) {
        this.interference.distortion = Math.random() * 5 + 2 + this.interference.progressiveDistortion;
        setTimeout(() => {
          this.interference.distortion = 0;
        }, 150 + Math.random() * 200);
      }
    }, 2500);
    
    // Random flicker
    const flickerInterval = setInterval(() => {
      const baseChance = 0.1;
      const progressiveChance = this.interference.flickerIntensity;
      if (Math.random() < (baseChance + progressiveChance)) {
        this.interference.flicker = true;
        setTimeout(() => {
          this.interference.flicker = false;
        }, 30 + Math.random() * 50);
      }
    }, 4000);
    
    // Random chromatic aberration spikes
    const chromaticInterval = setInterval(() => {
      const baseChance = 0.1;
      const progressiveChance = this.interference.glitchFrequency * 0.4;
      if (Math.random() < (baseChance + progressiveChance)) {
        this.interference.chromaticShift = Math.random() * 3 + 1 + this.interference.progressiveDistortion;
        setTimeout(() => {
          this.interference.chromaticShift = 0;
        }, 100 + Math.random() * 150);
      }
    }, 3500);
    
    // Random screen shake
    const shakeInterval = setInterval(() => {
      const baseChance = 0.08;
      const progressiveChance = this.interference.glitchFrequency * 0.3;
      if (Math.random() < (baseChance + progressiveChance)) {
        this.interference.screenShake = Math.random() * 4 + 2 + this.interference.progressiveDistortion;
        setTimeout(() => {
          this.interference.screenShake = 0;
        }, 80 + Math.random() * 120);
      }
    }, 5000);
    
    // Store intervals for cleanup
    this.interferenceIntervals = [
      glitchInterval,
      staticInterval,
      scanlineInterval,
      distortionInterval,
      flickerInterval,
      chromaticInterval,
      shakeInterval
    ];
  }

  /**
   * Update progressive effects (called from update loop)
   */
  updateProgressiveEffects(elapsed, totalDuration) {
    // This is handled by the requestAnimationFrame loop
    // But can be called manually if needed
  }

  /**
   * Stop interference effects
   */
  stop() {
    // Clear intervals
    if (this.interferenceIntervals) {
      this.interferenceIntervals.forEach(interval => clearInterval(interval));
      this.interferenceIntervals = [];
    }
    
    // Cancel animation frame
    if (this.progressiveEffectsFrameId) {
      cancelAnimationFrame(this.progressiveEffectsFrameId);
      this.progressiveEffectsFrameId = null;
    }
  }

  /**
   * Get current interference state
   */
  getInterferenceState() {
    return { ...this.interference };
  }

  /**
   * Toggle a continuous effect
   */
  toggleEffect(effectName, enabled) {
    if (this.interference.hasOwnProperty(effectName)) {
      this.interference[effectName] = enabled;
      return true;
    }
    return false;
  }

  /**
   * Set manual override for progressive effect
   */
  setManualOverride(effectName, value) {
    if (this.manualOverrides.hasOwnProperty(effectName)) {
      this.interference[effectName] = value;
      this.manualOverrides[effectName] = true;
      return true;
    }
    return false;
  }

  /**
   * Reset manual overrides
   */
  resetManualOverrides() {
    this.manualOverrides = {
      baseDistortion: false,
      progressiveDistortion: false,
      glitchFrequency: false,
      staticIntensity: false,
      flickerIntensity: false,
    };
  }

  /**
   * Set progressive intensity multiplier
   */
  setProgressiveIntensity(multiplier) {
    this.progressiveIntensityMultiplier = multiplier || 1.0;
  }

  /**
   * Destroy interference manager
   */
  destroy() {
    this.stop();
    this.interference = {};
    this.manualOverrides = {};
  }
}

