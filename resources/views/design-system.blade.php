@extends('layouts.app')

@section('title', 'Design System - Space Xplorer')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Hero Section -->
    <div class="mb-16 text-center">
        <h1 class="text-5xl font-bold text-gray-900 dark:text-white mb-4">
            Design System
        </h1>
        <p class="text-xl text-gray-600 dark:text-gray-400 mb-8 max-w-3xl mx-auto">
            Documentation complète du design system de Space Xplorer. 
            Esthétique rétro-futuriste inspirée des films Alien.
        </p>
    </div>

    <!-- Colors Section -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Palette de Couleurs</h2>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Space Black -->
            <div class="bg-gray-900 dark:bg-gray-950 rounded-lg p-6 border border-gray-800">
                <div class="h-24 rounded mb-4 bg-gray-950"></div>
                <h3 class="text-white font-semibold mb-2">Space Black</h3>
                <p class="text-gray-400 text-sm">#0a0a0a</p>
                <p class="text-gray-500 text-xs mt-2">Fond principal</p>
            </div>

            <!-- Primary -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="h-24 rounded mb-4" style="background-color: #00ff88;"></div>
                <h3 class="text-gray-900 dark:text-white font-semibold mb-2">Primary</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">#00ff88</p>
                <p class="text-gray-500 dark:text-gray-500 text-xs mt-2">Actions principales</p>
            </div>

            <!-- Secondary -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="h-24 rounded mb-4" style="background-color: #00aaff;"></div>
                <h3 class="text-gray-900 dark:text-white font-semibold mb-2">Secondary</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">#00aaff</p>
                <p class="text-gray-500 dark:text-gray-500 text-xs mt-2">Actions secondaires</p>
            </div>

            <!-- Accent -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="h-24 rounded mb-4" style="background-color: #ffaa00;"></div>
                <h3 class="text-gray-900 dark:text-white font-semibold mb-2">Accent</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">#ffaa00</p>
                <p class="text-gray-500 dark:text-gray-500 text-xs mt-2">Alertes importantes</p>
            </div>
        </div>

        <!-- Semantic Colors -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="h-24 rounded mb-4" style="background-color: #00ff88;"></div>
                <h3 class="text-gray-900 dark:text-white font-semibold mb-2">Success</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">#00ff88</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="h-24 rounded mb-4 bg-red-500"></div>
                <h3 class="text-gray-900 dark:text-white font-semibold mb-2">Error</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">#ff4444</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="h-24 rounded mb-4" style="background-color: #ffaa00;"></div>
                <h3 class="text-gray-900 dark:text-white font-semibold mb-2">Warning</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">#ffaa00</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="h-24 rounded mb-4" style="background-color: #00aaff;"></div>
                <h3 class="text-gray-900 dark:text-white font-semibold mb-2">Info</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">#00aaff</p>
            </div>
        </div>
    </section>

    <!-- Typography Section -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Typographie</h2>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg p-8 border border-gray-200 dark:border-gray-700">
            <div class="space-y-6">
                <div>
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white mb-2">Heading 1</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">2.5rem (40px) - Font Bold - Tracking Tight</p>
                </div>
                
                <div>
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white mb-2">Heading 2</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">2rem (32px) - Font Bold - Tracking Tight</p>
                </div>
                
                <div>
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">Heading 3</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">1.5rem (24px) - Font Semibold</p>
                </div>
                
                <div>
                    <p class="text-base text-gray-700 dark:text-gray-300 mb-2">Body Text - Regular paragraph text for content</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">1rem (16px) - Font Normal - Line Height 1.6</p>
                </div>
                
                <div>
                    <p class="text-lg text-gray-700 dark:text-gray-300 mb-2">Body Large - Important text and descriptions</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">1.125rem (18px) - Font Normal</p>
                </div>
                
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Small Text - Secondary text and metadata</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">0.875rem (14px) - Font Normal</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Components Section -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Composants</h2>
        
        <div class="space-y-8">
            <!-- Buttons -->
            <div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Boutons</h3>
                <div class="flex flex-wrap gap-4">
                    <button class="text-gray-900 dark:text-gray-900 font-bold py-3 px-6 rounded-lg transition-colors duration-150" style="background-color: #00ff88;">
                        Primary
                    </button>
                    <button class="text-white font-bold py-3 px-6 rounded-lg transition-colors duration-150" style="background-color: #00aaff;">
                        Secondary
                    </button>
                    <button class="bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-150">
                        Danger
                    </button>
                    <button class="bg-transparent hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-bold py-3 px-6 rounded-lg transition-colors duration-150 border border-gray-300 dark:border-gray-600">
                        Ghost
                    </button>
                </div>
            </div>

            <!-- Cards -->
            <div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Cards</h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                        <h4 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Card Standard</h4>
                        <p class="text-gray-600 dark:text-gray-400">
                            Conteneur pour afficher des informations groupées avec fond sombre et bordures subtiles.
                        </p>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 cursor-pointer">
                        <h4 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Card Interactive</h4>
                        <p class="text-gray-600 dark:text-gray-400">
                            Card cliquable avec effet hover pour les interactions utilisateur.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Elements -->
            <div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Formulaires</h3>
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 max-w-md">
                    <div class="mb-4">
                        <label for="example-input" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                            Label
                        </label>
                        <input
                            type="text"
                            id="example-input"
                            class="w-full py-2 px-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:border-blue-500 transition-all duration-150"
                            placeholder="Placeholder text"
                        >
                    </div>
                    <button class="text-gray-900 dark:text-gray-900 font-bold py-2 px-4 rounded-lg transition-colors duration-150 w-full" style="background-color: #00ff88;">
                        Submit
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Spacing Section -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Espacements</h2>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg p-8 border border-gray-200 dark:border-gray-700">
            <div class="space-y-4">
                <div class="flex items-center gap-4">
                    <div class="w-1 h-4 bg-blue-500"></div>
                    <span class="text-gray-700 dark:text-gray-300">4px (xs)</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-2 h-4 bg-blue-500"></div>
                    <span class="text-gray-700 dark:text-gray-300">8px (sm)</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-3 h-4 bg-blue-500"></div>
                    <span class="text-gray-700 dark:text-gray-300">12px (md)</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-4 h-4 bg-blue-500"></div>
                    <span class="text-gray-700 dark:text-gray-300">16px (base)</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-6 h-4 bg-blue-500"></div>
                    <span class="text-gray-700 dark:text-gray-300">24px (lg)</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-8 h-4 bg-blue-500"></div>
                    <span class="text-gray-700 dark:text-gray-300">32px (xl)</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Design Principles -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Principes de Design</h2>
        
        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Rétro-Futurisme</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Esthétique inspirée des films Alien : interfaces monochromes avec accents fluorescents, 
                    ambiance industrielle des vaisseaux spatiaux.
                </p>
            </div>
            
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Immersion</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Créer une atmosphère sombre et immersive qui transporte l'utilisateur dans l'univers spatial.
                </p>
            </div>
            
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Cohérence</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Maintenir une cohérence visuelle à travers toute l'application avec des composants réutilisables.
                </p>
            </div>
            
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Accessibilité</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Assurer l'accessibilité visuelle pour tous les utilisateurs avec des contrastes appropriés.
                </p>
            </div>
        </div>
    </section>

    <!-- Documentation Link -->
    <section class="text-center">
        <div class="rounded-lg p-12 text-white" style="background: linear-gradient(to right, #00ff88, #00aaff);">
            <h2 class="text-3xl font-bold mb-4 text-gray-900">Documentation Complète</h2>
            <p class="text-lg mb-8 opacity-90 text-gray-900">
                Consultez la documentation complète du design system dans le dossier <code class="bg-gray-900 text-[#00ff88] px-2 py-1 rounded">docs/design-system/</code>
            </p>
            <div class="flex justify-center gap-4">
                <a href="https://github.com/PiLep/space-xplorer/tree/feature/design-system/docs/design-system" 
                   target="_blank"
                   class="bg-gray-900 text-[#00ff88] hover:bg-gray-800 px-6 py-3 rounded-lg font-semibold transition-colors duration-150">
                    Voir la Documentation
                </a>
            </div>
        </div>
    </section>
</div>
@endsection

