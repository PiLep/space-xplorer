@extends('layouts.app')

@section('title', 'Design System - Components - Terminal')

@section('content')
<x-design-system.layout>
    <section>
        <div class="mb-6">
            <a href="{{ route('design-system.components') }}" class="text-space-secondary hover:text-space-secondary-light dark:text-space-secondary dark:hover:text-space-secondary-light font-mono text-sm mb-4 inline-block">
                ← Retour aux composants
            </a>
            <h2 class="text-3xl font-bold text-white mb-2 font-mono">COMPOSANTS_TERMINAL</h2>
            <p class="text-gray-600 dark:text-gray-400">
                Interfaces de type console pour créer l'ambiance spatiale
            </p>
        </div>
        
        <div class="space-y-8">
            <!-- Terminal Prompt -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Terminal Prompt</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    Ligne de commande terminal avec prompt système pour créer l'ambiance spatiale.
                </p>
                <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                    <div class="bg-space-black rounded-lg p-4 font-mono">
                        <x-terminal-prompt command="load_user_session" />
                        <x-terminal-prompt command="display_home_planet" />
                        <x-terminal-prompt command="query_planet_data" />
                    </div>
                    <div class="mt-4">
                        <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                        <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                            &lt;x-terminal-prompt command="command_name" /&gt;
                        </code>
                    </div>
                </div>
            </div>

            <!-- Terminal Boot -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Terminal Boot</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    Séquence de messages de démarrage système avec animations et effets de fade-out.
                </p>
                <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                    <div class="bg-space-black rounded-lg p-4 font-mono">
                        @php
                            $exampleBootMessages = [
                                '[INIT] Initializing terminal interface...',
                                '[OK] Terminal initialized',
                                '[LOAD] Connecting to database...',
                                '[OK] Database connection established',
                            ];
                        @endphp
                        <x-terminal-boot 
                            :bootMessages="$exampleBootMessages"
                            :terminalBooted="true"
                            :showPrompt="true"
                        />
                    </div>
                    <div class="mt-4">
                        <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                        <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                            &lt;x-terminal-boot :bootMessages="$bootMessages" :terminalBooted="$terminalBooted" :pollMethod="'nextBootStep'" /&gt;
                        </code>
                    </div>
                </div>
            </div>

            <!-- Terminal Message -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Terminal Message</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    Messages système avec style terminal et détection automatique du type. Le composant détecte automatiquement le préfixe ([OK], [ERROR], [INFO], etc.) et applique la couleur appropriée.
                </p>
                <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                    <div class="bg-space-black rounded-lg p-4 font-mono space-y-2">
                        <x-terminal-message message="[OK] System initialized" />
                        <x-terminal-message message="[SUCCESS] Operation completed successfully" />
                        <x-terminal-message message="[READY] System ready for commands" />
                        <x-terminal-message message="[ERROR] Failed to connect to database" />
                        <x-terminal-message message="[INFO] Please provide your credentials" />
                        <x-terminal-message message="[WAIT] Initializing dashboard..." />
                        <x-terminal-message message="[LOADING] Processing request..." />
                    </div>
                    <div class="mt-4">
                        <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Types supportés :</p>
                        <ul class="text-xs text-gray-500 dark:text-gray-500 space-y-1 mb-4 font-mono">
                            <li>• <span class="text-space-primary">[OK]</span>, <span class="text-space-primary">[SUCCESS]</span>, <span class="text-space-primary">[READY]</span> → Vert (success)</li>
                            <li>• <span class="text-error">[ERROR]</span> → Rouge (error)</li>
                            <li>• <span class="text-space-secondary">[INFO]</span> → Bleu (info)</li>
                            <li>• <span class="text-gray-500">[WAIT]</span>, <span class="text-gray-500">[LOADING]</span> → Gris (wait)</li>
                        </ul>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                        <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                            &lt;x-terminal-message message="[OK] System initialized" /&gt;<br>
                            &lt;x-terminal-message message="[ERROR] Connection failed" marginBottom="mb-4" /&gt;
                        </code>
                    </div>
                </div>
            </div>

            <!-- Terminal Link -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Terminal Link</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    Lien avec style terminal pour les interfaces terminal. Format de commande avec préfixe `>`.
                </p>
                <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                    <div class="bg-space-black rounded-lg p-4 font-mono">
                        <x-terminal-message message="[INFO] New user? Create an account:" />
                        <x-terminal-link href="#" text="> REGISTER_NEW_USER" marginTop="mt-2" :showBorder="false" />
                    </div>
                    <div class="mt-4">
                        <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                        <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                            &lt;x-terminal-link href="{{ route('register') }}" text="> REGISTER_NEW_USER" /&gt;
                        </code>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-design-system.layout>
@endsection

