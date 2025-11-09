# Design System - Espacements & Grilles

## Vue d'Ensemble

Le système d'espacement de Space Xplorer utilise une échelle basée sur 4px pour créer une cohérence visuelle à travers toute l'application. Les grilles adaptatives assurent un layout responsive sur tous les appareils.

## Système d'Espacement

### Base : 4px

Tous les espacements sont des multiples de 4px pour garantir la cohérence et faciliter l'alignement.

### Échelle Standardisée

| Taille | Valeur | Usage | Classe Tailwind |
|--------|--------|-------|-----------------|
| **xs** | 4px | Espacement minimal, padding interne | `p-1`, `m-1`, `gap-1` |
| **sm** | 8px | Espacement petit, padding compact | `p-2`, `m-2`, `gap-2` |
| **md** | 12px | Espacement moyen | `p-3`, `m-3`, `gap-3` |
| **base** | 16px | Espacement standard, padding de base | `p-4`, `m-4`, `gap-4` |
| **lg** | 24px | Espacement large, sections | `p-6`, `m-6`, `gap-6` |
| **xl** | 32px | Espacement très large, groupes | `p-8`, `m-8`, `gap-8` |
| **2xl** | 48px | Espacement extra large, sections majeures | `p-12`, `m-12`, `gap-12` |
| **3xl** | 64px | Espacement maximal, pages | `p-16`, `m-16`, `gap-16` |
| **4xl** | 96px | Espacement exceptionnel | `p-24`, `m-24`, `gap-24` |
| **5xl** | 128px | Espacement très exceptionnel | `p-32`, `m-32`, `gap-32` |

### Utilisation

#### Padding

```html
<!-- Padding compact -->
<div class="p-2">Contenu</div>

<!-- Padding standard -->
<div class="p-4">Contenu</div>

<!-- Padding large -->
<div class="p-8">Contenu</div>

<!-- Padding responsive -->
<div class="p-4 md:p-6 lg:p-8">Contenu</div>
```

#### Margin

```html
<!-- Margin bottom standard -->
<div class="mb-4">Section</div>

<!-- Margin top large -->
<div class="mt-8">Section</div>

<!-- Margin horizontal -->
<div class="mx-4">Contenu</div>

<!-- Margin vertical -->
<div class="my-6">Section</div>
```

#### Gap (Flexbox/Grid)

```html
<!-- Gap petit -->
<div class="flex gap-2">
  <div>Item 1</div>
  <div>Item 2</div>
</div>

<!-- Gap standard -->
<div class="grid grid-cols-3 gap-4">
  <div>Item 1</div>
  <div>Item 2</div>
  <div>Item 3</div>
</div>

<!-- Gap large -->
<div class="flex flex-col gap-8">
  <div>Section 1</div>
  <div>Section 2</div>
</div>
```

## Grilles

### Desktop (> 1024px)

- **Colonnes** : 12
- **Gutter** : 24px (`gap-6`)
- **Max-width container** : 1280px (`max-w-7xl`)

**Exemple** :
```html
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
  <div class="grid grid-cols-12 gap-6">
    <div class="col-span-12 md:col-span-8 lg:col-span-9">Contenu principal</div>
    <div class="col-span-12 md:col-span-4 lg:col-span-3">Sidebar</div>
  </div>
</div>
```

### Tablet (640px - 1024px)

- **Colonnes** : 8
- **Gutter** : 16px (`gap-4`)
- **Max-width container** : 1024px

**Exemple** :
```html
<div class="grid grid-cols-8 gap-4">
  <div class="col-span-8 md:col-span-5">Contenu principal</div>
  <div class="col-span-8 md:col-span-3">Sidebar</div>
</div>
```

### Mobile (< 640px)

- **Colonnes** : 4
- **Gutter** : 12px (`gap-3`)
- **Padding horizontal** : 16px (`px-4`)

**Exemple** :
```html
<div class="grid grid-cols-4 gap-3 px-4">
  <div class="col-span-4">Contenu plein largeur</div>
  <div class="col-span-2">Colonne 1</div>
  <div class="col-span-2">Colonne 2</div>
</div>
```

## Containers

### Container Standard

**Usage** : Contenu principal de la page

```html
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
  <!-- Contenu -->
</div>
```

**Spécifications** :
- Max-width : 1280px (`max-w-7xl`)
- Centré : `mx-auto`
- Padding horizontal responsive :
  - Mobile : 16px (`px-4`)
  - Tablet : 24px (`sm:px-6`)
  - Desktop : 32px (`lg:px-8`)

### Container Compact

**Usage** : Formulaires, modals, cards

```html
<div class="max-w-md mx-auto px-4">
  <!-- Contenu -->
</div>
```

**Spécifications** :
- Max-width : 448px (`max-w-md`)
- Centré : `mx-auto`
- Padding horizontal : 16px (`px-4`)

### Container Large

**Usage** : Dashboards, tableaux de bord

```html
<div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
  <!-- Contenu -->
</div>
```

**Spécifications** :
- Max-width : 1280px (`max-w-screen-xl`)
- Centré : `mx-auto`
- Padding horizontal responsive

## Espacements dans les Composants

### Cards

```html
<div class="bg-surface-dark rounded-lg p-6 mb-6">
  <h3 class="text-2xl font-semibold text-white mb-4">Titre</h3>
  <p class="text-gray-300 mb-4">Description</p>
  <div class="flex gap-4">
    <button>Action 1</button>
    <button>Action 2</button>
  </div>
</div>
```

**Spécifications** :
- Padding interne : 24px (`p-6`)
- Margin bottom : 24px (`mb-6`)
- Espacement titre/contenu : 16px (`mb-4`)
- Gap entre boutons : 16px (`gap-4`)

### Formulaires

```html
<form class="space-y-4">
  <div>
    <label class="block mb-2">Label</label>
    <input class="w-full p-3">
  </div>
  <div>
    <label class="block mb-2">Label</label>
    <input class="w-full p-3">
  </div>
  <button class="w-full p-3">Submit</button>
</form>
```

**Spécifications** :
- Espacement vertical entre champs : 16px (`space-y-4`)
- Margin bottom label : 8px (`mb-2`)
- Padding input : 12px (`p-3`)

### Navigation

```html
<nav class="px-4 sm:px-6 lg:px-8">
  <div class="flex items-center justify-between h-16">
    <div class="flex items-center gap-4">
      <a>Link 1</a>
      <a>Link 2</a>
    </div>
  </div>
</nav>
```

**Spécifications** :
- Padding horizontal responsive
- Hauteur : 64px (`h-16`)
- Gap entre liens : 16px (`gap-4`)

## Breakpoints

### Mobile First

Les breakpoints Tailwind standard sont utilisés :

| Breakpoint | Taille | Usage |
|------------|--------|-------|
| **sm** | 640px | Tablette petite |
| **md** | 768px | Tablette |
| **lg** | 1024px | Desktop |
| **xl** | 1280px | Desktop large |
| **2xl** | 1536px | Desktop très large |

### Exemples Responsive

```html
<!-- Padding responsive -->
<div class="p-4 md:p-6 lg:p-8">
  Contenu
</div>

<!-- Margin responsive -->
<div class="mb-4 md:mb-6 lg:mb-8">
  Section
</div>

<!-- Gap responsive -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
  <div>Item 1</div>
  <div>Item 2</div>
  <div>Item 3</div>
</div>
```

## Règles d'Espacement

### Vertical Rhythm

Maintenir un rythme vertical cohérent :

- **Sections principales** : 32px (`mb-8`)
- **Sous-sections** : 24px (`mb-6`)
- **Groupes** : 16px (`mb-4`)
- **Éléments** : 8px (`mb-2`)

### Horizontal Spacing

- **Éléments dans un groupe** : 16px (`gap-4`)
- **Sections** : 24px (`gap-6`)
- **Colonnes** : 24px (`gap-6`)

### Padding vs Margin

- **Padding** : Espacement interne d'un élément
- **Margin** : Espacement externe entre éléments
- **Gap** : Espacement dans flexbox/grid

## Exemples Complets

### Page Standard

```html
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <!-- Titre -->
  <div class="mb-8">
    <h1 class="text-4xl font-bold text-white mb-2">Titre</h1>
    <p class="text-lg text-gray-400">Description</p>
  </div>

  <!-- Contenu -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="bg-surface-dark rounded-lg p-6">
      Card 1
    </div>
    <div class="bg-surface-dark rounded-lg p-6">
      Card 2
    </div>
    <div class="bg-surface-dark rounded-lg p-6">
      Card 3
    </div>
  </div>
</div>
```

### Formulaire

```html
<div class="max-w-md mx-auto px-4 mt-8">
  <div class="bg-surface-dark rounded-lg p-6 md:p-8">
    <h2 class="text-2xl font-bold text-white mb-6">Formulaire</h2>
    <form class="space-y-4">
      <div>
        <label class="block text-gray-300 mb-2">Label</label>
        <input class="w-full p-3 rounded">
      </div>
      <button class="w-full p-3 mt-6">Submit</button>
    </form>
  </div>
</div>
```

## Notes de Design

- **Cohérence** : Toujours utiliser l'échelle standardisée (multiples de 4px)
- **Responsive** : Adapter les espacements selon la taille d'écran
- **Hiérarchie** : Utiliser les espacements pour créer une hiérarchie visuelle
- **Lisibilité** : Assurer un espacement suffisant pour la lisibilité

---

**Référence** : Voir **[DESIGN-SYSTEM.md](./DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

