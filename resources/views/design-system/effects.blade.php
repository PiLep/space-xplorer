@extends('layouts.app')

@section('title', 'Design System - Effects')

@section('content')
<x-design-system.layout>
    <section>
        <h2 class="text-3xl font-bold text-white mb-8 dark:text-glow-subtle font-mono">EFFETS_VISUELS_ALIEN</h2>
        <p class="text-gray-400 mb-8">
            Effets spéciaux inspirés de l'esthétique des films Alien pour créer une ambiance rétro-futuriste immersive.
        </p>
        
        <div class="space-y-8">
            <!-- Text Glow Effects -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Text Glow (Lueur de Texte)</h3>
                <div class="bg-white dark:bg-surface-dark rounded-lg p-8 border border-gray-200 dark:border-border-dark terminal-border-simple space-y-4">
                    <div>
                        <h4 class="text-2xl font-bold text-gray-900 dark:text-white dark:text-glow-subtle mb-2">Text Glow Subtle</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-500">Lueur subtile pour les titres principaux - <code class="text-space-primary">text-glow-subtle</code></p>
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold text-space-primary text-glow-primary mb-2">Text Glow Primary</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-500">Lueur verte fluorescente - <code class="text-space-primary">text-glow-primary</code></p>
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold text-space-secondary text-glow-secondary mb-2">Text Glow Secondary</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-500">Lueur bleue fluorescente - <code class="text-space-primary">text-glow-secondary</code></p>
                    </div>
                </div>
            </div>

            <!-- Box Glow Effects -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Box Glow (Lueur de Boîte)</h3>
                <div class="bg-white dark:bg-surface-dark rounded-lg p-8 border border-gray-200 dark:border-border-dark terminal-border-simple">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-space-black rounded-lg p-6 glow-primary">
                            <h4 class="text-white font-semibold mb-2">Glow Primary</h4>
                            <p class="text-gray-400 text-sm">Lueur verte fluorescente autour de l'élément</p>
                            <p class="text-xs text-gray-500 mt-2"><code class="text-space-primary">glow-primary</code></p>
                        </div>
                        <div class="bg-space-black rounded-lg p-6 glow-secondary">
                            <h4 class="text-white font-semibold mb-2">Glow Secondary</h4>
                            <p class="text-gray-400 text-sm">Lueur bleue fluorescente autour de l'élément</p>
                            <p class="text-xs text-gray-500 mt-2"><code class="text-space-primary">glow-secondary</code></p>
                        </div>
                        <div class="bg-space-black rounded-lg p-6 glow-border-primary border border-gray-300 dark:border-border-dark">
                            <h4 class="text-white font-semibold mb-2">Glow Border Primary</h4>
                            <p class="text-gray-400 text-sm">Lueur sur les bordures de l'élément</p>
                            <p class="text-xs text-gray-500 mt-2"><code class="text-space-primary">glow-border-primary</code></p>
                        </div>
                        <div class="bg-space-black rounded-lg p-6 pulse-glow border border-gray-300 dark:border-border-dark">
                            <h4 class="text-white font-semibold mb-2">Pulse Glow</h4>
                            <p class="text-gray-400 text-sm">Animation de pulsation avec lueur</p>
                            <p class="text-xs text-gray-500 mt-2"><code class="text-space-primary">pulse-glow</code></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scan Effect -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Scan Effect (Effet de Balayage)</h3>
                <div class="bg-white dark:bg-surface-dark rounded-lg p-8 border border-gray-200 dark:border-border-dark terminal-border-simple">
                    <div class="bg-space-black rounded-lg p-6 scan-effect relative overflow-hidden">
                        <h4 class="text-white font-semibold mb-2">Scan Effect</h4>
                        <p class="text-gray-400 text-sm mb-4">Animation de ligne de balayage qui descend continuellement</p>
                        <p class="text-xs text-gray-500"><code class="text-space-primary">scan-effect</code></p>
                        <p class="text-xs text-gray-500 mt-2">Évoque les écrans CRT des vaisseaux spatiaux</p>
                    </div>
                </div>
            </div>

            <!-- Hologram Effect -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Hologram Effect (Effet Holographique)</h3>
                <div class="bg-white dark:bg-surface-dark rounded-lg p-8 border border-gray-200 dark:border-border-dark terminal-border-simple">
                    <div class="bg-space-black rounded-lg p-6 hologram relative">
                        <h4 class="text-white font-semibold mb-2">Hologram Effect</h4>
                        <p class="text-gray-400 text-sm mb-4">Effet holographique avec flicker subtil et dégradé de couleur</p>
                        <p class="text-xs text-gray-500"><code class="text-space-primary">hologram</code></p>
                        <p class="text-xs text-gray-500 mt-2">Animation de luminosité subtile pour simuler un affichage holographique</p>
                    </div>
                </div>
            </div>

            <!-- Buttons with Glow -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Boutons avec Effets</h3>
                <div class="bg-white dark:bg-surface-dark rounded-lg p-8 border border-gray-200 dark:border-border-dark terminal-border-simple">
                    <div class="flex flex-wrap gap-4">
                        <button class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold py-3 px-6 rounded-lg transition-colors glow-primary hover:glow-primary font-mono text-sm">
                            Primary avec Glow
                        </button>
                        <button class="bg-space-secondary hover:bg-space-secondary-dark text-white font-bold py-3 px-6 rounded-lg transition-colors glow-secondary hover:glow-secondary font-mono text-sm">
                            Secondary avec Glow
                        </button>
                        <button class="bg-transparent hover:bg-gray-100 dark:hover:bg-surface-medium text-gray-700 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white font-bold py-3 px-6 rounded-lg transition-colors border border-gray-300 dark:border-border-dark dark:hover:glow-border-primary font-mono text-sm">
                            Ghost avec Border Glow
                        </button>
                    </div>
                </div>
            </div>

            <!-- Combined Effects -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Effets Combinés</h3>
                <div class="bg-white dark:bg-surface-dark rounded-lg p-8 border border-gray-200 dark:border-border-dark terminal-border-simple">
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Card with scan and hologram -->
                        <div class="bg-space-black rounded-lg p-6 border border-gray-300 dark:border-border-dark scan-effect hologram">
                            <h4 class="text-white font-semibold mb-2 dark:text-glow-subtle">Card avec Scan + Hologram</h4>
                            <p class="text-gray-400 text-sm">Combinaison de scan-effect et hologram pour un effet immersif</p>
                        </div>
                        
                        <!-- Card with glow -->
                        <div class="bg-space-black rounded-lg p-6 border border-gray-300 dark:border-border-dark glow-border-primary">
                            <h4 class="text-white font-semibold mb-2 dark:text-glow-subtle">Card avec Border Glow</h4>
                            <p class="text-gray-400 text-sm">Bordure avec lueur pour mettre en évidence</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Global Effects Info -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Effets Globaux</h3>
                <div class="bg-white dark:bg-surface-dark rounded-lg p-8 border border-gray-200 dark:border-border-dark terminal-border-simple">
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-gray-900 dark:text-white font-semibold mb-2">Scanlines (Lignes de Balayage CRT)</h4>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">
                                Effet appliqué globalement sur le body pour évoquer les écrans CRT des vaisseaux spatiaux.
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-500"><code class="text-space-primary">scanlines</code> sur le body</p>
                        </div>
                        <div>
                            <h4 class="text-gray-900 dark:text-white font-semibold mb-2">Grain (Texture de Grain)</h4>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">
                                Texture de grain subtile appliquée globalement pour un rendu plus organique et cinématographique.
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-500"><code class="text-space-primary">grain</code> sur le body</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-design-system.layout>
@endsection

