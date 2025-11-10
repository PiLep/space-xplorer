# COMPONENT-Badge

## Vue d'Ensemble

Le composant Badge affiche des indicateurs de statut, des labels et des tags dans le style du design system Space Xplorer. Il est utilisé pour afficher des informations catégorisées avec des couleurs sémantiques.

**Quand l'utiliser** :
- Indicateurs de statut (approved, pending, rejected, generating)
- Labels de catégorie (type de ressource, tags)
- Badges informatifs
- Indicateurs visuels de statut

## Design

### Apparence

Les badges utilisent des coins arrondis complets (`rounded-full`), un padding généreux, et des couleurs sémantiques pour une identification rapide.

### Variantes

#### Success
- **Usage** : Statut approuvé, succès, confirmation
- **Couleurs** : Vert fluorescent du design system (`bg-success-dark text-success-light border-success`)
- **Style** : Fond sombre avec texte vert fluorescent et bordure

#### Warning
- **Usage** : Statut en attente, avertissement
- **Couleurs** : Orange/Ambre du design system (`bg-warning-dark text-warning-light border-warning`)
- **Style** : Fond sombre avec texte orange fluorescent et bordure

#### Error
- **Usage** : Statut rejeté, erreur
- **Couleurs** : Rouge fluorescent (`bg-error-dark text-error-light border-error`)
- **Style** : Fond sombre avec texte rouge fluorescent et bordure

#### Info
- **Usage** : Informations générales
- **Couleurs** : Bleu fluorescent du design system (`bg-info-dark text-info-light border-info`)
- **Style** : Fond sombre avec texte bleu fluorescent et bordure

#### Generating
- **Usage** : Statut en génération (avec animation pulse)
- **Couleurs** : Bleu fluorescent avec animation (`bg-info-dark text-info-light border-info animate-pulse`)
- **Style** : Fond sombre avec texte bleu fluorescent, bordure et animation pulse

#### Default
- **Usage** : Badge neutre, tags génériques
- **Couleurs** : Surface medium avec texte gris (`bg-surface-medium text-gray-300 border-border-dark`)
- **Style** : Fond surface medium avec texte gris et bordure subtile

### Style Terminal

Le badge peut utiliser un style terminal avec la prop `terminal` :
- **Bordures carrées** au lieu d'arrondies (`rounded` au lieu de `rounded-full`)
- **Police monospace** pour un look terminal
- **Effets de glow** subtils avec `shadow-[0_0_8px_rgba(...)]` pour les variantes colorées
- **Meilleure visibilité** sur fond sombre avec les couleurs fluorescentes du design system

### Tailles

| Taille | Classes | Padding | Texte |
|--------|---------|---------|-------|
| `sm` | `px-2 py-0.5` | 8px × 2px | `text-xs` |
| `md` | `px-2.5 py-0.5` | 10px × 2px | `text-xs` |
| `lg` | `px-3 py-1` | 12px × 4px | `text-sm` |

## Spécifications Techniques

### Props

| Prop | Type | Défaut | Description |
|------|------|--------|-------------|
| `variant` | string | `'default'` | Variante du badge : `success`, `warning`, `error`, `info`, `generating`, `default` |
| `size` | string | `'md'` | Taille du badge : `sm`, `md`, `lg` |
| `pulse` | bool | `false` | Animation pulse (pour generating) |
| `terminal` | bool | `false` | Style terminal avec bordures carrées, police monospace et effets de glow |

## Code d'Implémentation

### Composant Blade

**Fichier** : `resources/views/components/badge.blade.php`

```blade
@props([
    'variant' => 'default',
    'size' => 'md',
    'pulse' => false,
    'terminal' => false,
])

@php
    $baseClasses = 'inline-flex items-center font-medium border transition-all duration-150';
    $roundedClass = $terminal ? 'rounded px-2 font-mono' : 'rounded-full';
    
    $variantClasses = [
        'success' => $terminal 
            ? 'bg-success-dark text-success-light border-success shadow-[0_0_8px_rgba(0,255,136,0.3)]' 
            : 'bg-success-dark text-success-light border-success/50',
        // ... autres variantes
    ];
    
    $sizeClasses = [
        'sm' => $terminal ? 'px-1.5 py-0.5 text-xs' : 'px-2 py-0.5 text-xs',
        'md' => $terminal ? 'px-2 py-0.5 text-xs' : 'px-2.5 py-0.5 text-xs',
        'lg' => $terminal ? 'px-2.5 py-1 text-sm' : 'px-3 py-1 text-sm',
    ];
    
    $classes = $baseClasses . ' ' . $roundedClass . ' ' . $variantClasses[$variant] . ' ' . $sizeClasses[$size] . ' ' . ($pulse ? 'animate-pulse' : '');
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
```

## Exemples d'Utilisation

### Exemple 1 : Badge de Statut Success

```blade
<x-badge variant="success">Approved</x-badge>
```

### Exemple 2 : Badge de Statut Warning

```blade
<x-badge variant="warning">Pending</x-badge>
```

### Exemple 3 : Badge de Statut Error

```blade
<x-badge variant="error">Rejected</x-badge>
```

### Exemple 4 : Badge Generating avec Animation

```blade
<x-badge variant="generating" :pulse="true">Generating</x-badge>
```

### Exemple 5 : Badge Info

```blade
<x-badge variant="info">New</x-badge>
```

### Exemple 6 : Badge Default (Tag)

```blade
<x-badge variant="default">Tag</x-badge>
```

### Exemple 7 : Taille Petite

```blade
<x-badge variant="success" size="sm">Small</x-badge>
```

### Exemple 8 : Taille Grande

```blade
<x-badge variant="info" size="lg">Large</x-badge>
```

### Exemple 9 : Style Terminal

```blade
<x-badge variant="success" terminal>APPROVED</x-badge>
<x-badge variant="error" terminal>REJECTED</x-badge>
<x-badge variant="generating" terminal :pulse="true">GENERATING</x-badge>
```

## Accessibilité

- Contraste de couleurs respecté pour tous les variants
- Support du mode sombre avec variantes `dark:`
- Texte lisible avec tailles appropriées
- Animation pulse pour attirer l'attention (generating)

## Notes de Design

- **Cohérence** : Utilise les couleurs sémantiques du design system (success, error, warning, info)
- **Lisibilité** : Texte toujours contrasté avec le fond sombre pour une meilleure visibilité
- **Animation** : Pulse uniquement pour le statut generating
- **Flexibilité** : Plusieurs tailles disponibles selon le contexte
- **Style Terminal** : Option pour un look rétro-futuriste avec bordures carrées et effets de glow
- **Effets Visuels** : Ombres subtiles (glow) pour les variantes colorées en mode terminal

---

**Référence** : Voir **[DESIGN-SYSTEM.md](../DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

