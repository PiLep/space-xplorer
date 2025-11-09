@extends('layouts.app')

@section('title', 'Design System - Space Xplorer')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Hero Section -->
    <div class="mb-16 text-center">
        <h1 class="text-5xl font-bold text-white mb-4">
            Design System
        </h1>
        <p class="text-xl text-gray-400 mb-8 max-w-3xl mx-auto">
            Documentation complète du design system de Space Xplorer. 
            Esthétique rétro-futuriste inspirée des films Alien.
        </p>
    </div>

    <!-- Colors Section -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold text-white mb-8">Palette de Couleurs</h2>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Space Black -->
            <div class="bg-[#1a1a1a] rounded-lg p-6 border border-[#333333]">
                <div class="h-24 rounded mb-4 bg-[#0a0a0a]"></div>
                <h3 class="text-white font-semibold mb-2">Space Black</h3>
                <p class="text-gray-400 text-sm">#0a0a0a</p>
                <p class="text-gray-500 text-xs mt-2">Fond principal</p>
            </div>

            <!-- Primary -->
            <div class="bg-[#1a1a1a] rounded-lg p-6 border border-[#333333]">
                <div class="h-24 rounded mb-4 bg-[#00ff88]"></div>
                <h3 class="text-white font-semibold mb-2">Primary</h3>
                <p class="text-gray-400 text-sm">#00ff88</p>
                <p class="text-gray-500 text-xs mt-2">Actions principales</p>
            </div>

            <!-- Secondary -->
            <div class="bg-[#1a1a1a] rounded-lg p-6 border border-[#333333]">
                <div class="h-24 rounded mb-4 bg-[#00aaff]"></div>
                <h3 class="text-white font-semibold mb-2">Secondary</h3>
                <p class="text-gray-400 text-sm">#00aaff</p>
                <p class="text-gray-500 text-xs mt-2">Actions secondaires</p>
            </div>

            <!-- Accent -->
            <div class="bg-[#1a1a1a] rounded-lg p-6 border border-[#333333]">
                <div class="h-24 rounded mb-4 bg-[#ffaa00]"></div>
                <h3 class="text-white font-semibold mb-2">Accent</h3>
                <p class="text-gray-400 text-sm">#ffaa00</p>
                <p class="text-gray-500 text-xs mt-2">Alertes importantes</p>
            </div>
        </div>

        <!-- Semantic Colors -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-[#1a1a1a] rounded-lg p-6 border border-[#333333]">
                <div class="h-24 rounded mb-4 bg-[#00ff88]"></div>
                <h3 class="text-white font-semibold mb-2">Success</h3>
                <p class="text-gray-400 text-sm">#00ff88</p>
            </div>

            <div class="bg-[#1a1a1a] rounded-lg p-6 border border-[#333333]">
                <div class="h-24 rounded mb-4 bg-[#ff4444]"></div>
                <h3 class="text-white font-semibold mb-2">Error</h3>
                <p class="text-gray-400 text-sm">#ff4444</p>
            </div>

            <div class="bg-[#1a1a1a] rounded-lg p-6 border border-[#333333]">
                <div class="h-24 rounded mb-4 bg-[#ffaa00]"></div>
                <h3 class="text-white font-semibold mb-2">Warning</h3>
                <p class="text-gray-400 text-sm">#ffaa00</p>
            </div>

            <div class="bg-[#1a1a1a] rounded-lg p-6 border border-[#333333]">
                <div class="h-24 rounded mb-4 bg-[#00aaff]"></div>
                <h3 class="text-white font-semibold mb-2">Info</h3>
                <p class="text-gray-400 text-sm">#00aaff</p>
            </div>
        </div>
    </section>

    <!-- Typography Section -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold text-white mb-8">Typographie</h2>
        
        <div class="bg-[#1a1a1a] rounded-lg p-8 border border-[#333333]">
            <div class="space-y-6">
                <div>
                    <h1 class="text-4xl font-bold tracking-tight text-white mb-2">Heading 1</h1>
                    <p class="text-sm text-gray-500">2.5rem (40px) - Font Bold - Tracking Tight</p>
                </div>
                
                <div>
                    <h2 class="text-3xl font-bold tracking-tight text-white mb-2">Heading 2</h2>
                    <p class="text-sm text-gray-500">2rem (32px) - Font Bold - Tracking Tight</p>
                </div>
                
                <div>
                    <h3 class="text-2xl font-semibold text-white mb-2">Heading 3</h3>
                    <p class="text-sm text-gray-500">1.5rem (24px) - Font Semibold</p>
                </div>
                
                <div>
                    <p class="text-base text-gray-300 mb-2">Body Text - Regular paragraph text for content</p>
                    <p class="text-sm text-gray-500">1rem (16px) - Font Normal - Line Height 1.6</p>
                </div>
                
                <div>
                    <p class="text-lg text-gray-300 mb-2">Body Large - Important text and descriptions</p>
                    <p class="text-sm text-gray-500">1.125rem (18px) - Font Normal</p>
                </div>
                
                <div>
                    <p class="text-sm text-gray-400 mb-2">Small Text - Secondary text and metadata</p>
                    <p class="text-sm text-gray-500">0.875rem (14px) - Font Normal</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Visual Effects Section -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold text-white mb-8 dark:text-glow-subtle">Effets Visuels Alien</h2>
        <p class="text-gray-400 mb-8">
            Effets spéciaux inspirés de l'esthétique des films Alien pour créer une ambiance rétro-futuriste immersive.
        </p>
        
        <div class="space-y-8">
            <!-- Text Glow Effects -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4">Text Glow (Lueur de Texte)</h3>
                <div class="bg-[#1a1a1a] rounded-lg p-8 border border-[#333333] space-y-4">
                    <div>
                        <h4 class="text-2xl font-bold text-white dark:text-glow-subtle mb-2">Text Glow Subtle</h4>
                        <p class="text-sm text-gray-500">Lueur subtile pour les titres principaux - <code class="text-[#00ff88]">text-glow-subtle</code></p>
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold text-[#00ff88] text-glow-primary mb-2">Text Glow Primary</h4>
                        <p class="text-sm text-gray-500">Lueur verte fluorescente - <code class="text-[#00ff88]">text-glow-primary</code></p>
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold text-[#00aaff] text-glow-secondary mb-2">Text Glow Secondary</h4>
                        <p class="text-sm text-gray-500">Lueur bleue fluorescente - <code class="text-[#00ff88]">text-glow-secondary</code></p>
                    </div>
                </div>
            </div>

            <!-- Box Glow Effects -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4">Box Glow (Lueur de Boîte)</h3>
                <div class="bg-[#1a1a1a] rounded-lg p-8 border border-[#333333]">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-[#0a0a0a] rounded-lg p-6 glow-primary">
                            <h4 class="text-white font-semibold mb-2">Glow Primary</h4>
                            <p class="text-gray-400 text-sm">Lueur verte fluorescente autour de l'élément</p>
                            <p class="text-xs text-gray-500 mt-2"><code class="text-[#00ff88]">glow-primary</code></p>
                        </div>
                        <div class="bg-[#0a0a0a] rounded-lg p-6 glow-secondary">
                            <h4 class="text-white font-semibold mb-2">Glow Secondary</h4>
                            <p class="text-gray-400 text-sm">Lueur bleue fluorescente autour de l'élément</p>
                            <p class="text-xs text-gray-500 mt-2"><code class="text-[#00ff88]">glow-secondary</code></p>
                        </div>
                        <div class="bg-[#0a0a0a] rounded-lg p-6 glow-border-primary border border-[#333333]">
                            <h4 class="text-white font-semibold mb-2">Glow Border Primary</h4>
                            <p class="text-gray-400 text-sm">Lueur sur les bordures de l'élément</p>
                            <p class="text-xs text-gray-500 mt-2"><code class="text-[#00ff88]">glow-border-primary</code></p>
                        </div>
                        <div class="bg-[#0a0a0a] rounded-lg p-6 pulse-glow border border-[#333333]">
                            <h4 class="text-white font-semibold mb-2">Pulse Glow</h4>
                            <p class="text-gray-400 text-sm">Animation de pulsation avec lueur</p>
                            <p class="text-xs text-gray-500 mt-2"><code class="text-[#00ff88]">pulse-glow</code></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scan Effect -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4">Scan Effect (Effet de Balayage)</h3>
                <div class="bg-[#1a1a1a] rounded-lg p-8 border border-[#333333]">
                    <div class="bg-[#0a0a0a] rounded-lg p-6 scan-effect relative overflow-hidden">
                        <h4 class="text-white font-semibold mb-2">Scan Effect</h4>
                        <p class="text-gray-400 text-sm mb-4">Animation de ligne de balayage qui descend continuellement</p>
                        <p class="text-xs text-gray-500"><code class="text-[#00ff88]">scan-effect</code></p>
                        <p class="text-xs text-gray-500 mt-2">Évoque les écrans CRT des vaisseaux spatiaux</p>
                    </div>
                </div>
            </div>

            <!-- Hologram Effect -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4">Hologram Effect (Effet Holographique)</h3>
                <div class="bg-[#1a1a1a] rounded-lg p-8 border border-[#333333]">
                    <div class="bg-[#0a0a0a] rounded-lg p-6 hologram relative">
                        <h4 class="text-white font-semibold mb-2">Hologram Effect</h4>
                        <p class="text-gray-400 text-sm mb-4">Effet holographique avec flicker subtil et dégradé de couleur</p>
                        <p class="text-xs text-gray-500"><code class="text-[#00ff88]">hologram</code></p>
                        <p class="text-xs text-gray-500 mt-2">Animation de luminosité subtile pour simuler un affichage holographique</p>
                    </div>
                </div>
            </div>

            <!-- Buttons with Glow -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4">Boutons avec Effets</h3>
                <div class="bg-[#1a1a1a] rounded-lg p-8 border border-[#333333]">
                    <div class="flex flex-wrap gap-4">
                        <button class="bg-[#00ff88] hover:bg-[#00cc6a] text-[#0a0a0a] font-bold py-3 px-6 rounded-lg transition-colors duration-150 glow-primary hover:glow-primary">
                            Primary avec Glow
                        </button>
                        <button class="bg-[#00aaff] hover:bg-[#0088cc] text-white font-bold py-3 px-6 rounded-lg transition-colors duration-150 glow-secondary hover:glow-secondary">
                            Secondary avec Glow
                        </button>
                        <button class="bg-transparent hover:bg-[#2a2a2a] text-gray-400 hover:text-white font-bold py-3 px-6 rounded-lg transition-colors duration-150 border border-[#333333] dark:hover:glow-border-primary">
                            Ghost avec Border Glow
                        </button>
                    </div>
                </div>
            </div>

            <!-- Combined Effects -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4">Effets Combinés</h3>
                <div class="bg-[#1a1a1a] rounded-lg p-8 border border-[#333333]">
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Card with scan and hologram -->
                        <div class="bg-[#0a0a0a] rounded-lg p-6 border border-[#333333] scan-effect hologram">
                            <h4 class="text-white font-semibold mb-2 dark:text-glow-subtle">Card avec Scan + Hologram</h4>
                            <p class="text-gray-400 text-sm">Combinaison de scan-effect et hologram pour un effet immersif</p>
                        </div>
                        
                        <!-- Card with glow -->
                        <div class="bg-[#0a0a0a] rounded-lg p-6 border border-[#333333] glow-border-primary">
                            <h4 class="text-white font-semibold mb-2 dark:text-glow-subtle">Card avec Border Glow</h4>
                            <p class="text-gray-400 text-sm">Bordure avec lueur pour mettre en évidence</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Global Effects Info -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4">Effets Globaux</h3>
                <div class="bg-[#1a1a1a] rounded-lg p-8 border border-[#333333]">
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-white font-semibold mb-2">Scanlines (Lignes de Balayage CRT)</h4>
                            <p class="text-gray-400 text-sm mb-2">
                                Effet appliqué globalement sur le body pour évoquer les écrans CRT des vaisseaux spatiaux.
                            </p>
                            <p class="text-xs text-gray-500"><code class="text-[#00ff88]">scanlines</code> sur le body</p>
                        </div>
                        <div>
                            <h4 class="text-white font-semibold mb-2">Grain (Texture de Grain)</h4>
                            <p class="text-gray-400 text-sm mb-2">
                                Texture de grain subtile appliquée globalement pour un rendu plus organique et cinématographique.
                            </p>
                            <p class="text-xs text-gray-500"><code class="text-[#00ff88]">grain</code> sur le body</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Components Section -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold text-white mb-8">Composants</h2>
        
        <div class="space-y-8">
            <!-- Buttons -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4">Boutons</h3>
                <div class="flex flex-wrap gap-4">
                    <button class="bg-[#00ff88] hover:bg-[#00cc6a] text-[#0a0a0a] font-bold py-3 px-6 rounded-lg transition-colors duration-150 glow-primary hover:glow-primary">
                        Primary
                    </button>
                    <button class="bg-[#00aaff] hover:bg-[#0088cc] text-white font-bold py-3 px-6 rounded-lg transition-colors duration-150 glow-secondary hover:glow-secondary">
                        Secondary
                    </button>
                    <button class="bg-[#ff4444] hover:bg-[#cc3333] text-white font-bold py-3 px-6 rounded-lg transition-colors duration-150">
                        Danger
                    </button>
                    <button class="bg-transparent hover:bg-[#2a2a2a] text-gray-400 hover:text-white font-bold py-3 px-6 rounded-lg transition-colors duration-150 border border-[#333333] dark:hover:glow-border-primary">
                        Ghost
                    </button>
                </div>
            </div>

            <!-- Cards -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4">Cards</h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-[#1a1a1a] border border-[#333333] rounded-lg p-6 scan-effect">
                        <h4 class="text-xl font-semibold text-white mb-2 dark:text-glow-subtle">Card Standard</h4>
                        <p class="text-gray-400">
                            Conteneur pour afficher des informations groupées avec fond sombre et bordures subtiles.
                        </p>
                    </div>
                    
                    <div class="bg-[#1a1a1a] border border-[#333333] rounded-lg p-6 hover:bg-[#2a2a2a] transition-colors duration-150 cursor-pointer hologram">
                        <h4 class="text-xl font-semibold text-white mb-2 dark:text-glow-subtle">Card Interactive</h4>
                        <p class="text-gray-400">
                            Card cliquable avec effet hover et hologram pour les interactions utilisateur.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Elements -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4">Formulaires</h3>
                <div class="bg-[#1a1a1a] border border-[#333333] rounded-lg p-6 max-w-md">
                    <div class="mb-4">
                        <label for="example-input" class="block text-gray-300 text-sm font-bold mb-2">
                            Label
                        </label>
                        <input
                            type="text"
                            id="example-input"
                            class="w-full py-2 px-3 bg-[#1a1a1a] border border-[#333333] rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#00ff88] focus:ring-offset-2 focus:ring-offset-[#0a0a0a] focus:border-[#00ff88] transition-all duration-150"
                            placeholder="Placeholder text"
                        >
                    </div>
                    <button class="bg-[#00ff88] hover:bg-[#00cc6a] text-[#0a0a0a] font-bold py-2 px-4 rounded-lg transition-colors duration-150 w-full glow-primary hover:glow-primary">
                        Submit
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Spacing Section -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold text-white mb-8">Espacements</h2>
        
        <div class="bg-[#1a1a1a] rounded-lg p-8 border border-[#333333]">
            <div class="space-y-4">
                <div class="flex items-center gap-4">
                    <div class="w-1 h-4 bg-[#00ff88]"></div>
                    <span class="text-gray-300">4px (xs)</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-2 h-4 bg-[#00ff88]"></div>
                    <span class="text-gray-300">8px (sm)</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-3 h-4 bg-[#00ff88]"></div>
                    <span class="text-gray-300">12px (md)</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-4 h-4 bg-[#00ff88]"></div>
                    <span class="text-gray-300">16px (base)</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-6 h-4 bg-[#00ff88]"></div>
                    <span class="text-gray-300">24px (lg)</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-8 h-4 bg-[#00ff88]"></div>
                    <span class="text-gray-300">32px (xl)</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Design Principles -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold text-white mb-8">Principes de Design</h2>
        
        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-[#1a1a1a] border border-[#333333] rounded-lg p-6">
                <h3 class="text-xl font-semibold text-white mb-3">Rétro-Futurisme</h3>
                <p class="text-gray-400">
                    Esthétique inspirée des films Alien : interfaces monochromes avec accents fluorescents, 
                    ambiance industrielle des vaisseaux spatiaux.
                </p>
            </div>
            
            <div class="bg-[#1a1a1a] border border-[#333333] rounded-lg p-6">
                <h3 class="text-xl font-semibold text-white mb-3">Immersion</h3>
                <p class="text-gray-400">
                    Créer une atmosphère sombre et immersive qui transporte l'utilisateur dans l'univers spatial.
                </p>
            </div>
            
            <div class="bg-[#1a1a1a] border border-[#333333] rounded-lg p-6">
                <h3 class="text-xl font-semibold text-white mb-3">Cohérence</h3>
                <p class="text-gray-400">
                    Maintenir une cohérence visuelle à travers toute l'application avec des composants réutilisables.
                </p>
            </div>
            
            <div class="bg-[#1a1a1a] border border-[#333333] rounded-lg p-6">
                <h3 class="text-xl font-semibold text-white mb-3">Accessibilité</h3>
                <p class="text-gray-400">
                    Assurer l'accessibilité visuelle pour tous les utilisateurs avec des contrastes appropriés.
                </p>
            </div>
        </div>
    </section>

    <!-- Documentation Link -->
    <section class="text-center">
        <div class="rounded-lg p-12 bg-gradient-to-r from-[#00ff88] to-[#00aaff]">
            <h2 class="text-3xl font-bold mb-4 text-[#0a0a0a]">Documentation Complète</h2>
            <p class="text-lg mb-8 opacity-90 text-[#0a0a0a]">
                Consultez la documentation complète du design system dans le dossier <code class="bg-[#0a0a0a] text-[#00ff88] px-2 py-1 rounded">docs/design-system/</code>
            </p>
            <div class="flex justify-center gap-4">
                <a href="https://github.com/PiLep/space-xplorer/tree/feature/design-system/docs/design-system" 
                   target="_blank"
                   class="bg-[#0a0a0a] text-[#00ff88] hover:bg-[#1a1a1a] px-6 py-3 rounded-lg font-semibold transition-colors duration-150">
                    Voir la Documentation
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
