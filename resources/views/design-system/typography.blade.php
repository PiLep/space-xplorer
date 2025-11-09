@extends('layouts.app')

@section('title', 'Design System - Typography')

@section('content')
<x-design-system.layout>
    <section>
        <h2 class="text-3xl font-bold text-white mb-8 font-mono">TYPOGRAPHIE</h2>
        
        <div class="bg-white dark:bg-surface-dark rounded-lg p-8 border border-gray-200 dark:border-border-dark terminal-border-simple">
            <div class="space-y-6">
                <div>
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white mb-2 dark:text-glow-subtle">Heading 1</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-500">2.5rem (40px) - Font Bold - Tracking Tight</p>
                </div>
                
                <div>
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white mb-2 dark:text-glow-subtle">Heading 2</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-500">2rem (32px) - Font Bold - Tracking Tight</p>
                </div>
                
                <div>
                    <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2 dark:text-glow-subtle">Heading 3</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-500">1.5rem (24px) - Font Semibold</p>
                </div>
                
                <div>
                    <p class="text-base text-gray-700 dark:text-gray-300 mb-2">Body Text - Regular paragraph text for content</p>
                    <p class="text-sm text-gray-500 dark:text-gray-500">1rem (16px) - Font Normal - Line Height 1.6</p>
                </div>
                
                <div>
                    <p class="text-lg text-gray-700 dark:text-gray-300 mb-2">Body Large - Important text and descriptions</p>
                    <p class="text-sm text-gray-500 dark:text-gray-500">1.125rem (18px) - Font Normal</p>
                </div>
                
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Small Text - Secondary text and metadata</p>
                    <p class="text-sm text-gray-500 dark:text-gray-500">0.875rem (14px) - Font Normal</p>
                </div>

                <div class="pt-6 border-t border-gray-200 dark:border-border-dark">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 font-mono">FONTS</h4>
                    <div class="space-y-3">
                        <div>
                            <p class="text-base font-sans text-gray-700 dark:text-gray-300 mb-1">Orbitron (Sans-serif)</p>
                            <p class="text-sm text-gray-500 dark:text-gray-500">Font principale pour les titres et l'interface</p>
                        </div>
                        <div>
                            <p class="text-base font-mono text-gray-700 dark:text-gray-300 mb-1">Share Tech Mono (Monospace)</p>
                            <p class="text-sm text-gray-500 dark:text-gray-500">Font pour les éléments terminal et code</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-design-system.layout>
@endsection

