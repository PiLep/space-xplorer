/**
 * UI Manager
 * Handles creation and management of UI elements
 */

import { COLORS, TIMING } from '../constants.js';

export class UIManager {
  constructor(container, config) {
    this.container = container;
    this.config = config;
    this.elements = {};
  }

  /**
   * Create the game UI
   */
  createUI() {
    // Clear container
    this.container.innerHTML = '';
    this.container.className = 'scanning-minigame';
    this.container.style.cssText = `
      width: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 1rem;
      position: relative;
    `;
    
    // Create game container
    const gameContainer = document.createElement('div');
    gameContainer.className = 'scanning-minigame__container';
    gameContainer.style.cssText = `
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 1rem;
      position: relative;
    `;
    
    // Create header
    const header = this.createHeader();
    gameContainer.appendChild(header);
    
    // Create canvas wrapper
    const canvasWrapper = this.createCanvasWrapper();
    gameContainer.appendChild(canvasWrapper);
    
    // Create instructions
    const instructions = this.createInstructions();
    gameContainer.appendChild(instructions);
    
    // Create start button
    const startButton = this.createStartButton();
    gameContainer.appendChild(startButton);
    
    this.container.appendChild(gameContainer);
    
    return {
      canvas: this.elements.canvas,
      canvasWrapper: this.elements.canvasWrapper,
      progressBarFill: this.elements.progressBarFill,
      intensityBarFill: this.elements.intensityBarFill,
      intensityLabel: this.elements.intensityLabel,
    };
  }

  /**
   * Create header with progress and intensity bars
   */
  createHeader() {
    const header = document.createElement('div');
    header.className = 'scanning-minigame__header';
    header.style.cssText = `
      display: flex;
      flex-direction: column;
      width: 100%;
      max-width: ${this.config.radarSize}px;
      margin-bottom: 1rem;
      gap: 1rem;
    `;
    
    // Progress bar
    const progressBar = this.createProgressBar();
    header.appendChild(progressBar);
    
    // Intensity bar
    const intensityBar = this.createIntensityBar();
    header.appendChild(intensityBar);
    
    return header;
  }

  /**
   * Create progress bar
   */
  createProgressBar() {
    const progressBar = document.createElement('div');
    progressBar.className = 'scanning-minigame__progress-bar';
    progressBar.style.cssText = `
      width: 100%;
      max-width: ${this.config.radarSize}px;
      height: 24px;
      background: rgba(0, 0, 0, 0.6);
      border: 2px solid ${COLORS.SECONDARY}66;
      border-radius: 4px;
      position: relative;
      overflow: hidden;
      box-shadow: 
        inset 0 0 10px rgba(0, 0, 0, 0.8),
        0 0 10px ${COLORS.SECONDARY}33;
    `;
    
    const fill = document.createElement('div');
    fill.className = 'scanning-minigame__progress-bar-fill';
    fill.style.cssText = `
      position: absolute;
      top: 0;
      left: 0;
      height: 100%;
      width: 100%;
      background: linear-gradient(
        90deg,
        ${COLORS.SECONDARY}CC 0%,
        ${COLORS.PRIMARY}CC 50%,
        ${COLORS.SECONDARY}CC 100%
      );
      background-size: 200% 100%;
      box-shadow: 
        0 0 10px ${COLORS.SECONDARY}80,
        inset 0 0 10px ${COLORS.PRIMARY}4D;
      transition: width 0.1s linear;
      transform-origin: left;
    `;
    
    const scanlines = document.createElement('div');
    scanlines.style.cssText = `
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: repeating-linear-gradient(
        0deg,
        rgba(0, 0, 0, 0.3) 0px,
        rgba(0, 0, 0, 0.3) 1px,
        transparent 1px,
        transparent 2px
      );
      pointer-events: none;
      z-index: 1;
    `;
    
    progressBar.appendChild(fill);
    progressBar.appendChild(scanlines);
    this.elements.progressBarFill = fill;
    
    return progressBar;
  }

  /**
   * Create intensity/completion bar
   */
  createIntensityBar() {
    const container = document.createElement('div');
    container.className = 'scanning-minigame__intensity-container';
    container.style.cssText = `
      width: 100%;
      max-width: ${this.config.radarSize}px;
      margin-bottom: 1rem;
    `;
    
    const label = document.createElement('div');
    label.className = 'scanning-minigame__intensity-label';
    label.textContent = 'SCAN COMPLETION: 0%';
    label.style.cssText = `
      font-size: 0.875rem;
      font-weight: bold;
      color: ${COLORS.PRIMARY};
      font-family: 'Share Tech Mono', monospace;
      text-shadow: 0 0 5px ${COLORS.PRIMARY}4D;
      letter-spacing: 0.05em;
      margin-bottom: 0.5rem;
      text-transform: uppercase;
    `;
    this.elements.intensityLabel = label;
    
    const bar = document.createElement('div');
    bar.className = 'scanning-minigame__intensity-bar';
    bar.style.cssText = `
      width: 100%;
      height: 20px;
      background: rgba(0, 0, 0, 0.6);
      border: 2px solid ${COLORS.PRIMARY}66;
      border-radius: 4px;
      position: relative;
      overflow: hidden;
      box-shadow: 
        inset 0 0 10px rgba(0, 0, 0, 0.8),
        0 0 10px ${COLORS.PRIMARY}33;
    `;
    
    const fill = document.createElement('div');
    fill.className = 'scanning-minigame__intensity-bar-fill';
    fill.style.cssText = `
      position: absolute;
      top: 0;
      left: 0;
      height: 100%;
      width: 0%;
      background: linear-gradient(
        90deg,
        ${COLORS.PRIMARY}99 0%,
        ${COLORS.PRIMARY}E6 25%,
        ${COLORS.PRIMARY}FF 50%,
        ${COLORS.PRIMARY}E6 75%,
        ${COLORS.PRIMARY}99 100%
      );
      background-size: 200% 100%;
      box-shadow: 
        0 0 15px ${COLORS.PRIMARY}99,
        inset 0 0 10px ${COLORS.PRIMARY}66;
      transition: width 0.2s ease-out;
      transform-origin: left;
    `;
    this.elements.intensityBarFill = fill;
    
    const scanlines = document.createElement('div');
    scanlines.style.cssText = `
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: repeating-linear-gradient(
        0deg,
        rgba(0, 0, 0, 0.2) 0px,
        rgba(0, 0, 0, 0.2) 1px,
        transparent 1px,
        transparent 2px
      );
      pointer-events: none;
      z-index: 1;
    `;
    
    bar.appendChild(fill);
    bar.appendChild(scanlines);
    container.appendChild(label);
    container.appendChild(bar);
    
    return container;
  }

  /**
   * Create canvas wrapper
   */
  createCanvasWrapper() {
    const wrapper = document.createElement('div');
    wrapper.className = 'scanning-minigame__canvas-wrapper';
    wrapper.style.cssText = `
      position: relative;
      display: inline-block;
    `;
    this.elements.canvasWrapper = wrapper;
    
    const canvas = document.createElement('canvas');
    canvas.width = this.config.radarSize;
    canvas.height = this.config.radarSize;
    canvas.className = 'scanning-minigame__canvas';
    canvas.style.cssText = `
      border: 2px solid ${COLORS.UI_BORDER};
      border-radius: 4px;
      background: ${COLORS.BACKGROUND};
      cursor: crosshair;
      display: block;
      box-shadow: 0 0 20px ${COLORS.PRIMARY}33;
    `;
    this.elements.canvas = canvas;
    this.elements.ctx = canvas.getContext('2d', { willReadFrequently: true });
    
    wrapper.appendChild(canvas);
    return wrapper;
  }

  /**
   * Create instructions
   */
  createInstructions() {
    const instructions = document.createElement('div');
    instructions.className = 'scanning-minigame__instructions';
    instructions.textContent = 'HOLD CLICK ON SIGNALS IN THE OPTIMAL ZONE TO LOCK THEM';
    instructions.style.cssText = `
      text-align: center;
      color: rgba(255, 255, 255, 0.6);
      font-size: 0.875rem;
      max-width: ${this.config.radarSize}px;
      font-family: 'Share Tech Mono', monospace;
      letter-spacing: 0.05em;
      text-transform: uppercase;
    `;
    return instructions;
  }

  /**
   * Create start button
   */
  createStartButton() {
    const button = document.createElement('button');
    button.className = 'scanning-minigame__start';
    button.textContent = 'START SCAN';
    button.style.cssText = `
      padding: 0.75rem 1.5rem;
      background: ${COLORS.PRIMARY};
      color: ${COLORS.BACKGROUND};
      border: 2px solid ${COLORS.PRIMARY};
      border-radius: 0.5rem;
      font-size: 1rem;
      font-weight: bold;
      font-family: 'Share Tech Mono', monospace;
      cursor: pointer;
      transition: all 0.2s;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      box-shadow: 0 0 10px ${COLORS.PRIMARY}4D, 0 0 20px ${COLORS.PRIMARY}33;
    `;
    
    button.addEventListener('mouseenter', () => {
      button.style.background = '#00cc6a';
      button.style.borderColor = '#00cc6a';
      button.style.boxShadow = `0 0 15px ${COLORS.PRIMARY}66, 0 0 25px ${COLORS.PRIMARY}4D`;
    });
    
    button.addEventListener('mouseleave', () => {
      button.style.background = COLORS.PRIMARY;
      button.style.borderColor = COLORS.PRIMARY;
      button.style.boxShadow = `0 0 10px ${COLORS.PRIMARY}4D, 0 0 20px ${COLORS.PRIMARY}33`;
    });
    
    this.elements.startButton = button;
    return button;
  }

  /**
   * Update progress bar
   */
  updateProgressBar(progress) {
    if (!this.elements.progressBarFill) return;
    const progressPercent = (1 - progress) * 100;
    this.elements.progressBarFill.style.width = `${progressPercent}%`;
    
    // Animate gradient
    const currentTime = Date.now();
    const gradientOffset = (currentTime % 2000) / 2000 * 100;
    this.elements.progressBarFill.style.backgroundPosition = `${gradientOffset}% 0`;
  }

  /**
   * Update intensity bar
   */
  updateIntensityBar(completionPercent) {
    if (!this.elements.intensityBarFill || !this.elements.intensityLabel) return;
    
    this.elements.intensityBarFill.style.width = `${completionPercent}%`;
    this.elements.intensityLabel.textContent = `SCAN COMPLETION: ${completionPercent}%`;
    
    // Animate gradient
    const currentTime = Date.now();
    const gradientOffset = (currentTime % 2000) / 2000 * 100;
    this.elements.intensityBarFill.style.backgroundPosition = `${gradientOffset}% 0`;
    
    // Change color based on completion
    if (completionPercent >= 80) {
      this.elements.intensityBarFill.style.background = `linear-gradient(
        90deg,
        ${COLORS.PRIMARY}CC 0%,
        ${COLORS.PRIMARY}FF 25%,
        ${COLORS.PRIMARY}FF 50%,
        ${COLORS.PRIMARY}FF 75%,
        ${COLORS.PRIMARY}CC 100%
      )`;
    } else if (completionPercent >= 50) {
      this.elements.intensityBarFill.style.background = `linear-gradient(
        90deg,
        ${COLORS.PRIMARY}99 0%,
        ${COLORS.PRIMARY}CC 25%,
        ${COLORS.PRIMARY}E6 50%,
        ${COLORS.PRIMARY}CC 75%,
        ${COLORS.PRIMARY}99 100%
      )`;
    } else {
      this.elements.intensityBarFill.style.background = `linear-gradient(
        90deg,
        ${COLORS.SECONDARY}80 0%,
        ${COLORS.PRIMARY}99 25%,
        ${COLORS.PRIMARY}B3 50%,
        ${COLORS.PRIMARY}99 75%,
        ${COLORS.SECONDARY}80 100%
      )`;
    }
  }

  /**
   * Hide start button and instructions
   */
  hideStartElements() {
    const startButton = this.container.querySelector('.scanning-minigame__start');
    if (startButton) {
      startButton.style.display = 'none';
    }
    
    const instructions = this.container.querySelector('.scanning-minigame__instructions');
    if (instructions) {
      instructions.style.display = 'none';
    }
  }

  /**
   * Show terminal sequence (simplified - full implementation can be added later)
   */
  showTerminalSequence(onComplete) {
    // Hide start elements immediately
    this.hideStartElements();
    
    // For now, simplified version - can be expanded later with full terminal animation
    // Full implementation would show typing animation like in original code
    setTimeout(() => {
      if (onComplete) {
        onComplete();
      }
    }, 500);
  }

  /**
   * Show results screen
   */
  showResults(result, onRestart) {
    const existingResults = this.elements.canvasWrapper?.querySelector('.scanning-minigame__results') ||
                           this.container.querySelector('.scanning-minigame__results');
    if (existingResults) {
      existingResults.remove();
    }
    
    const resultsDiv = document.createElement('div');
    resultsDiv.className = 'scanning-minigame__results';
    
    const isSuccess = result.acquisitionRate >= 60;
    const statusText = isSuccess ? 'SCAN SUCCESSFUL' : 'SCAN FAILED';
    const statusColor = isSuccess ? COLORS.UI_SUCCESS : COLORS.UI_ERROR;
    
    resultsDiv.style.cssText = `
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      padding: 2rem;
      background: rgba(10, 10, 10, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 0.5rem;
      border: 2px solid ${statusColor}99;
      width: ${this.config.radarSize * 0.85}px;
      max-width: 90%;
      box-shadow: 
        0 0 20px ${statusColor}66, 
        inset 0 0 20px ${statusColor}1A,
        0 0 40px rgba(0, 0, 0, 0.8);
      z-index: 1000;
      text-align: center;
    `;
    
    const title = document.createElement('h3');
    title.textContent = 'SCAN COMPLETE';
    title.style.cssText = `
      font-size: 1.5rem;
      font-weight: bold;
      color: ${COLORS.PRIMARY};
      font-family: 'Share Tech Mono', monospace;
      text-shadow: 0 0 5px ${COLORS.PRIMARY}4D, 0 0 10px ${COLORS.PRIMARY}33;
      margin-bottom: 0.5rem;
      text-transform: uppercase;
      letter-spacing: 0.1em;
    `;
    
    const statusDiv = document.createElement('div');
    statusDiv.textContent = `${isSuccess ? '✓' : '✗'} ${statusText}`;
    statusDiv.style.cssText = `
      font-size: 1.5rem;
      font-weight: bold;
      color: ${statusColor};
      font-family: 'Share Tech Mono', monospace;
      text-shadow: 0 0 10px ${statusColor}66, 0 0 20px ${statusColor}33;
      margin-bottom: 1rem;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      padding: 0.5rem 1rem;
      border: 2px solid ${statusColor}CC;
      border-radius: 0.25rem;
      background: ${statusColor}1A;
      display: inline-block;
    `;
    
    const acquisitionText = document.createElement('p');
    acquisitionText.textContent = `SIGNALS ACQUIRED: ${result.signalsAcquired}/${result.signalsGenerated}`;
    acquisitionText.style.cssText = `
      font-size: 1.25rem;
      color: ${isSuccess ? COLORS.PRIMARY : COLORS.ACCENT};
      font-family: 'Share Tech Mono', monospace;
      text-shadow: 0 0 5px ${isSuccess ? COLORS.PRIMARY + '4D' : COLORS.ACCENT + '4D'};
      margin-bottom: 0.5rem;
      letter-spacing: 0.05em;
    `;
    
    const qualityText = document.createElement('p');
    qualityText.textContent = `ACQUISITION QUALITY: ${result.acquisitionRate}%`;
    qualityText.style.cssText = `
      font-size: 1rem;
      color: ${isSuccess ? COLORS.PRIMARY + 'CC' : COLORS.ERROR + 'CC'};
      font-family: 'Share Tech Mono', monospace;
      margin-bottom: 1rem;
      letter-spacing: 0.05em;
      font-weight: ${isSuccess ? 'normal' : 'bold'};
    `;
    
    const restartButton = document.createElement('button');
    restartButton.textContent = 'RESCAN';
    restartButton.style.cssText = `
      padding: 0.75rem 1.5rem;
      background: ${COLORS.SECONDARY};
      color: ${COLORS.BACKGROUND};
      border: 2px solid ${COLORS.SECONDARY};
      border-radius: 0.5rem;
      font-size: 1rem;
      font-weight: bold;
      font-family: 'Share Tech Mono', monospace;
      cursor: pointer;
      transition: all 0.2s;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      box-shadow: 0 0 10px ${COLORS.SECONDARY}4D, 0 0 20px ${COLORS.SECONDARY}33;
    `;
    
    restartButton.addEventListener('click', () => {
      if (onRestart) {
        onRestart();
      }
    });
    
    resultsDiv.appendChild(title);
    resultsDiv.appendChild(statusDiv);
    resultsDiv.appendChild(acquisitionText);
    resultsDiv.appendChild(qualityText);
    resultsDiv.appendChild(restartButton);
    
    if (this.elements.canvasWrapper) {
      this.elements.canvasWrapper.appendChild(resultsDiv);
    } else {
      const gameContainer = this.container.querySelector('.scanning-minigame__container');
      if (gameContainer) {
        gameContainer.appendChild(resultsDiv);
      }
    }
  }

  /**
   * Destroy UI
   */
  destroy() {
    if (this.container) {
      this.container.innerHTML = '';
    }
    this.elements = {};
  }
}

