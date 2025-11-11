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

            <!-- Table -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Table</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    Composant complet pour afficher des données tabulaires avec headers, rows, pagination et variantes de style.
                </p>
                <div class="space-y-6">
                    <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 dark:text-glow-subtle font-mono">Variante Default</h4>
                        <x-table
                            :headers="[
                                ['label' => 'ID', 'key' => 'id', 'align' => 'right', 'cellClass' => 'font-mono text-gray-500 dark:text-gray-400'],
                                ['label' => 'Name', 'key' => 'name', 'cellClass' => 'font-medium text-gray-900 dark:text-white'],
                                ['label' => 'Status', 'key' => 'status', 'cellClass' => 'text-gray-900 dark:text-gray-300'],
                                ['label' => 'Created', 'key' => 'created_at', 'format' => 'datetime', 'cellClass' => 'text-gray-900 dark:text-gray-400'],
                            ]"
                            :rows="[
                                ['id' => 1, 'name' => 'John Doe', 'status' => 'Active', 'created_at' => '2025-01-15 10:30:00'],
                                ['id' => 2, 'name' => 'Jane Smith', 'status' => 'Pending', 'created_at' => '2025-01-14 14:20:00'],
                                ['id' => 3, 'name' => 'Bob Johnson', 'status' => 'Active', 'created_at' => '2025-01-13 09:15:00'],
                            ]"
                            emptyMessage="No data found"
                        />
                    </div>
                    <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 dark:text-glow-subtle font-mono">Variante Compact</h4>
                        <x-table
                            variant="compact"
                            :headers="[
                                ['label' => 'Name', 'key' => 'name', 'cellClass' => 'text-gray-900 dark:text-white'],
                                ['label' => 'Email', 'key' => 'email', 'cellClass' => 'text-gray-900 dark:text-gray-300'],
                                ['label' => 'Status', 'key' => 'status', 'cellClass' => 'text-gray-900 dark:text-gray-300'],
                            ]"
                            :rows="[
                                ['name' => 'John Doe', 'email' => 'john@example.com', 'status' => 'Active'],
                                ['name' => 'Jane Smith', 'email' => 'jane@example.com', 'status' => 'Pending'],
                            ]"
                        />
                    </div>
                    <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 dark:text-glow-subtle font-mono">Variante Striped</h4>
                        <x-table
                            variant="striped"
                            :headers="[
                                ['label' => 'Resource', 'key' => 'name', 'cellClass' => 'text-gray-900 dark:text-white'],
                                ['label' => 'Type', 'key' => 'type', 'cellClass' => 'text-gray-900 dark:text-gray-300'],
                                ['label' => 'Status', 'key' => 'status', 'cellClass' => 'text-gray-900 dark:text-gray-300'],
                            ]"
                            :rows="[
                                ['name' => 'Avatar Image', 'type' => 'avatar_image', 'status' => 'Approved'],
                                ['name' => 'Planet Image', 'type' => 'planet_image', 'status' => 'Generating'],
                                ['name' => 'Planet Video', 'type' => 'planet_video', 'status' => 'Pending'],
                            ]"
                        />
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                    <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                        &lt;x-table<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;:headers="[['label' => 'Name', 'key' => 'name']]"<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;:rows="$items"<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;variant="default"<br>
                        /&gt;
                    </code>
                </div>
            </div>

            <!-- Progress Bar -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Progress Bar</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    Indicateur visuel de progression avec pourcentage et couleurs personnalisables.
                </p>
                <div class="space-y-6">
                    <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 dark:text-glow-subtle font-mono">Couleurs</h4>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600 dark:text-gray-400">Blue (défaut)</span>
                                    <span class="text-gray-900 dark:text-white font-medium">75%</span>
                                </div>
                                <x-progress-bar :percentage="75" color="blue" />
                            </div>
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600 dark:text-gray-400">Green</span>
                                    <span class="text-gray-900 dark:text-white font-medium">90%</span>
                                </div>
                                <x-progress-bar :percentage="90" color="green" />
                            </div>
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600 dark:text-gray-400">Orange</span>
                                    <span class="text-gray-900 dark:text-white font-medium">50%</span>
                                </div>
                                <x-progress-bar :percentage="50" color="orange" />
                            </div>
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600 dark:text-gray-400">Red</span>
                                    <span class="text-gray-900 dark:text-white font-medium">25%</span>
                                </div>
                                <x-progress-bar :percentage="25" color="red" />
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 dark:text-glow-subtle font-mono">Tailles</h4>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600 dark:text-gray-400">Fine (h-2)</span>
                                    <span class="text-gray-900 dark:text-white font-medium">60%</span>
                                </div>
                                <x-progress-bar :percentage="60" color="blue" height="h-2" />
                            </div>
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600 dark:text-gray-400">Standard (h-3)</span>
                                    <span class="text-gray-900 dark:text-white font-medium">75%</span>
                                </div>
                                <x-progress-bar :percentage="75" color="blue" height="h-3" />
                            </div>
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600 dark:text-gray-400">Thick (h-6)</span>
                                    <span class="text-gray-900 dark:text-white font-medium">80%</span>
                                </div>
                                <x-progress-bar :percentage="80" color="green" height="h-6" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                    <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                        &lt;x-progress-bar :percentage="75" color="blue" /&gt;<br>
                        &lt;x-progress-bar :percentage="90" color="green" height="h-4" /&gt;
                    </code>
                </div>
            </div>
        </div>
    </section>
</x-design-system.layout>
@endsection

