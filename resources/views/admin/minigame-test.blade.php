@extends('admin.layout')

@section('content')
    <x-page-header title="Minigame Test - Scanning" />

    <div class="bg-space-black min-h-screen p-6">
        <div class="mx-auto max-w-6xl">
            <div class="mb-6 text-center">
                <h2 class="text-space-primary mb-2 font-mono text-2xl font-bold">SCANNING RADAR SYSTEM</h2>
                <p class="font-mono text-sm text-gray-400">
                    Test du minijeu de scanning - Implémentation full JS isolée
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Game Container -->
                <div class="lg:col-span-2">
                    <div
                        id="minigame-container"
                        class="minigame-container"
                    ></div>
                </div>

                <!-- Effects Control Panel -->
                <div class="lg:col-span-1">
                    <div
                        id="effects-control-panel"
                        class="space-y-4 rounded-lg border border-gray-700 bg-gray-900 p-4"
                    >
                        <div class="mb-4">
                            <h3 class="text-space-primary mb-2 font-mono text-lg font-bold">EFFETS D'INTERFÉRENCE</h3>
                            <p
                                class="font-mono text-xs text-gray-500"
                                id="game-state-info"
                            >
                                État: <span id="current-game-state">-</span>
                            </p>
                            <p
                                class="mt-1 text-xs text-yellow-400"
                                id="effects-note"
                                style="display: none;"
                            >
                                ⚠️ Démarrez le jeu pour voir les modifications en temps réel
                            </p>
                        </div>

                        <!-- Continuous Effects -->
                        <div class="space-y-2">
                            <h4 class="font-mono text-sm font-semibold text-gray-300">Effets Continus</h4>
                            <label class="flex cursor-pointer items-center space-x-2 text-gray-300">
                                <input
                                    type="checkbox"
                                    id="toggle-crt"
                                    checked
                                    class="text-space-primary h-4 w-4 rounded"
                                >
                                <span class="text-sm">Effet CRT</span>
                            </label>
                            <label class="flex cursor-pointer items-center space-x-2 text-gray-300">
                                <input
                                    type="checkbox"
                                    id="toggle-scanlines"
                                    checked
                                    class="text-space-primary h-4 w-4 rounded"
                                >
                                <span class="text-sm">Scanlines Continues</span>
                            </label>
                            <label class="flex cursor-pointer items-center space-x-2 text-gray-300">
                                <input
                                    type="checkbox"
                                    id="toggle-noise"
                                    checked
                                    class="text-space-primary h-4 w-4 rounded"
                                >
                                <span class="text-sm">Bruit Continu</span>
                            </label>
                            <label class="flex cursor-pointer items-center space-x-2 text-gray-300">
                                <input
                                    type="checkbox"
                                    id="toggle-grain"
                                    checked
                                    class="text-space-primary h-4 w-4 rounded"
                                >
                                <span class="text-sm">Grain</span>
                            </label>
                            <label class="flex cursor-pointer items-center space-x-2 text-gray-300">
                                <input
                                    type="checkbox"
                                    id="toggle-vignette"
                                    checked
                                    class="text-space-primary h-4 w-4 rounded"
                                >
                                <span class="text-sm">Vignette</span>
                            </label>
                            <label class="flex cursor-pointer items-center space-x-2 text-gray-300">
                                <input
                                    type="checkbox"
                                    id="toggle-chromatic"
                                    checked
                                    class="text-space-primary h-4 w-4 rounded"
                                >
                                <span class="text-sm">Aberration Chromatique</span>
                            </label>
                        </div>

                        <!-- Progressive Effects Intensity -->
                        <div class="space-y-3 border-t border-gray-700 pt-4">
                            <h4 class="font-mono text-sm font-semibold text-gray-300">Intensité des Effets Progressifs</h4>

                            <div>
                                <label class="mb-1 block text-xs text-gray-400">Multiplicateur Global</label>
                                <input
                                    type="range"
                                    id="slider-progressive-multiplier"
                                    min="0"
                                    max="3"
                                    step="0.1"
                                    value="1"
                                    class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-gray-700"
                                >
                                <div class="mt-1 flex justify-between text-xs text-gray-500">
                                    <span>0x</span>
                                    <span id="multiplier-value">1.0x</span>
                                    <span>3x</span>
                                </div>
                            </div>

                            <div>
                                <label class="mb-1 block text-xs text-gray-400">Distorsion de Base</label>
                                <input
                                    type="range"
                                    id="slider-base-distortion"
                                    min="0"
                                    max="10"
                                    step="0.1"
                                    value="0"
                                    class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-gray-700"
                                >
                                <div class="mt-1 flex justify-between text-xs text-gray-500">
                                    <span>0</span>
                                    <span id="base-distortion-value">Auto</span>
                                    <span>10</span>
                                </div>
                            </div>

                            <div>
                                <label class="mb-1 block text-xs text-gray-400">Distorsion Progressive</label>
                                <input
                                    type="range"
                                    id="slider-progressive-distortion"
                                    min="0"
                                    max="10"
                                    step="0.1"
                                    value="0"
                                    class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-gray-700"
                                >
                                <div class="mt-1 flex justify-between text-xs text-gray-500">
                                    <span>0</span>
                                    <span id="progressive-distortion-value">Auto</span>
                                    <span>10</span>
                                </div>
                            </div>

                            <div>
                                <label class="mb-1 block text-xs text-gray-400">Fréquence de Glitch</label>
                                <input
                                    type="range"
                                    id="slider-glitch-frequency"
                                    min="0"
                                    max="2"
                                    step="0.1"
                                    value="0"
                                    class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-gray-700"
                                >
                                <div class="mt-1 flex justify-between text-xs text-gray-500">
                                    <span>0</span>
                                    <span id="glitch-frequency-value">Auto</span>
                                    <span>2</span>
                                </div>
                            </div>

                            <div>
                                <label class="mb-1 block text-xs text-gray-400">Intensité du Statique</label>
                                <input
                                    type="range"
                                    id="slider-static-intensity"
                                    min="0"
                                    max="2"
                                    step="0.1"
                                    value="0"
                                    class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-gray-700"
                                >
                                <div class="mt-1 flex justify-between text-xs text-gray-500">
                                    <span>0</span>
                                    <span id="static-intensity-value">Auto</span>
                                    <span>2</span>
                                </div>
                            </div>

                            <div>
                                <label class="mb-1 block text-xs text-gray-400">Intensité du Flicker</label>
                                <input
                                    type="range"
                                    id="slider-flicker-intensity"
                                    min="0"
                                    max="2"
                                    step="0.1"
                                    value="0"
                                    class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-gray-700"
                                >
                                <div class="mt-1 flex justify-between text-xs text-gray-500">
                                    <span>0</span>
                                    <span id="flicker-intensity-value">Auto</span>
                                    <span>2</span>
                                </div>
                            </div>
                        </div>

                        <!-- Test Results UI -->
                        <div class="border-t border-gray-700 pt-4">
                            <h4 class="mb-2 font-mono text-sm font-semibold text-gray-300">Test Écran de Résultat</h4>
                            <div class="space-y-2">
                                <button
                                    id="test-results-victory"
                                    class="w-full rounded bg-green-600 px-4 py-2 font-mono text-sm text-white transition hover:bg-green-700"
                                >
                                    🏆 Test Victoire
                                </button>
                                <button
                                    id="test-results-defeat"
                                    class="w-full rounded bg-red-600 px-4 py-2 font-mono text-sm text-white transition hover:bg-red-700"
                                >
                                    ❌ Test Défaite
                                </button>
                                <button
                                    id="test-results-partial"
                                    class="w-full rounded bg-yellow-600 px-4 py-2 font-mono text-sm text-white transition hover:bg-yellow-700"
                                >
                                    ⚠️ Test Partiel
                                </button>
                            </div>
                        </div>

                        <!-- Test Button -->
                        <div class="border-t border-gray-700 pt-4">
                            <button
                                id="test-effects"
                                class="mb-2 w-full rounded bg-green-600 px-4 py-2 font-mono text-sm text-white transition hover:bg-green-700"
                            >
                                TEST MAX (Applique valeurs max)
                            </button>
                            <button
                                id="reset-effects"
                                class="w-full rounded bg-gray-800 px-4 py-2 font-mono text-sm text-gray-300 transition hover:bg-gray-700"
                            >
                                Réinitialiser les Effets
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for game to be initialized
            const checkGame = setInterval(() => {
                if (window.minigame) {
                    clearInterval(checkGame);
                    setupEffectsControls();
                }
            }, 100);

            function setupEffectsControls() {
                const game = window.minigame;
                if (!game) {
                    console.error('Game not initialized');
                    return;
                }

                // Update game state display
                function updateGameStateDisplay() {
                    const stateElement = document.getElementById('current-game-state');
                    const noteElement = document.getElementById('effects-note');
                    if (stateElement && game && game.state) {
                        // game.state is now a GameState object, use getState() to get the string value
                        const currentState = game.state.getState ? game.state.getState() : 'unknown';
                        stateElement.textContent = currentState;
                        stateElement.className = currentState === 'active' ? 'text-green-400' :
                            currentState === 'idle' ? 'text-yellow-400' : 'text-red-400';

                        if (noteElement) {
                            noteElement.style.display = currentState !== 'active' ? 'block' : 'none';
                        }
                    }
                }

                // Update state display periodically
                updateGameStateDisplay();
                setInterval(updateGameStateDisplay, 500);

                // Continuous effects toggles
                document.getElementById('toggle-crt').addEventListener('change', (e) => {
                    game.toggleEffect('crtEffect', e.target.checked);
                });
                document.getElementById('toggle-scanlines').addEventListener('change', (e) => {
                    game.toggleEffect('continuousScanlines', e.target.checked);
                });
                document.getElementById('toggle-noise').addEventListener('change', (e) => {
                    game.toggleEffect('continuousNoise', e.target.checked);
                });
                document.getElementById('toggle-grain').addEventListener('change', (e) => {
                    game.toggleEffect('continuousGrain', e.target.checked);
                });
                document.getElementById('toggle-vignette').addEventListener('change', (e) => {
                    game.toggleEffect('vignette', e.target.checked);
                });
                document.getElementById('toggle-chromatic').addEventListener('change', (e) => {
                    game.toggleEffect('chromaticAberration', e.target.checked);
                });

                // Progressive multiplier
                const multiplierSlider = document.getElementById('slider-progressive-multiplier');
                const multiplierValue = document.getElementById('multiplier-value');
                multiplierSlider.addEventListener('input', (e) => {
                    const value = parseFloat(e.target.value);
                    multiplierValue.textContent = value.toFixed(1) + 'x';
                    game.setProgressiveIntensity(value);
                });

                // Base distortion
                const baseDistSlider = document.getElementById('slider-base-distortion');
                const baseDistValue = document.getElementById('base-distortion-value');
                baseDistSlider.addEventListener('input', (e) => {
                    const value = parseFloat(e.target.value);
                    if (value === 0) {
                        baseDistValue.textContent = 'Auto';
                        game.manualOverrides.baseDistortion = false;
                        // Reset to auto-progression value if game is active
                        if (game.state && game.state.isActive && game.state.isActive() && game.startTime) {
                            const elapsed = Date.now() - game.startTime;
                            const progress = Math.min(elapsed / game.config.totalDuration, 1);
                            const multiplier = game.progressiveIntensityMultiplier || 1.0;
                            game.interference.baseDistortion = progress * 1.5 * multiplier;
                        } else {
                            game.interference.baseDistortion = 0;
                        }
                    } else {
                        baseDistValue.textContent = value.toFixed(1);
                        game.setBaseDistortion(value);
                    }
                });

                // Progressive distortion
                const progDistSlider = document.getElementById('slider-progressive-distortion');
                const progDistValue = document.getElementById('progressive-distortion-value');
                progDistSlider.addEventListener('input', (e) => {
                    const value = parseFloat(e.target.value);
                    if (value === 0) {
                        progDistValue.textContent = 'Auto';
                        game.manualOverrides.progressiveDistortion = false;
                        if (game.state && game.state.isActive && game.state.isActive() && game.startTime) {
                            const elapsed = Date.now() - game.startTime;
                            const progress = Math.min(elapsed / game.config.totalDuration, 1);
                            const multiplier = game.progressiveIntensityMultiplier || 1.0;
                            game.interference.progressiveDistortion = progress * 3 * multiplier;
                        } else {
                            game.interference.progressiveDistortion = 0;
                        }
                    } else {
                        progDistValue.textContent = value.toFixed(1);
                        game.setProgressiveDistortion(value);
                    }
                });

                // Glitch frequency
                const glitchSlider = document.getElementById('slider-glitch-frequency');
                const glitchValue = document.getElementById('glitch-frequency-value');
                glitchSlider.addEventListener('input', (e) => {
                    const value = parseFloat(e.target.value);
                    if (value === 0) {
                        glitchValue.textContent = 'Auto';
                        game.manualOverrides.glitchFrequency = false;
                        if (game.state && game.state.isActive && game.state.isActive() && game.startTime) {
                            const elapsed = Date.now() - game.startTime;
                            const progress = Math.min(elapsed / game.config.totalDuration, 1);
                            const multiplier = game.progressiveIntensityMultiplier || 1.0;
                            game.interference.glitchFrequency = progress * 0.5 * multiplier;
                        } else {
                            game.interference.glitchFrequency = 0;
                        }
                    } else {
                        glitchValue.textContent = value.toFixed(1);
                        game.setGlitchFrequency(value);
                    }
                });

                // Static intensity
                const staticSlider = document.getElementById('slider-static-intensity');
                const staticValue = document.getElementById('static-intensity-value');
                staticSlider.addEventListener('input', (e) => {
                    const value = parseFloat(e.target.value);
                    if (value === 0) {
                        staticValue.textContent = 'Auto';
                        game.manualOverrides.staticIntensity = false;
                        if (game.state && game.state.isActive && game.state.isActive() && game.startTime) {
                            const elapsed = Date.now() - game.startTime;
                            const progress = Math.min(elapsed / game.config.totalDuration, 1);
                            const multiplier = game.progressiveIntensityMultiplier || 1.0;
                            game.interference.staticIntensity = progress * 0.4 * multiplier;
                        } else {
                            game.interference.staticIntensity = 0;
                        }
                    } else {
                        staticValue.textContent = value.toFixed(1);
                        game.setStaticIntensity(value);
                    }
                });

                // Flicker intensity
                const flickerSlider = document.getElementById('slider-flicker-intensity');
                const flickerValue = document.getElementById('flicker-intensity-value');
                flickerSlider.addEventListener('input', (e) => {
                    const value = parseFloat(e.target.value);
                    if (value === 0) {
                        flickerValue.textContent = 'Auto';
                        game.manualOverrides.flickerIntensity = false;
                        if (game.state && game.state.isActive && game.state.isActive() && game.startTime) {
                            const elapsed = Date.now() - game.startTime;
                            const progress = Math.min(elapsed / game.config.totalDuration, 1);
                            const multiplier = game.progressiveIntensityMultiplier || 1.0;
                            game.interference.flickerIntensity = progress * 0.3 * multiplier;
                        } else {
                            game.interference.flickerIntensity = 0;
                        }
                    } else {
                        flickerValue.textContent = value.toFixed(1);
                        game.setFlickerIntensity(value);
                    }
                });

                // Test button - Apply max values to see if effects work at all
                document.getElementById('test-effects').addEventListener('click', () => {
                    // Set max values
                    baseDistSlider.value = 10;
                    baseDistValue.textContent = '10.0';
                    game.setBaseDistortion(10);

                    progDistSlider.value = 10;
                    progDistValue.textContent = '10.0';
                    game.setProgressiveDistortion(10);

                    glitchSlider.value = 2;
                    glitchValue.textContent = '2.0';
                    game.setGlitchFrequency(2);

                    staticSlider.value = 2;
                    staticValue.textContent = '2.0';
                    game.setStaticIntensity(2);

                    flickerSlider.value = 2;
                    flickerValue.textContent = '2.0';
                    game.setFlickerIntensity(2);

                    multiplierSlider.value = 3;
                    multiplierValue.textContent = '3.0x';
                    game.setProgressiveIntensity(3);

                    // Force immediate render
                    if (game.state && game.state.isActive && game.state.isActive() && game.ctx) {
                        game.render();
                    }
                });

                // Reset button
                document.getElementById('reset-effects').addEventListener('click', () => {
                    // Reset all sliders
                    multiplierSlider.value = 1;
                    multiplierValue.textContent = '1.0x';
                    game.setProgressiveIntensity(1);

                    baseDistSlider.value = 0;
                    baseDistValue.textContent = 'Auto';
                    progDistSlider.value = 0;
                    progDistValue.textContent = 'Auto';
                    glitchSlider.value = 0;
                    glitchValue.textContent = 'Auto';
                    staticSlider.value = 0;
                    staticValue.textContent = 'Auto';
                    flickerSlider.value = 0;
                    flickerValue.textContent = 'Auto';

                    // Reset manual overrides
                    game.resetManualOverrides();
                });

                // Test Results UI buttons
                function createTestResult(scenario) {
                    // Remove existing results overlay if present
                    const existingResults = document.querySelector('.scanning-minigame__results');
                    if (existingResults) {
                        existingResults.remove();
                    }

                    let testResult;
                    const signalCount = 8;
                    const startTime = Date.now() - 60000;
                    const endTime = Date.now();

                    switch (scenario) {
                        case 'victory':
                            testResult = {
                                score: 95,
                                acquisitionRate: 100,
                                signalsGenerated: signalCount,
                                signalsLocked: signalCount,
                                signalsAcquired: signalCount,
                                actions: [],
                                startTime: startTime,
                                endTime: endTime,
                                duration: 60000,
                            };
                            break;
                        case 'defeat':
                            testResult = {
                                score: 15,
                                acquisitionRate: 12,
                                signalsGenerated: signalCount,
                                signalsLocked: 1,
                                signalsAcquired: 1,
                                actions: [],
                                startTime: startTime,
                                endTime: endTime,
                                duration: 60000,
                            };
                            break;
                        case 'partial':
                            testResult = {
                                score: 65,
                                acquisitionRate: 62,
                                signalsGenerated: signalCount,
                                signalsLocked: 5,
                                signalsAcquired: 5,
                                actions: [],
                                startTime: startTime,
                                endTime: endTime,
                                duration: 60000,
                            };
                            break;
                    }

                    // Show results overlay
                    game.showResults(testResult);
                }

                document.getElementById('test-results-victory').addEventListener('click', () => {
                    createTestResult('victory');
                });

                document.getElementById('test-results-defeat').addEventListener('click', () => {
                    createTestResult('defeat');
                });

                document.getElementById('test-results-partial').addEventListener('click', () => {
                    createTestResult('partial');
                });
            }
        });
    </script>

    <style>
        /* Custom slider styling */
        input[type="range"] {
            -webkit-appearance: none;
            appearance: none;
            background: transparent;
        }

        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 16px;
            height: 16px;
            background: #00ff88;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid #0a0a0a;
        }

        input[type="range"]::-moz-range-thumb {
            width: 16px;
            height: 16px;
            background: #00ff88;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid #0a0a0a;
        }

        input[type="range"]::-webkit-slider-runnable-track {
            background: #374151;
            height: 8px;
            border-radius: 4px;
        }

        input[type="range"]::-moz-range-track {
            background: #374151;
            height: 8px;
            border-radius: 4px;
        }
    </style>
@endsection

@push('scripts')
    @vite(['resources/js/minigames/init.js'])
@endpush
