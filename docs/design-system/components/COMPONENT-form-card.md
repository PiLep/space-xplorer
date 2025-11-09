# COMPONENT-Form Card

## Vue d'Ensemble

Le composant Form Card est un conteneur standardisé pour les formulaires de l'application. Il encapsule la structure commune des cards de formulaire avec fond, ombre, bordures et effet scan.

**Quand l'utiliser** :
- Formulaires de connexion, inscription, profil
- Toute section de formulaire nécessitant une card avec titre
- Formulaires avec ou sans header séparé

## Design

### Apparence

Les form cards utilisent :
- Fond blanc en mode clair, `bg-surface-dark` en mode sombre
- Ombre standard (`shadow-md`) ou plus prononcée (`shadow-lg`)
- Bordures subtiles avec effet scan (`scan-effect`)
- Padding configurable selon le contexte

### Variantes

#### Standard (avec titre intégré)

**Usage** : Formulaires simples avec titre centré

**Structure** :
- Titre centré au-dessus du formulaire
- Padding uniforme (`px-8 pt-6 pb-8`)

**Exemple** :
```blade
<x-form-card title="Sign In">
    <form>
        <!-- Form fields -->
    </form>
</x-form-card>
```

#### Avec Header Séparé

**Usage** : Formulaires avec section header distincte (comme dans Profile)

**Structure** :
- Header séparé avec bordure inférieure
- Padding personnalisable pour le contenu

**Exemple** :
```blade
<x-form-card 
    title="Account Information" 
    headerSeparated 
    shadow="shadow-lg" 
    padding="px-8 py-6"
>
    <form>
        <!-- Form fields -->
    </form>
</x-form-card>
```

## Spécifications Techniques

### Classes Tailwind

#### Structure de Base

```html
<div class="
  bg-white 
  dark:bg-surface-dark 
  shadow-md 
  rounded-lg 
  px-8 
  pt-6 
  pb-8 
  mb-4 
  border 
  border-gray-200 
  dark:border-border-dark 
  scan-effect
">
  <!-- Content -->
</div>
```

### Props

| Prop | Type | Défaut | Description |
|------|------|--------|-------------|
| `title` | string | `null` | Titre de la card (optionnel) |
| `titleClass` | string | `'text-2xl font-bold...'` | Classes CSS pour le titre (mode standard) |
| `headerSeparated` | boolean | `false` | Si `true`, affiche le titre dans un header séparé avec bordure |
| `shadow` | string | `'shadow-md'` | Niveau d'ombre : `shadow-md`, `shadow-lg` |
| `padding` | string | `'px-8 pt-6 pb-8'` | Classes de padding pour le contenu |

### Structure HTML

#### Mode Standard

```html
<div class="bg-white dark:bg-surface-dark shadow-md rounded-lg px-8 pt-6 pb-8 mb-4 border border-gray-200 dark:border-border-dark scan-effect">
  <div class="px-8 pt-6 pb-8">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white dark:text-glow-subtle mb-6 text-center">
      Title
    </h2>
    <!-- Content -->
  </div>
</div>
```

#### Mode Header Séparé

```html
<div class="bg-white dark:bg-surface-dark shadow-lg rounded-lg overflow-hidden border border-gray-200 dark:border-border-dark scan-effect">
  <div class="px-8 py-6 border-b border-gray-200 dark:border-border-dark">
    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white dark:text-glow-subtle">
      Title
    </h2>
  </div>
  <div class="px-8 py-6">
    <!-- Content -->
  </div>
</div>
```

## Code d'Implémentation

### Blade Template

**Fichier** : `resources/views/components/form-card.blade.php`

```blade
@props([
    'title' => null,
    'titleClass' => 'text-2xl font-bold text-gray-900 dark:text-white dark:text-glow-subtle mb-6 text-center',
    'headerSeparated' => false,
    'shadow' => 'shadow-md',
    'padding' => 'px-8 pt-6 pb-8',
])

<div class="bg-white dark:bg-surface-dark {{ $shadow }} rounded-lg {{ $headerSeparated ? 'overflow-hidden' : '' }} border border-gray-200 dark:border-border-dark scan-effect">
    @if($title && $headerSeparated)
        <div class="px-8 py-6 border-b border-gray-200 dark:border-border-dark">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white dark:text-glow-subtle">
                {{ $title }}
            </h2>
        </div>
        <div class="{{ $padding }}">
            {{ $slot }}
        </div>
    @else
        <div class="{{ $padding }}">
            @if($title)
                <h2 class="{{ $titleClass }}">
                    {{ $title }}
                </h2>
            @endif
            
            {{ $slot }}
        </div>
    @endif
</div>
```

### Utilisation

```blade
<!-- Simple avec titre -->
<x-form-card title="Sign In">
    <form wire:submit="login">
        <!-- Form fields -->
    </form>
</x-form-card>

<!-- Sans titre -->
<x-form-card>
    <form>
        <!-- Form fields -->
    </form>
</x-form-card>

<!-- Avec header séparé -->
<x-form-card 
    title="Account Information" 
    headerSeparated 
    shadow="shadow-lg" 
    padding="px-8 py-6"
>
    <form wire:submit="updateProfile">
        <!-- Form fields -->
    </form>
</x-form-card>
```

## Exemples d'Utilisation

### Exemple 1 : Formulaire de Connexion

```blade
<div class="max-w-md mx-auto mt-8">
    <x-form-card title="Sign In">
        <form wire:submit="login">
            <x-form-input
                type="email"
                name="email"
                label="Email"
                wireModel="email"
                marginBottom="mb-4"
            />
            <x-form-input
                type="password"
                name="password"
                label="Password"
                wireModel="password"
                marginBottom="mb-6"
            />
            <x-button type="submit" variant="primary" wireLoading="login">
                Sign In
            </x-button>
        </form>
    </x-form-card>
</div>
```

### Exemple 2 : Formulaire avec Header Séparé

```blade
<x-form-card 
    title="Account Information" 
    headerSeparated 
    shadow="shadow-lg"
>
    <form wire:submit="updateProfile">
        <x-form-input
            type="text"
            name="name"
            label="Name"
            wireModel="name"
            marginBottom="mb-6"
        />
        <div class="flex items-center justify-end space-x-4">
            <x-button href="{{ route('dashboard') }}" variant="ghost">
                Cancel
            </x-button>
            <x-button type="submit" variant="primary" wireLoading="updateProfile">
                Save Changes
            </x-button>
        </div>
    </form>
</x-form-card>
```

## Accessibilité

### Structure Sémantique

- Utilise `<section>` avec `role="region"` pour une meilleure sémantique
- Utilise `<h2>` pour le titre (hiérarchie appropriée)
- `aria-labelledby` associé au titre pour les lecteurs d'écran
- Support du mode sombre avec classes `dark:`

### Contraste

- Texte du titre : `text-gray-900` (mode clair) / `text-white` (mode sombre)
  - Ratio : 15.8:1 (mode clair) / 12.6:1 (mode sombre) ✅
- Fond : `bg-white` (mode clair) / `bg-surface-dark` (mode sombre)
- Bordures : `border-gray-200` (mode clair) / `border-border-dark` (mode sombre)

### ARIA

- `role="region"` : Indique une région de contenu
- `aria-labelledby` : Associe le titre à la région pour les lecteurs d'écran
- ID unique généré automatiquement pour le titre (`form-card-{slug}`)

## Notes de Design

- **Cohérence** : Utiliser ce composant pour tous les formulaires afin d'assurer une apparence uniforme
- **Flexibilité** : Le composant supporte deux modes (standard et header séparé) pour s'adapter à différents contextes
- **Effet Scan** : L'effet `scan-effect` ajoute une touche futuriste cohérente avec le thème spatial
- **Responsive** : Le padding et les espacements s'adaptent automatiquement aux différentes tailles d'écran

---

**Référence** : Voir **[DESIGN-SYSTEM.md](../DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

