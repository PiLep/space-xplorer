@extends('layouts.app')

@section('title', 'Design System - Space Xplorer')

@section('content')
<x-design-system.layout>
    <!-- Hero Section -->
    <div class="mb-12 text-center">
        <h2 class="text-4xl font-bold text-white mb-4 dark:text-glow-subtle font-mono">
            OVERVIEW
        </h2>
        <p class="text-xl text-gray-400 mb-8 max-w-3xl mx-auto">
            Documentation complète du design system de Space Xplorer. 
            Esthétique rétro-futuriste inspirée des films Alien.
        </p>
    </div>

    <!-- Design Principles -->
    <section class="mb-12">
        <h3 class="text-2xl font-bold text-white mb-6 font-mono">PRINCIPES_DE_DESIGN</h3>
        
        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                <h4 class="text-xl font-semibold text-gray-900 dark:text-white mb-3 dark:text-glow-subtle font-mono">Rétro-Futurisme</h4>
                <p class="text-gray-600 dark:text-gray-400">
                    Esthétique inspirée des films Alien : interfaces monochromes avec accents fluorescents, 
                    ambiance industrielle des vaisseaux spatiaux.
                </p>
            </div>
            
            <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                <h4 class="text-xl font-semibold text-gray-900 dark:text-white mb-3 dark:text-glow-subtle font-mono">Immersion</h4>
                <p class="text-gray-600 dark:text-gray-400">
                    Créer une atmosphère sombre et immersive qui transporte l'utilisateur dans l'univers spatial.
                </p>
            </div>
            
            <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                <h4 class="text-xl font-semibold text-gray-900 dark:text-white mb-3 dark:text-glow-subtle font-mono">Cohérence</h4>
                <p class="text-gray-600 dark:text-gray-400">
                    Maintenir une cohérence visuelle à travers toute l'application avec des composants réutilisables.
                </p>
            </div>
            
            <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                <h4 class="text-xl font-semibold text-gray-900 dark:text-white mb-3 dark:text-glow-subtle font-mono">Accessibilité</h4>
                <p class="text-gray-600 dark:text-gray-400">
                    Assurer l'accessibilité visuelle pour tous les utilisateurs avec des contrastes appropriés.
                </p>
            </div>
        </div>
    </section>

    <!-- Quick Links -->
    <section class="mb-12">
        <h3 class="text-2xl font-bold text-white mb-6 font-mono">NAVIGATION_RAPIDE</h3>
        
        <div class="grid md:grid-cols-3 gap-4">
            <a href="{{ route('design-system.colors') }}" class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 hover:bg-gray-50 dark:hover:bg-surface-medium transition-colors terminal-border-simple group">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-space-primary dark:group-hover:text-space-primary font-mono">COLORS</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">Palette de couleurs complète</p>
            </a>
            
            <a href="{{ route('design-system.typography') }}" class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 hover:bg-gray-50 dark:hover:bg-surface-medium transition-colors terminal-border-simple group">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-space-primary dark:group-hover:text-space-primary font-mono">TYPOGRAPHY</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">Typographie et hiérarchie</p>
            </a>
            
            <a href="{{ route('design-system.components') }}" class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 hover:bg-gray-50 dark:hover:bg-surface-medium transition-colors terminal-border-simple group">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-space-primary dark:group-hover:text-space-primary font-mono">COMPONENTS</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">Composants réutilisables</p>
            </a>
            
            <a href="{{ route('design-system.spacing') }}" class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 hover:bg-gray-50 dark:hover:bg-surface-medium transition-colors terminal-border-simple group">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-space-primary dark:group-hover:text-space-primary font-mono">SPACING</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">Espacements et grilles</p>
            </a>
            
            <a href="{{ route('design-system.effects') }}" class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 hover:bg-gray-50 dark:hover:bg-surface-medium transition-colors terminal-border-simple group">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-space-primary dark:group-hover:text-space-primary font-mono">VISUAL_EFFECTS</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">Effets visuels Alien</p>
            </a>
            
            <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 font-mono">DOCUMENTATION</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Consultez la documentation complète dans le dossier <code class="text-space-primary dark:text-space-primary">docs/design-system/</code></p>
                <a href="https://github.com/PiLep/space-xplorer/tree/main/docs/design-system" 
                   target="_blank"
                   class="inline-block text-space-secondary dark:text-space-secondary hover:text-space-secondary-light dark:hover:text-space-secondary-light text-sm font-mono">
                    > VIEW_DOCS
                </a>
            </div>
        </div>
    </section>
</x-design-system.layout>
@endsection

