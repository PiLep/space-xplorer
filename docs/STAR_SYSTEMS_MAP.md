# Documentation - Map des Star Systems

## 📋 Vue d'ensemble

La map des star systems est une **visualisation interactive 2D** (projection sur canvas HTML5) d'un **espace 3D** contenant tous les systèmes stellaires de la galaxie. Les systèmes sont positionnés dans un espace 3D avec des coordonnées `(x, y, z)`, mais sont projetés sur un plan 2D pour l'affichage. Elle permet aux administrateurs de visualiser, explorer et analyser la distribution spatiale des systèmes stellaires dans l'univers du jeu.

**Note** : Ce n'est pas une visualisation 3D interactive (comme avec WebGL/Three.js), mais une projection 2D d'un espace 3D, avec la possibilité de changer le plan de projection (XY, XZ, YZ).

**Route d'accès** : `/admin/map` (nécessite authentification admin)

**Fichiers principaux** :
- **Contrôleur** : `app/Http/Controllers/Admin/MapController.php`
- **Vue** : `resources/views/admin/map.blade.php`
- **JavaScript** : `resources/js/admin/universe-map.js`
- **Modèle** : `app/Models/StarSystem.php`
- **Service** : `app/Services/StarSystemGeneratorService.php`
- **Configuration** : `config/star-systems.php`

---

## 🏗️ Architecture

### Structure des données

#### Modèle StarSystem

**Table** : `star_systems`

```sql
star_systems
├── id (ULID) - Identifiant unique
├── name (string) - Nom du système (ex: "Alpha Centauri-42")
├── x (decimal 15,2) - Position X dans la galaxie
├── y (decimal 15,2) - Position Y dans la galaxie
├── z (decimal 15,2) - Position Z dans la galaxie
├── star_type (string, nullable) - Type d'étoile
├── planet_count (integer) - Nombre de planètes dans le système
├── discovered (boolean) - Système découvert ou non
└── timestamps (created_at, updated_at)
```

**Index** :
- Index composite sur `(x, y, z)` pour recherches spatiales
- Index sur `discovered` pour filtrage rapide

#### Types d'étoiles

Les types d'étoiles sont définis dans `StarSystemGeneratorService` avec leurs probabilités :

| Type | Probabilité | Couleur | Description |
|------|-------------|---------|-------------|
| `yellow_dwarf` | 35% | `#FFD700` | Comme le Soleil |
| `red_dwarf` | 40% | `#FF6B6B` | Très commun |
| `orange_dwarf` | 15% | `#FF8C42` | Type K |
| `red_giant` | 5% | `#FF4500` | Étoiles évoluées |
| `blue_giant` | 3% | `#4169E1` | Rare |
| `white_dwarf` | 2% | `#F0F0F0` | Très rare |

#### Distribution des planètes

Chaque système stellaire contient entre 1 et 7 planètes selon cette distribution :

| Nombre de planètes | Probabilité |
|-------------------|-------------|
| 1 | 10% |
| 2 | 15% |
| 3 | 25% |
| 4 | 25% |
| 5 | 15% |
| 6 | 8% |
| 7 | 2% |

### Coordonnées spatiales

Les systèmes stellaires sont positionnés dans un espace 3D avec des coordonnées `(x, y, z)` :

- **Distance minimale entre systèmes** : 50 unités (configurable dans `config/star-systems.php`)
- **Rayon d'exploration par défaut** : 200 unités
- **Coordonnées générées** : Aléatoirement dans une sphère (distance minimale depuis l'origine : 100 unités)

**Calcul de distance** :
```php
distance = √((x₁ - x₂)² + (y₁ - y₂)² + (z₁ - z₂)²)
```

### Relations

- **StarSystem → Planets** : `HasMany` - Un système contient plusieurs planètes
- **Planet → StarSystem** : `BelongsTo` - Une planète appartient à un système

---

## 🎨 Interface utilisateur

### Composants Blade

La vue principale (`resources/views/admin/map.blade.php`) utilise plusieurs composants :

1. **`x-admin.universe-map-controls`** : Contrôles de navigation (zoom, vue, filtres)
2. **`x-admin.universe-map-legend`** : Légende des types d'étoiles
3. **`x-admin.universe-map-scale-info`** : Informations sur l'échelle
4. **`x-admin.universe-map-system-info`** : Panneau d'information sur le système sélectionné

### Canvas HTML5

La map est rendue sur un **`<canvas>` HTML5 2D** (pas WebGL/3D) avec :
- **Fond** : Dégradé radial sombre (`radial-gradient(circle, #0a0e27 0%, #000000 100%)`)
- **Taille minimale** : 600px de hauteur
- **Curseur** : `grab` (devient `grabbing` lors du drag)
- **Rendu** : Utilise l'API Canvas 2D (`getContext('2d')`) pour dessiner les systèmes comme des points 2D

---

## ⚙️ Fonctionnalités JavaScript

### Classe principale : `UniverseMap`

Le module JavaScript (`resources/js/admin/universe-map.js`) est organisé en plusieurs classes :

#### 1. CoordinateProjector

Gère la **projection 3D → 2D** pour l'affichage sur le canvas HTML5. Les systèmes stellaires sont stockés avec des coordonnées 3D `(x, y, z)`, mais sont projetés sur un plan 2D pour le rendu.

**Plans de projection disponibles** :
- **XY** : Projection sur le plan horizontal (vue de dessus) - affiche X et Y, masque Z
- **XZ** : Projection sur le plan vertical X (vue de côté) - affiche X et Z, masque Y
- **YZ** : Projection sur le plan vertical Y (vue de profil) - affiche Y et Z, masque X

**Méthodes** :
- `projectTo2D(system)` : Projette un système 3D en coordonnées 2D selon le plan actif (ignore l'axe non affiché)
- `calculateDistance3D(system1, system2)` : Calcule la distance 3D réelle entre deux systèmes (utilise les 3 axes)

#### 2. ViewTransformer

Gère les transformations de vue (zoom, pan, centrage).

**Propriétés** :
- `zoom` : Niveau de zoom actuel
- `initialZoom` : Zoom initial calculé pour afficher tous les systèmes
- `centerX`, `centerY` : Centre de la vue dans l'espace monde
- `dynamicMinZoom` : Zoom minimum dynamique (50% du zoom initial)

**Méthodes** :
- `worldToScreen(worldX, worldY)` : Convertit coordonnées monde → écran
- `screenToWorld(screenX, screenY)` : Convertit coordonnées écran → monde
- `calculateInitialView(systems, projector)` : Calcule la vue initiale pour afficher tous les systèmes

**Limites de zoom** :
- **Minimum** : 0.00001 (fallback) ou 50% du zoom initial
- **Maximum** : 10.0
- **Facteur de zoom** : 1.5 par clic
- **Zoom molette** : ±5% par scroll

#### 3. SystemsRenderer

Rend les systèmes stellaires sur le canvas.

**Styles de rendu** :
- **Systèmes découverts** : Points colorés selon le type d'étoile (taille 4px, 6px si sélectionné)
- **Systèmes non découverts** : Points gris (taille 2px, 4px si sélectionné) - sauf en mode God
- **Système sélectionné** : Cercle de sélection avec halo
- **Systèmes liés** : Cercle de liaison (systèmes proches du sélectionné)

#### 4. ConnectionsRenderer

Affiche les connexions entre systèmes proches.

**Fonctionnalités** :
- Affiche les lignes de connexion entre systèmes à distance ≤ `maxConnectionDistance`
- Option pour afficher les distances sur les connexions
- Filtrage selon l'état de découverte

**Configuration** :
- Distance maximale par défaut : 200 unités
- Configurable via slider (50-500 unités)

#### 5. GridRenderer

Affiche une grille de référence pour faciliter la navigation.

**Caractéristiques** :
- Grille adaptative selon le niveau de zoom
- Cible : ~10 lignes visibles à la fois
- Couleur : Gris semi-transparent

#### 6. ScaleRenderer

Affiche l'échelle de distance actuelle.

**Unités supportées** :
- **AU** (Astronomical Units) : < 1000 AU
- **kAU** (Kilo-AU) : ≥ 1000 AU
- **ly** (Light Years) : ≥ 63,241 AU
- **pc** (Parsecs) : ≥ 206,265 AU

**Constantes de conversion** :
```javascript
AU_PER_LIGHT_YEAR: 63241.0
AU_PER_PARSEC: 206265.0
```

#### 7. SystemInfoManager

Gère l'affichage des informations détaillées sur un système sélectionné.

**Informations affichées** :
- Nom du système
- Type d'étoile
- Nombre de planètes
- Coordonnées (x, y, z)
- Distance depuis le système sélectionné (pour les systèmes proches)
- Liste des systèmes proches (top 5)

---

## 🎮 Contrôles et interactions

### Contrôles de navigation

| Action | Méthode | Description |
|--------|---------|-------------|
| **Zoom +** | `zoomIn()` | Augmente le zoom de 1.5x |
| **Zoom -** | `zoomOut()` | Diminue le zoom de 1.5x |
| **Reset View** | `resetView()` | Réinitialise la vue pour afficher tous les systèmes |
| **Scroll** | Molette souris | Zoom avant/arrière (±5% par scroll) |
| **Drag** | Clic + glisser | Déplace la vue (pan) |

### Sélection de systèmes

- **Clic simple** : Sélectionne un système et affiche ses informations
- **Double-clic** : Zoom sur le système sélectionné et ses connexions

### Filtres et options

| Option | Fonction | Description |
|--------|----------|-------------|
| **Show Connections** | `toggleConnections()` | Affiche/masque les lignes de connexion |
| **Show Distances** | `toggleDistances()` | Affiche/masque les distances sur les connexions |
| **Show Only Discovered** | `toggleShowOnlyDiscovered()` | Filtre pour n'afficher que les systèmes découverts |
| **God Mode** | `setGodMode(enabled)` | Affiche tous les systèmes (même non découverts) avec leurs vraies couleurs |
| **Max Distance** | `updateMaxDistance(value)` | Définit la distance maximale pour les connexions (50-500) |
| **View Plane** | `changeViewPlane(plane)` | Change le plan de vue (XY, XZ, YZ) |

### Mode God

Le mode God permet aux administrateurs de voir tous les systèmes stellaires, même ceux qui ne sont pas encore découverts, avec leurs vraies couleurs et propriétés. C'est utile pour le debug et la gestion de l'univers.

**Activation** : Checkbox "🔮 God Mode" dans les contrôles

---

## 🔧 Configuration

### Configuration Laravel

Fichier : `config/star-systems.php`

```php
return [
    'generation' => [
        'min_distance_between_systems' => 50.0,  // Distance minimale entre systèmes
        'exploration_radius' => 200.0,            // Rayon d'exploration par défaut
        'max_nearby_systems' => 10,               // Nombre max de systèmes à générer lors d'une exploration
    ],
    'travel' => [
        'base_speed' => 1.0,                      // Base speed (units per hour)
        'speed_multiplier' => [                   // Multiplicateurs selon le type d'étoile
            'yellow_dwarf' => 1.0,
            'red_dwarf' => 0.8,
            // ...
        ],
    ],
];
```

### Configuration JavaScript

Fichier : `resources/js/admin/universe-map.js`

```javascript
const DEFAULT_CONFIG = {
    initialZoom: 1.0,
    minZoom: 0.00001,
    maxZoom: 10.0,
    zoomFactor: 1.5,
    wheelZoomFactor: 0.05,
    maxConnectionDistance: 200,
    gridTargetLines: 10,
    scaleTargetPixels: 0.15,
    clickRadius: 20,
    padding: 0.1,
    maxZoomOutFactor: 0.5,
    zoomAnimationDuration: 600,
};
```

---

## 📊 Méthodes du modèle StarSystem

### Méthodes utilitaires

#### `distanceTo(StarSystem $other): float`

Calcule la distance euclidienne 3D entre deux systèmes stellaires.

```php
$system1 = StarSystem::find($id1);
$system2 = StarSystem::find($id2);
$distance = $system1->distanceTo($system2);
```

#### `nearby(float $x, float $y, float $z, float $radius): Collection`

Trouve tous les systèmes stellaires dans un rayon donné autour d'une position.

```php
$nearbySystems = StarSystem::nearby(100.0, 200.0, 50.0, 150.0);
```

**Algorithme** :
1. Filtre initial avec `whereBetween` sur chaque axe (optimisation)
2. Filtre final avec calcul de distance exacte (précision)

#### `planets(): HasMany`

Relation Eloquent vers toutes les planètes du système.

```php
$system = StarSystem::find($id);
$planets = $system->planets;
```

---

## 🚀 Utilisation

### Accès à la map

1. Se connecter en tant qu'administrateur (`/admin/login`)
2. Accéder à la map via `/admin/map` ou via le menu admin

### Navigation de base

1. **Vue initiale** : La map s'ajuste automatiquement pour afficher tous les systèmes
2. **Zoomer** : Utiliser les boutons +/- ou la molette de la souris
3. **Déplacer** : Cliquer et glisser sur le canvas
4. **Sélectionner** : Cliquer sur un système pour voir ses informations
5. **Explorer** : Double-cliquer pour zoomer sur un système et ses connexions

### Filtrage et recherche

1. **Systèmes découverts uniquement** : Cocher "Show Only Discovered"
2. **Voir tous les systèmes** : Activer "God Mode"
3. **Afficher les connexions** : Cocher "Show Connections"
4. **Ajuster la distance** : Utiliser le slider "Max Distance"

### Changement de perspective

Utiliser les boutons **XY**, **XZ**, **YZ** pour changer le **plan de projection** et explorer l'univers sous différents angles. Chaque plan masque un axe différent :
- **XY** : Vue de dessus (masque Z)
- **XZ** : Vue de côté (masque Y)
- **YZ** : Vue de profil (masque X)

**Note** : La distance affichée entre systèmes sur la map est la distance 2D projetée, pas la distance 3D réelle. Pour voir la distance 3D réelle, consulter les informations du système sélectionné.

---

## 🔍 Optimisations et performances

### Caching

- **Screen coordinates cache** : Les coordonnées écran sont mises en cache pour éviter les recalculs
- **Linked systems cache** : Les systèmes liés sont mis en cache jusqu'à invalidation
- **RequestAnimationFrame** : Utilisation de `requestAnimationFrame` pour le rendu fluide

### Optimisations de rendu

- **Culling** : Les systèmes hors écran ne sont pas rendus
- **Lazy rendering** : Le rendu est déclenché uniquement lors des changements
- **Batch operations** : Les opérations de rendu sont groupées

### Optimisations de requêtes

- **Index spatial** : Index composite sur `(x, y, z)` pour recherches rapides
- **Eager loading** : Les relations sont chargées efficacement si nécessaire
- **Collection filtering** : Filtrage en mémoire après requête optimisée

---

## 🧪 Tests

### Tests unitaires

Les tests pour le modèle `StarSystem` se trouvent dans :
- `tests/Unit/Models/StarSystemTest.php` (si existant)

### Tests de génération

Les tests pour le service de génération se trouvent dans :
- `tests/Unit/Services/StarSystemGeneratorServiceTest.php` (si existant)

---

## 📝 Notes de développement

### Ajout de nouvelles fonctionnalités

Pour ajouter de nouvelles fonctionnalités à la map :

1. **Backend** : Modifier `MapController` pour ajouter de nouvelles données
2. **Frontend** : Étendre la classe `UniverseMap` dans `universe-map.js`
3. **Vue** : Ajouter les contrôles dans `map.blade.php` ou les composants associés

### Debugging

Pour déboguer la map :

1. **Console JavaScript** : Ouvrir la console du navigateur pour voir les logs
2. **God Mode** : Activer pour voir tous les systèmes
3. **Zoom** : Utiliser le reset view pour revenir à la vue initiale

### Améliorations futures possibles

- [ ] Recherche de systèmes par nom
- [ ] Filtrage par type d'étoile
- [ ] Export de la map en image
- [ ] Animation de transition entre vues
- [ ] Support du clavier pour la navigation
- [ ] Mini-map de navigation
- [ ] Historique de navigation (undo/redo)
- [ ] Marqueurs personnalisés
- [ ] Statistiques globales (nombre total, distribution, etc.)

---

## 📚 Références

- **Architecture complète** : `docs/memory_bank/ARCHITECTURE.md`
- **Modèle StarSystem** : `app/Models/StarSystem.php`
- **Service de génération** : `app/Services/StarSystemGeneratorService.php`
- **Contrôleur** : `app/Http/Controllers/Admin/MapController.php`
- **Vue principale** : `resources/views/admin/map.blade.php`
- **Module JavaScript** : `resources/js/admin/universe-map.js`
- **Configuration** : `config/star-systems.php`

---

**Dernière mise à jour** : 2025-01-27

