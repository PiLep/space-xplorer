/**
 * Radar Renderer
 * Handles all canvas rendering (radar, signals, interference effects)
 */

import { COLORS, SIZES, ANIMATIONS, INTERFERENCE, ZONES } from '../constants.js';

export class RadarRenderer {
  constructor(canvas, config) {
    this.canvas = canvas;
    this.ctx = canvas.getContext('2d', { willReadFrequently: true });
    this.config = config;
    this.size = config.radarSize;
    this.centerX = this.size / 2;
    this.centerY = this.size / 2;
  }

  /**
   * Main render method
   */
  render({ state, signals, interference, currentTime, startTime, endAnimationStartTime, endAnimationDuration, lockingSignal, lockProgress }) {
    if (state === 'destroyed') {
      return;
    }

    const ctx = this.ctx;
    const size = this.size;
    const centerX = this.centerX;
    const centerY = this.centerY;

    // Calculate end animation progress if ended
    let endAnimationProgress = 0;
    if (state === 'ended' && endAnimationStartTime) {
      const animationElapsed = currentTime - endAnimationStartTime;
      endAnimationProgress = Math.min(1, animationElapsed / endAnimationDuration);
    }

    // Apply screen shake
    let shakeX = 0;
    let shakeY = 0;
    if (interference.screenShake > 0) {
      shakeX = (Math.random() - 0.5) * interference.screenShake;
      shakeY = (Math.random() - 0.5) * interference.screenShake;
    }

    // Apply flicker effect
    const flickerAmount = interference.flicker ?
      (0.3 + interference.flickerIntensity * 0.2) :
      (1 - interference.flickerIntensity * 0.1);
    ctx.globalAlpha = Math.max(0.5, flickerAmount + (Math.random() - 0.5) * 0.1);

    // Clear canvas
    ctx.fillStyle = COLORS.BACKGROUND;
    ctx.fillRect(0, 0, size, size);

    // Calculate distortion
    const totalDistortion = interference.baseDistortion +
      interference.progressiveDistortion +
      interference.distortion;
    const distortionX = (Math.sin(currentTime / 50) * totalDistortion) + shakeX;
    const distortionY = (Math.cos(currentTime / 50) * totalDistortion) + shakeY;

    // Draw radar base
    this.drawRadarBase(ctx, centerX, centerY, size);

    // Calculate elapsed time
    const elapsed = currentTime - startTime;

    // Draw scanline
    if (state === 'active' || (state === 'ended' && endAnimationProgress < 0.8)) {
      this.drawScanline(ctx, size, elapsed, state, endAnimationProgress);
    }

    // Draw scanning line (rotating)
    this.drawScanningLine(ctx, centerX, centerY, size, elapsed, state, endAnimationProgress);

    // Draw signals
    this.drawSignals(ctx, signals, currentTime, interference, lockingSignal, lockProgress, distortionX, distortionY);

    // Draw interference effects
    this.drawInterferenceEffects(ctx, size, interference, currentTime);

    // End animation pulse
    if (state === 'ended' && endAnimationProgress > 0) {
      const pulseIntensity = Math.sin(endAnimationProgress * Math.PI * 6) * 0.1 + 0.1;
      ctx.globalCompositeOperation = 'screen';
      ctx.fillStyle = `rgba(0, 255, 136, ${pulseIntensity})`;
      ctx.fillRect(0, 0, size, size);
      ctx.globalCompositeOperation = 'source-over';
    }

    // Reset global alpha
    ctx.globalAlpha = 1;
  }

  /**
   * Draw radar base (circles and crosshair)
   */
  drawRadarBase(ctx, centerX, centerY, size) {
    // Draw radar circles
    const maxCircleRadius = size * 0.75;
    const numCircles = 8;

    for (let i = 1; i <= numCircles; i++) {
      const progress = i / numCircles;
      const opacity = Math.max(0.05, 0.3 * (1 - progress * 0.7));

      ctx.strokeStyle = `rgba(0, 255, 136, ${opacity})`;
      ctx.lineWidth = 1;
      ctx.beginPath();
      ctx.arc(centerX, centerY, (maxCircleRadius / numCircles) * i, 0, 2 * Math.PI);
      ctx.stroke();
    }

    // Draw crosshair
    ctx.strokeStyle = 'rgba(0, 255, 136, 0.3)';
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.moveTo(centerX, 0);
    ctx.lineTo(centerX, size);
    ctx.moveTo(0, centerY);
    ctx.lineTo(size, centerY);
    ctx.stroke();
  }

  /**
   * Draw horizontal scanline
   */
  drawScanline(ctx, size, elapsed, state, endAnimationProgress) {
    let scanlineY;
    if (state === 'ended') {
      scanlineY = size * endAnimationProgress * 1.25;
    } else {
      const scanlineSpeed = ANIMATIONS.SCANLINE_SPEED;
      scanlineY = ((elapsed / 1000) % scanlineSpeed) / scanlineSpeed * size;
    }

    const gradient = ctx.createLinearGradient(0, scanlineY - 2, 0, scanlineY + 2);
    gradient.addColorStop(0, 'rgba(0, 255, 136, 0)');
    gradient.addColorStop(0.3, 'rgba(0, 255, 136, 0.3)');
    gradient.addColorStop(0.5, 'rgba(0, 255, 136, 0.5)');
    gradient.addColorStop(0.7, 'rgba(0, 255, 136, 0.3)');
    gradient.addColorStop(1, 'rgba(0, 255, 136, 0)');

    ctx.strokeStyle = gradient;
    ctx.lineWidth = 2;
    ctx.shadowBlur = 15;
    ctx.shadowColor = 'rgba(0, 255, 136, 0.5)';
    ctx.beginPath();
    ctx.moveTo(0, scanlineY);
    ctx.lineTo(size, scanlineY);
    ctx.stroke();
    ctx.shadowBlur = 0;
  }

  /**
   * Draw rotating scanning line
   */
  drawScanningLine(ctx, centerX, centerY, size, elapsed, state, endAnimationProgress) {
    let rotation = -Math.PI / 2;

    if (state === 'active') {
      const rotationSpeed = 2 * Math.PI / ANIMATIONS.SCAN_ROTATION_PERIOD;
      rotation = -Math.PI / 2 + (elapsed * rotationSpeed);
    } else if (state === 'ended' && endAnimationProgress > 0) {
      rotation = -Math.PI / 2 + (endAnimationProgress * Math.PI * 2);
    }

    const maxScanLength = size * 0.75;

    // End animation pulse effect
    let scanLineOpacity = 1;
    let scanLineGlow = 10;
    if (state === 'ended' && endAnimationProgress > 0) {
      const pulse = Math.sin(endAnimationProgress * Math.PI * 4) * 0.3 + 0.7;
      scanLineOpacity = pulse;
      scanLineGlow = 10 + (pulse * 20);
    }

    // Draw glow trail
    ctx.strokeStyle = `rgba(0, 255, 136, ${0.1 * scanLineOpacity})`;
    ctx.lineWidth = 8;
    ctx.beginPath();
    ctx.moveTo(centerX, centerY);
    ctx.lineTo(
      centerX + maxScanLength * Math.cos(rotation),
      centerY + maxScanLength * Math.sin(rotation)
    );
    ctx.stroke();

    // Draw main scanning line
    ctx.strokeStyle = `rgba(0, 255, 136, ${scanLineOpacity})`;
    ctx.lineWidth = 2;
    ctx.shadowBlur = scanLineGlow;
    ctx.shadowColor = `rgba(0, 255, 136, ${0.5 * scanLineOpacity})`;
    ctx.beginPath();
    ctx.moveTo(centerX, centerY);
    ctx.lineTo(
      centerX + maxScanLength * Math.cos(rotation),
      centerY + maxScanLength * Math.sin(rotation)
    );
    ctx.stroke();
    ctx.shadowBlur = 0;
  }

  /**
   * Draw signals
   */
  drawSignals(ctx, signals, currentTime, interference, lockingSignal, lockProgress, distortionX, distortionY) {
    signals.forEach(signal => {
      const isActive = currentTime >= signal.startTime && currentTime <= signal.endTime;
      const isLocked = signal.locked;

      if (!isActive && !isLocked) {
        return;
      }

      // Calculate progress
      let progress = 0;
      if (isActive) {
        progress = (currentTime - signal.startTime) / signal.duration;
      } else if (isLocked) {
        progress = 1;
      }

      const isOptimal = progress >= ZONES.OPTIMAL_MIN && progress <= ZONES.OPTIMAL_MAX;

      // Size based on state
      let signalSize = SIZES.SIGNAL_DEFAULT;
      if (isLocked) {
        signalSize = SIZES.SIGNAL_LOCKED;
      } else if (isOptimal) {
        signalSize = SIZES.SIGNAL_OPTIMAL;
      }
      signalSize = Math.max(SIZES.SIGNAL_MIN, signalSize);

      // Color based on state
      let color = COLORS.SIGNAL_INACTIVE;
      let glowColor = COLORS.GLOW_INACTIVE;

      if (isLocked) {
        color = COLORS.SIGNAL_LOCKED;
        glowColor = COLORS.GLOW_LOCKED;
      } else if (isOptimal) {
        color = COLORS.SIGNAL_OPTIMAL;
        glowColor = COLORS.GLOW_OPTIMAL;
      } else if (isActive) {
        color = COLORS.SIGNAL_ACTIVE;
        glowColor = COLORS.GLOW_ACTIVE;
      }

      // Get rendered position (with jitter for unlocked signals)
      const renderedPos = this.getSignalRenderedPosition(signal, currentTime, interference, isLocked);

      // Draw glow effect
      ctx.shadowBlur = 15;
      ctx.shadowColor = glowColor;

      // Draw signal
      ctx.fillStyle = color;
      ctx.beginPath();
      ctx.arc(renderedPos.x, renderedPos.y, signalSize / 2, 0, 2 * Math.PI);
      ctx.fill();

      // Draw locking progress indicator
      if (lockingSignal && lockingSignal.id === signal.id && lockProgress > 0) {
        ctx.strokeStyle = COLORS.PRIMARY;
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.arc(
          renderedPos.x,
          renderedPos.y,
          signalSize / 2 + 4,
          -Math.PI / 2,
          -Math.PI / 2 + (lockProgress * 2 * Math.PI)
        );
        ctx.stroke();

        ctx.fillStyle = `rgba(0, 255, 136, ${lockProgress * 0.3})`;
        ctx.beginPath();
        ctx.arc(renderedPos.x, renderedPos.y, signalSize / 2, 0, 2 * Math.PI);
        ctx.fill();
      }

      ctx.shadowBlur = 0;

      // Draw pulse effect for active signals
      if (isActive && !isLocked) {
        ctx.strokeStyle = color;
        ctx.lineWidth = 1.5;
        ctx.shadowBlur = 8;
        ctx.shadowColor = glowColor;

        const totalDist = interference.baseDistortion +
          interference.progressiveDistortion +
          interference.distortion;
        let pulseJitter = 0;
        if (totalDist > 0) {
          pulseJitter = Math.sin(currentTime / 50 + signal.id) * (1 + totalDist * 0.3) * 0.6;
        }

        const pulseSize = Math.max(1, signalSize + (Math.sin(currentTime / 100) * 6) + pulseJitter);
        ctx.beginPath();
        ctx.arc(renderedPos.x, renderedPos.y, pulseSize / 2, 0, 2 * Math.PI);
        ctx.stroke();
        ctx.shadowBlur = 0;

        // Random flicker
        if (interference.flicker && Math.random() < 0.3) {
          ctx.globalAlpha = 0.3;
          ctx.fillStyle = color;
          ctx.beginPath();
          ctx.arc(renderedPos.x, renderedPos.y, signalSize / 2, 0, 2 * Math.PI);
          ctx.fill();
          ctx.globalAlpha = 1;
        }
      }

      // Draw locked indicator
      if (isLocked) {
        ctx.strokeStyle = COLORS.PRIMARY;
        ctx.lineWidth = 2;
        ctx.shadowBlur = 10;
        ctx.shadowColor = COLORS.GLOW_LOCKED;
        const ringRadius = Math.max(1, (signalSize / 2) + 3);
        ctx.beginPath();
        ctx.arc(renderedPos.x, renderedPos.y, ringRadius, 0, 2 * Math.PI);
        ctx.stroke();
        ctx.shadowBlur = 0;
      }
    });
  }

  /**
   * Get signal rendered position (with interference jitter)
   * Public method for click detection
   */
  getSignalRenderedPosition(signal, currentTime, interference, isLocked) {
    // Locked signals are NOT affected by interference
    if (isLocked) {
      return { x: signal.x, y: signal.y };
    }

    let jitterX = 0;
    let jitterY = 0;

    // Add position jitter from interference
    const totalDist = interference.baseDistortion +
      interference.progressiveDistortion +
      interference.distortion;
    if (totalDist > 0) {
      jitterX = (Math.sin(currentTime / 80 + signal.id) * totalDist * 0.6);
      jitterY = (Math.cos(currentTime / 80 + signal.id) * totalDist * 0.6);
    }

    // Add chromatic shift jitter
    if (interference.chromaticShift > 0) {
      jitterX += (Math.sin(currentTime / 60 + signal.id) * interference.chromaticShift * 0.3);
      jitterY += (Math.cos(currentTime / 60 + signal.id) * interference.chromaticShift * 0.3);
    }

    return {
      x: signal.x + jitterX,
      y: signal.y + jitterY
    };
  }

  /**
   * Draw all interference effects
   */
  drawInterferenceEffects(ctx, size, interference, currentTime) {
    // Continuous effects
    if (interference.continuousScanlines) {
      this.drawContinuousScanlines(ctx, size, currentTime);
    }

    if (interference.continuousNoise) {
      this.drawContinuousNoise(ctx, size, currentTime, interference);
    }

    if (interference.continuousGrain) {
      this.drawContinuousGrain(ctx, size, currentTime, interference);
    }

    // Acute effects
    if (interference.static) {
      this.drawStatic(ctx, size, currentTime, interference);
    }

    if (interference.scanline) {
      this.drawScanlineInterference(ctx, size, currentTime);
    }

    if (interference.glitch) {
      this.drawGlitch(ctx, size, currentTime, interference);
    }

    if (interference.chromaticShift > 0) {
      this.drawChromaticAberration(ctx, size, currentTime, interference);
    }

    // Continuous effects (always last)
    if (interference.vignette) {
      this.drawVignette(ctx, size, currentTime, interference);
    }

    if (interference.chromaticAberration) {
      this.drawContinuousChromaticAberration(ctx, size, currentTime, interference);
    }

    if (interference.crtEffect) {
      this.drawCRTEffect(ctx, size, currentTime);
    }
  }

  /**
   * Draw continuous scanlines
   */
  drawContinuousScanlines(ctx, size, currentTime) {
    ctx.strokeStyle = 'rgba(0, 255, 136, 0.01)';
    ctx.lineWidth = 1;

    const lineSpacing = INTERFERENCE.SCANLINE_SPACING;
    const offset = (currentTime / 20) % lineSpacing;

    for (let y = offset; y < size; y += lineSpacing) {
      ctx.beginPath();
      ctx.moveTo(0, y);
      ctx.lineTo(size, y);
      ctx.stroke();
    }
  }

  /**
   * Draw continuous noise
   */
  drawContinuousNoise(ctx, size, currentTime, interference) {
    const noiseIntensity = 0.008 + (interference.staticIntensity * 0.01);

    for (let i = 0; i < INTERFERENCE.NOISE_DOT_COUNT; i++) {
      const x = (Math.random() * size + currentTime / 10) % size;
      const y = (Math.random() * size + currentTime / 15) % size;
      const opacity = Math.random() * noiseIntensity;
      ctx.fillStyle = `rgba(0, 255, 136, ${opacity})`;
      ctx.fillRect(x, y, 1, 1);
    }
  }

  /**
   * Draw continuous grain
   */
  drawContinuousGrain(ctx, size, currentTime, interference) {
    const grainIntensity = 0.005 + (interference.staticIntensity * 0.008);

    for (let i = 0; i < INTERFERENCE.GRAIN_DOT_COUNT; i++) {
      const x = Math.random() * size;
      const y = Math.random() * size;
      const opacity = Math.random() * grainIntensity;
      ctx.fillStyle = `rgba(255, 255, 255, ${opacity})`;
      ctx.fillRect(x, y, 0.5, 0.5);
    }
  }

  /**
   * Draw static interference
   */
  drawStatic(ctx, size, currentTime, interference) {
    const intensity = 1 + interference.staticIntensity;

    // Update and draw particles
    if (interference.staticParticles) {
      interference.staticParticles.forEach(particle => {
        particle.life -= 16;
        if (particle.life <= 0) {
          particle.x = Math.random() * size;
          particle.y = Math.random() * size;
          particle.life = particle.maxLife;
          particle.opacity = Math.random() * 0.3 + 0.1;
        }

        ctx.fillStyle = `rgba(0, 255, 136, ${particle.opacity * intensity * 0.5 * (particle.life / particle.maxLife)})`;
        ctx.fillRect(particle.x, particle.y, particle.size, particle.size);
      });
    }

    // Random noise dots
    const dotCount = Math.floor(40 * intensity);
    for (let i = 0; i < dotCount; i++) {
      const x = Math.random() * size;
      const y = Math.random() * size;
      const opacity = Math.random() * 0.15 * intensity;
      ctx.fillStyle = `rgba(0, 255, 136, ${opacity})`;
      ctx.fillRect(x, y, 1, 1);
    }
  }

  /**
   * Draw scanline interference
   */
  drawScanlineInterference(ctx, size, currentTime) {
    const lineCount = 5 + Math.floor(Math.random() * 10);
    const lineSpacing = size / lineCount;

    ctx.strokeStyle = 'rgba(0, 255, 136, 0.15)';
    ctx.lineWidth = 1;

    for (let i = 0; i < lineCount; i++) {
      const y = (i * lineSpacing) + (Math.sin(currentTime / 100 + i) * 2);
      ctx.beginPath();
      ctx.moveTo(0, y);
      ctx.lineTo(size, y);
      ctx.stroke();
    }

    const barY = Math.random() * size;
    ctx.fillStyle = 'rgba(0, 0, 0, 0.2)';
    ctx.fillRect(0, barY, size, 3);
  }

  /**
   * Draw glitch effect
   */
  drawGlitch(ctx, size, currentTime, interference) {
    const intensity = interference.glitchIntensity;
    const offset = (Math.random() - 0.5) * intensity * 8;

    const imageData = ctx.getImageData(0, 0, size, size);
    const glitchHeight = 8 + Math.random() * 15;
    const glitchY = Math.random() * (size - glitchHeight);

    ctx.putImageData(imageData, offset, 0);

    ctx.fillStyle = `rgba(255, 0, 0, ${0.04 * intensity})`;
    ctx.fillRect(0, glitchY, size, glitchHeight);

    ctx.fillStyle = `rgba(0, 0, 255, ${0.04 * intensity})`;
    ctx.fillRect(0, glitchY + glitchHeight, size, glitchHeight);

    for (let i = 0; i < 2; i++) {
      const x = Math.random() * size;
      ctx.strokeStyle = `rgba(0, 255, 136, ${0.2 * intensity})`;
      ctx.lineWidth = 1;
      ctx.beginPath();
      ctx.moveTo(x, 0);
      ctx.lineTo(x + offset, size);
      ctx.stroke();
    }
  }

  /**
   * Draw chromatic aberration
   */
  drawChromaticAberration(ctx, size, currentTime, interference) {
    const shift = interference.chromaticShift;

    ctx.globalCompositeOperation = 'screen';
    ctx.fillStyle = 'rgba(255, 0, 0, 0.08)';
    ctx.fillRect(-shift, 0, size, size);

    ctx.fillStyle = 'rgba(0, 0, 255, 0.08)';
    ctx.fillRect(shift, 0, size, size);

    ctx.fillStyle = 'rgba(0, 255, 136, 0.04)';
    ctx.fillRect(shift * 0.3, 0, size, size);

    ctx.globalCompositeOperation = 'source-over';
  }

  /**
   * Draw vignette
   */
  drawVignette(ctx, size, currentTime, interference) {
    const gradient = ctx.createRadialGradient(size / 2, size / 2, size * 0.3, size / 2, size / 2, size * 0.7);
    const vignetteIntensity = 0.1 + (interference.progressiveDistortion * 0.03);
    gradient.addColorStop(0, 'rgba(0, 0, 0, 0)');
    gradient.addColorStop(0.7, 'rgba(0, 0, 0, 0)');
    gradient.addColorStop(1, `rgba(0, 0, 0, ${vignetteIntensity})`);

    ctx.fillStyle = gradient;
    ctx.fillRect(0, 0, size, size);
  }

  /**
   * Draw continuous chromatic aberration
   */
  drawContinuousChromaticAberration(ctx, size, currentTime, interference) {
    const aberrationAmount = 0.3 + (interference.progressiveDistortion * 0.2);

    ctx.globalCompositeOperation = 'screen';
    ctx.fillStyle = `rgba(255, 0, 0, ${0.015 * aberrationAmount})`;
    ctx.fillRect(-aberrationAmount, 0, size, size);

    ctx.fillStyle = `rgba(0, 0, 255, ${0.015 * aberrationAmount})`;
    ctx.fillRect(aberrationAmount, 0, size, size);

    ctx.globalCompositeOperation = 'source-over';
  }

  /**
   * Draw CRT effect
   */
  drawCRTEffect(ctx, size, currentTime) {
    // Scanlines
    ctx.strokeStyle = 'rgba(0, 0, 0, 0.3)';
    ctx.lineWidth = 1;
    const scanlineSpacing = INTERFERENCE.CRT_SCANLINE_SPACING;
    for (let y = 0; y < size; y += scanlineSpacing) {
      ctx.beginPath();
      ctx.moveTo(0, y);
      ctx.lineTo(size, y);
      ctx.stroke();
    }

    ctx.strokeStyle = 'rgba(0, 255, 136, 0.015)';
    for (let y = 0; y < size; y += scanlineSpacing * 2) {
      ctx.beginPath();
      ctx.moveTo(0, y);
      ctx.lineTo(size, y);
      ctx.stroke();
    }

    // Screen curvature
    const gradient = ctx.createRadialGradient(size / 2, size / 2, size * 0.3, size / 2, size / 2, size * 0.7);
    gradient.addColorStop(0, 'rgba(0, 0, 0, 0)');
    gradient.addColorStop(0.4, 'rgba(0, 0, 0, 0)');
    gradient.addColorStop(0.7, 'rgba(0, 0, 0, 0.1)');
    gradient.addColorStop(1, 'rgba(0, 0, 0, 0.2)');
    ctx.fillStyle = gradient;
    ctx.fillRect(0, 0, size, size);

    // Corner vignette
    const cornerGradient = ctx.createRadialGradient(0, 0, size * 0.2, 0, 0, size * 0.5);
    cornerGradient.addColorStop(0, 'rgba(0, 0, 0, 0.15)');
    cornerGradient.addColorStop(1, 'rgba(0, 0, 0, 0)');

    ctx.fillStyle = cornerGradient;
    ctx.fillRect(0, 0, size * 0.5, size * 0.5);

    // Other corners (simplified)
    const corners = [
      { x: size, y: 0 },
      { x: 0, y: size },
      { x: size, y: size }
    ];

    corners.forEach(corner => {
      const grad = ctx.createRadialGradient(corner.x, corner.y, size * 0.2, corner.x, corner.y, size * 0.5);
      grad.addColorStop(0, 'rgba(0, 0, 0, 0.15)');
      grad.addColorStop(1, 'rgba(0, 0, 0, 0)');
      ctx.fillStyle = grad;
      ctx.fillRect(
        corner.x === 0 ? 0 : size * 0.5,
        corner.y === 0 ? 0 : size * 0.5,
        size * 0.5,
        size * 0.5
      );
    });

    // Phosphor glow
    ctx.globalCompositeOperation = 'screen';
    ctx.fillStyle = 'rgba(0, 255, 136, 0.02)';
    ctx.fillRect(0, 0, size, size);
    ctx.globalCompositeOperation = 'source-over';

    // Subtle flicker
    const flicker = Math.sin(currentTime / 200) * 0.02 + 0.98;
    ctx.globalAlpha = flicker;
    ctx.fillStyle = 'rgba(0, 0, 0, 0.01)';
    ctx.fillRect(0, 0, size, size);
    ctx.globalAlpha = 1;

    // Screen reflection
    const reflectionGradient = ctx.createLinearGradient(0, 0, 0, size * 0.15);
    reflectionGradient.addColorStop(0, 'rgba(0, 255, 136, 0.05)');
    reflectionGradient.addColorStop(1, 'rgba(0, 255, 136, 0)');
    ctx.fillStyle = reflectionGradient;
    ctx.fillRect(0, 0, size, size * 0.15);
  }
}

