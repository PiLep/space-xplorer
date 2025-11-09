@extends('layouts.app')

@section('title', 'Design System - Colors')

@section('content')
<x-design-system.layout>
    <section>
        <h2 class="text-3xl font-bold text-white mb-8 font-mono">PALETTE_DE_COULEURS</h2>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Space Black -->
            <div class="bg-white dark:bg-surface-dark rounded-lg p-6 border border-gray-200 dark:border-border-dark terminal-border-simple">
                <div class="h-24 rounded mb-4 bg-space-black"></div>
                <h3 class="text-gray-900 dark:text-white font-semibold mb-2">Space Black</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">#0a0a0a</p>
                <p class="text-gray-500 dark:text-gray-500 text-xs mt-2">Fond principal</p>
            </div>

            <!-- Primary -->
            <div class="bg-white dark:bg-surface-dark rounded-lg p-6 border border-gray-200 dark:border-border-dark terminal-border-simple">
                <div class="h-24 rounded mb-4 bg-space-primary"></div>
                <h3 class="text-gray-900 dark:text-white font-semibold mb-2">Primary</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">#00ff88</p>
                <p class="text-gray-500 dark:text-gray-500 text-xs mt-2">Actions principales</p>
            </div>

            <!-- Secondary -->
            <div class="bg-white dark:bg-surface-dark rounded-lg p-6 border border-gray-200 dark:border-border-dark terminal-border-simple">
                <div class="h-24 rounded mb-4 bg-space-secondary"></div>
                <h3 class="text-gray-900 dark:text-white font-semibold mb-2">Secondary</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">#00aaff</p>
                <p class="text-gray-500 dark:text-gray-500 text-xs mt-2">Actions secondaires</p>
            </div>

            <!-- Accent -->
            <div class="bg-white dark:bg-surface-dark rounded-lg p-6 border border-gray-200 dark:border-border-dark terminal-border-simple">
                <div class="h-24 rounded mb-4 bg-space-accent"></div>
                <h3 class="text-gray-900 dark:text-white font-semibold mb-2">Accent</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">#ffaa00</p>
                <p class="text-gray-500 dark:text-gray-500 text-xs mt-2">Alertes importantes</p>
            </div>
        </div>

        <!-- Semantic Colors -->
        <div>
            <h3 class="text-2xl font-bold text-white mb-6 font-mono">COULEURS_SEMANTIQUES</h3>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-surface-dark rounded-lg p-6 border border-gray-200 dark:border-border-dark terminal-border-simple">
                    <div class="h-24 rounded mb-4 bg-success"></div>
                    <h4 class="text-gray-900 dark:text-white font-semibold mb-2">Success</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">#00ff88</p>
                </div>

                <div class="bg-white dark:bg-surface-dark rounded-lg p-6 border border-gray-200 dark:border-border-dark terminal-border-simple">
                    <div class="h-24 rounded mb-4 bg-error"></div>
                    <h4 class="text-gray-900 dark:text-white font-semibold mb-2">Error</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">#ff4444</p>
                </div>

                <div class="bg-white dark:bg-surface-dark rounded-lg p-6 border border-gray-200 dark:border-border-dark terminal-border-simple">
                    <div class="h-24 rounded mb-4 bg-warning"></div>
                    <h4 class="text-gray-900 dark:text-white font-semibold mb-2">Warning</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">#ffaa00</p>
                </div>

                <div class="bg-white dark:bg-surface-dark rounded-lg p-6 border border-gray-200 dark:border-border-dark terminal-border-simple">
                    <div class="h-24 rounded mb-4 bg-info"></div>
                    <h4 class="text-gray-900 dark:text-white font-semibold mb-2">Info</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">#00aaff</p>
                </div>
            </div>
        </div>
    </section>
</x-design-system.layout>
@endsection

