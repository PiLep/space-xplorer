# Design System - Composants

## Vue d'Ensemble

Les composants de Space Xplorer sont conçus pour être réutilisables, cohérents, et alignés avec l'identité visuelle rétro-futuriste inspirée des films Alien. Chaque composant respecte les principes du design system et assure une expérience utilisateur optimale.

## Structure des Composants

Chaque composant est documenté individuellement dans `docs/design-system/components/` avec :
- Vue d'ensemble et usage
- Design et variantes
- Spécifications techniques
- Code d'implémentation
- Exemples d'utilisation
- Accessibilité

## Composants de Base

### Boutons

Voir **[COMPONENT-button.md](./components/COMPONENT-button.md)** pour la documentation complète.

**Variantes** :
- Primary : Action principale
- Secondary : Action secondaire
- Danger : Actions destructives
- Ghost : Actions subtiles

**Exemple** :
```html
<button class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold py-3 px-6 rounded-lg transition-colors duration-150">
  Action principale
</button>
```

### Formulaires

Voir **[COMPONENT-form.md](./components/COMPONENT-form.md)** pour la documentation complète.

**Éléments** :
- Inputs : Champs de saisie avec validation
- Labels : Labels clairs et accessibles
- Validation : Messages d'erreur visuels
- États : Focus, disabled, error

**Exemple** :
```html
<div class="mb-4">
  <label for="email" class="block text-gray-300 text-sm font-bold mb-2">
    Email
  </label>
  <input
    type="email"
    id="email"
    class="w-full py-2 px-3 bg-surface-dark border border-border-dark rounded text-white focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 focus:ring-offset-space-black"
  >
</div>
```

### Cards

Voir **[COMPONENT-card.md](./components/COMPONENT-card.md)** pour la documentation complète.

**Usage** : Conteneurs pour afficher des informations

**Exemple** :
```html
<div class="bg-surface-dark border border-border-dark rounded-lg p-6 mb-6">
  <h3 class="text-2xl font-semibold text-white mb-4">Titre</h3>
  <p class="text-gray-300">Contenu</p>
</div>
```

### Navigation

Voir **[COMPONENT-navigation.md](./components/COMPONENT-navigation.md)** pour la documentation complète.

**Usage** : Barre de navigation principale

**Exemple** :
```html
<nav class="bg-surface-dark border-b border-border-dark">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16">
      <div class="flex items-center">
        <a href="/" class="text-xl font-bold text-white">Space Xplorer</a>
      </div>
      <div class="flex items-center gap-4">
        <a href="/dashboard" class="text-gray-400 hover:text-white transition-colors">Dashboard</a>
      </div>
    </div>
  </div>
</nav>
```

### Modals

Voir **[COMPONENT-modal.md](./components/COMPONENT-modal.md)** pour la documentation complète.

**Usage** : Dialogs pour les interactions importantes

**Exemple** :
```html
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-surface-dark border border-border-dark rounded-lg p-6 max-w-md w-full">
    <h2 class="text-2xl font-bold text-white mb-4">Titre</h2>
    <p class="text-gray-300 mb-6">Contenu</p>
    <button class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold py-2 px-4 rounded">Fermer</button>
  </div>
</div>
```

### Badges

Voir **[COMPONENT-badge.md](./components/COMPONENT-badge.md)** pour la documentation complète.

**Usage** : Indicateurs de statut, labels

**Exemple** :
```html
<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-space-primary text-space-black">
  Actif
</span>
```

### Alerts

Voir **[COMPONENT-alert.md](./components/COMPONENT-alert.md)** pour la documentation complète.

**Usage** : Messages d'alerte et notifications

**Variantes** :
- Success
- Error
- Warning
- Info

**Exemple** :
```html
<div class="bg-success-dark border border-success text-success-light px-4 py-3 rounded mb-4">
  Opération réussie
</div>
```

## Composants Spécialisés

### Planet Card

**Usage** : Affichage des caractéristiques d'une planète

**Spécifications** :
- Header avec gradient selon le type de planète
- Description de la planète
- Grille des caractéristiques
- Couleurs adaptées au type de planète

Voir **[COMPONENT-planet-card.md](./components/COMPONENT-planet-card.md)** pour la documentation complète.

### Loading Spinner

**Usage** : Indicateur de chargement

**Exemple** :
```html
<div class="flex justify-center items-center py-12">
  <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-space-primary"></div>
</div>
```

### Empty State

**Usage** : État vide (pas de données)

**Exemple** :
```html
<div class="text-center py-12">
  <p class="text-gray-400 text-lg">Aucune donnée disponible</p>
</div>
```

## Principes de Composition

### Réutilisabilité

- Créer des composants réutilisables
- Utiliser des props/variants pour la flexibilité
- Documenter les cas d'usage

### Cohérence

- Respecter le design system
- Utiliser les couleurs, espacements, et typographie standardisés
- Maintenir la cohérence visuelle

### Accessibilité

- Utiliser les attributs ARIA appropriés
- Assurer la navigation au clavier
- Maintenir les contrastes de couleurs
- Fournir des labels clairs

### Performance

- Optimiser les animations
- Éviter les re-renders inutiles
- Utiliser le lazy loading si nécessaire

## Structure des Fichiers

```
docs/design-system/components/
├── COMPONENT-button.md
├── COMPONENT-form.md
├── COMPONENT-card.md
├── COMPONENT-navigation.md
├── COMPONENT-modal.md
├── COMPONENT-badge.md
├── COMPONENT-alert.md
├── COMPONENT-planet-card.md
└── ...
```

## Utilisation dans Livewire

Les composants peuvent être implémentés comme :
- Composants Livewire réutilisables
- Partial Blade réutilisables
- Classes Tailwind avec variants

### Exemple : Composant Livewire

```php
// app/Livewire/Button.php
class Button extends Component
{
    public $variant = 'primary';
    public $label;
    
    public function render()
    {
        return view('livewire.button');
    }
}
```

```blade
<!-- resources/views/livewire/button.blade.php -->
<button class="
  @if($variant === 'primary') bg-space-primary hover:bg-space-primary-dark @endif
  @if($variant === 'secondary') bg-space-secondary hover:bg-space-secondary-dark @endif
  text-space-black font-bold py-3 px-6 rounded-lg transition-colors duration-150
">
  {{ $label }}
</button>
```

## Checklist de Création de Composant

- [ ] Le composant respecte le design system
- [ ] Les variantes et états sont définis
- [ ] Le composant est responsive
- [ ] L'accessibilité est assurée
- [ ] La documentation est complète
- [ ] Des exemples d'utilisation sont fournis
- [ ] Le code est propre et réutilisable
- [ ] Les animations sont optimisées

## Notes de Design

- **Cohérence** : Tous les composants doivent respecter le design system
- **Réutilisabilité** : Créer des composants flexibles et réutilisables
- **Accessibilité** : Assurer l'accessibilité pour tous les utilisateurs
- **Performance** : Optimiser pour la performance

---

**Référence** : Voir **[DESIGN-SYSTEM.md](./DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

