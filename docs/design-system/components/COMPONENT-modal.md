# COMPONENT-Modal

## Vue d'Ensemble

Le composant Modal est utilisé pour afficher des dialogues, des confirmations, ou des contenus importants qui nécessitent l'attention de l'utilisateur. Il respecte le design system avec un style rétro-futuriste inspiré des interfaces de vaisseaux spatiaux.

**Quand l'utiliser** :
- Confirmations d'actions importantes (suppression, modifications critiques)
- Affichage de formulaires dans un contexte isolé
- Affichage de détails supplémentaires
- Alertes importantes nécessitant une action utilisateur

## Design

### Apparence

Les modals utilisent un overlay sombre semi-transparent, un conteneur centré avec fond sombre (`bg-surface-dark`), des bordures avec effet terminal (`terminal-border-simple`), et une typographie monospace pour créer une ambiance rétro-futuriste.

### Variantes

#### Standard

**Usage** : Modal standard pour la plupart des dialogues

**Spécifications** :
- Overlay : `bg-black bg-opacity-50` ou `bg-black/50`
- Container : `bg-surface-dark` (`#1a1a1a`)
- Border : `border border-border-dark` (`#333333`)
- Border radius : `rounded-lg`
- Padding : `p-6` (24px)
- Max width : `max-w-md` (448px) ou `max-w-lg` (512px)
- Shadow : Optionnel avec `shadow-xl`

**Exemple** :
```html
<!-- Overlay -->
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <!-- Modal -->
    <div class="bg-surface-dark dark:bg-surface-dark border border-border-dark dark:border-border-dark rounded-lg p-6 max-w-md w-full terminal-border-simple">
        <h2 class="text-2xl font-bold text-white mb-4 font-mono">TITRE</h2>
        <p class="text-gray-300 mb-6">Contenu du modal</p>
        <div class="flex justify-end gap-4">
            <button class="px-4 py-2 rounded text-gray-400 hover:text-white transition-colors">Annuler</button>
            <button class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold px-4 py-2 rounded transition-colors">Confirmer</button>
        </div>
    </div>
</div>
```

#### Confirmation

**Usage** : Modal de confirmation pour actions destructives ou importantes

**Spécifications** :
- Style similaire au standard
- Message d'avertissement avec couleur d'alerte
- Boutons d'action clairs (Annuler / Confirmer)

**Exemple** :
```html
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-surface-dark border border-border-dark rounded-lg p-6 max-w-md w-full terminal-border-simple">
        <h2 class="text-2xl font-bold text-white mb-4 font-mono">CONFIRMATION</h2>
        <p class="text-gray-300 mb-2">Êtes-vous sûr de vouloir supprimer cet élément ?</p>
        <p class="text-error dark:text-error text-sm mb-6">Cette action est irréversible.</p>
        <div class="flex justify-end gap-4">
            <button class="px-4 py-2 rounded text-gray-400 hover:text-white transition-colors">Annuler</button>
            <button class="bg-error hover:bg-error-dark text-white font-bold px-4 py-2 rounded transition-colors">Supprimer</button>
        </div>
    </div>
</div>
```

#### Form Modal

**Usage** : Modal contenant un formulaire

**Spécifications** :
- Max width plus large : `max-w-lg` ou `max-w-xl`
- Padding généreux pour le formulaire
- Boutons d'action en bas

**Exemple** :
```html
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-surface-dark border border-border-dark rounded-lg p-6 max-w-lg w-full terminal-border-simple max-h-[90vh] overflow-y-auto">
        <h2 class="text-2xl font-bold text-white mb-6 font-mono">NOUVEAU_ELEMENT</h2>
        <form>
            <!-- Champs du formulaire -->
            <div class="flex justify-end gap-4 mt-6">
                <button type="button" class="px-4 py-2 rounded text-gray-400 hover:text-white transition-colors">Annuler</button>
                <button type="submit" class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold px-4 py-2 rounded transition-colors">Créer</button>
            </div>
        </form>
    </div>
</div>
```

### États

#### Default
- Visible avec overlay
- Contenu centré

#### Loading
- Désactiver les boutons : `disabled opacity-50`
- Afficher un spinner ou message de chargement

#### Error
- Afficher un message d'erreur avec `text-error`
- Garder le modal ouvert pour afficher l'erreur

## Spécifications Techniques

### Classes Tailwind

**Overlay** :
```html
<div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-black/50 flex items-center justify-center z-50">
```

**Container Modal** :
```html
<div class="bg-surface-dark dark:bg-surface-dark border border-border-dark dark:border-border-dark rounded-lg p-6 max-w-md w-full terminal-border-simple">
```

**Header Modal** :
```html
<h2 class="text-2xl font-bold text-white mb-4 font-mono">TITRE</h2>
```

**Footer avec Actions** :
```html
<div class="flex justify-end gap-4 mt-6">
    <button class="px-4 py-2 rounded text-gray-400 hover:text-white transition-colors">Annuler</button>
    <button class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold px-4 py-2 rounded transition-colors">Confirmer</button>
</div>
```

### Structure HTML

**Structure de base** :
```html
<!-- Overlay avec backdrop -->
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <!-- Modal Container -->
    <div class="bg-surface-dark border border-border-dark rounded-lg p-6 max-w-md w-full terminal-border-simple">
        <!-- Header -->
        <h2 class="text-2xl font-bold text-white mb-4 font-mono">TITRE</h2>
        
        <!-- Content -->
        <div class="mb-6">
            <!-- Contenu du modal -->
        </div>
        
        <!-- Footer / Actions -->
        <div class="flex justify-end gap-4">
            <!-- Boutons d'action -->
        </div>
    </div>
</div>
```

## Code d'Implémentation

### Composant Blade Simple

```blade
@props(['show' => false, 'title' => '', 'maxWidth' => 'md'])

@if($show)
<div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-black/50 flex items-center justify-center z-50" 
     x-data="{ show: @js($show) }"
     x-show="show"
     x-transition>
    <div class="bg-surface-dark dark:bg-surface-dark border border-border-dark dark:border-border-dark rounded-lg p-6 max-w-{{ $maxWidth }} w-full terminal-border-simple">
        <h2 class="text-2xl font-bold text-white mb-4 font-mono">{{ $title }}</h2>
        
        <div class="mb-6">
            {{ $slot }}
        </div>
        
        <div class="flex justify-end gap-4">
            {{ $footer ?? '' }}
        </div>
    </div>
</div>
@endif
```

### Composant Livewire

```php
<?php

namespace App\Livewire;

use Livewire\Component;

class Modal extends Component
{
    public $show = false;
    public $title = '';
    public $maxWidth = 'md';

    public function open()
    {
        $this->show = true;
    }

    public function close()
    {
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.modal');
    }
}
```

```blade
<!-- resources/views/livewire/modal.blade.php -->
@if($show)
<div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-black/50 flex items-center justify-center z-50" wire:click.self="close">
    <div class="bg-surface-dark dark:bg-surface-dark border border-border-dark dark:border-border-dark rounded-lg p-6 max-w-{{ $maxWidth }} w-full terminal-border-simple">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-white font-mono">{{ $title }}</h2>
            <button wire:click="close" class="text-gray-400 hover:text-white transition-colors">
                <span class="font-mono">×</span>
            </button>
        </div>
        
        <div class="mb-6">
            {{ $slot }}
        </div>
        
        <div class="flex justify-end gap-4">
            {{ $footer ?? '' }}
        </div>
    </div>
</div>
@endif
```

## Exemples d'Utilisation

### Exemple 1 : Modal de Confirmation Simple

```blade
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-surface-dark border border-border-dark rounded-lg p-6 max-w-md w-full terminal-border-simple">
        <h2 class="text-2xl font-bold text-white mb-4 font-mono">CONFIRMER_SUPPRESSION</h2>
        <p class="text-gray-300 mb-6">Cette action est irréversible. Voulez-vous continuer ?</p>
        <div class="flex justify-end gap-4">
            <button class="px-4 py-2 rounded text-gray-400 hover:text-white transition-colors">
                Annuler
            </button>
            <button class="bg-error hover:bg-error-dark text-white font-bold px-4 py-2 rounded transition-colors">
                Supprimer
            </button>
        </div>
    </div>
</div>
```

### Exemple 2 : Modal avec Formulaire

```blade
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-surface-dark border border-border-dark rounded-lg p-6 max-w-lg w-full terminal-border-simple max-h-[90vh] overflow-y-auto">
        <h2 class="text-2xl font-bold text-white mb-6 font-mono">NOUVEAU_PLANETE</h2>
        <form>
            <div class="mb-4">
                <label class="block text-gray-300 text-sm font-bold mb-2">Nom</label>
                <input type="text" class="w-full py-2 px-3 bg-surface-medium border border-border-dark rounded text-white">
            </div>
            <div class="flex justify-end gap-4 mt-6">
                <button type="button" class="px-4 py-2 rounded text-gray-400 hover:text-white transition-colors">
                    Annuler
                </button>
                <button type="submit" class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold px-4 py-2 rounded transition-colors">
                    Créer
                </button>
            </div>
        </form>
    </div>
</div>
```

### Exemple 3 : Modal avec Message Terminal

```blade
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-surface-dark border border-border-dark rounded-lg p-6 max-w-md w-full terminal-border-simple">
        <div class="mb-4">
            <div class="text-sm text-space-primary font-mono mb-2">
                SYSTEM@STELLAR:~$ <span class="text-space-secondary">confirm_action</span>
            </div>
        </div>
        <h2 class="text-2xl font-bold text-white mb-4 font-mono">CONFIRMATION</h2>
        <p class="text-gray-300 mb-6">Voulez-vous vraiment effectuer cette action ?</p>
        <div class="flex justify-end gap-4">
            <button class="px-4 py-2 rounded text-gray-400 hover:text-white transition-colors font-mono">
                > CANCEL
            </button>
            <button class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold px-4 py-2 rounded transition-colors font-mono">
                > CONFIRM
            </button>
        </div>
    </div>
</div>
```

## Responsive

### Mobile (< 640px)
- Max width : `w-full` avec padding latéral (`mx-4`)
- Padding réduit : `p-4` au lieu de `p-6`
- Boutons empilés verticalement si nécessaire

### Tablet (640px - 1024px)
- Max width : `max-w-md` (448px)
- Padding standard : `p-6`

### Desktop (> 1024px)
- Max width : `max-w-lg` ou `max-w-xl` selon le contenu
- Padding généreux : `p-6` ou `p-8`

## Accessibilité

- **Focus trap** : Le focus doit rester dans le modal quand il est ouvert
- **Escape key** : Fermer le modal avec la touche Escape
- **ARIA labels** : Utiliser `aria-labelledby` pour le titre et `aria-describedby` pour la description
- **Focus initial** : Mettre le focus sur le premier élément interactif à l'ouverture
- **Backdrop click** : Optionnel pour fermer au clic sur l'overlay

**Exemple avec ARIA** :
```html
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" 
     role="dialog" 
     aria-labelledby="modal-title" 
     aria-describedby="modal-description">
    <div class="bg-surface-dark border border-border-dark rounded-lg p-6 max-w-md w-full">
        <h2 id="modal-title" class="text-2xl font-bold text-white mb-4 font-mono">TITRE</h2>
        <p id="modal-description" class="text-gray-300 mb-6">Description</p>
        <!-- Contenu -->
    </div>
</div>
```

## Animations

### Ouverture
- Fade in de l'overlay
- Scale up du conteneur modal
- Durée : 150ms - 300ms

**Exemple avec Alpine.js** :
```html
<div x-data="{ show: false }"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100">
    <!-- Modal -->
</div>
```

## Notes de Design

- **Z-index** : Utiliser `z-50` pour s'assurer que le modal est au-dessus de tout
- **Overlay** : Semi-transparent (`bg-opacity-50`) pour créer une séparation visuelle
- **Centrage** : Utiliser `flex items-center justify-center` sur l'overlay
- **Scroll** : Pour les modals avec beaucoup de contenu, utiliser `max-h-[90vh] overflow-y-auto`
- **Style Terminal** : Utiliser `font-mono` pour les titres et messages pour maintenir la cohérence

---

**Référence** : Voir **[DESIGN-SYSTEM.md](../DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.




