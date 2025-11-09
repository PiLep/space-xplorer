@extends('layouts.app')

@section('title', 'Design System - Components')

@section('content')
    <x-design-system.layout>
        <section>
            <h2 class="mb-8 font-mono text-3xl font-bold text-white">COMPOSANTS</h2>

            <div class="space-y-8">
                <!-- Buttons -->
                <div>
                    <h3 class="mb-4 font-mono text-xl font-semibold text-white">Button</h3>
                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                        Composant réutilisable pour tous les boutons de l'application. Supporte 4 variantes, 3 tailles, et
                        intégration Livewire.
                    </p>
                    <div class="space-y-6">
                        <!-- Variantes -->
                        <div
                            class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                            <h4
                                class="dark:text-glow-subtle mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">
                                Variantes</h4>
                            <div class="flex flex-wrap gap-4">
                                <x-button
                                    variant="primary"
                                    size="md"
                                >
                                    Primary
                                </x-button>
                                <x-button
                                    variant="secondary"
                                    size="md"
                                >
                                    Secondary
                                </x-button>
                                <x-button
                                    variant="danger"
                                    size="md"
                                >
                                    Danger
                                </x-button>
                                <x-button
                                    variant="ghost"
                                    size="md"
                                >
                                    Ghost
                                </x-button>
                            </div>
                        </div>

                        <!-- Tailles -->
                        <div
                            class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                            <h4
                                class="dark:text-glow-subtle mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">
                                Tailles</h4>
                            <div class="flex flex-wrap items-center gap-4">
                                <x-button
                                    variant="primary"
                                    size="sm"
                                >
                                    Small
                                </x-button>
                                <x-button
                                    variant="primary"
                                    size="md"
                                >
                                    Medium
                                </x-button>
                                <x-button
                                    variant="primary"
                                    size="lg"
                                >
                                    Large
                                </x-button>
                            </div>
                        </div>

                        <!-- Style Terminal -->
                        <div
                            class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                            <h4
                                class="dark:text-glow-subtle mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">
                                Style Terminal</h4>
                            <div class="flex flex-wrap gap-4">
                                <x-button
                                    variant="primary"
                                    size="lg"
                                    terminal
                                >
                                    > EXECUTE_COMMAND
                                </x-button>
                                <x-button
                                    variant="ghost"
                                    size="lg"
                                    terminal
                                >
                                    > CANCEL_OPERATION
                                </x-button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="mb-2 text-xs text-gray-500 dark:text-gray-500">Usage :</p>
                        <code class="text-space-primary bg-space-black block rounded px-2 py-1 font-mono text-xs">
                            &lt;x-button variant="primary" size="md"&gt;Action&lt;/x-button&gt;<br>
                            &lt;x-button type="submit" wireLoading="login" wireLoadingText="Signing in..."&gt;Sign
                            In&lt;/x-button&gt;<br>
                            &lt;x-button href="{{ route('dashboard') }}" variant="ghost"&gt;Cancel&lt;/x-button&gt;
                        </code>
                    </div>
                </div>

                <!-- Cards -->
                <div>
                    <h3 class="mb-4 font-mono text-xl font-semibold text-white">Cards</h3>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div
                            class="dark:bg-surface-dark dark:border-border-dark scan-effect terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                            <h4
                                class="dark:text-glow-subtle mb-2 font-mono text-xl font-semibold text-gray-900 dark:text-white">
                                Card Standard</h4>
                            <p class="text-gray-600 dark:text-gray-400">
                                Conteneur pour afficher des informations groupées avec fond sombre et bordures subtiles.
                            </p>
                        </div>

                        <div
                            class="dark:bg-surface-dark dark:border-border-dark dark:hover:bg-surface-medium hologram terminal-border-simple cursor-pointer rounded-lg border border-gray-200 bg-white p-6 transition-colors hover:bg-gray-50">
                            <h4
                                class="dark:text-glow-subtle mb-2 font-mono text-xl font-semibold text-gray-900 dark:text-white">
                                Card Interactive</h4>
                            <p class="text-gray-600 dark:text-gray-400">
                                Card cliquable avec effet hover et hologram pour les interactions utilisateur.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Elements -->
                <div>
                    <h3 class="mb-4 font-mono text-xl font-semibold text-white">Form Elements</h3>

                    <!-- Form Card -->
                    <div class="mb-8">
                        <h4 class="mb-4 font-mono text-lg font-semibold text-white">Form Card</h4>
                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                            Conteneur standardisé pour les formulaires avec fond, ombre, bordures et effet scan. Supporte
                            deux modes : standard (titre intégré) et header séparé.
                        </p>
                        <div class="space-y-6">
                            <!-- Standard Mode -->
                            <div
                                class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                                <h5
                                    class="dark:text-glow-subtle mb-4 font-mono text-base font-semibold text-gray-900 dark:text-white">
                                    Mode Standard</h5>
                                <div class="max-w-md">
                                    <x-form-card title="Sign In">
                                        <form>
                                            <x-form-input
                                                type="email"
                                                name="demo_email"
                                                id="demo_email"
                                                label="Email"
                                                placeholder="Enter your email"
                                                marginBottom="mb-4"
                                            />
                                            <x-form-input
                                                type="password"
                                                name="demo_password"
                                                id="demo_password"
                                                label="Password"
                                                placeholder="Enter your password"
                                                marginBottom="mb-6"
                                            />
                                            <x-button
                                                type="submit"
                                                variant="primary"
                                                size="md"
                                                class="w-full"
                                            >
                                                Sign In
                                            </x-button>
                                        </form>
                                    </x-form-card>
                                </div>
                                <div class="mt-4">
                                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-500">Usage :</p>
                                    <code
                                        class="text-space-primary bg-space-black block rounded px-2 py-1 font-mono text-xs"
                                    >
                                        &lt;x-form-card title="Sign In"&gt;<br>
                                        &nbsp;&nbsp;&lt;form&gt;...&lt;/form&gt;<br>
                                        &lt;/x-form-card&gt;
                                    </code>
                                </div>
                            </div>

                            <!-- Header Separated Mode -->
                            <div
                                class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                                <h5
                                    class="dark:text-glow-subtle mb-4 font-mono text-base font-semibold text-gray-900 dark:text-white">
                                    Mode Header Séparé</h5>
                                <div class="max-w-md">
                                    <x-form-card
                                        title="Account Information"
                                        headerSeparated
                                        shadow="shadow-lg"
                                        padding="px-8 py-6"
                                    >
                                        <form>
                                            <x-form-input
                                                type="text"
                                                name="demo_name"
                                                id="demo_name"
                                                label="Name"
                                                placeholder="Enter your name"
                                                marginBottom="mb-6"
                                            />
                                            <div class="flex items-center justify-end space-x-4">
                                                <x-button
                                                    href="#"
                                                    variant="ghost"
                                                    size="md"
                                                >
                                                    Cancel
                                                </x-button>
                                                <x-button
                                                    type="submit"
                                                    variant="primary"
                                                    size="md"
                                                >
                                                    Save Changes
                                                </x-button>
                                            </div>
                                        </form>
                                    </x-form-card>
                                </div>
                                <div class="mt-4">
                                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-500">Usage :</p>
                                    <code
                                        class="text-space-primary bg-space-black block rounded px-2 py-1 font-mono text-xs"
                                    >
                                        &lt;x-form-card<br>
                                        &nbsp;&nbsp;title="Account Information"<br>
                                        &nbsp;&nbsp;headerSeparated<br>
                                        &nbsp;&nbsp;shadow="shadow-lg"<br>
                                        &nbsp;&nbsp;padding="px-8 py-6"<br>
                                        &gt;<br>
                                        &nbsp;&nbsp;&lt;form&gt;...&lt;/form&gt;<br>
                                        &lt;/x-form-card&gt;
                                    </code>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Input -->
                    <div>
                        <h4 class="mb-4 font-mono text-lg font-semibold text-white">Form Input</h4>
                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                            Composant réutilisable pour les champs de formulaire avec label, input, validation et messages
                            d'erreur. Supporte deux variantes : Classic et Terminal.
                        </p>
                        <div class="space-y-6">
                            <!-- Classic Variant -->
                            <div
                                class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                                <h4
                                    class="dark:text-glow-subtle mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">
                                    Variante Classic (défaut)</h4>
                                <div class="max-w-md space-y-4">
                                    <x-form-input
                                        type="text"
                                        name="example_name"
                                        id="example_name"
                                        label="Name"
                                        placeholder="Enter your name"
                                        marginBottom="mb-4"
                                    />
                                    <x-form-input
                                        type="email"
                                        name="example_email"
                                        id="example_email"
                                        label="Email"
                                        placeholder="Enter your email"
                                        marginBottom="mb-4"
                                    />
                                    <x-form-input
                                        type="password"
                                        name="example_password"
                                        id="example_password"
                                        label="Password"
                                        placeholder="Enter your password"
                                        marginBottom="mb-6"
                                    />
                                    <x-form-input
                                        type="text"
                                        name="example_readonly"
                                        id="example_readonly"
                                        label="User ID"
                                        value="12345"
                                        disabled
                                        helpText="This is your unique user identifier."
                                        marginBottom="mb-4"
                                    />
                                </div>
                                <div class="mt-4">
                                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-500">Usage :</p>
                                    <code
                                        class="text-space-primary bg-space-black block rounded px-2 py-1 font-mono text-xs"
                                    >
                                        &lt;x-form-input<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;type="email"<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;name="email"<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;label="Email"<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;wireModel="email"<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;placeholder="Enter your email"<br>
                                        /&gt;
                                    </code>
                                </div>
                            </div>

                            <!-- Terminal Variant -->
                            <div
                                class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                                <h4
                                    class="dark:text-glow-subtle mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">
                                    Variante Terminal</h4>
                                <div class="max-w-md space-y-4 font-mono">
                                    <x-form-input
                                        type="email"
                                        name="terminal_email"
                                        label="enter_email"
                                        placeholder="user@domain.com"
                                        variant="terminal"
                                        marginBottom="mb-6"
                                    />
                                    <x-form-input
                                        type="password"
                                        name="terminal_password"
                                        label="enter_password"
                                        placeholder="••••••••"
                                        variant="terminal"
                                        marginBottom="mb-6"
                                    />
                                </div>
                                <div class="mt-4">
                                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-500">Usage :</p>
                                    <code
                                        class="text-space-primary bg-space-black block rounded px-2 py-1 font-mono text-xs"
                                    >
                                        &lt;x-form-input<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;type="email"<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;name="email"<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;label="enter_email"<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;variant="terminal"<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;wireModel="email"<br>
                                        /&gt;
                                    </code>
                                </div>
                            </div>
                        </div>

                        <!-- Form Link -->
                        <div class="mt-8">
                            <h4 class="mb-4 font-mono text-lg font-semibold text-white">Form Link</h4>
                            <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                Lien de navigation entre formulaires avec texte descriptif et lien stylisé. Utilisé pour la
                                navigation entre les formulaires d'authentification.
                            </p>
                            <div
                                class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                                <div class="max-w-md">
                                    <x-form-card title="Sign In">
                                        <form>
                                            <x-form-input
                                                type="email"
                                                name="demo_form_email"
                                                id="demo_form_email"
                                                label="Email"
                                                placeholder="Enter your email"
                                                marginBottom="mb-4"
                                            />
                                            <x-form-input
                                                type="password"
                                                name="demo_form_password"
                                                id="demo_form_password"
                                                label="Password"
                                                placeholder="Enter your password"
                                                marginBottom="mb-6"
                                            />
                                            <x-button
                                                type="submit"
                                                variant="primary"
                                                size="md"
                                                class="w-full"
                                            >
                                                Sign In
                                            </x-button>
                                            <x-form-link
                                                text="Don't have an account?"
                                                linkText="Register"
                                                href="#"
                                            />
                                        </form>
                                    </x-form-card>
                                </div>
                                <div class="mt-4">
                                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-500">Usage :</p>
                                    <code
                                        class="text-space-primary bg-space-black block rounded px-2 py-1 font-mono text-xs"
                                    >
                                        &lt;x-form-link<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;text="Don't have an account?"<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;linkText="Register"<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;:href="route('register')"<br>
                                        /&gt;
                                    </code>
                                </div>
                            </div>
                        </div>

                        <!-- Form Link -->
                        <div class="mt-8">
                            <h4 class="mb-4 font-mono text-lg font-semibold text-white">Form Link</h4>
                            <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                Lien de navigation entre formulaires avec texte descriptif et lien stylisé. Utilisé pour la
                                navigation entre les formulaires d'authentification.
                            </p>
                            <div
                                class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                                <div class="max-w-md">
                                    <x-form-card title="Sign In">
                                        <form>
                                            <x-form-input
                                                type="email"
                                                name="demo_form_email"
                                                id="demo_form_email"
                                                label="Email"
                                                placeholder="Enter your email"
                                                marginBottom="mb-4"
                                            />
                                            <x-form-input
                                                type="password"
                                                name="demo_form_password"
                                                id="demo_form_password"
                                                label="Password"
                                                placeholder="Enter your password"
                                                marginBottom="mb-6"
                                            />
                                            <x-button
                                                type="submit"
                                                variant="primary"
                                                size="md"
                                                class="w-full"
                                            >
                                                Sign In
                                            </x-button>
                                            <x-form-link
                                                text="Don't have an account?"
                                                linkText="Register"
                                                href="#"
                                            />
                                        </form>
                                    </x-form-card>
                                </div>
                                <div class="mt-4">
                                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-500">Usage :</p>
                                    <code
                                        class="text-space-primary bg-space-black block rounded px-2 py-1 font-mono text-xs"
                                    >
                                        &lt;x-form-link<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;text="Don't have an account?"<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;linkText="Register"<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;:href="route('register')"<br>
                                        /&gt;
                                    </code>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Page Header -->
                    <div>
                        <h3 class="mb-4 font-mono text-xl font-semibold text-white">Page Header</h3>
                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                            En-tête de page standardisé avec titre et description optionnelle. Assure une cohérence visuelle
                            pour les en-têtes de page.
                        </p>
                        <div
                            class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                            <x-page-header
                                title="Profile Settings"
                                description="Manage your account information and preferences."
                            />
                        </div>
                        <div class="mt-4">
                            <p class="mb-2 text-xs text-gray-500 dark:text-gray-500">Usage :</p>
                            <code class="text-space-primary bg-space-black block rounded px-2 py-1 font-mono text-xs">
                                &lt;x-page-header<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;title="Profile Settings"<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;description="Manage your account information and preferences."<br>
                                /&gt;
                            </code>
                        </div>
                    </div>

                    <!-- Specialized Components -->
                    <div>
                        <h3 class="mb-4 font-mono text-xl font-semibold text-white">Composants Spécialisés</h3>
                        <div class="space-y-6">
                            <!-- Planet Card -->
                            <div
                                class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                                <h4
                                    class="dark:text-glow-subtle mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">
                                    Planet Card</h4>
                                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                    Card spécialisée pour l'affichage des planètes avec layout horizontal, image,
                                    description et caractéristiques.
                                </p>
                                <div class="bg-space-black rounded-lg p-4">
                                    @php
                                        $examplePlanet = (object) [
                                            'name' => 'Kepler-452b',
                                            'type' => 'tellurique',
                                            'size' => 'moyenne',
                                            'temperature' => 'tempérée',
                                            'atmosphere' => 'respirable',
                                            'terrain' => 'forestier',
                                            'resources' => 'abondantes',
                                            'description' =>
                                                'Une planète tellurique de taille moyenne avec une température tempérée et une atmosphère respirable. Le terrain est principalement forestier avec des ressources abondantes, faisant de cette planète un candidat idéal pour la colonisation.',
                                        ];
                                    @endphp
                                    <x-planet-card
                                        :planet="$examplePlanet"
                                        :showImage="false"
                                    />
                                </div>
                                <div class="mt-4">
                                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-500">Usage :</p>
                                    <code
                                        class="text-space-primary bg-space-black block rounded px-2 py-1 font-mono text-xs"
                                    >
                                        &lt;x-planet-card :planet="$planet" /&gt;
                                    </code>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terminal Components -->
                    <div>
                        <h3 class="mb-4 font-mono text-xl font-semibold text-white">Composants Terminal</h3>
                        <div class="space-y-6">
                            <!-- Terminal Prompt -->
                            <div
                                class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                                <h4
                                    class="dark:text-glow-subtle mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">
                                    Terminal Prompt</h4>
                                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                    Ligne de commande terminal avec prompt système pour créer l'ambiance spatiale.
                                </p>
                                <div class="bg-space-black rounded-lg p-4 font-mono">
                                    <x-terminal-prompt command="load_user_session" />
                                    <x-terminal-prompt command="display_home_planet" />
                                    <x-terminal-prompt command="query_planet_data" />
                                </div>
                                <div class="mt-4">
                                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-500">Usage :</p>
                                    <code
                                        class="text-space-primary bg-space-black block rounded px-2 py-1 font-mono text-xs"
                                    >
                                        &lt;x-terminal-prompt command="command_name" /&gt;
                                    </code>
                                </div>
                            </div>

                            <!-- Alert -->
                            <div
                                class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                                <h4
                                    class="dark:text-glow-subtle mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">
                                    Alert</h4>
                                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                    Messages d'alerte avec style terminal. Supporte 4 variantes : error, warning, success,
                                    info.
                                </p>
                                <div class="space-y-4">
                                    <x-alert
                                        type="error"
                                        message="Failed to load planet data"
                                    />
                                    <x-alert
                                        type="warning"
                                        message="Low fuel reserves detected"
                                    />
                                    <x-alert
                                        type="success"
                                        message="Planet data loaded successfully"
                                    />
                                    <x-alert
                                        type="info"
                                        message="System maintenance scheduled for tonight"
                                    />
                                </div>
                                <div class="mt-4">
                                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-500">Usage :</p>
                                    <code
                                        class="text-space-primary bg-space-black block rounded px-2 py-1 font-mono text-xs"
                                    >
                                        &lt;x-alert type="error" message="Your message" /&gt;
                                    </code>
                                </div>
                            </div>

                            <!-- Loading Spinner -->
                            <div
                                class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                                <h4
                                    class="dark:text-glow-subtle mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">
                                    Loading Spinner</h4>
                                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                    Indicateur de chargement avec message terminal. Disponible en 2 variantes (terminal,
                                    simple) et 3 tailles : sm, md, lg.
                                </p>
                                <div class="space-y-6">
                                    <div>
                                        <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">Variante Terminal (défaut)
                                            - Taille Medium :</p>
                                        <x-loading-spinner message="[LOADING] Accessing planetary database..." />
                                    </div>
                                    <div>
                                        <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">Variante Terminal - Taille
                                            Small :</p>
                                        <x-loading-spinner
                                            message="[LOADING] Processing..."
                                            size="sm"
                                        />
                                    </div>
                                    <div>
                                        <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">Variante Terminal - Taille
                                            Large :</p>
                                        <x-loading-spinner
                                            message="[LOADING] Initializing system..."
                                            size="lg"
                                        />
                                    </div>
                                    <div>
                                        <p class="mb-2 text-sm text-gray-600 dark:text-gray-400">Variante Simple (sans
                                            message) :</p>
                                        <x-loading-spinner
                                            variant="simple"
                                            size="md"
                                            :showMessage="false"
                                        />
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-500">Usage :</p>
                                    <code
                                        class="text-space-primary bg-space-black block rounded px-2 py-1 font-mono text-xs"
                                    >
                                        &lt;x-loading-spinner message="[LOADING] ..." size="md" /&gt;<br>
                                        &lt;x-loading-spinner variant="simple" size="md" :showMessage="false" /&gt;
                                    </code>
                                </div>
                            </div>

                            <!-- Terminal Link -->
                            <div
                                class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                                <h4
                                    class="dark:text-glow-subtle mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">
                                    Terminal Link</h4>
                                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                    Lien avec style terminal pour les interfaces terminal. Format de commande avec préfixe
                                    `>`.
                                </p>
                                <div class="bg-space-black rounded-lg p-4 font-mono">
                                    <x-terminal-message message="[INFO] New user? Create an account:" />
                                    <x-terminal-link
                                        href="#"
                                        text="> REGISTER_NEW_USER"
                                        marginTop="mt-2"
                                        :showBorder="false"
                                    />
                                </div>
                                <div class="mt-4">
                                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-500">Usage :</p>
                                    <code
                                        class="text-space-primary bg-space-black block rounded px-2 py-1 font-mono text-xs"
                                    >
                                        &lt;x-terminal-link<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;href="{{ route('register') }}"<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;text="> REGISTER_NEW_USER"<br>
                                        /&gt;
                                    </code>
                                </div>
                            </div>

                            <!-- Terminal Message -->
                            <div
                                class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                                <h4
                                    class="dark:text-glow-subtle mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">
                                    Terminal Message</h4>
                                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                    Messages système avec style terminal et détection automatique du type. Le composant
                                    détecte automatiquement le préfixe ([OK], [ERROR], [INFO], etc.) et applique la couleur
                                    appropriée.
                                </p>
                                <div class="bg-space-black space-y-2 rounded-lg p-4 font-mono">
                                    <x-terminal-message message="[OK] System initialized" />
                                    <x-terminal-message message="[SUCCESS] Operation completed successfully" />
                                    <x-terminal-message message="[READY] System ready for commands" />
                                    <x-terminal-message message="[ERROR] Failed to connect to database" />
                                    <x-terminal-message message="[INFO] Please provide your credentials" />
                                    <x-terminal-message message="[WAIT] Initializing dashboard..." />
                                    <x-terminal-message message="[LOADING] Processing request..." />
                                </div>
                                <div class="mt-4">
                                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-500">Types supportés :</p>
                                    <ul class="mb-4 space-y-1 font-mono text-xs text-gray-500 dark:text-gray-500">
                                        <li>• <span class="text-space-primary">[OK]</span>, <span
                                                class="text-space-primary"
                                            >[SUCCESS]</span>, <span class="text-space-primary">[READY]</span> → Vert
                                            (success)</li>
                                        <li>• <span class="text-error">[ERROR]</span> → Rouge (error)</li>
                                        <li>• <span class="text-space-secondary">[INFO]</span> → Bleu (info)</li>
                                        <li>• <span class="text-gray-500">[WAIT]</span>, <span
                                                class="text-gray-500">[LOADING]</span> → Gris (wait)</li>
                                    </ul>
                                    <p class="mb-2 text-xs text-gray-500 dark:text-gray-500">Usage :</p>
                                    <code
                                        class="text-space-primary bg-space-black block rounded px-2 py-1 font-mono text-xs"
                                    >
                                        &lt;x-terminal-message message="[OK] System initialized" /&gt;<br>
                                        &lt;x-terminal-message message="[ERROR] Connection failed" marginBottom="mb-4"
                                        /&gt;
                                    </code>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Button Group -->
                    <div>
                        <h3 class="mb-4 font-mono text-xl font-semibold text-white">Button Group</h3>
                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                            Groupe de boutons avec layout flexible. Supporte différents alignements et espacements.
                        </p>
                        <div class="space-y-6">
                            <div
                                class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                                <h4
                                    class="dark:text-glow-subtle mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">
                                    Alignement Center (défaut)</h4>
                                <x-button-group>
                                    <x-button
                                        variant="primary"
                                        size="lg"
                                        terminal
                                    >
                                        Action 1
                                    </x-button>
                                    <x-button
                                        variant="secondary"
                                        size="lg"
                                        terminal
                                    >
                                        Action 2
                                    </x-button>
                                </x-button-group>
                            </div>
                            <div
                                class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                                <h4
                                    class="dark:text-glow-subtle mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">
                                    Alignement Left</h4>
                                <x-button-group align="left">
                                    <x-button
                                        variant="primary"
                                        size="lg"
                                        terminal
                                    >
                                        Action 1
                                    </x-button>
                                    <x-button
                                        variant="secondary"
                                        size="lg"
                                        terminal
                                    >
                                        Action 2
                                    </x-button>
                                </x-button-group>
                            </div>
                            <div
                                class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                                <h4
                                    class="dark:text-glow-subtle mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">
                                    Full Width</h4>
                                <x-button-group
                                    full-width
                                    spacing="sm"
                                >
                                    <x-button
                                        variant="primary"
                                        size="lg"
                                        terminal
                                        class="flex-1"
                                    >
                                        Action 1
                                    </x-button>
                                    <x-button
                                        variant="secondary"
                                        size="lg"
                                        terminal
                                        class="flex-1"
                                    >
                                        Action 2
                                    </x-button>
                                </x-button-group>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="mb-2 text-xs text-gray-500 dark:text-gray-500">Usage :</p>
                            <code class="text-space-primary bg-space-black block rounded px-2 py-1 font-mono text-xs">
                                &lt;x-button-group align="center" spacing="md"&gt;<br>
                                &nbsp;&nbsp;&lt;x-button variant="primary"&gt;Action 1&lt;/x-button&gt;<br>
                                &nbsp;&nbsp;&lt;x-button variant="secondary"&gt;Action 2&lt;/x-button&gt;<br>
                                &lt;/x-button-group&gt;
                            </code>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div>
                        <h3 class="mb-4 font-mono text-xl font-semibold text-white">Navigation</h3>
                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                            Navigation principale avec style rétro-futuriste. Disponible en 3 variantes : Sidebar, Top Menu,
                            Terminal Command Bar.
                        </p>
                        <div class="space-y-6">
                            <div
                                class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                                <h4
                                    class="dark:text-glow-subtle mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">
                                    Sidebar Navigation</h4>
                                <x-navigation
                                    variant="sidebar"
                                    :items="[
                                        ['route' => 'design-system.overview', 'label' => 'Overview'],
                                        ['route' => 'design-system.colors', 'label' => 'Colors'],
                                        ['route' => 'design-system.typography', 'label' => 'Typography'],
                                    ]"
                                />
                            </div>
                            <div
                                class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                                <h4
                                    class="dark:text-glow-subtle mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">
                                    Top Navigation</h4>
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
                            <p class="mb-2 text-xs text-gray-500 dark:text-gray-500">Usage :</p>
                            <code class="text-space-primary bg-space-black block rounded px-2 py-1 font-mono text-xs">
                                &lt;x-navigation variant="sidebar" :items="$navItems" /&gt;
                            </code>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div>
                        <h3 class="mb-4 font-mono text-xl font-semibold text-white">Modal</h3>
                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                            Dialogs pour les interactions importantes. Disponible en 3 variantes : Standard, Confirmation,
                            Form.
                        </p>
                        <div class="space-y-6">
                            <div
                                class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                                <h4
                                    class="dark:text-glow-subtle mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">
                                    Modal Standard</h4>
                                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                    Cliquez sur le bouton pour ouvrir le modal :
                                </p>
                                <div
                                    x-data="{ showModal: false }"
                                    x-cloak
                                >
                                    <x-button
                                        @click="showModal = true"
                                        variant="primary"
                                        size="lg"
                                        terminal
                                    >
                                        Ouvrir Modal
                                    </x-button>
                                    <template x-if="showModal">
                                        <x-modal
                                            :show="true"
                                            title="Modal Standard"
                                            variant="standard"
                                            :closeable="true"
                                        >
                                            <p class="mb-4 text-gray-300">Ceci est un exemple de modal standard avec du
                                                contenu personnalisé.</p>
                                            <p class="text-sm text-gray-400">Vous pouvez ajouter n'importe quel contenu
                                                dans le slot du modal.</p>
                                            <x-slot name="footer">
                                                <x-button
                                                    @click="showModal = false"
                                                    variant="ghost"
                                                    size="sm"
                                                    terminal
                                                >
                                                    > CLOSE
                                                </x-button>
                                            </x-slot>
                                        </x-modal>
                                    </template>
                                </div>
                            </div>
                            <div
                                class="dark:bg-surface-dark dark:border-border-dark terminal-border-simple rounded-lg border border-gray-200 bg-white p-6">
                                <h4
                                    class="dark:text-glow-subtle mb-4 font-mono text-lg font-semibold text-gray-900 dark:text-white">
                                    Modal Confirmation</h4>
                                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                                    Cliquez sur le bouton pour ouvrir le modal de confirmation :
                                </p>
                                <div
                                    x-data="{ showConfirm: false }"
                                    x-cloak
                                >
                                    <x-button
                                        @click="showConfirm = true"
                                        variant="danger"
                                        size="lg"
                                        terminal
                                    >
                                        Supprimer
                                    </x-button>
                                    <template x-if="showConfirm">
                                        <x-modal
                                            :show="true"
                                            title="Confirmation"
                                            variant="confirmation"
                                            :closeable="true"
                                        >
                                            <p class="mb-2 text-gray-300">Êtes-vous sûr de vouloir supprimer cet élément ?
                                            </p>
                                            <p class="text-error dark:text-error mb-4 text-sm">Cette action est
                                                irréversible.</p>
                                            <x-slot name="footer">
                                                <x-button
                                                    @click="showConfirm = false"
                                                    variant="ghost"
                                                    size="sm"
                                                    terminal
                                                >
                                                    > CANCEL
                                                </x-button>
                                                <x-button
                                                    @click="showConfirm = false"
                                                    variant="danger"
                                                    size="sm"
                                                    terminal
                                                >
                                                    > CONFIRM
                                                </x-button>
                                            </x-slot>
                                        </x-modal>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="mb-2 text-xs text-gray-500 dark:text-gray-500">Usage :</p>
                            <code class="text-space-primary bg-space-black block rounded px-2 py-1 font-mono text-xs">
                                &lt;x-modal show="true" title="Titre" variant="standard"&gt;<br>
                                &nbsp;&nbsp;&lt;p&gt;Contenu du modal&lt;/p&gt;<br>
                                &lt;/x-modal&gt;
                            </code>
                        </div>
                    </div>
                </div>
        </section>
    </x-design-system.layout>
@endsection
