@extends('layouts.app')

@section('title', 'Design System - Components - Base')

@section('content')
<x-design-system.layout>
    <section>
        <div class="mb-6">
            <a href="{{ route('design-system.components') }}" class="text-space-secondary hover:text-space-secondary-light dark:text-space-secondary dark:hover:text-space-secondary-light font-mono text-sm mb-4 inline-block">
                ← Retour aux composants
            </a>
            <h2 class="text-3xl font-bold text-white mb-2 font-mono">COMPOSANTS_DE_BASE</h2>
            <p class="text-gray-600 dark:text-gray-400">
                Éléments fondamentaux réutilisables dans toute l'application
            </p>
        </div>
        
        <div class="space-y-8">
            <!-- Buttons -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Button</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    Composant réutilisable pour tous les boutons de l'application. Supporte 4 variantes, 3 tailles, et intégration Livewire.
                </p>
                <div class="space-y-6">
                    <!-- Variantes -->
                    <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 dark:text-glow-subtle font-mono">Variantes</h4>
                        <div class="flex flex-wrap gap-4">
                            <x-button variant="primary" size="md">Primary</x-button>
                            <x-button variant="secondary" size="md">Secondary</x-button>
                            <x-button variant="danger" size="md">Danger</x-button>
                            <x-button variant="ghost" size="md">Ghost</x-button>
                        </div>
                    </div>

                    <!-- Tailles -->
                    <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 dark:text-glow-subtle font-mono">Tailles</h4>
                        <div class="flex flex-wrap items-center gap-4">
                            <x-button variant="primary" size="sm">Small</x-button>
                            <x-button variant="primary" size="md">Medium</x-button>
                            <x-button variant="primary" size="lg">Large</x-button>
                        </div>
                    </div>

                    <!-- Style Terminal -->
                    <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 dark:text-glow-subtle font-mono">Style Terminal</h4>
                        <div class="flex flex-wrap gap-4">
                            <x-button variant="primary" size="lg" terminal>> EXECUTE_COMMAND</x-button>
                            <x-button variant="ghost" size="lg" terminal>> CANCEL_OPERATION</x-button>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                    <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                        &lt;x-button variant="primary" size="md"&gt;Action&lt;/x-button&gt;<br>
                        &lt;x-button type="submit" wireLoading="login" wireLoadingText="Signing in..."&gt;Sign In&lt;/x-button&gt;<br>
                        &lt;x-button href="{{ route('dashboard') }}" variant="ghost"&gt;Cancel&lt;/x-button&gt;
                    </code>
                </div>
            </div>

            <!-- Cards -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Cards</h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 scan-effect terminal-border-simple">
                        <h4 class="text-xl font-semibold text-gray-900 dark:text-white mb-2 dark:text-glow-subtle font-mono">Card Standard</h4>
                        <p class="text-gray-600 dark:text-gray-400">
                            Conteneur pour afficher des informations groupées avec fond sombre et bordures subtiles.
                        </p>
                    </div>
                    
                    <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 hover:bg-gray-50 dark:hover:bg-surface-medium transition-colors cursor-pointer hologram terminal-border-simple">
                        <h4 class="text-xl font-semibold text-gray-900 dark:text-white mb-2 dark:text-glow-subtle font-mono">Card Interactive</h4>
                        <p class="text-gray-600 dark:text-gray-400">
                            Card cliquable avec effet hover et hologram pour les interactions utilisateur.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Elements -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Form Elements</h3>
                
                <!-- Form Card -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-white mb-4 font-mono">Form Card</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Conteneur standardisé pour les formulaires avec fond, ombre, bordures et effet scan. Supporte deux modes : standard (titre intégré) et header séparé.
                    </p>
                    <div class="space-y-6">
                        <!-- Standard Mode -->
                        <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                            <h5 class="text-base font-semibold text-gray-900 dark:text-white mb-4 dark:text-glow-subtle font-mono">Mode Standard</h5>
                            <div class="max-w-md">
                                <x-form-card title="Sign In">
                                    <form>
                                        <x-form-input type="email" name="demo_email" id="demo_email" label="Email" placeholder="Enter your email" marginBottom="mb-4" />
                                        <x-form-input type="password" name="demo_password" id="demo_password" label="Password" placeholder="Enter your password" marginBottom="mb-6" />
                                        <x-button type="submit" variant="primary" size="md" class="w-full">Sign In</x-button>
                                    </form>
                                </x-form-card>
                            </div>
                            <div class="mt-4">
                                <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                                <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                                    &lt;x-form-card title="Sign In"&gt;<br>
                                    &nbsp;&nbsp;&lt;form&gt;...&lt;/form&gt;<br>
                                    &lt;/x-form-card&gt;
                                </code>
                            </div>
                        </div>

                        <!-- Header Separated Mode -->
                        <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                            <h5 class="text-base font-semibold text-gray-900 dark:text-white mb-4 dark:text-glow-subtle font-mono">Mode Header Séparé</h5>
                            <div class="max-w-md">
                                <x-form-card title="Account Information" headerSeparated shadow="shadow-lg" padding="px-8 py-6">
                                    <form>
                                        <x-form-input type="text" name="demo_name" id="demo_name" label="Name" placeholder="Enter your name" marginBottom="mb-6" />
                                        <div class="flex items-center justify-end space-x-4">
                                            <x-button href="#" variant="ghost" size="md">Cancel</x-button>
                                            <x-button type="submit" variant="primary" size="md">Save Changes</x-button>
                                        </div>
                                    </form>
                                </x-form-card>
                            </div>
                            <div class="mt-4">
                                <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                                <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                                    &lt;x-form-card title="Account Information" headerSeparated shadow="shadow-lg" padding="px-8 py-6"&gt;<br>
                                    &nbsp;&nbsp;&lt;form&gt;...&lt;/form&gt;<br>
                                    &lt;/x-form-card&gt;
                                </code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Input -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-white mb-4 font-mono">Form Input</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Composant réutilisable pour les champs de formulaire avec label, input, validation et messages d'erreur. Supporte deux variantes : Classic et Terminal.
                    </p>
                    <div class="space-y-6">
                        <!-- Classic Variant -->
                        <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 dark:text-glow-subtle font-mono">Variante Classic (défaut)</h4>
                            <div class="max-w-md space-y-4">
                                <x-form-input type="text" name="example_name" id="example_name" label="Name" placeholder="Enter your name" marginBottom="mb-4" />
                                <x-form-input type="email" name="example_email" id="example_email" label="Email" placeholder="Enter your email" marginBottom="mb-4" />
                                <x-form-input type="password" name="example_password" id="example_password" label="Password" placeholder="Enter your password" marginBottom="mb-6" />
                                <x-form-input type="text" name="example_readonly" id="example_readonly" label="User ID" value="12345" disabled helpText="This is your unique user identifier." marginBottom="mb-4" />
                            </div>
                            <div class="mt-4">
                                <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                                <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                                    &lt;x-form-input type="email" name="email" label="Email" wireModel="email" placeholder="Enter your email" /&gt;
                                </code>
                            </div>
                        </div>

                        <!-- Terminal Variant -->
                        <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 dark:text-glow-subtle font-mono">Variante Terminal</h4>
                            <div class="max-w-md space-y-4 font-mono">
                                <x-form-input type="email" name="terminal_email" label="enter_email" placeholder="user@domain.com" variant="terminal" marginBottom="mb-6" />
                                <x-form-input type="password" name="terminal_password" label="enter_password" placeholder="••••••••" variant="terminal" marginBottom="mb-6" />
                            </div>
                            <div class="mt-4">
                                <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                                <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                                    &lt;x-form-input type="email" name="email" label="enter_email" variant="terminal" wireModel="email" /&gt;
                                </code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Link -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-white mb-4 font-mono">Form Link</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Lien de navigation entre formulaires avec texte descriptif et lien stylisé. Utilisé pour la navigation entre les formulaires d'authentification.
                    </p>
                    <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                        <div class="max-w-md">
                            <x-form-card title="Sign In">
                                <form>
                                    <x-form-input type="email" name="demo_form_email" id="demo_form_email" label="Email" placeholder="Enter your email" marginBottom="mb-4" />
                                    <x-form-input type="password" name="demo_form_password" id="demo_form_password" label="Password" placeholder="Enter your password" marginBottom="mb-6" />
                                    <x-button type="submit" variant="primary" size="md" class="w-full">Sign In</x-button>
                                    <x-form-link text="Don't have an account?" linkText="Register" href="#" />
                                </form>
                            </x-form-card>
                        </div>
                        <div class="mt-4">
                            <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                            <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                                &lt;x-form-link text="Don't have an account?" linkText="Register" :href="route('register')" /&gt;
                            </code>
                        </div>
                    </div>
                </div>

                <!-- Page Header -->
                <div>
                    <h4 class="text-lg font-semibold text-white mb-4 font-mono">Page Header</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        En-tête de page standardisé avec titre et description optionnelle. Assure une cohérence visuelle pour les en-têtes de page.
                    </p>
                    <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                        <x-page-header title="Profile Settings" description="Manage your account information and preferences." />
                    </div>
                    <div class="mt-4">
                        <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                        <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                            &lt;x-page-header title="Profile Settings" description="Manage your account information and preferences." /&gt;
                        </code>
                    </div>
                </div>

                <!-- Form Select -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-white mb-4 font-mono">Form Select</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Champ select avec label, validation et support du mode sombre. Compatible avec le design system.
                    </p>
                    <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                        <div class="max-w-md">
                            <x-form-select
                                name="example_type"
                                label="Resource Type"
                                placeholder="Select a type"
                                :options="[
                                    ['value' => 'avatar_image', 'label' => 'Avatar Image'],
                                    ['value' => 'planet_image', 'label' => 'Planet Image'],
                                    ['value' => 'planet_video', 'label' => 'Planet Video'],
                                ]"
                            />
                        </div>
                        <div class="mt-4">
                            <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                            <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                                &lt;x-form-select name="type" label="Type" :options="[...]" /&gt;
                            </code>
                        </div>
                    </div>
                </div>

                <!-- Badge -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold text-white mb-4 font-mono">Badge</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Indicateurs de statut et labels avec variantes sémantiques. Disponible en 3 tailles : sm, md, lg. Style standard arrondi ou style terminal avec bordures et effets de glow.
                    </p>
                    <div class="space-y-6">
                        <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                            <h5 class="text-base font-semibold text-gray-900 dark:text-white mb-4 dark:text-glow-subtle font-mono">Variantes Standard</h5>
                            <div class="flex flex-wrap gap-2">
                                <x-badge variant="success">Approved</x-badge>
                                <x-badge variant="warning">Pending</x-badge>
                                <x-badge variant="error">Rejected</x-badge>
                                <x-badge variant="info">New</x-badge>
                                <x-badge variant="generating" :pulse="true">Generating</x-badge>
                                <x-badge variant="default">Tag</x-badge>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                            <h5 class="text-base font-semibold text-gray-900 dark:text-white mb-4 dark:text-glow-subtle font-mono">Style Terminal</h5>
                            <div class="bg-space-black rounded-lg p-4">
                                <div class="flex flex-wrap gap-2">
                                    <x-badge variant="success" terminal>APPROVED</x-badge>
                                    <x-badge variant="warning" terminal>PENDING</x-badge>
                                    <x-badge variant="error" terminal>REJECTED</x-badge>
                                    <x-badge variant="info" terminal>NEW</x-badge>
                                    <x-badge variant="generating" terminal :pulse="true">GENERATING</x-badge>
                                    <x-badge variant="default" terminal>TAG</x-badge>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                            <h5 class="text-base font-semibold text-gray-900 dark:text-white mb-4 dark:text-glow-subtle font-mono">Tailles</h5>
                            <div class="flex flex-wrap gap-2 items-center">
                                <x-badge variant="success" size="sm">Small</x-badge>
                                <x-badge variant="success" size="md">Medium</x-badge>
                                <x-badge variant="success" size="lg">Large</x-badge>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                        <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                            &lt;x-badge variant="success" size="md"&gt;Approved&lt;/x-badge&gt;<br>
                            &lt;x-badge variant="generating" :pulse="true"&gt;Generating&lt;/x-badge&gt;<br>
                            &lt;x-badge variant="success" terminal&gt;APPROVED&lt;/x-badge&gt;
                        </code>
                    </div>
                </div>

                <!-- Alert -->
                <div class="mt-8">
                    <h4 class="text-lg font-semibold text-white mb-4 font-mono">Alert</h4>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                        Messages d'alerte avec style terminal. Supporte 4 variantes : error, warning, success, info.
                    </p>
                    <div class="space-y-4">
                        <x-alert type="error" message="Failed to load planet data" />
                        <x-alert type="warning" message="Low fuel reserves detected" />
                        <x-alert type="success" message="Planet data loaded successfully" />
                        <x-alert type="info" message="System maintenance scheduled for tonight" />
                    </div>
                    <div class="mt-4">
                        <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                        <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                            &lt;x-alert type="error" message="Your message" /&gt;
                        </code>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-design-system.layout>
@endsection

