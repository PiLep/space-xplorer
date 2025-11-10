@extends('layouts.app')

@section('title', 'Design System - Components - Utilities')

@section('content')
<x-design-system.layout>
    <section>
        <div class="mb-6">
            <a href="{{ route('design-system.components') }}" class="text-space-secondary hover:text-space-secondary-light dark:text-space-secondary dark:hover:text-space-secondary-light font-mono text-sm mb-4 inline-block">
                ← Retour aux composants
            </a>
            <h2 class="text-3xl font-bold text-white mb-2 font-mono">COMPOSANTS_UTILITAIRES</h2>
            <p class="text-gray-600 dark:text-gray-400">
                Composants d'organisation et de mise en page
            </p>
        </div>
        
        <div class="space-y-8">
            <!-- Button Group -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Button Group</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    Groupe de boutons avec layout flexible. Supporte différents alignements et espacements.
                </p>
                <div class="space-y-6">
                    <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 dark:text-glow-subtle font-mono">Alignement Center (défaut)</h4>
                        <x-button-group>
                            <x-button variant="primary" size="lg" terminal>Action 1</x-button>
                            <x-button variant="secondary" size="lg" terminal>Action 2</x-button>
                        </x-button-group>
                    </div>
                    <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 dark:text-glow-subtle font-mono">Alignement Left</h4>
                        <x-button-group align="left">
                            <x-button variant="primary" size="lg" terminal>Action 1</x-button>
                            <x-button variant="secondary" size="lg" terminal>Action 2</x-button>
                        </x-button-group>
                    </div>
                    <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 dark:text-glow-subtle font-mono">Full Width</h4>
                        <x-button-group full-width spacing="sm">
                            <x-button variant="primary" size="lg" terminal class="flex-1">Action 1</x-button>
                            <x-button variant="secondary" size="lg" terminal class="flex-1">Action 2</x-button>
                        </x-button-group>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                    <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                        &lt;x-button-group align="center" spacing="md"&gt;<br>
                        &nbsp;&nbsp;&lt;x-button variant="primary"&gt;Action 1&lt;/x-button&gt;<br>
                        &nbsp;&nbsp;&lt;x-button variant="secondary"&gt;Action 2&lt;/x-button&gt;<br>
                        &lt;/x-button-group&gt;
                    </code>
                </div>
            </div>

            <!-- Navigation -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Navigation</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    Navigation principale avec style rétro-futuriste. Disponible en 3 variantes : Sidebar, Top Menu, Terminal Command Bar.
                </p>
                <div class="space-y-6">
                    <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 dark:text-glow-subtle font-mono">Sidebar Navigation</h4>
                        <x-navigation 
                            variant="sidebar" 
                            :items="[
                                ['route' => 'design-system.overview', 'label' => 'Overview'],
                                ['route' => 'design-system.colors', 'label' => 'Colors'],
                                ['route' => 'design-system.typography', 'label' => 'Typography'],
                            ]"
                        />
                    </div>
                    <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 dark:text-glow-subtle font-mono">Top Navigation</h4>
                        <x-navigation 
                            variant="top" 
                            :items="[
                                ['route' => 'design-system.overview', 'label' => 'Overview'],
                                ['route' => 'design-system.colors', 'label' => 'Colors'],
                                ['route' => 'design-system.typography', 'label' => 'Typography'],
                            ]"
                        />
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                    <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                        &lt;x-navigation variant="sidebar" :items="$navItems" /&gt;
                    </code>
                </div>
            </div>

            <!-- Modal -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Modal</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    Dialogs pour les interactions importantes. Disponible en 3 variantes : Standard, Confirmation, Form.
                </p>
                <div class="space-y-6">
                    <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 dark:text-glow-subtle font-mono">Modal Standard</h4>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                            Cliquez sur le bouton pour ouvrir le modal :
                        </p>
                        <div x-data="{ showModal: false }" x-cloak>
                            <x-button @click="showModal = true" variant="primary" size="lg" terminal>
                                Ouvrir Modal
                            </x-button>
                            <template x-if="showModal">
                                <x-modal :show="true" title="Modal Standard" variant="standard" :closeable="true">
                                    <p class="text-gray-300 mb-4">Ceci est un exemple de modal standard avec du contenu personnalisé.</p>
                                    <p class="text-gray-400 text-sm">Vous pouvez ajouter n'importe quel contenu dans le slot du modal.</p>
                                    <x-slot name="footer">
                                        <x-button @click="showModal = false" variant="ghost" size="sm" terminal>> CLOSE</x-button>
                                    </x-slot>
                                </x-modal>
                            </template>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 dark:text-glow-subtle font-mono">Modal Confirmation</h4>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                            Cliquez sur le bouton pour ouvrir le modal de confirmation :
                        </p>
                        <div x-data="{ showConfirm: false }" x-cloak>
                            <x-button @click="showConfirm = true" variant="danger" size="lg" terminal>
                                Supprimer
                            </x-button>
                            <template x-if="showConfirm">
                                <x-modal :show="true" title="Confirmation" variant="confirmation" :closeable="true">
                                    <p class="text-gray-300 mb-2">Êtes-vous sûr de vouloir supprimer cet élément ?</p>
                                    <p class="text-error dark:text-error text-sm mb-4">Cette action est irréversible.</p>
                                    <x-slot name="footer">
                                        <x-button @click="showConfirm = false" variant="ghost" size="sm" terminal>> CANCEL</x-button>
                                        <x-button @click="showConfirm = false" variant="danger" size="sm" terminal>> CONFIRM</x-button>
                                    </x-slot>
                                </x-modal>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                    <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                        &lt;x-modal show="true" title="Titre" variant="standard"&gt;<br>
                        &nbsp;&nbsp;&lt;p&gt;Contenu du modal&lt;/p&gt;<br>
                        &lt;/x-modal&gt;
                    </code>
                </div>
            </div>

            <!-- Filter Card -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Filter Card</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    Conteneur standardisé pour les sections de filtres avec style cohérent du design system.
                </p>
                <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                    <x-filter-card title="Filters">
                        <form method="GET" class="flex gap-4 items-end">
                            <div class="flex-1">
                                <x-form-select
                                    name="type"
                                    label="Type"
                                    placeholder="All Types"
                                    :options="[
                                        ['value' => 'avatar_image', 'label' => 'Avatar Image'],
                                        ['value' => 'planet_image', 'label' => 'Planet Image'],
                                    ]"
                                />
                            </div>
                            <x-button type="submit" variant="ghost" size="sm">Filter</x-button>
                        </form>
                    </x-filter-card>
                    <div class="mt-4">
                        <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                        <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                            &lt;x-filter-card title="Filters"&gt;<br>
                            &nbsp;&nbsp;&lt;form&gt;...&lt;/form&gt;<br>
                            &lt;/x-filter-card&gt;
                        </code>
                    </div>
                </div>
            </div>

            <!-- Description List -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Description List</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    Liste de descriptions pour afficher des paires terme/valeur avec grille responsive pour les pages de détails.
                </p>
                <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                    <x-description-list :columns="2">
                        <x-description-item term="ID" value="01ARZ3NDEKTSV4RRFFQ69G5FAV" :mono="true" />
                        <x-description-item term="Type" value="Planet Image" />
                        <x-description-item term="Status">
                            <x-badge variant="success">Approved</x-badge>
                        </x-description-item>
                        <x-description-item term="Created" value="2025-11-10 12:34:56" />
                    </x-description-list>
                    <div class="mt-4">
                        <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                        <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                            &lt;x-description-list :columns="2"&gt;<br>
                            &nbsp;&nbsp;&lt;x-description-item term="Label" value="Value" /&gt;<br>
                            &lt;/x-description-list&gt;
                        </code>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Empty State</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    État vide avec icône optionnelle, titre, description et action pour guider l'utilisateur.
                </p>
                <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                    <div class="bg-space-black rounded-lg p-8">
                        <x-empty-state
                            title="No resources found"
                            description="Get started by creating your first resource."
                        >
                            <x-slot:icon>
                                <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </x-slot:icon>
                            <x-slot:action>
                                <x-button variant="primary" size="sm">Create Resource</x-button>
                            </x-slot:action>
                        </x-empty-state>
                    </div>
                    <div class="mt-4">
                        <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                        <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                            &lt;x-empty-state title="No items" description="..."&gt;<br>
                            &nbsp;&nbsp;&lt;x-slot:icon&gt;...&lt;/x-slot:icon&gt;<br>
                            &nbsp;&nbsp;&lt;x-slot:action&gt;...&lt;/x-slot:action&gt;<br>
                            &lt;/x-empty-state&gt;
                        </code>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-design-system.layout>
@endsection

