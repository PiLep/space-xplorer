@extends('layouts.app')

@section('title', 'Design System - Logo Preview')

@section('content')
<x-design-system.layout>
    <section>
        <h2 class="text-3xl font-bold text-white mb-8 font-mono">LOGO_PREVIEW</h2>
        
        <p class="text-gray-600 dark:text-gray-400 mb-8">
            Aperçu du composant Logo avec toutes ses variantes de taille et options.
        </p>

        <!-- Variantes de Taille -->
        <div class="mb-12">
            <h3 class="mb-6 font-mono text-xl font-semibold text-white">Variantes de Taille</h3>
            
            <div class="space-y-12 bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-8 terminal-border-simple">
                <!-- Extra Small -->
                <div class="border-b border-gray-200 dark:border-border-dark pb-8">
                    <h4 class="mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">Extra Small (xs)</h4>
                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">Usage: Barre de navigation en bas</p>
                    <div class="bg-space-black rounded-lg p-6 flex items-center justify-center">
                        <x-logo size="xs" :showScanlines="false" />
                    </div>
                </div>

                <!-- Small -->
                <div class="border-b border-gray-200 dark:border-border-dark pb-8">
                    <h4 class="mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">Small (sm)</h4>
                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">Usage: Navigation, footer</p>
                    <div class="bg-space-black rounded-lg p-6 flex items-center justify-center">
                        <x-logo size="sm" :showScanlines="true" />
                    </div>
                </div>

                <!-- Medium -->
                <div class="border-b border-gray-200 dark:border-border-dark pb-8">
                    <h4 class="mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">Medium (md)</h4>
                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">Usage: En-têtes de pages</p>
                    <div class="bg-space-black rounded-lg p-6 flex items-center justify-center">
                        <x-logo size="md" :showScanlines="true" />
                    </div>
                </div>

                <!-- Large -->
                <div class="border-b border-gray-200 dark:border-border-dark pb-8">
                    <h4 class="mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">Large (lg)</h4>
                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">Usage: Landing page principale</p>
                    <div class="bg-space-black rounded-lg p-6 flex items-center justify-center">
                        <x-logo size="lg" :showScanlines="true" />
                    </div>
                </div>

                <!-- Extra Large -->
                <div class="pb-8">
                    <h4 class="mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">Extra Large (xl)</h4>
                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">Usage: Hero sections</p>
                    <div class="bg-space-black rounded-lg p-6 flex items-center justify-center">
                        <x-logo size="xl" :showScanlines="true" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Options -->
        <div class="mb-12">
            <h3 class="mb-6 font-mono text-xl font-semibold text-white">Options</h3>
            
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Avec Scanlines -->
                <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                    <h4 class="mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">Avec Scanlines</h4>
                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">showScanlines="true" (défaut)</p>
                    <div class="bg-space-black rounded-lg p-6 flex items-center justify-center">
                        <x-logo size="lg" :showScanlines="true" />
                    </div>
                </div>

                <!-- Sans Scanlines -->
                <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                    <h4 class="mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">Sans Scanlines</h4>
                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">showScanlines="false"</p>
                    <div class="bg-space-black rounded-lg p-6 flex items-center justify-center">
                        <x-logo size="lg" :showScanlines="false" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Exemples d'Usage -->
        <div class="mb-12">
            <h3 class="mb-6 font-mono text-xl font-semibold text-white">Exemples d'Usage</h3>
            
            <div class="space-y-6">
                <!-- Landing Page -->
                <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                    <h4 class="mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">Landing Page</h4>
                    <div class="bg-space-black rounded-lg p-12 flex flex-col items-center justify-center">
                        <x-logo size="lg" :showScanlines="true" />
                        <p class="mt-6 text-sm text-gray-500 dark:text-gray-500 font-mono">[READY] System initialized</p>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                    <h4 class="mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">Navigation</h4>
                    <div class="bg-space-black rounded-lg p-4 flex items-center justify-between">
                        <x-logo size="sm" :showScanlines="false" />
                        <nav class="flex gap-4">
                            <a href="#" class="text-gray-400 hover:text-space-primary font-mono text-sm">HOME</a>
                            <a href="#" class="text-gray-400 hover:text-space-primary font-mono text-sm">DASHBOARD</a>
                        </nav>
                    </div>
                </div>

                <!-- Barre de Navigation en Bas -->
                <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                    <h4 class="mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">Barre de Navigation en Bas</h4>
                    <div class="bg-surface-dark dark:bg-surface-dark border-t border-border-dark dark:border-border-dark rounded-lg p-3">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('home') }}" class="flex-shrink-0">
                                <x-logo size="xs" :showScanlines="false" />
                            </a>
                            <span class="text-gray-500 dark:text-gray-500 text-sm font-mono">SYSTEM@STELLAR:~$</span>
                            <div class="flex items-center gap-4 text-sm">
                                <a href="#" class="text-space-primary dark:text-space-primary hover:text-space-primary-light dark:hover:text-space-primary-light font-mono">> DASHBOARD</a>
                                <a href="#" class="text-space-primary dark:text-space-primary hover:text-space-primary-light dark:hover:text-space-primary-light font-mono">> PROFILE</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Favicons -->
        <div class="mb-12">
            <h3 class="mb-6 font-mono text-xl font-semibold text-white">FAVICONS</h3>
            
            <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-8 terminal-border-simple">
                <div class="mb-6">
                    <h4 class="mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">Icônes Disponibles</h4>
                    <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                        Les favicons de Stellar sont conçus pour représenter l'identité visuelle dans les navigateurs, 
                        les écrans d'accueil mobiles, et les applications web progressives (PWA).
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Favicon Standard -->
                    <div class="bg-space-black rounded-lg p-6 flex flex-col items-center justify-center">
                        <img src="{{ asset('favicon-32x32.png') }}" alt="Favicon 32x32" class="mb-3">
                        <p class="text-xs text-gray-500 dark:text-gray-500 font-mono text-center">favicon-32x32.png</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 text-center mt-1">32x32px</p>
                    </div>

                    <!-- Favicon 16x16 -->
                    <div class="bg-space-black rounded-lg p-6 flex flex-col items-center justify-center">
                        <img src="{{ asset('favicon-16x16.png') }}" alt="Favicon 16x16" class="mb-3">
                        <p class="text-xs text-gray-500 dark:text-gray-500 font-mono text-center">favicon-16x16.png</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 text-center mt-1">16x16px</p>
                    </div>

                    <!-- Apple Touch Icon -->
                    <div class="bg-space-black rounded-lg p-6 flex flex-col items-center justify-center">
                        <img src="{{ asset('apple-touch-icon.png') }}" alt="Apple Touch Icon" class="mb-3 w-20 h-20 object-contain">
                        <p class="text-xs text-gray-500 dark:text-gray-500 font-mono text-center">apple-touch-icon.png</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 text-center mt-1">180x180px (iOS)</p>
                    </div>

                    <!-- Android Chrome 192 -->
                    <div class="bg-space-black rounded-lg p-6 flex flex-col items-center justify-center">
                        <img src="{{ asset('android-chrome-192x192.png') }}" alt="Android Chrome 192" class="mb-3 w-24 h-24 object-contain">
                        <p class="text-xs text-gray-500 dark:text-gray-500 font-mono text-center">android-chrome-192x192.png</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 text-center mt-1">192x192px (PWA)</p>
                    </div>

                    <!-- Android Chrome 512 -->
                    <div class="bg-space-black rounded-lg p-6 flex flex-col items-center justify-center">
                        <img src="{{ asset('android-chrome-512x512.png') }}" alt="Android Chrome 512" class="mb-3 w-24 h-24 object-contain">
                        <p class="text-xs text-gray-500 dark:text-gray-500 font-mono text-center">android-chrome-512x512.png</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 text-center mt-1">512x512px (PWA)</p>
                    </div>

                    <!-- Favicon ICO -->
                    <div class="bg-space-black rounded-lg p-6 flex flex-col items-center justify-center">
                        <img src="{{ asset('favicon.ico') }}" alt="Favicon ICO" class="mb-3 w-8 h-8">
                        <p class="text-xs text-gray-500 dark:text-gray-500 font-mono text-center">favicon.ico</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 text-center mt-1">16x16, 32x32px</p>
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-border-dark pt-6">
                    <h4 class="mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">Configuration PWA</h4>
                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                        Le fichier <code class="text-space-primary dark:text-space-primary">site.webmanifest</code> configure l'application comme Progressive Web App :
                    </p>
                    <div class="bg-space-black rounded-lg p-4 font-mono text-xs text-gray-400 overflow-x-auto">
                        <pre class="text-gray-400">{
  "name": "Stellar - Explore the Universe",
  "short_name": "Stellar",
  "theme_color": "#0a0a0a",
  "background_color": "#0a0a0a",
  "display": "standalone"
}</pre>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-border-dark">
                    <p class="text-xs text-gray-500 dark:text-gray-500 font-mono mb-2">
                        <span class="text-space-primary dark:text-space-primary">[INFO]</span> Tous les favicons sont automatiquement inclus dans le layout principal
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 font-mono mb-2">
                        <span class="text-space-primary dark:text-space-primary">[INFO]</span> Support complet pour iOS, Android et PWA
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 font-mono">
                        <span class="text-space-primary dark:text-space-primary">[INFO]</span> Voir <a href="{{ url('docs/design-system/BRANDING-FAVICONS.md') }}" target="_blank" class="text-space-secondary dark:text-space-secondary hover:underline">BRANDING-FAVICONS.md</a> pour la documentation complète
                    </p>
                </div>
            </div>
        </div>
    </section>
</x-design-system.layout>
@endsection

