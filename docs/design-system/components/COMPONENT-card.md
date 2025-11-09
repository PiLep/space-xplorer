# COMPONENT-Card

## Vue d'Ensemble

Le composant Card est utilisé pour afficher des contenus groupés dans un conteneur visuellement distinct. Il respecte le design system avec un fond sombre, des bordures subtiles, et un espacement cohérent.

**Quand l'utiliser** :
- Affichage d'informations groupées
- Conteneurs de contenu
- Sections de contenu dans une page
- Affichage de données structurées

## Design

### Apparence

Les cards utilisent un fond sombre (`bg-surface-dark`), des bordures subtiles (`border-border-dark`), des coins arrondis (`rounded-lg`), et un padding généreux pour créer une séparation visuelle claire.

### Variantes

#### Standard

**Usage** : Card standard pour la plupart des contenus

**Spécifications** :
- Background : `bg-surface-dark` (`#1a1a1a`)
- Border : `border border-border-dark` (`#333333`)
- Padding : `p-6` (24px)
- Border radius : `rounded-lg`

**Exemple** :
```html
<div class="bg-surface-dark border border-border-dark rounded-lg p-6">
  <h3 class="text-2xl font-semibold text-white mb-4">Titre</h3>
  <p class="text-gray-300">Contenu</p>
</div>
```

#### Interactive

**Usage** : Card cliquable avec effet hover

**Spécifications** :
- Hover : `hover:bg-surface-medium` (`#2a2a2a`)
- Cursor : `cursor-pointer`
- Transition : `transition-colors duration-150`
- Hover transform : `hover:-translate-y-1` (optionnel)

**Exemple** :
```html
<div class="bg-surface-dark border border-border-dark rounded-lg p-6 hover:bg-surface-medium cursor-pointer transition-all duration-150 hover:-translate-y-1 hover:shadow-lg">
  <h3 class="text-2xl font-semibold text-white mb-4">Titre</h3>
  <p class="text-gray-300">Contenu</p>
</div>
```

#### Elevated

**Usage** : Card avec ombre pour plus de profondeur

**Spécifications** :
- Shadow : `shadow-lg`
- Background : `bg-surface-dark`

**Exemple** :
```html
<div class="bg-surface-dark border border-border-dark rounded-lg p-6 shadow-lg">
  <h3 class="text-2xl font-semibold text-white mb-4">Titre</h3>
  <p class="text-gray-300">Contenu</p>
</div>
```

### États

#### Default

État par défaut de la card, contenu statique.

#### Hover (si interactive)

Transition vers un fond plus clair avec légère élévation.

#### Focus (si cliquable)

Contour visible pour l'accessibilité au clavier.

## Spécifications Techniques

### Classes Tailwind

#### Structure de Base

```html
<div class="bg-surface-dark border border-border-dark rounded-lg p-6">
  <!-- Contenu -->
</div>
```

#### Card Standard

```html
<div class="bg-surface-dark border border-border-dark rounded-lg p-6 mb-6">
  <h3 class="text-2xl font-semibold text-white mb-4">Titre</h3>
  <p class="text-gray-300 mb-4">Description</p>
  <div class="flex gap-4">
    <!-- Actions -->
  </div>
</div>
```

#### Card Interactive

```html
<div class="
  bg-surface-dark 
  border 
  border-border-dark 
  rounded-lg 
  p-6 
  hover:bg-surface-medium 
  hover:-translate-y-1
  hover:shadow-lg
  cursor-pointer 
  transition-all 
  duration-300 
  ease-in-out
">
  <!-- Contenu -->
</div>
```

#### Card avec Header

```html
<div class="bg-surface-dark border border-border-dark rounded-lg overflow-hidden">
  <!-- Header -->
  <div class="bg-gradient-to-r from-space-primary to-space-secondary px-8 py-6">
    <h2 class="text-3xl font-bold text-space-black mb-2">Titre</h2>
    <p class="text-space-black opacity-80 text-lg">Sous-titre</p>
  </div>
  
  <!-- Body -->
  <div class="px-8 py-6">
    <p class="text-gray-300">Contenu</p>
  </div>
</div>
```

### Structure HTML

```html
<div class="bg-surface-dark border border-border-dark rounded-lg p-6">
  <!-- Header (optionnel) -->
  <div class="mb-4">
    <h3 class="text-2xl font-semibold text-white">Titre</h3>
  </div>
  
  <!-- Content -->
  <div>
    <p class="text-gray-300">Contenu</p>
  </div>
  
  <!-- Footer (optionnel) -->
  <div class="mt-4 flex gap-4">
    <!-- Actions -->
  </div>
</div>
```

## Code d'Implémentation

### Blade Component (Optionnel)

```blade
@props(['title' => null, 'interactive' => false, 'elevated' => false])

@php
    $classes = 'bg-surface-dark border border-border-dark rounded-lg p-6';
    
    if ($interactive) {
        $classes .= ' hover:bg-surface-medium hover:-translate-y-1 hover:shadow-lg cursor-pointer transition-all duration-300 ease-in-out';
    }
    
    if ($elevated) {
        $classes .= ' shadow-lg';
    }
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @if($title)
        <h3 class="text-2xl font-semibold text-white mb-4">{{ $title }}</h3>
    @endif
    
    {{ $slot }}
</div>
```

### Utilisation

```blade
<!-- Simple -->
<x-card title="Titre">
  <p class="text-gray-300">Contenu</p>
</x-card>

<!-- Interactive -->
<x-card title="Titre" :interactive="true">
  <p class="text-gray-300">Contenu cliquable</p>
</x-card>

<!-- Elevated -->
<x-card title="Titre" :elevated="true">
  <p class="text-gray-300">Contenu avec ombre</p>
</x-card>
```

## Exemples d'Utilisation

### Exemple 1 : Card Simple

```html
<div class="bg-surface-dark border border-border-dark rounded-lg p-6 mb-6">
  <h3 class="text-2xl font-semibold text-white mb-4">Planet Characteristics</h3>
  <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="bg-surface-medium rounded-lg p-4">
      <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-2">Size</h4>
      <p class="text-lg font-semibold text-white capitalize">Large</p>
    </div>
    <!-- Plus de caractéristiques -->
  </div>
</div>
```

### Exemple 2 : Card avec Header Coloré

```html
<div class="bg-surface-dark border border-border-dark rounded-lg overflow-hidden mb-8">
  <!-- Header -->
  <div class="bg-gradient-to-r from-space-primary to-space-secondary px-8 py-6">
    <h2 class="text-3xl font-bold text-space-black mb-2">Kepler-452b</h2>
    <p class="text-space-black opacity-80 text-lg capitalize">Tellurique</p>
  </div>
  
  <!-- Description -->
  <div class="px-8 py-6 border-b border-border-dark">
    <p class="text-gray-300 text-lg leading-relaxed">
      Description de la planète...
    </p>
  </div>
  
  <!-- Characteristics -->
  <div class="px-8 py-6">
    <h3 class="text-xl font-semibold text-white mb-4">Caractéristiques</h3>
    <!-- Grille de caractéristiques -->
  </div>
</div>
```

### Exemple 3 : Card Interactive

```html
<div 
  onclick="window.location.href='/planet/1'"
  class="bg-surface-dark border border-border-dark rounded-lg p-6 hover:bg-surface-medium hover:-translate-y-1 hover:shadow-lg cursor-pointer transition-all duration-300 ease-in-out"
>
  <h3 class="text-2xl font-semibold text-white mb-2">Planet Name</h3>
  <p class="text-gray-400 mb-4">Type: Tellurique</p>
  <div class="flex items-center text-space-primary">
    <span>Explorer →</span>
  </div>
</div>
```

### Exemple 4 : Card avec Actions

```html
<div class="bg-surface-dark border border-border-dark rounded-lg p-6">
  <h3 class="text-2xl font-semibold text-white mb-4">Planet Details</h3>
  <p class="text-gray-300 mb-6">Description de la planète...</p>
  
  <!-- Actions -->
  <div class="flex gap-4">
    <button class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold py-2 px-4 rounded-lg transition-colors duration-150">
      Explorer
    </button>
    <button class="bg-transparent hover:bg-surface-medium text-gray-400 hover:text-white font-bold py-2 px-4 rounded-lg transition-colors duration-150">
      Annuler
    </button>
  </div>
</div>
```

## Responsive

Les cards s'adaptent automatiquement :

- **Mobile** : Padding réduit (`p-4`), layout vertical
- **Tablet** : Padding standard (`p-6`), layout adaptatif
- **Desktop** : Padding généreux (`p-8`), layout optimisé

**Exemple responsive** :
```html
<div class="bg-surface-dark border border-border-dark rounded-lg p-4 md:p-6 lg:p-8">
  <!-- Contenu -->
</div>
```

## Accessibilité

### Structure Sémantique

- Utiliser des éléments sémantiques (`<article>`, `<section>`) si approprié
- Titres hiérarchiques (`h2`, `h3`, etc.)
- Contenu structuré et logique

### Focus (si interactive)

- Contour visible pour la navigation au clavier
- Transition fluide

### ARIA

```html
<article 
  class="bg-surface-dark border border-border-dark rounded-lg p-6"
  aria-labelledby="card-title"
>
  <h3 id="card-title" class="text-2xl font-semibold text-white mb-4">
    Titre
  </h3>
  <p>Contenu</p>
</article>
```

## Notes de Design

- **Cohérence** : Utiliser les mêmes styles pour toutes les cards
- **Hiérarchie** : Utiliser les headers pour créer une hiérarchie claire
- **Espacement** : Padding généreux pour la lisibilité
- **Séparation** : Bordures subtiles pour la séparation visuelle

---

**Référence** : Voir **[DESIGN-SYSTEM.md](../DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

