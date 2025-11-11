# COMPONENT-Logo

## Vue d'Ensemble

Le composant Logo affiche le logo "STELLAR" avec le style terminal/sci-fi du design system. Il inclut des effets visuels comme le glow, les scanlines discrètes, et l'animation pulse.

**Quand l'utiliser** :
- Landing page principale
- En-têtes de pages
- Navigation
- Footer
- Toute page nécessitant le branding STELLAR

## Design

### Apparence

- Texte "STELLAR" en monospace
- Couleur primary (vert fluorescent `#00ff88`)
- Effet glow discret
- Animation pulse subtile
- Scanlines discrètes optionnelles

### Variantes de Taille

| Variante | Mobile | Tablet | Desktop | Usage |
|----------|--------|--------|---------|-------|
| `xs` | text-base (16px) | text-lg (18px) | text-lg (18px) | Barre de navigation en bas |
| `sm` | text-2xl (24px) | text-3xl (30px) | text-3xl (30px) | Navigation, footer |
| `md` | text-3xl (30px) | text-4xl (36px) | text-4xl (36px) | En-têtes de pages |
| `lg` | text-4xl (36px) | text-5xl (48px) | text-6xl (60px) | Landing page principale |
| `xl` | text-5xl (48px) | text-6xl (60px) | text-7xl (72px) | Hero sections |

## Spécifications Techniques

### Props

| Prop | Type | Défaut | Description |
|------|------|--------|-------------|
| `size` | string | `'lg'` | Taille du logo : `xs`, `sm`, `md`, `lg`, `xl` |
| `showScanlines` | bool | `true` | Afficher les scanlines discrètes sur le logo |

### Classes CSS Utilisées

- `font-mono` : Police monospace
- `text-space-primary` : Couleur verte fluorescente
- `text-glow-primary` : Effet glow discret
- `pulse-glow` : Animation pulse subtile
- `scanlines-title` : Scanlines discrètes (si activées)

## Code d'Implémentation

### Composant Blade

**Fichier** : `resources/views/components/logo.blade.php`

```blade
@props([
    'size' => 'lg', // xs, sm, md, lg, xl
    'showScanlines' => true,
])

@php
    $sizeClasses = [
        'xs' => 'text-base sm:text-lg',
        'sm' => 'text-2xl sm:text-3xl',
        'md' => 'text-3xl sm:text-4xl',
        'lg' => 'text-4xl sm:text-5xl lg:text-6xl',
        'xl' => 'text-5xl sm:text-6xl lg:text-7xl',
    ];
    
    $classes = $sizeClasses[$size] ?? $sizeClasses['lg'];
    $scanlinesClass = $showScanlines ? 'scanlines-title' : '';
@endphp

<h1 class="{{ $classes }} font-bold font-mono text-space-primary dark:text-space-primary text-glow-primary pulse-glow {{ $scanlinesClass }}" style="letter-spacing: 0.15em; line-height: 1;" @if($showScanlines) data-text="STELLAR" @endif>
    STELLAR
</h1>
```

## Exemples d'Utilisation

### Exemple 1 : Landing Page (Taille Large)

```blade
<div class="mb-12 text-center">
    <x-logo size="lg" :showScanlines="true" />
</div>
```

### Exemple 2 : Navigation (Taille Petite)

```blade
<nav>
    <x-logo size="sm" :showScanlines="false" />
</nav>
```

### Exemple 3 : En-tête de Page (Taille Moyenne)

```blade
<header>
    <x-logo size="md" :showScanlines="true" />
</header>
```

### Exemple 4 : Hero Section (Taille Extra Large)

```blade
<div class="hero-section">
    <x-logo size="xl" :showScanlines="true" />
</div>
```

### Exemple 5 : Barre de Navigation en Bas (Taille Extra Small)

```blade
<div class="fixed bottom-0 left-0 right-0 bg-surface-dark border-t border-border-dark">
    <div class="flex items-center gap-3">
        <a href="{{ route('landing') }}">
            <x-logo size="xs" :showScanlines="false" />
        </a>
        <!-- Autres éléments de navigation -->
    </div>
</div>
```

## Responsive

Le composant s'adapte automatiquement :
- **Mobile** : Taille réduite selon la variante
- **Tablet** : Taille intermédiaire
- **Desktop** : Taille maximale selon la variante

## Accessibilité

- Le logo utilise une balise `<h1>` sémantique
- Contraste élevé avec le fond sombre
- Support du mode sombre intégré
- Les animations sont subtiles et ne gênent pas la lecture

## Notes de Design

- **Cohérence** : Utilise les couleurs et effets du design system
- **Flexibilité** : Variantes de taille pour différents contextes
- **Performance** : Les animations CSS sont optimisées (GPU)
- **Scanlines** : Option discrète pour l'ambiance terminal/sci-fi

## Preview

Une page de preview interactive est disponible pour visualiser toutes les variantes du composant Logo :

**Route** : `/design-system/components/logo`

**Preview** : Voir **[logo-preview.png](../assets/logo/logo-preview.png)** pour un aperçu visuel de toutes les variantes.

La page de preview affiche :
- Toutes les variantes de taille (sm, md, lg, xl)
- Options avec et sans scanlines
- Exemples d'usage dans différents contextes (landing page, navigation)

---

**Référence** : Voir **[DESIGN-SYSTEM.md](../DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

