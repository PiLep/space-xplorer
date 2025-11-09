# Design System - Typographie

## Vue d'Ensemble

La typographie de Space Xplorer combine une police moderne et lisible (Instrument Sans) avec une police monospace pour les données techniques, créant une hiérarchie claire qui guide l'utilisateur dans l'exploration de l'univers spatial.

## Familles de Polices

### Police Principale : Instrument Sans

**Usage** : Texte principal, titres, interface utilisateur

**Caractéristiques** :
- Moderne et lisible
- Technique mais accessible
- Excellente lisibilité sur fond sombre
- Supporte les poids : 400 (normal), 500 (medium), 600 (semibold), 700 (bold)

**Chargement** :
```html
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
```

### Police Monospace : Courier New

**Usage** : Données techniques, codes, interfaces de vaisseaux spatiaux

**Caractéristiques** :
- Style technique et industriel
- Évoque les écrans CRT des vaisseaux spatiaux
- Parfait pour les données numériques et les codes

**Classe Tailwind** : `font-mono`

## Hiérarchie Typographique

### H1 - Titre Principal

**Usage** : Titres de page, titres principaux

**Spécifications** :
- Taille : `2.5rem` (40px)
- Poids : `font-bold` (700)
- Line-height : `1.2`
- Letter-spacing : `tracking-tight` (-0.025em)
- Margin-bottom : `1rem` (16px)

**Classe Tailwind** : `text-4xl font-bold tracking-tight`

**Exemple** :
```html
<h1 class="text-4xl font-bold tracking-tight text-white mb-4">
  Welcome to Space Xplorer
</h1>
```

### H2 - Titre de Section

**Usage** : Titres de sections, sous-titres

**Spécifications** :
- Taille : `2rem` (32px)
- Poids : `font-bold` (700)
- Line-height : `1.3`
- Letter-spacing : `tracking-tight` (-0.025em)
- Margin-bottom : `0.75rem` (12px)

**Classe Tailwind** : `text-3xl font-bold tracking-tight`

**Exemple** :
```html
<h2 class="text-3xl font-bold tracking-tight text-white mb-3">
  Planet Characteristics
</h2>
```

### H3 - Titre de Sous-Section

**Usage** : Titres de sous-sections, groupes

**Spécifications** :
- Taille : `1.5rem` (24px)
- Poids : `font-semibold` (600)
- Line-height : `1.4`
- Letter-spacing : `tracking-normal` (0em)
- Margin-bottom : `0.5rem` (8px)

**Classe Tailwind** : `text-2xl font-semibold`

**Exemple** :
```html
<h3 class="text-2xl font-semibold text-white mb-2">
  Planet Details
</h3>
```

### H4 - Titre de Groupe

**Usage** : Titres de groupes, labels de sections

**Spécifications** :
- Taille : `1.25rem` (20px)
- Poids : `font-semibold` (600)
- Line-height : `1.5`
- Letter-spacing : `tracking-normal` (0em)
- Margin-bottom : `0.5rem` (8px)

**Classe Tailwind** : `text-xl font-semibold`

**Exemple** :
```html
<h4 class="text-xl font-semibold text-gray-400 uppercase tracking-wide">
  Size
</h4>
```

### Body - Texte Principal

**Usage** : Paragraphes, contenu principal

**Spécifications** :
- Taille : `1rem` (16px)
- Poids : `font-normal` (400)
- Line-height : `1.6`
- Letter-spacing : `tracking-normal` (0em)
- Margin-bottom : `1rem` (16px)

**Classe Tailwind** : `text-base`

**Exemple** :
```html
<p class="text-base text-gray-300 leading-relaxed">
  Discover your home planet and begin your journey through the cosmos.
</p>
```

### Body Large - Texte Important

**Usage** : Texte important, descriptions mises en avant

**Spécifications** :
- Taille : `1.125rem` (18px)
- Poids : `font-normal` (400)
- Line-height : `1.6`
- Letter-spacing : `tracking-normal` (0em)

**Classe Tailwind** : `text-lg`

**Exemple** :
```html
<p class="text-lg text-gray-300 leading-relaxed">
  {{ $planet->description }}
</p>
```

### Small - Texte Secondaire

**Usage** : Texte secondaire, métadonnées, labels

**Spécifications** :
- Taille : `0.875rem` (14px)
- Poids : `font-normal` (400)
- Line-height : `1.5`
- Letter-spacing : `tracking-normal` (0em)

**Classe Tailwind** : `text-sm`

**Exemple** :
```html
<span class="text-sm text-gray-400">
  Last updated: {{ $date }}
</span>
```

### Caption - Texte de Légende

**Usage** : Légendes, notes, texte très petit

**Spécifications** :
- Taille : `0.75rem` (12px)
- Poids : `font-normal` (400)
- Line-height : `1.4`
- Letter-spacing : `tracking-normal` (0em)

**Classe Tailwind** : `text-xs`

**Exemple** :
```html
<span class="text-xs text-gray-500">
  * Required field
</span>
```

## Styles de Texte

### Uppercase

**Usage** : Labels, badges, navigation

**Classe Tailwind** : `uppercase tracking-wide`

**Exemple** :
```html
<span class="text-sm font-semibold text-gray-400 uppercase tracking-wide">
  PLANET TYPE
</span>
```

### Monospace

**Usage** : Données techniques, codes, interfaces

**Classe Tailwind** : `font-mono`

**Exemple** :
```html
<code class="font-mono text-space-primary">
  SYSTEM-STATUS: ONLINE
</code>
```

### Truncate

**Usage** : Texte tronqué avec ellipsis

**Classe Tailwind** : `truncate`

**Exemple** :
```html
<p class="truncate text-gray-300">
  Very long text that will be truncated...
</p>
```

## Couleurs de Texte

Voir **[DESIGN-SYSTEM-COLORS.md](./DESIGN-SYSTEM-COLORS.md)** pour la palette complète.

### Couleurs Principales

- **Texte principal** : `text-white` (`#ffffff`)
- **Texte secondaire** : `text-gray-400` (`#aaaaaa`)
- **Texte muted** : `text-gray-500` (`#666666`)

### Couleurs Sémantiques

- **Primary** : `text-space-primary` (`#00ff88`)
- **Secondary** : `text-space-secondary` (`#00aaff`)
- **Success** : `text-success` (`#00ff88`)
- **Error** : `text-error` (`#ff4444`)
- **Warning** : `text-warning` (`#ffaa00`)
- **Info** : `text-info` (`#00aaff`)

## Responsive Typography

### Mobile (< 640px)

- **H1** : `text-3xl` (30px)
- **H2** : `text-2xl` (24px)
- **H3** : `text-xl` (20px)
- **Body** : `text-base` (16px)

### Tablet (640px - 1024px)

- **H1** : `text-4xl` (36px)
- **H2** : `text-3xl` (30px)
- **H3** : `text-2xl` (24px)
- **Body** : `text-base` (16px)

### Desktop (> 1024px)

- **H1** : `text-4xl` (40px)
- **H2** : `text-3xl` (32px)
- **H3** : `text-2xl` (24px)
- **Body** : `text-base` (16px)

## Exemples d'Utilisation

### Titre de Page

```html
<div class="mb-8">
  <h1 class="text-4xl font-bold text-white mb-2">
    Welcome back, Explorer!
  </h1>
  <p class="text-lg text-gray-400">
    Discover your home planet and begin your journey through the cosmos.
  </p>
</div>
```

### Section avec Titre

```html
<div class="mb-6">
  <h3 class="text-2xl font-semibold text-white mb-4">
    Planet Characteristics
  </h3>
  <div class="space-y-4">
    <!-- Contenu -->
  </div>
</div>
```

### Label avec Valeur

```html
<div class="bg-surface-dark rounded-lg p-4">
  <div class="flex items-center mb-2">
    <span class="text-sm font-semibold text-gray-400 uppercase tracking-wide">
      Size
    </span>
  </div>
  <p class="text-lg font-semibold text-white capitalize">
    {{ $planet->size }}
  </p>
</div>
```

### Données Techniques (Monospace)

```html
<div class="font-mono text-space-primary text-sm">
  <div>SYSTEM: ONLINE</div>
  <div>STATUS: OPERATIONAL</div>
  <div>TIME: {{ now()->format('H:i:s') }}</div>
</div>
```

## Accessibilité

### Contraste

- **Texte blanc sur fond noir** : Ratio 21:1 ✅
- **Texte gray-400 sur fond noir** : Ratio 4.5:1 ✅
- **Texte gray-500 sur fond noir** : Ratio 3.2:1 ⚠️ (utiliser avec précaution)

### Tailles Minimales

- Texte principal : Minimum 16px (1rem)
- Texte secondaire : Minimum 14px (0.875rem)
- Texte de légende : Minimum 12px (0.75rem) - uniquement pour les labels

### Line-height

- Minimum 1.5 pour le texte principal
- 1.2-1.3 pour les titres
- 1.4-1.6 pour les paragraphes

## Configuration Tailwind

La configuration actuelle utilise Instrument Sans comme police principale :

```css
@theme {
  --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
}
```

## Notes de Design

- **Hiérarchie claire** : La hiérarchie typographique guide l'utilisateur dans la lecture
- **Lisibilité** : Les tailles et espacements assurent une excellente lisibilité
- **Style technique** : La police monospace évoque les interfaces de vaisseaux spatiaux
- **Cohérence** : Utiliser ces styles de manière cohérente à travers l'application

---

**Référence** : Voir **[DESIGN-SYSTEM.md](./DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

