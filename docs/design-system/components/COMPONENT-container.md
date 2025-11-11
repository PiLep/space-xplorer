# Container Component - Design System

## Vue d'Ensemble

Le composant Container est un composant utilitaire pour la mise en page qui standardise les largeurs maximales et le padding horizontal responsive à travers toute l'application. Il assure une cohérence visuelle et une expérience utilisateur optimale sur tous les appareils.

## Usage

Composant utilitaire pour créer des conteneurs avec largeurs maximales standardisées et padding responsive.

**Fichier** : `resources/views/components/container.blade.php`

## Variantes

### Standard (défaut)

Largeur optimale pour le contenu principal (dashboard, listes, pages de contenu).

```blade
<x-container variant="standard" class="py-8">
    <!-- Contenu -->
</x-container>
```

**Spécifications** :
- Desktop : `max-w-7xl` (1280px)
- Tablet : `max-w-5xl` (1024px) via breakpoint `md`
- Mobile : Pleine largeur avec padding horizontal
- Padding horizontal : `px-4 sm:px-6 lg:px-8`

### Compact

Largeur réduite pour améliorer la lisibilité (profiles, formulaires, modals).

```blade
<x-container variant="compact" class="py-8">
    <!-- Contenu -->
</x-container>
```

**Spécifications** :
- Desktop : `max-w-4xl` (896px)
- Tablet : `max-w-3xl` (768px) via breakpoint `md`
- Mobile : Pleine largeur avec padding horizontal
- Padding horizontal : Identique au standard

### Full

Pleine largeur pour les pages immersives (galeries, visualisations).

```blade
<x-container variant="full" class="py-8">
    <!-- Contenu -->
</x-container>
```

**Spécifications** :
- Toutes tailles : Pleine largeur (pas de max-width)
- Padding horizontal : Identique au standard

## Props

### variant (string, optionnel)

Variante de largeur : `'standard'`, `'compact'`, `'full'`. Défaut : `'standard'`

```blade
<x-container variant="compact">
    <!-- Contenu -->
</x-container>
```

### class (string, optionnel)

Classes CSS additionnelles à ajouter au conteneur.

```blade
<x-container variant="standard" class="py-8 bg-gray-50">
    <!-- Contenu -->
</x-container>
```

## Padding Horizontal Responsive

Le padding horizontal est standardisé pour toutes les variantes :

- **Mobile** (< 640px) : `px-4` (16px)
- **Tablet** (640px - 1024px) : `sm:px-6` (24px)
- **Desktop** (≥ 1024px) : `lg:px-8` (32px)

## Exemples d'Utilisation

### Dashboard Principal

```blade
<x-container variant="standard" class="py-8">
    <x-page-header title="Dashboard" />
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Cards -->
    </div>
</x-container>
```

### Page de Profil

```blade
<x-container variant="compact" class="py-8">
    <x-page-header title="Profile Settings" />
    
    <x-form-card title="Account Information">
        <!-- Formulaire -->
    </x-form-card>
</x-container>
```

### Page Full-Width (Galerie)

```blade
<x-container variant="full" class="py-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Images pleine largeur -->
    </div>
</x-container>
```

### Avec Classes Additionnelles

```blade
<x-container variant="standard" class="py-8 bg-gray-50 dark:bg-space-black">
    <div class="space-y-6">
        <!-- Contenu avec espacement -->
    </div>
</x-container>
```

## Spécifications Techniques

### Structure HTML

```html
<div class="mx-auto max-w-7xl md:max-w-5xl px-4 sm:px-6 lg:px-8">
    <!-- Contenu -->
</div>
```

### Classes CSS par Variante

**Standard** :
- `max-w-7xl md:max-w-5xl` : Largeurs maximales
- `px-4 sm:px-6 lg:px-8` : Padding horizontal responsive
- `mx-auto` : Centrage horizontal

**Compact** :
- `max-w-4xl md:max-w-3xl` : Largeurs maximales réduites
- `px-4 sm:px-6 lg:px-8` : Padding horizontal responsive
- `mx-auto` : Centrage horizontal

**Full** :
- Pas de max-width
- `px-4 sm:px-6 lg:px-8` : Padding horizontal responsive
- `mx-auto` : Centrage horizontal (peu d'effet sans max-width)

## Stratégie de Largeur Maximale

### Par Type de Page

**1. Pages Standard (Dashboard, Liste, Contenu principal)**
- Utiliser `variant="standard"`
- Largeur optimale pour le contenu principal
- Permet d'afficher plusieurs colonnes sur desktop

**2. Pages Compactes (Profile, Formulaires)**
- Utiliser `variant="compact"`
- Largeur réduite pour améliorer la lisibilité
- Idéal pour les formulaires et les pages de profil

**3. Pages Full-Width**
- Utiliser `variant="full"`
- Pour les pages nécessitant toute la largeur disponible
- Utiliser avec parcimonie

## Responsive

Le composant est entièrement responsive avec :
- Largeurs maximales adaptatives selon les breakpoints
- Padding horizontal qui s'adapte à la taille de l'écran
- Support des breakpoints Tailwind (`sm:`, `md:`, `lg:`)

## Bonnes Pratiques

1. **Choisir la bonne variante** : Utiliser `standard` par défaut, `compact` pour les formulaires, `full` pour les galeries
2. **Ajouter le padding vertical** : Utiliser `class="py-8"` ou similaire pour l'espacement vertical
3. **Cohérence** : Utiliser le même variant sur toutes les pages similaires
4. **Mobile-first** : Le composant est conçu mobile-first, le padding s'adapte automatiquement

## Cas d'Usage

- **Dashboard** : `variant="standard"` pour afficher plusieurs colonnes
- **Profile** : `variant="compact"` pour meilleure lisibilité
- **Formulaires** : `variant="compact"` pour formulaires centrés
- **Galeries** : `variant="full"` pour images pleine largeur
- **Listes** : `variant="standard"` pour listes de données

## Relation avec le Design System

Le Container est complémentaire aux autres composants :
- Utilisé avec `<x-page-header>` pour les en-têtes de page
- Utilisé avec `<x-form-card>` pour les formulaires
- Utilisé avec `<x-table>` pour les tableaux de données
- Utilisé avec les grids Tailwind pour les layouts

---

**Référence** : Voir **[DESIGN-SYSTEM-SPACING.md](../DESIGN-SYSTEM-SPACING.md)** pour plus de détails sur les espacements et **[DESIGN-SYSTEM.md](../DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

