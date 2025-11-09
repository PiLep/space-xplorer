# COMPONENT-Loading Spinner

## Vue d'Ensemble

Le composant Loading Spinner affiche un indicateur de chargement avec un message optionnel dans le style terminal de Space Xplorer. Il est utilisé pour indiquer qu'une opération est en cours.

**Quand l'utiliser** :
- Chargement de données asynchrones
- Requêtes API en cours
- Initialisation de modules
- Opérations longues

## Design

### Variantes

#### Terminal (Défaut)
- Message de chargement en style terminal (optionnel)
- Spinner animé avec couleur primaire (vert)
- Centré verticalement et horizontalement
- Style monospace pour l'ambiance terminal

#### Simple
- Spinner animé uniquement, sans message
- Style minimaliste sans police monospace
- Idéal pour les chargements discrets

### Structure

**Terminal** :
```
[LOADING] Message de chargement...
    [Spinner animé]
```

**Simple** :
```
    [Spinner animé]
```

## Spécifications Techniques

### Props

| Prop | Type | Défaut | Description |
|------|------|--------|-------------|
| `message` | string | `'[LOADING] Loading...'` | Message à afficher (variante terminal uniquement) |
| `size` | string | `'md'` | Taille du spinner : `sm`, `md`, `lg` |
| `showMessage` | bool | `true` | Afficher le message de chargement (variante terminal uniquement) |
| `variant` | string | `'terminal'` | Variante du spinner : `terminal`, `simple` |

### Tailles

| Taille | Classes | Dimensions | Bordure |
|--------|---------|------------|---------|
| `sm` | `h-8 w-8` | 32px × 32px | `border-b-2` |
| `md` | `h-12 w-12` | 48px × 48px | `border-b-2` |
| `lg` | `h-14 w-14` | 56px × 56px | `border-b-3` |

### Classes Tailwind

```blade
<div class="flex justify-center items-center py-12 font-mono">
    <div class="text-center">
        @if($showMessage)
            <div class="text-sm text-gray-500 dark:text-gray-500 mb-4">
                {{ $message }}
            </div>
        @endif
        <div class="animate-spin rounded-full {{ $spinnerSize }} border-b-2 border-space-primary mx-auto"></div>
    </div>
</div>
```

## Code d'Implémentation

### Composant Blade

**Fichier** : `resources/views/components/loading-spinner.blade.php`

```blade
@props([
    'message' => '[LOADING] Loading...',
    'size' => 'md', // sm, md, lg
    'showMessage' => true,
])

@php
    $sizeConfig = [
        'sm' => 'h-8 w-8',
        'md' => 'h-12 w-12',
        'lg' => 'h-16 w-16',
    ];
    
    $spinnerSize = $sizeConfig[$size] ?? $sizeConfig['md'];
@endphp

<div class="flex justify-center items-center py-12 font-mono">
    <div class="text-center">
        @if($showMessage)
            <div class="text-sm text-gray-500 dark:text-gray-500 mb-4">
                {{ $message }}
            </div>
        @endif
        <div class="animate-spin rounded-full {{ $spinnerSize }} border-b-2 border-space-primary mx-auto"></div>
    </div>
</div>
```

## Exemples d'Utilisation

### Exemple 1 : Chargement Standard

```blade
<x-loading-spinner message="[LOADING] Accessing planetary database..." />
```

**Résultat** :
```
[LOADING] Accessing planetary database...
    [Spinner animé]
```

### Exemple 2 : Taille Petite

```blade
<x-loading-spinner 
    message="[LOADING] Processing..." 
    size="sm" 
/>
```

### Exemple 3 : Taille Grande

```blade
<x-loading-spinner 
    message="[LOADING] Initializing system..." 
    size="lg" 
/>
```

### Exemple 4 : Sans Message

```blade
<x-loading-spinner :showMessage="false" />
```

**Résultat** :
```
    [Spinner animé]
```

### Exemple 5 : Variante Simple

```blade
<x-loading-spinner variant="simple" size="md" :showMessage="false" />
```

**Résultat** :
```
    [Spinner animé simple]
```

**Usage** : Pour les chargements discrets sans message terminal, comme dans les formulaires ou les sections de contenu.

### Exemple 6 : Message Personnalisé

```blade
<x-loading-spinner message="[LOADING] Connecting to server..." />
```

## Responsive

Le composant s'adapte automatiquement :
- **Mobile** : Padding réduit (`py-8`)
- **Tablet** : Padding standard (`py-12`)
- **Desktop** : Padding généreux (`py-12`)

## Accessibilité

- Le spinner utilise `animate-spin` de Tailwind pour l'animation
- Le message texte permet aux lecteurs d'écran de comprendre l'état
- Contraste de couleurs respecté
- Support du mode sombre

## Notes de Design

- **Animation** : Utilise l'animation CSS native pour de meilleures performances
- **Cohérence** : Style terminal cohérent avec le reste de l'application
- **Flexibilité** : Plusieurs tailles disponibles selon le contexte
- **Performance** : Animation CSS pure, pas de JavaScript requis

---

**Référence** : Voir **[DESIGN-SYSTEM.md](../DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

