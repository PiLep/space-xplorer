# COMPONENT-Planet Card

## Vue d'Ensemble

Le composant Planet Card est spécialement conçu pour afficher les caractéristiques et informations d'une planète dans Space Xplorer. Il utilise un layout horizontal avec image, header avec nom et type, description, et liste de caractéristiques pour une présentation claire et immersive.

**Quand l'utiliser** :
- Affichage de la planète d'origine d'un joueur
- Visualisation des caractéristiques d'une planète
- Cards de planètes dans une liste
- Détails d'une planète explorée

## Design

### Apparence

Le Planet Card combine :
- Image de la planète (optionnelle)
- Header avec nom en majuscules et type
- Description de la planète
- Liste de caractéristiques avec format terminal (SIZE, TEMP, ATMOS, TERRAIN, RESOURCES, TYPE)

### Structure

#### Layout Horizontal

Le composant utilise un layout horizontal avec :
- **Image** (gauche) : Image de la planète (1/3 de la largeur sur desktop)
- **Contenu** (droite) : Header, description et caractéristiques

#### Header

- Nom de la planète en majuscules avec glow subtil
- Type de planète en majuscules avec tracking large

#### Description

Section de description avec :
- Message terminal `[INFO] Planetary description retrieved`
- Texte de description lisible

#### Liste de Caractéristiques

Liste verticale avec format terminal affichant :
- SIZE (Taille)
- TEMP (Température)
- ATMOS (Atmosphère)
- TERRAIN
- RESOURCES (Ressources)
- TYPE

Chaque caractéristique affiche le label en gris et la valeur en couleur primaire.

### Variantes

#### Standard (Dashboard)

**Usage** : Affichage principal sur le dashboard

**Spécifications** :
- Layout horizontal avec image
- Header avec nom et type
- Description complète
- Liste de 6 caractéristiques avec format terminal

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

### Composant Blade

**Fichier** : `resources/views/components/planet-card.blade.php`

```blade
@props([
    'planet',
    'showImage' => true,
    'imageUrl' => null,
])

@if($planet)
    <div class="bg-white dark:bg-surface-dark shadow-lg rounded-lg overflow-hidden mb-8 terminal-border-simple scan-effect hologram">
        <div class="flex flex-col md:flex-row">
            @if($showImage)
                <!-- Planet Image -->
                <div class="md:w-1/3 lg:w-2/5 flex-shrink-0">
                    <img 
                        src="{{ $imageUrl ?? 'https://images.unsplash.com/photo-1446776653964-20c1d3a81b06?w=800&h=600&fit=crop&q=80' }}" 
                        alt="{{ $planet->name }}"
                        class="w-full h-64 md:h-full object-cover"
                        onerror="this.src='https://via.placeholder.com/800x600/1a1a1a/00ff88?text={{ urlencode($planet->name) }}'"
                    >
                </div>
            @endif

            <!-- Planet Content -->
            <div class="flex-1 flex flex-col">
                <!-- Planet Header -->
                <div class="px-8 py-6 border-b border-gray-200 dark:border-border-dark">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2 dark:text-glow-subtle font-mono">{{ strtoupper($planet->name) }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 text-lg uppercase tracking-wider font-mono">{{ $planet->type }}</p>
                </div>

                <!-- Planet Description -->
                <div class="px-8 py-6 border-b border-gray-200 dark:border-border-dark flex-1 font-mono">
                    <div class="text-sm text-gray-500 dark:text-gray-500 mb-3">
                        [INFO] Planetary description retrieved
                    </div>
                    <p class="text-gray-700 dark:text-white text-base leading-relaxed">
                        {{ $planet->description }}
                    </p>
                </div>

                <!-- Planet Characteristics -->
                <div class="px-8 py-6 border-t border-gray-200 dark:border-border-dark">
                    <x-terminal-prompt command="query_planet_data" />
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 dark:text-glow-subtle font-mono">SYSTEM_DATA</h3>
                    <div class="space-y-3 font-mono">
                        <!-- Liste des caractéristiques -->
                    </div>
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

