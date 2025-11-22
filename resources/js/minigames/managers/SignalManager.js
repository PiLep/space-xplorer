/**
 * Signal Manager
 * Handles signal generation, lifecycle, and locking
 */

import { ZONES, TIMING } from '../constants.js';

export class SignalManager {
  constructor(config) {
    this.config = config;
    this.signals = [];
  }

  /**
   * Generate signals for the game
   */
  generateSignals(startTime, endTime) {
    const centerX = this.config.radarSize / 2;
    const centerY = this.config.radarSize / 2;
    
    // Scan line rotation speed: (Math.PI / 2) radians per second = one rotation per 4 seconds
    const scanRotationSpeed = Math.PI / 2; // radians per second
    const scanRotationPeriod = (2 * Math.PI) / scanRotationSpeed; // 4 seconds per rotation
    
    this.signals = [];
    
    for (let i = 0; i < this.config.signalCount; i++) {
      // Random position on radar (circular)
      const angle = (2 * Math.PI * i) / this.config.signalCount + (Math.random() * 0.5);
      const radius = this.config.radiusMin + Math.random() * (this.config.radiusMax - this.config.radiusMin);
      const x = centerX + radius * Math.cos(angle);
      const y = centerY + radius * Math.sin(angle);
      
      // Random duration
      const duration = this.config.signalDurationMin + 
        Math.random() * (this.config.signalDurationMax - this.config.signalDurationMin);
      
      // Calculate when scan line will pass over this signal's angle
      const normalizedAngle = ((angle % (2 * Math.PI)) + (2 * Math.PI)) % (2 * Math.PI);
      
      // Distribute signals across multiple rotations
      const maxRotations = Math.floor(this.config.totalDuration / (scanRotationPeriod * 1000));
      const rotationNumber = Math.floor(Math.random() * Math.max(1, maxRotations));
      
      // Calculate delay: time for scan line to reach this angle in the chosen rotation
      const delay = ((normalizedAngle + (rotationNumber * 2 * Math.PI)) / scanRotationSpeed) * 1000;
      
      // Ensure signal appears before game ends (with margin for duration)
      const maxDelay = Math.max(1000, this.config.totalDuration - duration - 1000);
      const finalDelay = Math.min(delay, maxDelay);
      
      const signalStartTime = startTime + finalDelay;
      const signalEndTime = Math.min(signalStartTime + duration, endTime);
      
      this.signals.push({
        id: i + 1,
        x: Math.round(x),
        y: Math.round(y),
        startTime: signalStartTime,
        endTime: signalEndTime,
        duration: duration,
        locked: false,
        angle: normalizedAngle,
      });
    }
    
    // Sort signals by start time for better distribution
    this.signals.sort((a, b) => a.startTime - b.startTime);
    
    return this.signals;
  }

  /**
   * Get all signals
   */
  getSignals() {
    return this.signals;
  }

  /**
   * Get active signals at current time
   */
  getActiveSignals(currentTime) {
    return this.signals.filter(signal => {
      return currentTime >= signal.startTime && currentTime <= signal.endTime;
    });
  }

  /**
   * Get locked signals
   */
  getLockedSignals() {
    return this.signals.filter(signal => signal.locked);
  }

  /**
   * Get signal by ID
   */
  getSignalById(id) {
    return this.signals.find(signal => signal.id === id);
  }

  /**
   * Check if signal is in optimal zone
   */
  isSignalOptimal(signal, currentTime) {
    if (currentTime < signal.startTime || currentTime > signal.endTime) {
      return false;
    }
    
    const progress = (currentTime - signal.startTime) / signal.duration;
    return progress >= ZONES.OPTIMAL_MIN && progress <= ZONES.OPTIMAL_MAX;
  }

  /**
   * Get signal progress (0-1)
   */
  getSignalProgress(signal, currentTime) {
    if (currentTime < signal.startTime) {
      return 0;
    }
    if (currentTime > signal.endTime) {
      return 1;
    }
    return (currentTime - signal.startTime) / signal.duration;
  }

  /**
   * Lock a signal
   */
  lockSignal(signalId, currentTime) {
    const signal = this.getSignalById(signalId);
    if (!signal || signal.locked) {
      return false;
    }
    
    // Check if signal is active (with tolerance)
    const tolerance = TIMING.SIGNAL_TOLERANCE;
    const isActive = currentTime >= (signal.startTime - tolerance) && 
                     currentTime <= (signal.endTime + tolerance);
    
    if (!isActive) {
      return false;
    }
    
    signal.locked = true;
    return true;
  }

  /**
   * Check if all signals are resolved (locked or expired)
   */
  areAllSignalsResolved(currentTime) {
    return this.signals.every(signal => {
      return signal.locked || currentTime > signal.endTime;
    });
  }

  /**
   * Get acquisition rate (0-100)
   */
  getAcquisitionRate() {
    if (this.signals.length === 0) {
      return 0;
    }
    const locked = this.getLockedSignals().length;
    return Math.round((locked / this.signals.length) * 100);
  }

  /**
   * Reset signals
   */
  reset() {
    this.signals = [];
  }
}

