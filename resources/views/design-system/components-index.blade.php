@extends('layouts.app')

@section('title', 'Design System - Components')

@section('content')
<x-design-system.layout>
    <section>
        <h2 class="text-3xl font-bold text-white mb-8 font-mono">COMPOSANTS</h2>
        
        <p class="text-gray-600 dark:text-gray-400 mb-8">
            Explorez les composants réutilisables du design system organisés par catégorie.
        </p>
        
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Composants de Base -->
            <a href="{{ route('design-system.components.base') }}" class="group">
                <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple hover:bg-gray-50 dark:hover:bg-surface-medium transition-colors cursor-pointer">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2 dark:text-glow-subtle font-mono group-hover:text-space-primary dark:group-hover:text-space-primary transition-colors">
                        > COMPOSANTS_DE_BASE
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Éléments fondamentaux réutilisables : Button, Form Input, Form Card, Form Link, Page Header, Card, Alert
                    </p>
                    <p class="text-xs text-space-secondary dark:text-space-secondary font-mono">
                        7 composants →
                    </p>
                </div>
            </a>
            
            <!-- Composants Terminal -->
            <a href="{{ route('design-system.components.terminal') }}" class="group">
                <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple hover:bg-gray-50 dark:hover:bg-surface-medium transition-colors cursor-pointer">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2 dark:text-glow-subtle font-mono group-hover:text-space-primary dark:group-hover:text-space-primary transition-colors">
                        > COMPOSANTS_TERMINAL
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Interfaces de type console : Terminal Prompt, Terminal Boot, Terminal Message, Terminal Link
                    </p>
                    <p class="text-xs text-space-secondary dark:text-space-secondary font-mono">
                        4 composants →
                    </p>
                </div>
            </a>
            
            <!-- Composants Spécialisés -->
            <a href="{{ route('design-system.components.specialized') }}" class="group">
                <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple hover:bg-gray-50 dark:hover:bg-surface-medium transition-colors cursor-pointer">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2 dark:text-glow-subtle font-mono group-hover:text-space-primary dark:group-hover:text-space-primary transition-colors">
                        > COMPOSANTS_SPECIALISES
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Composants spécifiques au projet : Planet Card, Loading Spinner, Scan Placeholder
                    </p>
                    <p class="text-xs text-space-secondary dark:text-space-secondary font-mono">
                        3 composants →
                    </p>
                </div>
            </a>
            
            <!-- Composants Utilitaires -->
            <a href="{{ route('design-system.components.utilities') }}" class="group">
                <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple hover:bg-gray-50 dark:hover:bg-surface-medium transition-colors cursor-pointer">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2 dark:text-glow-subtle font-mono group-hover:text-space-primary dark:group-hover:text-space-primary transition-colors">
                        > COMPOSANTS_UTILITAIRES
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Composants d'organisation : Button Group, Navigation, Modal
                    </p>
                    <p class="text-xs text-space-secondary dark:text-space-secondary font-mono">
                        3 composants →
                    </p>
                </div>
            </a>
        </div>
    </section>
</x-design-system.layout>
@endsection

