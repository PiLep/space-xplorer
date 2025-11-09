# COMPONENT-Planet Card

## Vue d'Ensemble

Le composant Planet Card est spécialement conçu pour afficher les caractéristiques et informations d'une planète dans Space Xplorer. Il utilise un header avec gradient coloré selon le type de planète et une grille de caractéristiques pour une présentation claire et immersive.

**Quand l'utiliser** :
- Affichage de la planète d'origine d'un joueur
- Visualisation des caractéristiques d'une planète
- Cards de planètes dans une liste
- Détails d'une planète explorée

## Design

### Apparence

Le Planet Card combine un header avec gradient coloré selon le type de planète, une description, et une grille de caractéristiques organisées de manière claire et lisible.

### Structure

#### Header avec Gradient

Le header utilise un gradient qui varie selon le type de planète :

- **Tellurique** : Gradient bleu-vert (`from-blue-600 to-purple-600`)
- **Gazeuse** : Gradient violet (`from-purple-600 to-pink-600`)
- **Glacée** : Gradient cyan (`from-cyan-500 to-blue-500`)
- **Désertique** : Gradient orange (`from-orange-500 to-red-500`)
- **Océanique** : Gradient vert-cyan (`from-green-600 to-cyan-500`)

#### Description

Section de description avec texte lisible et espacement généreux.

#### Grille de Caractéristiques

Grille responsive affichant les caractéristiques de la planète :
- Size (Taille)
- Temperature (Température)
- Atmosphere (Atmosphère)
- Terrain
- Resources (Ressources)
- Type

### Variantes

#### Standard (Dashboard)

**Usage** : Affichage principal sur le dashboard

**Spécifications** :
- Header avec gradient selon le type
- Description complète
- Grille de 6 caractéristiques (3 colonnes sur desktop)

**Exemple** :
```html
<div class="bg-surface-dark border border-border-dark rounded-lg overflow-hidden mb-8">
  <!-- Header -->
  <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6">
    <h2 class="text-3xl font-bold text-white mb-2">Kepler-452b</h2>
    <p class="text-blue-100 text-lg capitalize">Tellurique</p>
  </div>

  <!-- Description -->
  <div class="px-8 py-6 border-b border-border-dark">
    <p class="text-gray-300 text-lg leading-relaxed">
      Description de la planète...
    </p>
  </div>

  <!-- Characteristics -->
  <div class="px-8 py-6">
    <h3 class="text-xl font-semibold text-white mb-4">Planet Characteristics</h3>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Caractéristiques -->
    </div>
  </div>
</div>
```

#### Compact (Liste)

**Usage** : Affichage dans une liste de planètes

**Spécifications** :
- Header réduit
- Description tronquée
- Caractéristiques principales seulement

## Spécifications Techniques

### Classes Tailwind

#### Structure Complète

```html
<div class="bg-surface-dark border border-border-dark rounded-lg overflow-hidden mb-8">
  <!-- Header avec gradient -->
  <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6">
    <h2 class="text-3xl font-bold text-white mb-2">{{ $planet->name }}</h2>
    <p class="text-blue-100 text-lg capitalize">{{ $planet->type }}</p>
  </div>

  <!-- Description -->
  <div class="px-8 py-6 border-b border-border-dark">
    <p class="text-gray-300 text-lg leading-relaxed">
      {{ $planet->description }}
    </p>
  </div>

  <!-- Characteristics -->
  <div class="px-8 py-6">
    <h3 class="text-xl font-semibold text-white mb-4">Planet Characteristics</h3>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Caractéristique -->
      <div class="bg-surface-medium rounded-lg p-4">
        <div class="mb-2">
          <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Size</h4>
        </div>
        <p class="text-lg font-semibold text-white capitalize">{{ $planet->size }}</p>
      </div>
      <!-- Plus de caractéristiques -->
    </div>
  </div>
</div>
```

#### Gradient par Type de Planète

```php
@php
    $gradients = [
        'tellurique' => 'from-blue-600 to-purple-600',
        'gazeuse' => 'from-purple-600 to-pink-600',
        'glacée' => 'from-cyan-500 to-blue-500',
        'désertique' => 'from-orange-500 to-red-500',
        'océanique' => 'from-green-600 to-cyan-500',
    ];
    
    $gradient = $gradients[strtolower($planet->type)] ?? 'from-gray-600 to-gray-700';
@endphp

<div class="bg-gradient-to-r {{ $gradient }} px-8 py-6">
  <!-- Header -->
</div>
```

### Structure HTML

```html
<div class="bg-surface-dark border border-border-dark rounded-lg overflow-hidden">
  <!-- Header -->
  <div class="bg-gradient-to-r [gradient] px-8 py-6">
    <h2 class="text-3xl font-bold text-white mb-2">Nom de la planète</h2>
    <p class="text-[color]-100 text-lg capitalize">Type</p>
  </div>

  <!-- Description -->
  <div class="px-8 py-6 border-b border-border-dark">
    <p class="text-gray-300 text-lg leading-relaxed">
      Description
    </p>
  </div>

  <!-- Characteristics -->
  <div class="px-8 py-6">
    <h3 class="text-xl font-semibold text-white mb-4">Planet Characteristics</h3>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Caractéristique -->
      <div class="bg-surface-medium rounded-lg p-4">
        <div class="mb-2">
          <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Label</h4>
        </div>
        <p class="text-lg font-semibold text-white capitalize">Valeur</p>
      </div>
    </div>
  </div>
</div>
```

## Code d'Implémentation

### Blade Template (Dashboard)

```blade
@if ($planet)
  <div class="bg-surface-dark border border-border-dark rounded-lg overflow-hidden mb-8">
    @php
      $gradients = [
        'tellurique' => ['from' => 'from-blue-600', 'to' => 'to-purple-600', 'text' => 'text-blue-100'],
        'gazeuse' => ['from' => 'from-purple-600', 'to' => 'to-pink-600', 'text' => 'text-purple-100'],
        'glacée' => ['from' => 'from-cyan-500', 'to' => 'to-blue-500', 'text' => 'text-cyan-100'],
        'désertique' => ['from' => 'from-orange-500', 'to' => 'to-red-500', 'text' => 'text-orange-100'],
        'océanique' => ['from' => 'from-green-600', 'to' => 'to-cyan-500', 'text' => 'text-green-100'],
      ];
      
      $planetGradient = $gradients[strtolower($planet->type)] ?? ['from' => 'from-gray-600', 'to' => 'to-gray-700', 'text' => 'text-gray-100'];
    @endphp

    <!-- Header -->
    <div class="bg-gradient-to-r {{ $planetGradient['from'] }} {{ $planetGradient['to'] }} px-8 py-6">
      <h2 class="text-3xl font-bold text-white mb-2">{{ $planet->name }}</h2>
      <p class="{{ $planetGradient['text'] }} text-lg capitalize">{{ $planet->type }}</p>
    </div>

    <!-- Description -->
    <div class="px-8 py-6 border-b border-border-dark">
      <p class="text-gray-300 text-lg leading-relaxed">
        {{ $planet->description }}
      </p>
    </div>

    <!-- Characteristics -->
    <div class="px-8 py-6">
      <h3 class="text-xl font-semibold text-white mb-4">Planet Characteristics</h3>
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Size -->
        <div class="bg-surface-medium rounded-lg p-4">
          <div class="mb-2">
            <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Size</h4>
          </div>
          <p class="text-lg font-semibold text-white capitalize">{{ $planet->size }}</p>
        </div>

        <!-- Temperature -->
        <div class="bg-surface-medium rounded-lg p-4">
          <div class="mb-2">
            <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Temperature</h4>
          </div>
          <p class="text-lg font-semibold text-white capitalize">{{ $planet->temperature }}</p>
        </div>

        <!-- Atmosphere -->
        <div class="bg-surface-medium rounded-lg p-4">
          <div class="mb-2">
            <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Atmosphere</h4>
          </div>
          <p class="text-lg font-semibold text-white capitalize">{{ $planet->atmosphere }}</p>
        </div>

        <!-- Terrain -->
        <div class="bg-surface-medium rounded-lg p-4">
          <div class="mb-2">
            <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Terrain</h4>
          </div>
          <p class="text-lg font-semibold text-white capitalize">{{ $planet->terrain }}</p>
        </div>

        <!-- Resources -->
        <div class="bg-surface-medium rounded-lg p-4">
          <div class="mb-2">
            <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Resources</h4>
          </div>
          <p class="text-lg font-semibold text-white capitalize">{{ $planet->resources }}</p>
        </div>

        <!-- Type -->
        <div class="bg-surface-medium rounded-lg p-4">
          <div class="mb-2">
            <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Type</h4>
          </div>
          <p class="text-lg font-semibold text-white capitalize">{{ $planet->type }}</p>
        </div>
      </div>
    </div>
  </div>
@endif
```

## Exemples d'Utilisation

### Exemple 1 : Planet Card Standard

Voir le code d'implémentation ci-dessus pour l'exemple complet.

### Exemple 2 : Planet Card avec Actions

```html
<div class="bg-surface-dark border border-border-dark rounded-lg overflow-hidden mb-8">
  <!-- Header et contenu comme ci-dessus -->
  
  <!-- Actions -->
  <div class="px-8 py-6 border-t border-border-dark">
    <div class="flex justify-center gap-4">
      <button class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold py-3 px-6 rounded-lg transition-colors duration-150">
        Explore More Planets
      </button>
      <a href="/profile" class="bg-surface-medium hover:bg-surface-dark text-white font-bold py-3 px-6 rounded-lg transition-colors duration-150">
        View Profile
      </a>
    </div>
  </div>
</div>
```

## Responsive

Le Planet Card s'adapte automatiquement :

- **Mobile** : 
  - Header : Padding réduit (`px-4 py-4`)
  - Grille : 1 colonne
  - Caractéristiques : Layout vertical

- **Tablet** : 
  - Header : Padding standard (`px-6 py-5`)
  - Grille : 2 colonnes
  - Caractéristiques : Layout adaptatif

- **Desktop** : 
  - Header : Padding généreux (`px-8 py-6`)
  - Grille : 3 colonnes
  - Caractéristiques : Layout optimisé

**Exemple responsive** :
```html
<div class="px-4 md:px-6 lg:px-8 py-4 md:py-5 lg:py-6">
  <!-- Header -->
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5 lg:gap-6">
  <!-- Caractéristiques -->
</div>
```

## Accessibilité

### Structure Sémantique

- Utiliser `<article>` pour la card complète
- Titres hiérarchiques (`h2` pour le nom, `h3` pour la section)
- Labels clairs pour les caractéristiques

### Contraste

- Texte blanc sur gradient : Vérifier selon le gradient utilisé
- Texte sur fond sombre : Ratio 21:1 ✅

### ARIA

```html
<article 
  class="bg-surface-dark border border-border-dark rounded-lg overflow-hidden"
  aria-labelledby="planet-name"
>
  <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6">
    <h2 id="planet-name" class="text-3xl font-bold text-white mb-2">
      {{ $planet->name }}
    </h2>
    <p class="text-blue-100 text-lg capitalize">{{ $planet->type }}</p>
  </div>
  <!-- Contenu -->
</article>
```

## Notes de Design

- **Gradients** : Utiliser des gradients différents selon le type de planète pour créer une identité visuelle unique
- **Hiérarchie** : Le header avec gradient attire l'attention sur le nom et le type
- **Lisibilité** : Espacement généreux et typographie claire pour une excellente lisibilité
- **Cohérence** : Respecter la structure et les espacements du design system

---

**Référence** : Voir **[DESIGN-SYSTEM.md](../DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

