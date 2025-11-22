# Documentation Technique - Mini-Jeu de Scanning

## Vue d'Ensemble

Le mini-jeu de scanning est un jeu interactif où le joueur doit verrouiller des signaux éphémères apparaissant sur un radar circulaire. L'architecture est modulaire et séparée en plusieurs composants JavaScript réutilisables.

## Architecture Technique

### Structure des Fichiers

```
resources/js/minigames/
├── constants.js                    # Constantes centralisées
├── scanning.js                     # Classe principale (orchestrateur)
├── init.js                         # Initialisation globale
├── managers/
│   ├── GameState.js               # Machine à états
│   ├── SignalManager.js           # Gestion des signaux
│   ├── UIManager.js               # Interface utilisateur
│   └── InterferenceManager.js     # Effets d'interférence
└── renderers/
    └── RadarRenderer.js           # Rendu du canvas
```

### Flux de Données

```
ScanningMinigame (orchestrateur)
    ├── GameState (état du jeu)
    ├── SignalManager (logique des signaux)
    ├── UIManager (affichage UI)
    ├── InterferenceManager (effets visuels)
    └── RadarRenderer (rendu canvas)
```

## Composants Principaux

### 1. `ScanningMinigame` (Classe Principale)

**Fichier** : `resources/js/minigames/scanning.js`

**Responsabilité** : Orchestrer tous les modules et gérer le cycle de vie du jeu.

**API Publique** :

```javascript
const game = new ScanningMinigame({
  container: HTMLElement,        // Conteneur DOM pour le jeu
  config: {                       // Configuration optionnelle
    signalCount: 8,               // Nombre de signaux
    signalDurationMin: 4000,      // Durée min d'un signal (ms)
    signalDurationMax: 8000,      // Durée max d'un signal (ms)
    totalDuration: 60000,         // Durée totale du jeu (ms)
    radarSize: 700,               // Taille du radar (px)
    radiusMin: 210,                // Rayon min pour position signal
    radiusMax: 280                 // Rayon max pour position signal
  },
  onComplete: (result) => {},     // Callback appelé à la fin
  onProgress: (progress) => {}    // Callback appelé pendant le jeu
});

game.start();   // Démarrer le jeu
game.destroy(); // Nettoyer les ressources
```

**Cycle de Vie** :

1. **Initialisation** (`init()`) :
   - Création de l'UI via `UIManager`
   - Initialisation du renderer
   - Configuration des event handlers

2. **Démarrage** (`start()`) :
   - Transition vers l'état `ACTIVE`
   - Génération des signaux
   - Démarrage des effets d'interférence
   - Lancement de la boucle de mise à jour

3. **Mise à jour** (`update()`) :
   - Boucle `requestAnimationFrame`
   - Mise à jour de l'état de locking
   - Mise à jour de l'UI (barres de progression)
   - Rendu du canvas
   - Vérification des conditions de fin

4. **Fin** (`end()`) :
   - Transition vers l'état `ENDED`
   - Arrêt des effets d'interférence
   - Animation de fin
   - Calcul du résultat
   - Appel du callback `onComplete`

**État du Jeu** :

```javascript
{
  startTime: number,           // Timestamp de début
  endTime: number,             // Timestamp de fin prévu
  actions: Array,             // Actions enregistrées
  lockingSignal: Signal|null,  // Signal en cours de locking
  lockStartTime: number|null,  // Début du locking
  lockProgress: number,        // Progression du locking (0-1)
  animationFrameId: number     // ID de l'animation frame
}
```

### 2. `GameState` (Machine à États)

**Fichier** : `resources/js/minigames/managers/GameState.js`

**Responsabilité** : Gérer les transitions d'état et valider les opérations.

**États** :

- `IDLE` : Jeu initialisé mais pas démarré
- `ACTIVE` : Jeu en cours
- `ENDED` : Jeu terminé
- `DESTROYED` : Jeu détruit (cleanup effectué)

**Transitions Valides** :

```
IDLE → ACTIVE → ENDED → IDLE
  ↓       ↓       ↓
DESTROYED DESTROYED DESTROYED
```

**API** :

```javascript
const state = new GameState();

state.getState();                    // Retourne l'état actuel
state.canTransitionTo(newState);    // Vérifie si transition valide
state.transitionTo(newState);        // Effectue la transition
state.isActive();                    // Vérifie si actif
state.isIdle();                      // Vérifie si idle
state.isEnded();                     // Vérifie si terminé
state.onStateChange(callback);       // Écouter les changements
```

### 3. `SignalManager` (Gestion des Signaux)

**Fichier** : `resources/js/minigames/managers/SignalManager.js`

**Responsabilité** : Générer, gérer et valider les signaux.

**Structure d'un Signal** :

```javascript
{
  id: number,              // Identifiant unique
  x: number,               // Position X (0-1)
  y: number,               // Position Y (0-1)
  startTime: number,       // Timestamp d'apparition
  endTime: number,        // Timestamp de disparition
  locked: boolean,        // Verrouillé ou non
  lockedAt: number|null,  // Timestamp de verrouillage
  intensity: number       // Intensité du signal (50-100)
}
```

**API** :

```javascript
const signalManager = new SignalManager(config);

// Génération
signalManager.generateSignals(startTime, endTime);

// Récupération
signalManager.getSignals();                    // Tous les signaux
signalManager.getActiveSignals(currentTime);  // Signaux actifs
signalManager.getLockedSignals();             // Signaux verrouillés

// Opérations
signalManager.lockSignal(signalId, currentTime);  // Verrouiller un signal
signalManager.isSignalOptimal(signal, time);     // Vérifier zone optimale
signalManager.getSignalProgress(signal, time);  // Progression (0-1)
signalManager.getAcquisitionRate();              // Taux d'acquisition (0-1)
signalManager.areAllSignalsResolved(time);      // Tous résolus ?
```

**Génération des Signaux** :

- Nombre : Configurable via `config.signalCount` (défaut: 8)
- Durée : Entre `signalDurationMin` et `signalDurationMax`
- Position : Rayon aléatoire entre `radiusMin` et `radiusMax`, angle aléatoire
- Timing : Distribués sur toute la durée du jeu

**Zone Optimale** :

- Définie par `ZONES.OPTIMAL_MIN` (0.25) et `ZONES.OPTIMAL_MAX` (0.75)
- Correspond à 25%-75% de la durée de vie du signal
- Verrouillage optimal si effectué dans cette zone

### 4. `UIManager` (Interface Utilisateur)

**Fichier** : `resources/js/minigames/managers/UIManager.js`

**Responsabilité** : Créer et gérer tous les éléments UI.

**Éléments Créés** :

- Conteneur principal avec classe `.scanning-minigame`
- Canvas pour le radar
- Barre de progression temporelle
- Barre d'intensité (acquisition)
- Bouton de démarrage
- Écran de résultats
- Séquence terminal (animation de texte)

**API** :

```javascript
const uiManager = new UIManager(container, config);

// Création
const elements = uiManager.createUI();
// Retourne: { canvas, canvasWrapper, ... }

// Mise à jour
uiManager.updateProgressBar(progress);      // 0-1
uiManager.updateIntensityBar(completion);  // 0-1

// Affichage
uiManager.showTerminalSequence(callback);   // Animation terminal
uiManager.showResults(result, callback);    // Écran de résultats

// Nettoyage
uiManager.destroy();
```

**Structure HTML Générée** :

```html
<div class="scanning-minigame">
  <div class="scanning-minigame__terminal">...</div>
  <button class="scanning-minigame__start">Start</button>
  <div class="scanning-minigame__canvas-wrapper">
    <canvas class="scanning-minigame__canvas"></canvas>
  </div>
  <div class="scanning-minigame__ui">
    <div class="scanning-minigame__progress-bar">...</div>
    <div class="scanning-minigame__intensity-bar">...</div>
  </div>
  <div class="scanning-minigame__results">...</div>
</div>
```

### 5. `InterferenceManager` (Effets d'Interférence)

**Fichier** : `resources/js/minigames/managers/InterferenceManager.js`

**Responsabilité** : Gérer tous les effets visuels d'interférence (glitch, static, scanlines, etc.).

**Types d'Effets** :

1. **Effets Continus** :
   - Scanlines horizontales
   - Noise (bruit)
   - Grain (grain)
   - Vignette (assombrissement des bords)
   - Chromatic aberration (aberration chromatique)
   - Effet CRT (lignes de balayage)

2. **Effets Progressifs** :
   - Distortion (distorsion progressive)
   - Glitch frequency (fréquence de glitch)
   - Static intensity (intensité du statique)
   - Flicker intensity (intensité du scintillement)

3. **Effets Aigus** :
   - Glitch (glitch aléatoire)
   - Static (statique)
   - Scanline (ligne de balayage)
   - Distortion (distorsion)
   - Flicker (scintillement)
   - Noise (bruit)
   - Chromatic shift (décalage chromatique)
   - Screen shake (tremblement d'écran)

**API** :

```javascript
const interferenceManager = new InterferenceManager();

// Initialisation
interferenceManager.initStaticParticles(radarSize);

// Démarrage/Arrêt
interferenceManager.start(startTime, totalDuration);
interferenceManager.stop();

// Récupération de l'état
const state = interferenceManager.getInterferenceState();
// Retourne: { distortion, glitch, static, scanlines, ... }

// Nettoyage
interferenceManager.destroy();
```

**Particules Statiques** :

- Nombre : `INTERFERENCE.STATIC_PARTICLES_COUNT` (30)
- Position aléatoire sur le canvas
- Utilisées pour l'effet de statique

### 6. `RadarRenderer` (Rendu du Canvas)

**Fichier** : `resources/js/minigames/renderers/RadarRenderer.js`

**Responsabilité** : Rendre tous les éléments visuels sur le canvas.

**Éléments Rendu** :

1. **Radar** :
   - Cercles concentriques
   - Lignes de repère (crosshair)
   - Centre du radar

2. **Ligne de Scan** :
   - Ligne rotative autour du centre
   - Rotation continue selon `ANIMATIONS.SCAN_ROTATION_SPEED`

3. **Signaux** :
   - Cercle avec taille variable selon l'état
   - Couleur selon l'état (actif, optimal, verrouillé)
   - Effet de glow (lueur)
   - Animation de pulsation

4. **Effets d'Interférence** :
   - Tous les effets gérés par `InterferenceManager`
   - Appliqués en couches sur le canvas

**API** :

```javascript
const renderer = new RadarRenderer(canvas, config);

// Rendu principal
renderer.render({
  state: string,                    // État du jeu
  signals: Array<Signal>,           // Signaux à afficher
  interference: Object,            // État des interférences
  currentTime: number,              // Temps actuel
  startTime: number,                // Temps de début
  endAnimationStartTime: number,    // Début animation fin
  endAnimationDuration: number,     // Durée animation fin
  lockingSignal: Signal|null,      // Signal en locking
  lockProgress: number              // Progression locking (0-1)
});

// Utilitaires
const pos = renderer.getSignalRenderedPosition(
  signal, 
  currentTime, 
  interferenceState, 
  applyJitter
);
```

**Calcul des Positions Rendu** :

- Position de base : Calculée depuis les coordonnées polaires du signal
- Jitter : Application d'un décalage aléatoire pour l'effet d'interférence
- Synchronisation : Les positions rendues correspondent aux positions de clic

### 7. `constants.js` (Constantes)

**Fichier** : `resources/js/minigames/constants.js`

**Responsabilité** : Centraliser toutes les constantes de configuration.

**Catégories** :

- `COLORS` : Couleurs du thème (primary, secondary, accent, etc.)
- `SIZES` : Tailles des éléments (signaux, rayons de clic, etc.)
- `TIMING` : Durées et timings (locking, animations, tolérance)
- `ZONES` : Zones optimales (min, max, center)
- `INTENSITY` : Paramètres d'intensité (min, max, bonus, pénalités)
- `ANIMATIONS` : Paramètres d'animation (vitesses, périodes)
- `INTERFERENCE` : Paramètres d'interférence (particules, dots, spacing)
- `SCORING` : Poids pour le calcul du score
- `DEFAULT_CONFIG` : Configuration par défaut

## Mécaniques de Jeu

### Verrouillage d'un Signal

**Processus** :

1. **Clic** (`handleCanvasMouseDown`) :
   - Détection du signal cliqué (rayon de clic)
   - Vérification que le signal est actif (dans sa fenêtre temporelle)
   - Démarrage du locking (`lockingSignal`, `lockStartTime`)

2. **Maintien** (`updateLocking`) :
   - Calcul de la progression du locking (0-1)
   - Vérification que le signal est toujours dans la zone optimale
   - Auto-complétion si durée suffisante ET zone optimale

3. **Relâchement** (`handleCanvasMouseUp`) :
   - Complétion du locking si conditions remplies
   - Calcul de l'intensité selon la précision
   - Enregistrement de l'action

**Conditions de Succès** :

- Durée de maintien ≥ `TIMING.LOCK_REQUIRED_DURATION` (400ms)
- Signal dans la zone optimale (25%-75% de sa durée)
- Signal toujours actif (dans sa fenêtre temporelle)

**Calcul de l'Intensité** :

```javascript
// Distance du centre de la zone optimale
const distanceFromCenter = Math.abs(progress - ZONES.OPTIMAL_CENTER);

// Intensité de base (50-100)
let intensity = 100 - ((distanceFromCenter / maxDistance) * 50);

// Bonus pour maintien plus long
const holdBonus = Math.min(20, (lockDuration - requiredDuration) / 10);
intensity = Math.min(100, intensity + holdBonus);
```

### Calcul du Score

**Formule** :

```javascript
const acquisitionRate = signalsLocked / signalsTotal;
const avgIntensity = average(intensities of locked signals);
const qualityScore = (acquisitionRate * 0.6) + (avgIntensity * 0.4);
```

**Facteurs** :

- **Taux d'acquisition** (60%) : Pourcentage de signaux verrouillés
- **Intensité moyenne** (40%) : Précision moyenne des verrouillages

**Résultat** :

- Score final : 0-100
- Actions enregistrées : Array avec toutes les actions (succès/échec)

### Conditions de Fin

Le jeu se termine si :

1. **Timeout** : `currentTime >= endTime`
2. **Tous les signaux résolus** : Tous verrouillés ou expirés
3. **Fin manuelle** : Appel de `end()` (non implémenté actuellement)

## Événements et Callbacks

### `onProgress`

Appelé à chaque frame pendant le jeu :

```javascript
onProgress({
  elapsed: number,           // Temps écoulé (ms)
  remaining: number,         // Temps restant (ms)
  signalsLocked: number,     // Nombre de signaux verrouillés
  signalsTotal: number       // Nombre total de signaux
});
```

### `onComplete`

Appelé à la fin du jeu :

```javascript
onComplete({
  score: number,             // Score final (0-100)
  acquisitionRate: number,   // Taux d'acquisition (0-1)
  signalsGenerated: number,  // Nombre de signaux générés
  signalsLocked: number,     // Nombre de signaux verrouillés
  signalsAcquired: number,   // Nombre de signaux acquis
  actions: Array,            // Toutes les actions enregistrées
  startTime: number,         // Timestamp de début
  endTime: number,          // Timestamp de fin
  duration: number           // Durée totale (ms)
});
```

## Performance et Optimisations

### Boucle de Rendu

- Utilisation de `requestAnimationFrame` pour synchronisation avec le rafraîchissement d'écran
- Cible : 60 FPS
- Arrêt automatique quand le jeu se termine

### Gestion Mémoire

- Cleanup automatique des event listeners
- Nettoyage des références dans `destroy()`
- Arrêt des intervals d'interférence

### Canvas

- Contexte 2D avec option `willReadFrequently: true`
- Rendu optimisé avec réutilisation des calculs
- Effets d'interférence calculés une fois par frame

## Intégration

### Initialisation Globale

**Fichier** : `resources/js/minigames/init.js`

Gère l'initialisation automatique du minijeu depuis les éléments DOM avec l'attribut `data-scanning-minigame`.

### Utilisation dans une Vue Blade

```blade
<div id="scanning-game" data-scanning-minigame>
  <!-- Le jeu sera initialisé automatiquement -->
</div>
```

Ou manuellement :

```javascript
import { ScanningMinigame } from './minigames/scanning.js';

const game = new ScanningMinigame({
  container: document.getElementById('game-container'),
  config: {
    signalCount: 8,
    totalDuration: 60000
  },
  onComplete: (result) => {
    console.log('Score:', result.score);
    // Envoyer au serveur via Livewire/AJAX
  }
});

game.start();
```

## Sécurité et Validation

### Validation Côté Client

- Vérification des timestamps
- Validation de la zone optimale
- Vérification de la durée de locking

### Validation Côté Serveur (À Implémenter)

Le système de validation serveur devrait :

1. **Vérifier les timestamps** :
   - Ordre chronologique des actions
   - Pas de gaps suspects
   - Durée totale cohérente

2. **Valider les positions** :
   - Positions des signaux correspondantes
   - Rayon de clic respecté
   - Zone optimale vérifiée

3. **Calculer le score** :
   - Score recalculé côté serveur
   - Comparaison avec le score client
   - Rejet si différence suspecte

4. **Protection anti-hack** :
   - Limitation du nombre d'actions
   - Vérification de la cohérence temporelle
   - Détection de patterns suspects

## Extensibilité

### Ajout de Nouveaux Types de Signaux

1. Étendre `SignalManager.generateSignals()` pour générer différents types
2. Ajouter la logique de rendu dans `RadarRenderer`
3. Adapter la validation dans le calcul du score

### Ajout de Nouvelles Mécaniques

1. Étendre `GameState` pour nouveaux états si nécessaire
2. Ajouter la logique dans `ScanningMinigame.update()`
3. Mettre à jour l'UI dans `UIManager`

### Configuration Dynamique

Tous les paramètres sont configurables via l'objet `config` passé au constructeur, permettant une personnalisation complète sans modifier le code.

## Tests Recommandés

### Tests Unitaires

- `GameState` : Transitions d'état
- `SignalManager` : Génération et validation des signaux
- `UIManager` : Création et mise à jour de l'UI
- `InterferenceManager` : Calcul des effets
- `RadarRenderer` : Calcul des positions

### Tests d'Intégration

- Cycle complet : Démarrage → Jeu → Fin
- Verrouillage de signaux multiples
- Conditions de fin (timeout, tous résolus)
- Callbacks appelés correctement

### Tests de Performance

- FPS stable à 60
- Pas de fuites mémoire
- Cleanup complet après destruction

## Références

- [Documentation Game Design](../game-design/mini-games-system.md) - Spécifications du système de mini-jeux
- [Code Review](../reviews/CODE-REVIEW-007-scanning-minigame.md) - Review du code
- [Gameplay Review](../reviews/GAMEPLAY-REVIEW-scanning-minigame.md) - Review gameplay

