@extends('layouts.app')

@section('title', 'Design System - Components - Specialized')

@section('content')
    <x-design-system.layout>
        <section>
            <div class="mb-6">
                <a
                    href="{{ route('design-system.components') }}"
                    class="text-space-secondary hover:text-space-secondary-light dark:text-space-secondary dark:hover:text-space-secondary-light mb-4 inline-block font-mono text-sm"
                >
                    ← Retour aux composants
                </a>
                <h2 class="mb-2 font-mono text-3xl font-bold text-white">COMPOSANTS_SPECIALISES</h2>
                <p class="text-gray-600 dark:text-gray-400">
                    Composants spécifiques au projet Space Xplorer
                </p>
            </div>

            <div class="space-y-8">
                <!-- Planet Card -->
                <div>
                    <h3 class="mb-4 font-mono text-xl font-semibold text-white">Planet Card</h3>
                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                        Card spécialisée pour l'affichage des planètes avec layout horizontal, image, description et
                        caractéristiques.
                    </p>
                    <div
                        class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                        <div class="bg-space-black rounded-lg p-4">
                            @php
                                $examplePlanet = (object) [
                                    'name' => 'Kepler-452b',
                                    'type' => 'tellurique',
                                    'size' => 'moyenne',
                                    'temperature' => 'tempérée',
                                    'atmosphere' => 'respirable',
                                    'terrain' => 'forestier',
                                    'resources' => 'abondantes',
                                    'description' =>
                                        'Une planète tellurique de taille moyenne avec une température tempérée et une atmosphère respirable. Le terrain est principalement forestier avec des ressources abondantes, faisant de cette planète un candidat idéal pour la colonisation.',
                                ];
                            @endphp
                            <x-planet-card
                                :planet="$examplePlanet"
                                :showImage="false"
                            />
                        </div>
                        <div class="mt-4">
                            <p class="mb-2 text-xs text-gray-500 dark:text-gray-500">Usage :</p>
                            <code class="text-space-primary bg-space-black block rounded px-2 py-1 font-mono text-xs">
                                &lt;x-planet-card :planet="$planet" /&gt;
                            </code>
                        </div>
                    </div>
                </div>

                <!-- Loading Spinner -->
                <div>
                    <h3 class="mb-4 font-mono text-xl font-semibold text-white">Loading Spinner</h3>
                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                        Indicateur de chargement avec message terminal. Disponible en 2 variantes (terminal, simple) et 3
                        tailles : sm, md, lg.
                    </p>
                    <div
                        class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                        <div class="space-y-6">
                            <div>
                                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">Variante Terminal (défaut) - Taille
                                    Medium :</p>
                                <x-loading-spinner message="[LOADING] Accessing planetary database..." />
                            </div>
                            <div>
                                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">Variante Terminal - Taille Small :
                                </p>
                                <x-loading-spinner
                                    message="[LOADING] Processing..."
                                    size="sm"
                                />
                            </div>
                            <div>
                                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">Variante Terminal - Taille Large :
                                </p>
                                <x-loading-spinner
                                    message="[LOADING] Initializing system..."
                                    size="lg"
                                />
                            </div>
                            <div>
                                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">Variante Simple (sans message) :
                                </p>
                                <x-loading-spinner
                                    variant="simple"
                                    size="md"
                                    :showMessage="false"
                                />
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="mb-2 text-xs text-gray-500 dark:text-gray-500">Usage :</p>
                            <code class="text-space-primary bg-space-black block rounded px-2 py-1 font-mono text-xs">
                                &lt;x-loading-spinner message="[LOADING] ..." size="md" /&gt;<br>
                                &lt;x-loading-spinner variant="simple" size="md" :showMessage="false" /&gt;
                            </code>
                        </div>
                    </div>
                </div>

                <!-- Scan Placeholder -->
                <div>
                    <h3 class="mb-4 font-mono text-xl font-semibold text-white">Scan Placeholder</h3>
                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                        Indicateur visuel de scan en cours pour les générations d'images, vidéos ou avatars. Style
                        Alien/sci-fi avec lignes de scan animées, grille de fond et spinner central.
                    </p>
                    <div
                        class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                        <div class="space-y-6">
                            <div>
                                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">Type Image (défaut) :</p>
                                <div class="h-48 w-full overflow-hidden rounded-lg">
                                    <x-scan-placeholder
                                        type="image"
                                        label="SCANNING_PLANETARY_SYSTEM: KEPLER-452B"
                                        class="h-full w-full"
                                    />
                                </div>
                            </div>
                            <div>
                                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">Type Video :</p>
                                <div class="h-48 w-full overflow-hidden rounded-lg">
                                    <x-scan-placeholder
                                        type="video"
                                        label="SCANNING_VIDEO: KEPLER-452B"
                                        class="h-full w-full"
                                    />
                                </div>
                            </div>
                            <div>
                                <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">Type Avatar :</p>
                                <div class="h-24 w-24 overflow-hidden rounded-lg">
                                    <x-scan-placeholder
                                        type="avatar"
                                        class="h-full w-full"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="mb-2 text-xs text-gray-500 dark:text-gray-500">Usage :</p>
                            <code class="text-space-primary bg-space-black block rounded px-2 py-1 font-mono text-xs">
                                &lt;x-scan-placeholder type="image"
                                :label="'SCANNING_PLANETARY_SYSTEM: ' . strtoupper($planet->name)" class="h-64 w-full"
                                /&gt;<br>
                                &lt;x-scan-placeholder type="video"
                                :label="'SCANNING_PLANETARY_SYSTEM: ' . strtoupper($planet->name)" /&gt;<br>
                                &lt;x-scan-placeholder type="avatar"
                                :label="'SCANNING_PILOT_PROFIL: ' . strtoupper($pilot->name)" /&gt;
                            </code>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </x-design-system.layout>
@endsection
