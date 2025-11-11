# Progress Bar Component - Design System

## Vue d'Ensemble

Le composant Progress Bar est un indicateur visuel de progression avec support de pourcentages et de couleurs personnalisables. Il est conçu pour afficher l'état d'avancement d'une tâche ou d'un processus.

## Usage

Composant réutilisable pour afficher une barre de progression avec pourcentage et couleurs personnalisables.

**Fichier** : `resources/views/components/progress-bar.blade.php`

## Variantes de Couleur

### Blue (défaut)

Couleur bleue par défaut pour les progressions générales.

```blade
<x-progress-bar :percentage="75" color="blue" />
```

### Green

Couleur verte pour les progressions positives ou les succès.

```blade
<x-progress-bar :percentage="90" color="green" />
```

### Orange

Couleur orange pour les avertissements ou progressions moyennes.

```blade
<x-progress-bar :percentage="50" color="orange" />
```

### Red

Couleur rouge pour les erreurs ou progressions critiques.

```blade
<x-progress-bar :percentage="25" color="red" />
```

## Props

### percentage (int|float, requis)

Pourcentage de progression (0-100). Les valeurs sont automatiquement limitées entre 0 et 100.

```blade
<x-progress-bar :percentage="75" />
```

### color (string, optionnel)

Couleur de la barre de progression : `'blue'`, `'green'`, `'orange'`, `'red'`. Défaut : `'blue'`

```blade
<x-progress-bar :percentage="60" color="green" />
```

### height (string, optionnel)

Classe Tailwind pour la hauteur de la barre. Défaut : `'h-3'`

**Exemples** :
- `'h-2'` : Barre fine (8px)
- `'h-3'` : Barre standard (12px) - défaut
- `'h-4'` : Barre épaisse (16px)
- `'h-6'` : Barre très épaisse (24px)

```blade
<x-progress-bar :percentage="75" height="h-4" />
```

## Exemples d'Utilisation

### Barre de Progression Simple

```blade
<x-progress-bar :percentage="75" />
```

### Barre avec Couleur Personnalisée

```blade
<x-progress-bar :percentage="90" color="green" />
```

### Barre Épaisse

```blade
<x-progress-bar :percentage="50" color="orange" height="h-6" />
```

### Dans un Contexte de Statistiques

```blade
<div class="space-y-2">
    <div class="flex justify-between text-sm">
        <span class="text-gray-600 dark:text-gray-400">Storage Used</span>
        <span class="text-gray-900 dark:text-white font-medium">75%</span>
    </div>
    <x-progress-bar :percentage="75" color="blue" />
</div>
```

### Avec Label et Valeur

```blade
<div class="space-y-1">
    <div class="flex justify-between items-center">
        <span class="text-sm font-medium text-gray-900 dark:text-white">
            Resource Generation
        </span>
        <span class="text-sm text-gray-500 dark:text-gray-400">
            {{ $progress }}%
        </span>
    </div>
    <x-progress-bar :percentage="$progress" color="green" />
</div>
```

### Barres Multiples (Comparaison)

```blade
<div class="space-y-4">
    <div>
        <div class="flex justify-between text-sm mb-1">
            <span class="text-gray-600 dark:text-gray-400">Avatar Images</span>
            <span class="text-gray-900 dark:text-white">45%</span>
        </div>
        <x-progress-bar :percentage="45" color="blue" />
    </div>
    
    <div>
        <div class="flex justify-between text-sm mb-1">
            <span class="text-gray-600 dark:text-gray-400">Planet Images</span>
            <span class="text-gray-900 dark:text-white">78%</span>
        </div>
        <x-progress-bar :percentage="78" color="green" />
    </div>
    
    <div>
        <div class="flex justify-between text-sm mb-1">
            <span class="text-gray-600 dark:text-gray-400">Planet Videos</span>
            <span class="text-gray-900 dark:text-white">23%</span>
        </div>
        <x-progress-bar :percentage="23" color="orange" />
    </div>
</div>
```

## Spécifications Techniques

### Structure HTML

```html
<div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
    <div 
        class="bg-blue-600 rounded-full" 
        style="width: 75%; height: 100%;"
    ></div>
</div>
```

### Classes CSS

**Conteneur** :
- `flex-1` : Prend toute la largeur disponible dans un flex container
- `bg-gray-200 dark:bg-gray-700` : Fond de la barre
- `rounded-full` : Coins arrondis
- `overflow-hidden` : Cache le débordement

**Barre de progression** :
- `bg-{color}-600` : Couleur selon la variante (blue, green, orange, red)
- `rounded-full` : Coins arrondis
- `width: {percentage}%` : Largeur dynamique selon le pourcentage
- `height: 100%` : Hauteur complète du conteneur

### Couleurs Disponibles

- **Blue** : `bg-blue-600`
- **Green** : `bg-green-600`
- **Orange** : `bg-orange-600`
- **Red** : `bg-red-600`

## Accessibilité

- Contraste suffisant entre le fond et la barre de progression
- Support du mode sombre avec classes `dark:`
- Les valeurs sont automatiquement limitées entre 0 et 100 pour éviter les erreurs visuelles

## Bonnes Pratiques

1. **Utiliser des couleurs sémantiques** : Vert pour succès, orange pour avertissement, rouge pour erreur
2. **Afficher le pourcentage** : Toujours afficher la valeur numérique à côté de la barre
3. **Hauteur appropriée** : Utiliser `h-3` par défaut, `h-4` ou `h-6` pour plus de visibilité
4. **Labels clairs** : Ajouter un label descriptif au-dessus de la barre
5. **Contexte** : Utiliser dans des cards ou sections pour donner du contexte

## Cas d'Usage

- **Statistiques** : Affichage de pourcentages dans les dashboards
- **Chargement** : Indicateur de progression pour les tâches longues
- **Quotas** : Affichage de l'utilisation de ressources (storage, API calls, etc.)
- **Comparaisons** : Comparaison visuelle de plusieurs métriques
- **Formulaires** : Indicateur de progression dans les formulaires multi-étapes

## Relation avec le Design System

Le Progress Bar est souvent utilisé avec :
- `<x-stat-card>` pour afficher des statistiques avec progression
- Cards pour créer des sections de métriques
- Tableaux pour afficher des données avec progression visuelle

---

**Référence** : Voir **[DESIGN-SYSTEM.md](../DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

