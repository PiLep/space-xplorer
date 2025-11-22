/**
 * Game State Manager
 * Manages state transitions and validates operations
 */

export const STATES = {
  IDLE: 'idle',
  ACTIVE: 'active',
  ENDED: 'ended',
  DESTROYED: 'destroyed',
};

const VALID_TRANSITIONS = {
  [STATES.IDLE]: [STATES.ACTIVE, STATES.DESTROYED],
  [STATES.ACTIVE]: [STATES.ENDED, STATES.DESTROYED],
  [STATES.ENDED]: [STATES.IDLE, STATES.DESTROYED],
  [STATES.DESTROYED]: [],
};

export class GameState {
  constructor() {
    this.state = STATES.IDLE;
    this.listeners = [];
  }

  /**
   * Get current state
   */
  getState() {
    return this.state;
  }

  /**
   * Check if transition is valid
   */
  canTransitionTo(newState) {
    return VALID_TRANSITIONS[this.state]?.includes(newState) ?? false;
  }

  /**
   * Transition to new state
   */
  transitionTo(newState) {
    if (!this.canTransitionTo(newState)) {
      return false;
    }

    const oldState = this.state;
    this.state = newState;

    // Notify listeners
    this.listeners.forEach(listener => {
      try {
        listener(oldState, newState);
      } catch (error) {
        // Log error but don't break state transition
        if (typeof console !== 'undefined' && console.error) {
          console.error('[GameState] Error in state change listener:', error);
        }
      }
    });

    return true;
  }

  /**
   * Add state change listener
   */
  onStateChange(listener) {
    this.listeners.push(listener);
    return () => {
      this.listeners = this.listeners.filter(l => l !== listener);
    };
  }

  /**
   * Check if game is active
   */
  isActive() {
    return this.state === STATES.ACTIVE;
  }

  /**
   * Check if game is idle
   */
  isIdle() {
    return this.state === STATES.IDLE;
  }

  /**
   * Check if game has ended
   */
  isEnded() {
    return this.state === STATES.ENDED;
  }

  /**
   * Check if game is destroyed
   */
  isDestroyed() {
    return this.state === STATES.DESTROYED;
  }

  /**
   * Reset to idle state
   */
  reset() {
    this.state = STATES.IDLE;
  }

  /**
   * Destroy state manager
   */
  destroy() {
    this.listeners = [];
    this.state = STATES.DESTROYED;
  }
}

