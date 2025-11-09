# COMPONENT-Form

## Vue d'Ensemble

Le composant Form définit les styles et comportements pour tous les éléments de formulaire dans l'application. Il assure une expérience utilisateur cohérente avec validation visuelle claire et feedback immédiat.

**Quand l'utiliser** :
- Formulaires de connexion/inscription
- Formulaires de profil
- Formulaires de recherche
- Tous les champs de saisie utilisateur

## Design

### Apparence

Les formulaires utilisent un fond sombre (`bg-surface-dark`), des bordures subtiles (`border-border-dark`), et des états visuels clairs pour la validation et le focus.

### Éléments

#### Input (Text, Email, Password, etc.)

**États** :
- Default : Fond sombre, bordure subtile
- Focus : Bordure primary avec ring
- Error : Bordure rouge, message d'erreur
- Disabled : Opacité réduite, cursor not-allowed

**Exemple** :
```html
<input
  type="email"
  class="w-full py-2 px-3 bg-surface-dark border border-border-dark rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 focus:ring-offset-space-black focus:border-space-primary transition-all duration-150"
  placeholder="Enter your email"
>
```

#### Label

**Style** : Texte clair, poids bold, margin bottom

**Exemple** :
```html
<label for="email" class="block text-gray-300 text-sm font-bold mb-2">
  Email
</label>
```

#### Textarea

**Style** : Similaire à input, avec resize vertical

**Exemple** :
```html
<textarea
  class="w-full py-2 px-3 bg-surface-dark border border-border-dark rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 focus:ring-offset-space-black focus:border-space-primary transition-all duration-150 resize-y"
  rows="4"
></textarea>
```

#### Select

**Style** : Similaire à input, avec flèche de sélection

**Exemple** :
```html
<select
  class="w-full py-2 px-3 bg-surface-dark border border-border-dark rounded text-white focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 focus:ring-offset-space-black focus:border-space-primary transition-all duration-150"
>
  <option>Option 1</option>
  <option>Option 2</option>
</select>
```

#### Checkbox

**Style** : Case à cocher avec accent primary

**Exemple** :
```html
<label class="flex items-center">
  <input
    type="checkbox"
    class="w-4 h-4 text-space-primary bg-surface-dark border-border-dark rounded focus:ring-space-primary focus:ring-2"
  >
  <span class="ml-2 text-gray-300">Accepter les conditions</span>
</label>
```

#### Radio

**Style** : Bouton radio avec accent primary

**Exemple** :
```html
<label class="flex items-center">
  <input
    type="radio"
    name="option"
    class="w-4 h-4 text-space-primary bg-surface-dark border-border-dark focus:ring-space-primary focus:ring-2"
  >
  <span class="ml-2 text-gray-300">Option 1</span>
</label>
```

### États

#### Default

État par défaut du champ, prêt à recevoir une saisie.

#### Focus

Bordure primary avec ring pour indiquer le focus actif.

#### Error

Bordure rouge, message d'erreur affiché en dessous.

**Exemple** :
```html
<div class="mb-4">
  <label for="email" class="block text-gray-300 text-sm font-bold mb-2">
    Email
  </label>
  <input
    type="email"
    id="email"
    class="w-full py-2 px-3 bg-surface-dark border border-error rounded text-white focus:outline-none focus:ring-2 focus:ring-error"
  >
  <p class="text-error text-xs mt-1">Email invalide</p>
</div>
```

#### Success

Bordure verte pour indiquer une validation réussie.

**Exemple** :
```html
<input
  type="email"
  class="w-full py-2 px-3 bg-surface-dark border border-success rounded text-white"
>
```

#### Disabled

Opacité réduite, cursor not-allowed, pas d'interaction.

**Exemple** :
```html
<input
  type="text"
  disabled
  class="w-full py-2 px-3 bg-surface-dark border border-border-dark rounded text-gray-500 opacity-50 cursor-not-allowed"
>
```

### Responsive

Les formulaires s'adaptent automatiquement :
- **Mobile** : Padding réduit, labels au-dessus
- **Tablet/Desktop** : Layout optimisé, labels à gauche si nécessaire

## Spécifications Techniques

### Classes Tailwind

#### Structure de Base

```html
<div class="mb-4">
  <label for="field" class="block text-gray-300 text-sm font-bold mb-2">
    Label
  </label>
  <input
    type="text"
    id="field"
    class="w-full py-2 px-3 bg-surface-dark border border-border-dark rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 focus:ring-offset-space-black focus:border-space-primary transition-all duration-150"
    placeholder="Placeholder"
  >
</div>
```

#### Avec Validation (Livewire)

```html
<div class="mb-4">
  <label for="email" class="block text-gray-300 text-sm font-bold mb-2">
    Email
  </label>
  <input
    type="email"
    id="email"
    wire:model="email"
    class="w-full py-2 px-3 bg-surface-dark border rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 focus:ring-offset-space-black transition-all duration-150 @error('email') border-error focus:ring-error @else border-border-dark focus:border-space-primary @enderror"
  >
  @error('email')
    <p class="text-error text-xs mt-1">{{ $message }}</p>
  @enderror
</div>
```

### Props (si composant Livewire)

- `name` : `string` - Nom du champ
- `label` : `string` - Label du champ
- `type` : `string` - Type HTML (`text`, `email`, `password`, etc.)
- `placeholder` : `string` - Texte placeholder
- `required` : `boolean` - Champ requis
- `disabled` : `boolean` - Champ désactivé
- `value` : `string` - Valeur initiale

### Structure HTML

```html
<form class="space-y-4">
  <!-- Champ simple -->
  <div class="mb-4">
    <label for="field" class="block text-gray-300 text-sm font-bold mb-2">
      Label
    </label>
    <input
      type="text"
      id="field"
      name="field"
      class="[classes]"
      [required]
      [disabled]
    >
    <!-- Message d'erreur -->
    <p class="text-error text-xs mt-1">[erreur]</p>
  </div>
  
  <!-- Bouton de soumission -->
  <button type="submit" class="...">
    Soumettre
  </button>
</form>
```

## Code d'Implémentation

### Livewire Component

```php
<?php

namespace App\Livewire;

use Livewire\Component;

class Form extends Component
{
    public $email = '';
    public $password = '';
    
    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:8',
    ];
    
    public function submit()
    {
        $this->validate();
        // Traitement
    }
    
    public function render()
    {
        return view('livewire.form');
    }
}
```

### Blade Template

```blade
<form wire:submit="submit" class="space-y-4">
  <!-- Email -->
  <div class="mb-4">
    <label for="email" class="block text-gray-300 text-sm font-bold mb-2">
      Email
    </label>
    <input
      type="email"
      id="email"
      wire:model="email"
      class="w-full py-2 px-3 bg-surface-dark border rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 focus:ring-offset-space-black transition-all duration-150 @error('email') border-error focus:ring-error @else border-border-dark focus:border-space-primary @enderror"
      placeholder="Enter your email"
    >
    @error('email')
      <p class="text-error text-xs mt-1">{{ $message }}</p>
    @enderror
  </div>

  <!-- Password -->
  <div class="mb-4">
    <label for="password" class="block text-gray-300 text-sm font-bold mb-2">
      Password
    </label>
    <input
      type="password"
      id="password"
      wire:model="password"
      class="w-full py-2 px-3 bg-surface-dark border rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 focus:ring-offset-space-black transition-all duration-150 @error('password') border-error focus:ring-error @else border-border-dark focus:border-space-primary @enderror"
      placeholder="Enter your password"
    >
    @error('password')
      <p class="text-error text-xs mt-1">{{ $message }}</p>
    @enderror
  </div>

  <!-- Submit -->
  <button
    type="submit"
    wire:loading.attr="disabled"
    class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold py-3 px-6 rounded-lg transition-colors duration-150 disabled:opacity-50 w-full"
  >
    <span wire:loading.remove wire:target="submit">Submit</span>
    <span wire:loading wire:target="submit">Submitting...</span>
  </button>
</form>
```

## Exemples d'Utilisation

### Exemple 1 : Formulaire de Connexion

```html
<form class="max-w-md mx-auto mt-8">
  <div class="bg-surface-dark border border-border-dark rounded-lg px-8 pt-6 pb-8">
    <h2 class="text-2xl font-bold text-white mb-6 text-center">Sign In</h2>
    
    <form wire:submit="login" class="space-y-4">
      <!-- Email -->
      <div class="mb-4">
        <label for="email" class="block text-gray-300 text-sm font-bold mb-2">
          Email
        </label>
        <input
          type="email"
          id="email"
          wire:model="email"
          class="w-full py-2 px-3 bg-surface-dark border border-border-dark rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 focus:ring-offset-space-black focus:border-space-primary transition-all duration-150"
          placeholder="Enter your email"
        >
        @error('email')
          <p class="text-error text-xs mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Password -->
      <div class="mb-6">
        <label for="password" class="block text-gray-300 text-sm font-bold mb-2">
          Password
        </label>
        <input
          type="password"
          id="password"
          wire:model="password"
          class="w-full py-2 px-3 bg-surface-dark border border-border-dark rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 focus:ring-offset-space-black focus:border-space-primary transition-all duration-150"
          placeholder="Enter your password"
        >
        @error('password')
          <p class="text-error text-xs mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Submit -->
      <button
        type="submit"
        wire:loading.attr="disabled"
        class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline disabled:opacity-50 w-full transition-colors duration-150"
      >
        <span wire:loading.remove wire:target="login">Sign In</span>
        <span wire:loading wire:target="login">Signing in...</span>
      </button>
    </form>
  </div>
</form>
```

### Exemple 2 : Formulaire avec Checkbox

```html
<form class="space-y-4">
  <div class="mb-4">
    <label for="name" class="block text-gray-300 text-sm font-bold mb-2">
      Name
    </label>
    <input
      type="text"
      id="name"
      class="w-full py-2 px-3 bg-surface-dark border border-border-dark rounded text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 focus:ring-offset-space-black focus:border-space-primary transition-all duration-150"
    >
  </div>

  <div class="mb-4">
    <label class="flex items-center">
      <input
        type="checkbox"
        class="w-4 h-4 text-space-primary bg-surface-dark border-border-dark rounded focus:ring-space-primary focus:ring-2"
      >
      <span class="ml-2 text-gray-300 text-sm">Accepter les conditions</span>
    </label>
  </div>

  <button type="submit" class="...">
    Soumettre
  </button>
</form>
```

## Accessibilité

### Labels

- Toujours associer un label à chaque champ
- Utiliser `for` et `id` pour l'association
- Labels clairs et descriptifs

### Contraste

- Texte blanc sur fond sombre : Ratio 21:1 ✅
- Placeholder : Ratio 3.2:1 (acceptable pour placeholder)

### Focus

- Contour visible avec couleur primary
- Ring de 2px avec offset
- Transition fluide

### Validation

- Messages d'erreur clairs et descriptifs
- Indication visuelle (bordure rouge)
- Messages accessibles (ARIA)

### ARIA

```html
<input
  type="email"
  id="email"
  aria-label="Email address"
  aria-required="true"
  aria-invalid="false"
  aria-describedby="email-error"
>
<p id="email-error" class="text-error text-xs mt-1" role="alert">
  Email invalide
</p>
```

## Notes de Design

- **Cohérence** : Utiliser les mêmes styles pour tous les champs
- **Feedback** : Validation visuelle immédiate et claire
- **Accessibilité** : Labels clairs, focus visible, messages d'erreur descriptifs
- **Performance** : Transitions optimisées, validation côté client quand possible

---

**Référence** : Voir **[DESIGN-SYSTEM.md](../DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

