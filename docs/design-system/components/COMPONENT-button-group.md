# COMPONENT-Button Group

## Vue d'Ensemble

Le composant Button Group est utilisé pour grouper plusieurs boutons d'action ensemble avec un layout cohérent et flexible. Il respecte le design system et permet d'organiser les actions de manière claire et intuitive.

**Quand l'utiliser** :
- Grouper plusieurs actions liées sur une même page
- Créer des groupes d'actions rapides
- Navigation secondaire avec plusieurs boutons
- Actions multiples dans un formulaire ou une card

## Design

### Apparence

Le Button Group utilise un layout flex avec `flex-wrap` pour permettre aux boutons de s'adapter à différentes tailles d'écran. L'espacement entre les boutons est cohérent et respecte le design system.

### Variantes d'Alignement

#### Center (Défaut)

**Usage** : Alignement centré, idéal pour les actions principales au centre de la page

**Exemple** :
```blade
<x-button-group align="center">
    <button class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold py-3 px-6 rounded-lg transition-colors">
        Action 1
    </button>
    <button class="bg-space-secondary hover:bg-space-secondary-dark text-white font-bold py-3 px-6 rounded-lg transition-colors">
        Action 2
    </button>
</x-button-group>
```

#### Left

**Usage** : Alignement à gauche, pour les actions alignées avec le contenu principal

**Exemple** :
```blade
<x-button-group align="left">
    <button>Action 1</button>
    <button>Action 2</button>
</x-button-group>
```

#### Right

**Usage** : Alignement à droite, pour les actions alignées avec la fin du contenu

**Exemple** :
```blade
<x-button-group align="right">
    <button>Action 1</button>
    <button>Action 2</button>
</x-button-group>
```

### Variantes d'Espacement

#### Small (`sm`)

**Usage** : Espacement réduit entre les boutons (gap-2)

**Exemple** :
```blade
<x-button-group spacing="sm">
    <button>Action 1</button>
    <button>Action 2</button>
</x-button-group>
```

#### Medium (`md`) - Défaut

**Usage** : Espacement standard entre les boutons (gap-4)

**Exemple** :
```blade
<x-button-group spacing="md">
    <button>Action 1</button>
    <button>Action 2</button>
</x-button-group>
```

#### Large (`lg`)

**Usage** : Espacement généreux entre les boutons (gap-6)

**Exemple** :
```blade
<x-button-group spacing="lg">
    <button>Action 1</button>
    <button>Action 2</button>
</x-button-group>
```

### Full Width

**Usage** : Le groupe de boutons prend toute la largeur disponible

**Exemple** :
```blade
<x-button-group full-width>
    <button class="flex-1">Action 1</button>
    <button class="flex-1">Action 2</button>
</x-button-group>
```

## Spécifications Techniques

### Props

| Prop | Type | Défaut | Description |
|------|------|--------|-------------|
| `align` | `string` | `'center'` | Alignement du groupe (`center`, `left`, `right`) |
| `spacing` | `string` | `'md'` | Espacement entre les boutons (`sm`, `md`, `lg`) |
| `fullWidth` | `boolean` | `false` | Le groupe prend toute la largeur disponible |

### Structure HTML

```html
<div class="flex flex-wrap justify-center gap-4">
    <!-- Boutons enfants -->
</div>
```

### Classes CSS Utilisées

- **Layout** : `flex flex-wrap`
- **Alignement** : `justify-center`, `justify-start`, `justify-end`
- **Espacement** : `gap-2`, `gap-4`, `gap-6`
- **Largeur** : `w-full` (si `fullWidth` est activé)

## Code d'Implémentation

### Blade Component

```blade
@props([
    'align' => 'center', // center, left, right
    'spacing' => 'md', // sm, md, lg
    'fullWidth' => false,
])

@php
    $alignClasses = [
        'center' => 'justify-center',
        'left' => 'justify-start',
        'right' => 'justify-end',
    ];
    
    $spacingClasses = [
        'sm' => 'gap-2',
        'md' => 'gap-4',
        'lg' => 'gap-6',
    ];
    
    $baseClasses = 'flex flex-wrap ' . $alignClasses[$align] . ' ' . $spacingClasses[$spacing];
    
    if ($fullWidth) {
        $baseClasses .= ' w-full';
    }
@endphp

<div {{ $attributes->merge(['class' => $baseClasses]) }}>
    {{ $slot }}
</div>
```

## Exemples d'Utilisation

### Exemple 1 : Actions du Dashboard

```blade
<!-- Action Commands -->
<div class="mt-8 font-mono">
    <div class="text-sm text-gray-500 dark:text-gray-500 mb-4">
        [READY] System ready for commands
    </div>
    <x-button-group>
        <button class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold py-3 px-6 rounded-lg transition-colors glow-primary hover:glow-primary font-mono text-sm">
            > EXPLORE_PLANETS
        </button>
        <a href="{{ route('profile') }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-surface-medium dark:hover:bg-surface-dark text-gray-900 dark:text-white font-bold py-3 px-6 rounded-lg transition-colors border border-gray-300 dark:border-border-dark dark:hover:glow-border-primary font-mono text-sm">
            > VIEW_PROFILE
        </a>
    </x-button-group>
</div>
```

### Exemple 2 : Actions de Formulaire

```blade
<x-button-group align="right" spacing="md">
    <button type="button" class="bg-gray-200 hover:bg-gray-300 dark:bg-surface-medium dark:hover:bg-surface-dark text-gray-900 dark:text-white font-bold py-3 px-6 rounded-lg transition-colors">
        Annuler
    </button>
    <button type="submit" class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold py-3 px-6 rounded-lg transition-colors">
        Enregistrer
    </button>
</x-button-group>
```

### Exemple 3 : Actions avec Largeur Complète

```blade
<x-button-group full-width spacing="sm">
    <button class="flex-1 bg-space-primary hover:bg-space-primary-dark text-space-black font-bold py-3 px-6 rounded-lg transition-colors">
        Action 1
    </button>
    <button class="flex-1 bg-space-secondary hover:bg-space-secondary-dark text-white font-bold py-3 px-6 rounded-lg transition-colors">
        Action 2
    </button>
    <button class="flex-1 bg-gray-200 hover:bg-gray-300 dark:bg-surface-medium dark:hover:bg-surface-dark text-gray-900 dark:text-white font-bold py-3 px-6 rounded-lg transition-colors">
        Action 3
    </button>
</x-button-group>
```

### Exemple 4 : Navigation Secondaire

```blade
<x-button-group align="left" spacing="lg">
    <a href="{{ route('dashboard') }}" class="bg-transparent hover:bg-surface-medium text-gray-400 hover:text-white font-bold py-2 px-4 rounded-lg transition-colors">
        Dashboard
    </a>
    <a href="{{ route('planets') }}" class="bg-transparent hover:bg-surface-medium text-gray-400 hover:text-white font-bold py-2 px-4 rounded-lg transition-colors">
        Planètes
    </a>
    <a href="{{ route('profile') }}" class="bg-transparent hover:bg-surface-medium text-gray-400 hover:text-white font-bold py-2 px-4 rounded-lg transition-colors">
        Profil
    </a>
</x-button-group>
```

## Responsive Design

Le composant Button Group est responsive par défaut grâce à `flex-wrap` :
- **Desktop** : Les boutons s'affichent en ligne
- **Mobile** : Les boutons se répartissent sur plusieurs lignes si nécessaire

### Optimisation Mobile

Pour une meilleure expérience mobile, vous pouvez utiliser `full-width` avec des boutons `flex-1` :

```blade
<x-button-group full-width spacing="sm">
    <button class="flex-1">Action 1</button>
    <button class="flex-1">Action 2</button>
</x-button-group>
```

## Accessibilité

### Bonnes Pratiques

- **Navigation au clavier** : Les boutons enfants doivent être accessibles au clavier
- **Focus visible** : Assurez-vous que les boutons enfants ont un état focus visible
- **Labels clairs** : Utilisez des labels descriptifs pour chaque bouton
- **Ordre logique** : Organisez les boutons dans un ordre logique (gauche à droite, haut en bas)

### Attributs ARIA

Le composant Button Group n'a pas besoin d'attributs ARIA spécifiques, mais les boutons enfants doivent avoir des labels appropriés :

```blade
<x-button-group>
    <button aria-label="Explorer les planètes">
        > EXPLORE_PLANETS
    </button>
    <a href="{{ route('profile') }}" aria-label="Voir le profil">
        > VIEW_PROFILE
    </a>
</x-button-group>
```

## Intégration avec le Design System

### Couleurs

Le Button Group utilise les couleurs du design system via les boutons enfants :
- **Primary** : `bg-space-primary` pour les actions principales
- **Secondary** : `bg-space-secondary` pour les actions secondaires
- **Ghost** : `bg-transparent` pour les actions subtiles

### Espacements

Les espacements respectent le design system :
- **Small** : `gap-2` (8px)
- **Medium** : `gap-4` (16px) - Défaut
- **Large** : `gap-6` (24px)

### Typographie

La typographie est gérée par les boutons enfants, généralement avec `font-mono` pour le style terminal.

## Notes de Design

- **Cohérence** : Utilisez le même espacement (`spacing`) dans toute l'application pour maintenir la cohérence visuelle
- **Hiérarchie** : L'alignement (`align`) peut aider à créer une hiérarchie visuelle (actions principales centrées, actions secondaires à droite)
- **Responsive** : Le `flex-wrap` garantit que les boutons s'adaptent automatiquement aux différentes tailles d'écran
- **Performance** : Le composant est léger et n'ajoute pas de JavaScript supplémentaire

## Checklist de Création

- [x] Le composant respecte le design system
- [x] Les variantes d'alignement sont définies
- [x] Les variantes d'espacement sont définies
- [x] Le composant est responsive
- [x] L'accessibilité est assurée
- [x] La documentation est complète
- [x] Des exemples d'utilisation sont fournis
- [x] Le code est propre et réutilisable

---

**Référence** : Voir **[DESIGN-SYSTEM-COMPONENTS.md](../DESIGN-SYSTEM-COMPONENTS.md)** pour la vue d'ensemble des composants.

