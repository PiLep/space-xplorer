# COMPONENT-Button

## Vue d'Ensemble

Le composant Button est utilisé pour toutes les actions interactives dans l'application. Il respecte le design system avec des variantes pour différents types d'actions et des états visuels clairs pour le feedback utilisateur.

**Quand l'utiliser** :
- Actions principales (soumission de formulaire, navigation)
- Actions secondaires (annulation, actions alternatives)
- Actions destructives (suppression, confirmation)
- Actions subtiles (liens stylisés comme boutons)

## Design

### Apparence

Les boutons utilisent des coins arrondis (`rounded-lg`), un padding généreux (`py-3 px-6`), et des transitions fluides pour une expérience utilisateur optimale.

### Variantes

#### Primary

**Usage** : Action principale sur une page ou dans un formulaire

**Couleurs** :
- Background : `bg-space-primary` (`#00ff88`)
- Hover : `bg-space-primary-dark` (`#00cc6a`)
- Text : `text-space-black` (`#0a0a0a`)

**Exemple** :
```html
<button class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold py-3 px-6 rounded-lg transition-colors duration-150">
  Action principale
</button>
```

#### Secondary

**Usage** : Action secondaire, alternative à l'action principale

**Couleurs** :
- Background : `bg-space-secondary` (`#00aaff`)
- Hover : `bg-space-secondary-dark` (`#0088cc`)
- Text : `text-white` (`#ffffff`)

**Exemple** :
```html
<button class="bg-space-secondary hover:bg-space-secondary-dark text-white font-bold py-3 px-6 rounded-lg transition-colors duration-150">
  Action secondaire
</button>
```

#### Danger

**Usage** : Actions destructives (suppression, confirmation de danger)

**Couleurs** :
- Background : `bg-error` (`#ff4444`)
- Hover : `bg-error-dark` (`#cc3333`)
- Text : `text-white` (`#ffffff`)

**Exemple** :
```html
<button class="bg-error hover:bg-error-dark text-white font-bold py-3 px-6 rounded-lg transition-colors duration-150">
  Supprimer
</button>
```

#### Ghost

**Usage** : Actions subtiles, liens stylisés comme boutons

**Couleurs** :
- Background : Transparent
- Hover : `bg-surface-medium` (`#2a2a2a`)
- Text : `text-gray-400` (`#aaaaaa`)
- Hover text : `text-white` (`#ffffff`)

**Exemple** :
```html
<button class="bg-transparent hover:bg-surface-medium text-gray-400 hover:text-white font-bold py-3 px-6 rounded-lg transition-colors duration-150">
  Action subtile
</button>
```

### États

#### Default

État par défaut du bouton, prêt à être cliqué.

#### Hover

Transition vers la couleur "dark" correspondante avec une durée de 150ms.

#### Active

Légère réduction de scale (`scale-98`) pour un feedback tactile.

#### Focus

Contour visible avec couleur primary (`ring-2 ring-space-primary`) pour l'accessibilité.

#### Disabled

Opacité réduite à 50%, cursor `not-allowed`, pas d'interaction possible.

**Exemple** :
```html
<button 
  disabled
  class="bg-space-primary text-space-black font-bold py-3 px-6 rounded-lg opacity-50 cursor-not-allowed"
>
  Action désactivée
</button>
```

#### Loading

État de chargement avec spinner ou texte de chargement.

**Exemple** :
```html
<button 
  wire:loading.attr="disabled"
  class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold py-3 px-6 rounded-lg transition-colors duration-150 disabled:opacity-50"
>
  <span wire:loading.remove wire:target="submit">Soumettre</span>
  <span wire:loading wire:target="submit">Chargement...</span>
</button>
```

### Responsive

Les boutons s'adaptent automatiquement à la taille d'écran :
- **Mobile** : Padding réduit (`py-2 px-4`), texte plus petit si nécessaire
- **Tablet/Desktop** : Padding standard (`py-3 px-6`)

**Exemple responsive** :
```html
<button class="py-2 px-4 md:py-3 md:px-6 text-sm md:text-base font-bold rounded-lg">
  Action
</button>
```

## Spécifications Techniques

### Classes Tailwind

#### Structure de Base

```html
<button class="
  font-bold 
  py-3 
  px-6 
  rounded-lg 
  transition-colors 
  duration-150
">
  Label
</button>
```

#### Variantes Complètes

```html
<!-- Primary -->
<button class="
  bg-space-primary 
  hover:bg-space-primary-dark 
  active:scale-98
  focus:outline-none 
  focus:ring-2 
  focus:ring-space-primary 
  focus:ring-offset-2 
  focus:ring-offset-space-black
  disabled:opacity-50 
  disabled:cursor-not-allowed
  text-space-black 
  font-bold 
  py-3 
  px-6 
  rounded-lg 
  transition-all 
  duration-150 
  ease-in-out
">
  Action principale
</button>

<!-- Secondary -->
<button class="
  bg-space-secondary 
  hover:bg-space-secondary-dark 
  active:scale-98
  focus:outline-none 
  focus:ring-2 
  focus:ring-space-secondary 
  focus:ring-offset-2 
  focus:ring-offset-space-black
  disabled:opacity-50 
  disabled:cursor-not-allowed
  text-white 
  font-bold 
  py-3 
  px-6 
  rounded-lg 
  transition-all 
  duration-150 
  ease-in-out
">
  Action secondaire
</button>

<!-- Danger -->
<button class="
  bg-error 
  hover:bg-error-dark 
  active:scale-98
  focus:outline-none 
  focus:ring-2 
  focus:ring-error 
  focus:ring-offset-2 
  focus:ring-offset-space-black
  disabled:opacity-50 
  disabled:cursor-not-allowed
  text-white 
  font-bold 
  py-3 
  px-6 
  rounded-lg 
  transition-all 
  duration-150 
  ease-in-out
">
  Supprimer
</button>

<!-- Ghost -->
<button class="
  bg-transparent 
  hover:bg-surface-medium 
  active:scale-98
  focus:outline-none 
  focus:ring-2 
  focus:ring-space-primary 
  focus:ring-offset-2 
  focus:ring-offset-space-black
  disabled:opacity-50 
  disabled:cursor-not-allowed
  text-gray-400 
  hover:text-white 
  font-bold 
  py-3 
  px-6 
  rounded-lg 
  transition-all 
  duration-150 
  ease-in-out
">
  Action subtile
</button>
```

### Props (si composant Livewire)

Si le bouton est implémenté comme composant Livewire :

- `variant` : `string` - Variante du bouton (`primary`, `secondary`, `danger`, `ghost`)
- `label` : `string` - Texte du bouton
- `disabled` : `boolean` - État désactivé
- `loading` : `boolean` - État de chargement
- `type` : `string` - Type HTML (`button`, `submit`, `reset`)
- `size` : `string` - Taille (`sm`, `md`, `lg`)

### Structure HTML

```html
<button 
  type="submit|button|reset"
  class="[variantes] [états]"
  [disabled]
  [wire:loading.attr="disabled"]
>
  <span [wire:loading.remove]>Label</span>
  <span [wire:loading]>Chargement...</span>
</button>
```

## Code d'Implémentation

### Livewire Component (Optionnel)

```php
<?php

namespace App\Livewire;

use Livewire\Component;

class Button extends Component
{
    public string $variant = 'primary';
    public string $label;
    public bool $disabled = false;
    public bool $loading = false;
    public string $type = 'button';
    public string $size = 'md';

    public function render()
    {
        return view('livewire.button');
    }
}
```

### Blade Template

```blade
@props(['variant' => 'primary', 'size' => 'md', 'disabled' => false, 'loading' => false])

@php
    $baseClasses = 'font-bold rounded-lg transition-all duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-space-black disabled:opacity-50 disabled:cursor-not-allowed active:scale-98';
    
    $variantClasses = [
        'primary' => 'bg-space-primary hover:bg-space-primary-dark text-space-black focus:ring-space-primary',
        'secondary' => 'bg-space-secondary hover:bg-space-secondary-dark text-white focus:ring-space-secondary',
        'danger' => 'bg-error hover:bg-error-dark text-white focus:ring-error',
        'ghost' => 'bg-transparent hover:bg-surface-medium text-gray-400 hover:text-white focus:ring-space-primary',
    ];
    
    $sizeClasses = [
        'sm' => 'py-2 px-4 text-sm',
        'md' => 'py-3 px-6 text-base',
        'lg' => 'py-4 px-8 text-lg',
    ];
    
    $classes = $baseClasses . ' ' . $variantClasses[$variant] . ' ' . $sizeClasses[$size];
@endphp

<button 
    type="{{ $type ?? 'button' }}"
    {{ $attributes->merge(['class' => $classes]) }}
    @if($disabled) disabled @endif
>
    {{ $slot }}
</button>
```

### Utilisation

```blade
<!-- Simple -->
<x-button variant="primary">Action</x-button>

<!-- Avec props -->
<x-button variant="secondary" size="lg" disabled>Action désactivée</x-button>

<!-- Avec Livewire -->
<button wire:click="submit" wire:loading.attr="disabled" class="...">
  <span wire:loading.remove>Soumettre</span>
  <span wire:loading>Chargement...</span>
</button>
```

## Exemples d'Utilisation

### Exemple 1 : Formulaire de Connexion

```html
<form>
  <div class="mb-4">
    <input type="email" placeholder="Email" class="...">
  </div>
  <div class="mb-6">
    <input type="password" placeholder="Password" class="...">
  </div>
  <button 
    type="submit"
    class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold py-3 px-6 rounded-lg transition-colors duration-150 w-full"
  >
    Se connecter
  </button>
</form>
```

### Exemple 2 : Actions dans une Card

```html
<div class="bg-surface-dark rounded-lg p-6">
  <h3 class="text-white mb-4">Planet Details</h3>
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

### Exemple 3 : Bouton de Suppression

```html
<button 
  onclick="confirm('Êtes-vous sûr ?')"
  class="bg-error hover:bg-error-dark text-white font-bold py-2 px-4 rounded-lg transition-colors duration-150"
>
  Supprimer
</button>
```

## Accessibilité

### Contraste

- **Primary** : Texte noir sur fond vert (`#00ff88`) - Ratio 4.8:1 ✅
- **Secondary** : Texte blanc sur fond bleu (`#00aaff`) - Ratio 4.2:1 ✅
- **Danger** : Texte blanc sur fond rouge (`#ff4444`) - Ratio 4.5:1 ✅

### Focus

- Contour visible avec couleur primary (`ring-2 ring-space-primary`)
- Offset de 2px pour la visibilité
- Transition fluide

### Navigation Clavier

- Accessible via Tab
- Activé via Enter ou Espace
- Focus visible clairement

### ARIA

```html
<button 
  aria-label="Soumettre le formulaire"
  aria-busy="false"
>
  Soumettre
</button>
```

## Notes de Design

- **Cohérence** : Utiliser les variantes de manière cohérente à travers l'application
- **Hiérarchie** : Primary pour l'action principale, secondary pour les alternatives
- **Feedback** : Les états hover, active, et focus fournissent un feedback clair
- **Performance** : Les transitions sont optimisées pour la performance (GPU)

---

**Référence** : Voir **[DESIGN-SYSTEM.md](../DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

