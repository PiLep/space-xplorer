@extends('layouts.app')

@section('title', 'Design System - Components - Specialized')

@section('content')
<x-design-system.layout>
    <section>
        <div class="mb-6">
            <a href="{{ route('design-system.components') }}" class="text-space-secondary hover:text-space-secondary-light dark:text-space-secondary dark:hover:text-space-secondary-light font-mono text-sm mb-4 inline-block">
                ← Retour aux composants
            </a>
            <h2 class="text-3xl font-bold text-white mb-2 font-mono">COMPOSANTS_SPECIALISES</h2>
            <p class="text-gray-600 dark:text-gray-400">
                Composants spécifiques au projet Space Xplorer
            </p>
        </div>
        
        <div class="space-y-8">
            <!-- Planet Card -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Planet Card</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    Card spécialisée pour l'affichage des planètes avec layout horizontal, image, description et caractéristiques.
                </p>
                <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                    <div class="bg-space-black rounded-lg p-4">
                        @php
                            $examplePlanet = (object)[
                                'name' => 'Kepler-452b',
                                'type' => 'tellurique',
                                'size' => 'moyenne',
                                'temperature' => 'tempérée',
                                'atmosphere' => 'respirable',
                                'terrain' => 'forestier',
                                'resources' => 'abondantes',
                                'description' => 'Une planète tellurique de taille moyenne avec une température tempérée et une atmosphère respirable. Le terrain est principalement forestier avec des ressources abondantes, faisant de cette planète un candidat idéal pour la colonisation.',
                            ];
                        @endphp
                        <x-planet-card :planet="$examplePlanet" :showImage="false" />
                    </div>
                    <div class="mt-4">
                        <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                        <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                            &lt;x-planet-card :planet="$planet" /&gt;
                        </code>
                    </div>
                </div>
            </div>

            <!-- Loading Spinner -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Loading Spinner</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    Indicateur de chargement avec message terminal. Disponible en 2 variantes (terminal, simple) et 3 tailles : sm, md, lg.
                </p>
                <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                    <div class="space-y-6">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Variante Terminal (défaut) - Taille Medium :</p>
                            <x-loading-spinner message="[LOADING] Accessing planetary database..." />
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Variante Terminal - Taille Small :</p>
                            <x-loading-spinner message="[LOADING] Processing..." size="sm" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Variante Terminal - Taille Large :</p>
                            <x-loading-spinner message="[LOADING] Initializing system..." size="lg" />
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Variante Simple (sans message) :</p>
                            <x-loading-spinner variant="simple" size="md" :showMessage="false" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                        <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                            &lt;x-loading-spinner message="[LOADING] ..." size="md" /&gt;<br>
                            &lt;x-loading-spinner variant="simple" size="md" :showMessage="false" /&gt;
                        </code>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-design-system.layout>
@endsection

