/**
 * Scanning Minigame - Refactored Implementation
 * 
 * Entrée: Configuration et callbacks
 * Sortie: Résultat du jeu via callback
 * 
 * Usage:
 * const game = new ScanningMinigame({
 *   container: document.getElementById('game-container'),
 *   config: {
 *     signalCount: 8,
 *     signalDurationMin: 4000,
 *     signalDurationMax: 8000,
 *     totalDuration: 60000,
 *     radarSize: 500
 *   },
 *   onComplete: (result) => {
 *     // Handle game completion
 *   }
 * });
 * game.start();
 */

import { GameState, STATES } from './managers/GameState.js';
import { SignalManager } from './managers/SignalManager.js';
import { UIManager } from './managers/UIManager.js';
import { InterferenceManager } from './managers/InterferenceManager.js';
import { RadarRenderer } from './renderers/RadarRenderer.js';
import { DEFAULT_CONFIG, TIMING, SIZES, ZONES, INTENSITY, SCORING } from './constants.js';

export class ScanningMinigame {
  constructor(options = {}) {
    this.container = options.container;
    this.config = {
      ...DEFAULT_CONFIG,
      ...options.config
    };
    this.onComplete = options.onComplete || (() => {});
    this.onProgress = options.onProgress || (() => {});
    
    // Managers
    this.state = new GameState();
    this.signalManager = new SignalManager(this.config);
    this.uiManager = new UIManager(this.container, this.config);
    this.interferenceManager = new InterferenceManager();
    
    // Game state
    this.startTime = null;
    this.endTime = null;
    this.actions = [];
    this.lockingSignal = null;
    this.lockStartTime = null;
    this.lockProgress = 0;
    this.lockRequiredDuration = TIMING.LOCK_REQUIRED_DURATION;
    this.endAnimationStartTime = null;
    this.endAnimationDuration = TIMING.END_ANIMATION_DURATION;
    this.animationFrameId = null;
    
    // UI elements (set by init)
    this.canvas = null;
    this.canvasWrapper = null;
    this.renderer = null;
    this.ctx = null; // Expose ctx for backward compatibility with admin panel
    
    this.init();
  }
  
  /**
   * Initialize the game
   */
  init() {
    if (!this.container) {
      throw new Error('Container element is required');
    }
    
    const uiElements = this.uiManager.createUI();
    this.canvas = uiElements.canvas;
    this.canvasWrapper = uiElements.canvasWrapper;
    this.ctx = this.canvas ? this.canvas.getContext('2d', { willReadFrequently: true }) : null;
    this.renderer = new RadarRenderer(uiElements.canvas, this.config);
    
    // Setup event handlers
    this.setupEventHandlers();
    
    // Setup start button
    const startButton = this.container.querySelector('.scanning-minigame__start');
    if (startButton) {
      startButton.addEventListener('click', () => {
        this.uiManager.showTerminalSequence(() => this.start());
      });
    }
  }
  
  /**
   * Setup canvas event handlers
   */
  setupEventHandlers() {
    if (!this.canvas) return;
    
    this.canvas.addEventListener('mousedown', (e) => this.handleCanvasMouseDown(e));
    this.canvas.addEventListener('mouseup', (e) => this.handleCanvasMouseUp(e));
    this.canvas.addEventListener('mouseleave', (e) => this.handleCanvasMouseLeave(e));
  }
  
  /**
   * Start the game
   */
  start() {
    if (!this.state.canTransitionTo(STATES.ACTIVE)) {
      return;
    }
    
    this.state.transitionTo(STATES.ACTIVE);
    this.startTime = Date.now();
    this.endTime = this.startTime + this.config.totalDuration;
    this.actions = [];
    
    // Generate signals
    this.signalManager.generateSignals(this.startTime, this.endTime);
    
    // Initialize interference particles
    this.interferenceManager.initStaticParticles(this.config.radarSize);
    
    // Start interference
    this.interferenceManager.start(this.startTime, this.config.totalDuration);
    
    // Start update loop
    this.update();
    
    // Auto-end after duration
    setTimeout(() => {
      if (this.state.isActive()) {
        this.end();
      }
    }, this.config.totalDuration);
  }
  
  /**
   * Update loop
   */
  update() {
    if (!this.state.isActive()) {
      return;
    }
    
    const currentTime = Date.now();
    
    // Check if should end
    if (currentTime >= this.endTime || 
        this.signalManager.areAllSignalsResolved(currentTime)) {
      this.end();
      return;
    }
    
    // Update locking progress
    this.updateLocking(currentTime);
    
    // Update UI
    const elapsed = currentTime - this.startTime;
    const progress = elapsed / this.config.totalDuration;
    this.uiManager.updateProgressBar(progress);
    
    const completion = this.signalManager.getAcquisitionRate();
    this.uiManager.updateIntensityBar(completion);
    
    // Render
    this.renderer.render({
      state: this.state.getState(),
      signals: this.signalManager.getSignals(),
      interference: this.interferenceManager.getInterferenceState(),
      currentTime,
      startTime: this.startTime,
      endAnimationStartTime: this.endAnimationStartTime,
      endAnimationDuration: this.endAnimationDuration,
      lockingSignal: this.lockingSignal,
      lockProgress: this.lockProgress,
    });
    
    // Call progress callback
    this.onProgress({
      elapsed,
      remaining: this.endTime - currentTime,
      signalsLocked: this.signalManager.getLockedSignals().length,
      signalsTotal: this.signalManager.getSignals().length,
    });
    
    // Continue loop
    this.animationFrameId = requestAnimationFrame(() => this.update());
  }
  
  /**
   * Update locking progress
   */
  updateLocking(currentTime) {
    if (!this.lockingSignal || !this.lockStartTime) {
      return;
    }
    
    const lockDuration = currentTime - this.lockStartTime;
    this.lockProgress = Math.min(1, lockDuration / this.lockRequiredDuration);
    
    const signal = this.lockingSignal;
    const progress = this.signalManager.getSignalProgress(signal, currentTime);
    const isOptimal = this.signalManager.isSignalOptimal(signal, currentTime);
    
    // Auto-complete if held long enough and in optimal zone
    if (lockDuration >= this.lockRequiredDuration && isOptimal) {
      this.completeLocking(currentTime);
    } else if (!isOptimal) {
      // Cancel if signal left optimal zone
      this.lockingSignal = null;
      this.lockStartTime = null;
      this.lockProgress = 0;
    }
  }
  
  /**
   * Handle canvas mousedown
   */
  handleCanvasMouseDown(e) {
    if (!this.state.isActive()) {
      return;
    }
    
    const rect = this.canvas.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    const currentTime = Date.now();
    
    // Find clicked signal
    for (const signal of this.signalManager.getSignals()) {
      if (signal.locked) continue;
      
      const renderedPos = this.renderer.getSignalRenderedPosition(
        signal,
        currentTime,
        this.interferenceManager.getInterferenceState(),
        false
      );
      
      const distance = Math.sqrt(
        Math.pow(x - renderedPos.x, 2) + Math.pow(y - renderedPos.y, 2)
      );
      
      if (distance <= SIZES.CLICK_RADIUS) {
        const tolerance = TIMING.SIGNAL_TOLERANCE;
        const isActive = currentTime >= (signal.startTime - tolerance) && 
                         currentTime <= (signal.endTime + tolerance);
        
        if (isActive) {
          this.lockingSignal = signal;
          this.lockStartTime = currentTime;
          this.lockProgress = 0;
          break;
        }
      }
    }
  }
  
  /**
   * Handle canvas mouseup
   */
  handleCanvasMouseUp(e) {
    if (!this.state.isActive() || !this.lockingSignal) {
      return;
    }
    this.completeLocking(Date.now());
  }
  
  /**
   * Handle canvas mouseleave
   */
  handleCanvasMouseLeave(e) {
    if (!this.state.isActive() || !this.lockingSignal) {
      return;
    }
    this.lockingSignal = null;
    this.lockStartTime = null;
    this.lockProgress = 0;
  }
  
  /**
   * Complete or cancel locking process
   */
  completeLocking(currentTime) {
    if (!this.lockingSignal || !this.lockStartTime) {
      return;
    }
    
    const signal = this.lockingSignal;
    const lockDuration = currentTime - this.lockStartTime;
    
    // Check if signal is already locked
    if (signal.locked) {
      this.lockingSignal = null;
      this.lockStartTime = null;
      this.lockProgress = 0;
      return;
    }
    
    // Check if signal is still active
    const tolerance = TIMING.SIGNAL_TOLERANCE;
    const isActive = currentTime >= (signal.startTime - tolerance) && 
                     currentTime <= (signal.endTime + tolerance);
    
    if (!isActive) {
      this.lockingSignal = null;
      this.lockStartTime = null;
      this.lockProgress = 0;
      return;
    }
    
    // Calculate progress
    const progress = this.signalManager.getSignalProgress(signal, currentTime);
    const isOptimal = progress >= ZONES.OPTIMAL_MIN && progress <= ZONES.OPTIMAL_MAX;
    
    // Only lock if held long enough AND in optimal zone
    if (lockDuration >= this.lockRequiredDuration && isOptimal) {
      const intensity = this.calculateSignalIntensity(progress, lockDuration);
      
      // Lock signal
      if (this.signalManager.lockSignal(signal.id, currentTime)) {
        this.recordSuccessfulAction(signal, currentTime, progress, intensity, lockDuration);
      }
    }
    
    // Reset locking state
    this.lockingSignal = null;
    this.lockStartTime = null;
    this.lockProgress = 0;
  }
  
  /**
   * End the game
   */
  end() {
    if (!this.state.canTransitionTo(STATES.ENDED)) {
      return;
    }
    
    this.state.transitionTo(STATES.ENDED);
    this.endTime = Date.now();
    this.endAnimationStartTime = Date.now();
    
    // Stop interference
    this.interferenceManager.stop();
    
    // Cancel animation frame
    if (this.animationFrameId) {
      cancelAnimationFrame(this.animationFrameId);
      this.animationFrameId = null;
    }
    
    // Start end animation
    this.animateEnd();
    
    // Calculate result
    const result = this.calculateResult();
    
    // Show results after animation
    setTimeout(() => {
      this.uiManager.showResults(result, () => this.reset());
      this.onComplete(result);
    }, this.endAnimationDuration);
  }
  
  /**
   * Animate end sequence
   */
  animateEnd() {
    if (!this.state.isEnded()) {
      return;
    }
    
    const currentTime = Date.now();
    const animationElapsed = currentTime - this.endAnimationStartTime;
    const animationProgress = Math.min(1, animationElapsed / this.endAnimationDuration);
    
    // Render with end animation
    this.renderer.render({
      state: this.state.getState(),
      signals: this.signalManager.getSignals(),
      interference: this.interferenceManager.getInterferenceState(),
      currentTime,
      startTime: this.startTime,
      endAnimationStartTime: this.endAnimationStartTime,
      endAnimationDuration: this.endAnimationDuration,
      lockingSignal: null,
      lockProgress: 0,
    });
    
    // Continue animation until complete
    if (animationProgress < 1) {
      this.animationFrameId = requestAnimationFrame(() => this.animateEnd());
    }
  }
  
  /**
   * Calculate signal intensity based on position and hold duration
   * @param {number} progress - Signal progress (0-1)
   * @param {number} lockDuration - Duration signal was held (ms)
   * @returns {number} Calculated intensity (50-100)
   */
  calculateSignalIntensity(progress, lockDuration) {
    // Calculate intensity based on how centered in optimal zone
    const optimalCenter = ZONES.OPTIMAL_CENTER;
    const distanceFromCenter = Math.abs(progress - optimalCenter);
    const maxDistance = ZONES.INTENSITY_MAX_DISTANCE;
    
    let intensity = INTENSITY.MAX - ((distanceFromCenter / maxDistance) * INTENSITY.BASE_PENALTY);
    intensity = Math.max(INTENSITY.MIN, Math.min(INTENSITY.MAX, intensity));
    
    // Bonus for holding longer (up to HOLD_BONUS_MAX% bonus)
    const holdBonus = Math.min(
      INTENSITY.HOLD_BONUS_MAX,
      (lockDuration - this.lockRequiredDuration) / INTENSITY.HOLD_BONUS_DIVISOR
    );
    intensity = Math.min(INTENSITY.MAX, intensity + holdBonus);
    
    return Math.round(intensity * 10) / 10;
  }
  
  /**
   * Record a successful signal lock action
   * @param {Object} signal - The locked signal
   * @param {number} currentTime - Current timestamp
   * @param {number} progress - Signal progress (0-1)
   * @param {number} intensity - Calculated intensity
   * @param {number} lockDuration - Duration signal was held (ms)
   */
  recordSuccessfulAction(signal, currentTime, progress, intensity, lockDuration) {
    this.actions.push({
      signalId: signal.id,
      clickedAt: currentTime,
      position: { x: signal.x, y: signal.y },
      success: true,
      intensity: intensity,
      progress: Math.round(progress * 100) / 100,
      lockDuration: lockDuration,
    });
  }
  
  /**
   * Calculate average intensity from successful actions
   * @returns {number} Average intensity (0-100)
   */
  calculateAverageIntensity() {
    const successfulActions = this.actions.filter(a => a.success);
    if (successfulActions.length === 0) {
      return 0;
    }
    
    const totalIntensity = successfulActions.reduce((sum, a) => sum + (a.intensity || 0), 0);
    return totalIntensity / successfulActions.length;
  }
  
  /**
   * Calculate result
   */
  calculateResult() {
    const locked = this.signalManager.getLockedSignals().length;
    const total = this.signalManager.getSignals().length;
    const acquisitionRate = this.signalManager.getAcquisitionRate();
    const avgIntensity = this.calculateAverageIntensity();
    
    const qualityScore = Math.round(
      (acquisitionRate * SCORING.ACQUISITION_RATE_WEIGHT) +
      (avgIntensity * SCORING.AVG_INTENSITY_WEIGHT)
    );
    
    return {
      score: qualityScore,
      acquisitionRate: acquisitionRate,
      signalsGenerated: total,
      signalsLocked: locked,
      signalsAcquired: locked,
      actions: this.actions,
      startTime: this.startTime,
      endTime: this.endTime,
      duration: this.endTime - this.startTime,
    };
  }
  
  /**
   * Reset the game
   */
  reset() {
    this.state.reset();
    this.signalManager.reset();
    this.actions = [];
    this.startTime = null;
    this.endTime = null;
    this.lockingSignal = null;
    this.lockStartTime = null;
    this.lockProgress = 0;
    this.endAnimationStartTime = null;
    
    if (this.animationFrameId) {
      cancelAnimationFrame(this.animationFrameId);
      this.animationFrameId = null;
    }
    
    // Recreate UI
    this.init();
  }
  
  /**
   * Destroy the game
   */
  destroy() {
    this.state.destroy();
    this.interferenceManager.destroy();
    this.uiManager.destroy();
    
    if (this.animationFrameId) {
      cancelAnimationFrame(this.animationFrameId);
    }
    
    if (this.container) {
      this.container.innerHTML = '';
    }
    
    this.state.transitionTo(STATES.DESTROYED);
  }
  
  /**
   * Control methods for interference effects (for testing/debugging)
   */
  
  toggleEffect(effectName, enabled) {
    return this.interferenceManager.toggleEffect(effectName, enabled);
  }
  
  setBaseDistortion(intensity) {
    return this.interferenceManager.setManualOverride('baseDistortion', intensity);
  }
  
  setProgressiveDistortion(intensity) {
    return this.interferenceManager.setManualOverride('progressiveDistortion', intensity);
  }
  
  setGlitchFrequency(frequency) {
    return this.interferenceManager.setManualOverride('glitchFrequency', frequency);
  }
  
  setStaticIntensity(intensity) {
    return this.interferenceManager.setManualOverride('staticIntensity', intensity);
  }
  
  setFlickerIntensity(intensity) {
    return this.interferenceManager.setManualOverride('flickerIntensity', intensity);
  }
  
  setProgressiveIntensity(multiplier) {
    this.interferenceManager.setProgressiveIntensity(multiplier);
  }
  
  resetManualOverrides() {
    this.interferenceManager.resetManualOverrides();
  }
  
  getInterferenceState() {
    return this.interferenceManager.getInterferenceState();
  }
  
  /**
   * Render method (for backward compatibility with admin panel)
   */
  render() {
    if (!this.renderer || !this.state.isActive()) {
      return;
    }
    
    const currentTime = Date.now();
    this.renderer.render({
      state: this.state.getState(),
      signals: this.signalManager.getSignals(),
      interference: this.interferenceManager.getInterferenceState(),
      currentTime,
      startTime: this.startTime,
      endAnimationStartTime: this.endAnimationStartTime,
      endAnimationDuration: this.endAnimationDuration,
      lockingSignal: this.lockingSignal,
      lockProgress: this.lockProgress,
    });
  }
  
  /**
   * Get interference object (for backward compatibility with admin panel)
   * Returns a reference to the actual interference object for direct modification
   */
  get interference() {
    return this.interferenceManager.interference;
  }
  
  /**
   * Get manual overrides (for backward compatibility with admin panel)
   * Returns a reference to the actual manualOverrides object
   */
  get manualOverrides() {
    return this.interferenceManager.manualOverrides;
  }
  
  /**
   * Get progressive intensity multiplier (for backward compatibility with admin panel)
   */
  get progressiveIntensityMultiplier() {
    return this.interferenceManager.progressiveIntensityMultiplier || 1.0;
  }
  
  /**
   * Set progressive intensity multiplier (for backward compatibility with admin panel)
   */
  set progressiveIntensityMultiplier(value) {
    this.interferenceManager.setProgressiveIntensity(value);
  }
}

// Export for use in modules
export default ScanningMinigame;

// Also make available globally for direct script inclusion
if (typeof window !== 'undefined') {
  window.ScanningMinigame = ScanningMinigame;
}

