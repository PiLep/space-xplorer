@extends('layouts.app')

@section('title', 'Design System - Spacing')

@section('content')
<x-design-system.layout>
    <section>
        <h2 class="text-3xl font-bold text-white mb-8 font-mono">ESPACEMENTS</h2>
        
        <div class="bg-white dark:bg-surface-dark rounded-lg p-8 border border-gray-200 dark:border-border-dark terminal-border-simple">
            <div class="space-y-4">
                <div class="flex items-center gap-4">
                    <div class="w-1 h-4 bg-space-primary"></div>
                    <span class="text-gray-700 dark:text-gray-300 font-mono">4px (xs)</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-2 h-4 bg-space-primary"></div>
                    <span class="text-gray-700 dark:text-gray-300 font-mono">8px (sm)</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-3 h-4 bg-space-primary"></div>
                    <span class="text-gray-700 dark:text-gray-300 font-mono">12px (md)</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-4 h-4 bg-space-primary"></div>
                    <span class="text-gray-700 dark:text-gray-300 font-mono">16px (base)</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-6 h-4 bg-space-primary"></div>
                    <span class="text-gray-700 dark:text-gray-300 font-mono">24px (lg)</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-8 h-4 bg-space-primary"></div>
                    <span class="text-gray-700 dark:text-gray-300 font-mono">32px (xl)</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-4 bg-space-primary"></div>
                    <span class="text-gray-700 dark:text-gray-300 font-mono">48px (2xl)</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-16 h-4 bg-space-primary"></div>
                    <span class="text-gray-700 dark:text-gray-300 font-mono">64px (3xl)</span>
                </div>
            </div>
        </div>

        <div class="mt-8">
            <h3 class="text-2xl font-bold text-white mb-6 font-mono">EXEMPLES_D_UTILISATION</h3>
            <div class="space-y-6">
                <div class="bg-white dark:bg-surface-dark rounded-lg p-6 border border-gray-200 dark:border-border-dark terminal-border-simple">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 font-mono">Padding Standard</h4>
                    <div class="bg-space-black p-6 rounded">
                        <p class="text-white text-sm font-mono">p-6 (24px padding)</p>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-surface-dark rounded-lg p-6 border border-gray-200 dark:border-border-dark terminal-border-simple">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 font-mono">Margin Bottom</h4>
                    <div class="space-y-4">
                        <div class="bg-space-primary p-4 rounded">
                            <p class="text-space-black text-sm font-mono">mb-8 (32px margin-bottom)</p>
                        </div>
                        <div class="bg-space-secondary p-4 rounded">
                            <p class="text-white text-sm font-mono">Section suivante</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-surface-dark rounded-lg p-6 border border-gray-200 dark:border-border-dark terminal-border-simple">
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 font-mono">Gap dans une Grille</h4>
                    <div class="grid grid-cols-3 gap-6">
                        <div class="bg-space-primary p-4 rounded text-center">
                            <p class="text-space-black text-xs font-mono">gap-6</p>
                        </div>
                        <div class="bg-space-primary p-4 rounded text-center">
                            <p class="text-space-black text-xs font-mono">gap-6</p>
                        </div>
                        <div class="bg-space-primary p-4 rounded text-center">
                            <p class="text-space-black text-xs font-mono">gap-6</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-design-system.layout>
@endsection

