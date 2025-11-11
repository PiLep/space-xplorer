# COMPONENT-Form Input

## Vue d'Ensemble

Le composant Form Input est un composant réutilisable pour créer des champs de formulaire avec label, input, validation et messages d'erreur. Il supporte deux variantes de style : classique et terminal.

**Quand l'utiliser** :
- Tous les champs de formulaire (text, email, password, etc.)
- Formulaires de connexion/inscription
- Formulaires de profil
- Champs avec validation Livewire
- Champs read-only avec texte d'aide

## Design

### Variantes

#### Classic (Défaut)
- Style standard avec label au-dessus
- Bordure arrondie avec shadow
- Fond adapté au mode clair/sombre
- Messages d'erreur en italique

#### Terminal
- Style terminal avec prompt système
- Bordure inférieure uniquement (border-b-2)
- Fond transparent
- Police monospace
- Messages d'erreur avec préfixe [ERROR]

### États

#### Default
État par défaut du champ, prêt à recevoir une saisie.

#### Focus
- Bordure primary avec ring pour le style classique
- Bordure primary pour le style terminal

#### Error
- Bordure rouge (`border-error`)
- Message d'erreur affiché en dessous
- Style différent selon la variante (italique pour classic, [ERROR] pour terminal)

#### Disabled
- Opacité réduite
- Cursor not-allowed
- Fond grisé
- Support du texte d'aide (`helpText`)

## Spécifications Techniques

### Props

| Prop | Type | Défaut | Description |
|------|------|--------|-------------|
| `type` | string | `'text'` | Type HTML de l'input : `text`, `email`, `password`, `number`, etc. |
| `name` | string | `''` | Nom du champ (pour le name et l'id si id non fourni) |
| `id` | string | `null` | ID HTML du champ (défaut: utilise `name`) |
| `label` | string | `''` | Label du champ |
| `placeholder` | string | `''` | Texte placeholder |
| `value` | string | `null` | Valeur initiale du champ |
| `wireModel` | string | `null` | Nom du modèle Livewire (ex: `"email"` pour `wire:model="email"`) |
| `disabled` | boolean | `false` | Désactive le champ |
| `required` | boolean | `false` | Marque le champ comme requis |
| `autofocus` | boolean | `false` | Active l'autofocus sur le champ |
| `variant` | string | `'classic'` | Style du champ : `'classic'` ou `'terminal'` |
| `marginBottom` | string | `'mb-4'` | Classe Tailwind pour la marge inférieure (`mb-4`, `mb-6`, etc.) |
| `helpText` | string | `null` | Texte d'aide affiché sous le champ (utile pour les champs read-only) |
| `errorField` | string | `null` | Nom du champ pour la validation d'erreur (défaut: utilise `name`) |

### Structure HTML

#### Variante Classic

```blade
<x-form-input
    type="email"
    name="email"
    id="email"
    label="Email"
    wireModel="email"
    placeholder="Enter your email"
    marginBottom="mb-4"
/>
```

**Rendu** :
```html
<div class="mb-4">
    <label for="email" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
        Email
    </label>
    <input
        type="email"
        id="email"
        name="email"
        wire:model="email"
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-surface-medium dark:border-border-dark leading-tight focus:outline-none focus:ring-2 focus:ring-space-primary focus:border-space-primary @error('email') border-error dark:border-error @enderror"
        placeholder="Enter your email"
    >
    @error('email')
        <p class="text-error text-xs italic mt-1">{{ $message }}</p>
    @enderror
</div>
```

#### Variante Terminal

```blade
<x-form-input
    type="email"
    name="email"
    label="enter_email"
    wireModel="email"
    placeholder="user@domain.com"
    variant="terminal"
    autofocus
    marginBottom="mb-6"
/>
```

**Rendu** :
```html
<div class="mb-6">
    <div class="text-sm text-gray-500 dark:text-gray-500 mb-2">
        <span class="text-space-primary dark:text-space-primary">SYSTEM@STELLAR:~$</span> 
        <span class="text-space-secondary dark:text-space-secondary">enter_email</span>
    </div>
    <input
        type="email"
        name="email"
        wire:model="email"
        class="w-full bg-transparent border-b-2 border-gray-300 dark:border-border-dark focus:border-space-primary dark:focus:border-space-primary text-gray-900 dark:text-white py-2 px-0 focus:outline-none font-mono text-sm transition-colors @error('email') border-error dark:border-error @enderror"
        placeholder="user@domain.com"
        autofocus
    >
    @error('email')
        <div class="mt-2 text-xs text-error dark:text-error">
            [ERROR] {{ $message }}
        </div>
    @enderror
</div>
```

## Code d'Implémentation

### Composant Blade

**Fichier** : `resources/views/components/form-input.blade.php`

Voir le fichier complet pour l'implémentation détaillée.

## Exemples d'Utilisation

### Exemple 1 : Champ Email Standard

```blade
<x-form-input
    type="email"
    name="email"
    id="email"
    label="Email"
    wireModel="email"
    placeholder="Enter your email"
    required
    autofocus
/>
```

### Exemple 2 : Champ Password avec Validation

```blade
<x-form-input
    type="password"
    name="password"
    id="password"
    label="Password"
    wireModel="password"
    placeholder="Enter your password"
    marginBottom="mb-6"
/>
```

### Exemple 3 : Champ Read-Only avec Texte d'Aide

```blade
<x-form-input
    type="text"
    name="user_id"
    id="user_id"
    label="User ID"
    value="{{ $user->id }}"
    disabled
    helpText="This is your unique user identifier."
/>
```

### Exemple 4 : Champ Terminal Style

```blade
<x-form-input
    type="password"
    name="password"
    label="enter_password"
    wireModel="password"
    placeholder="••••••••"
    variant="terminal"
    marginBottom="mb-6"
/>
```

### Exemple 5 : Champ avec Error Field Personnalisé

```blade
<x-form-input
    type="text"
    name="password_confirmation"
    id="password_confirmation"
    label="Confirm Password"
    wireModel="password_confirmation"
    errorField="password_confirmation"
    placeholder="Confirm your password"
/>
```

## Intégration avec Livewire

Le composant est conçu pour fonctionner avec Livewire :

```blade
<form wire:submit="login">
    <x-form-input
        type="email"
        name="email"
        label="Email"
        wireModel="email"
        placeholder="Enter your email"
    />
    
    <x-form-input
        type="password"
        name="password"
        label="Password"
        wireModel="password"
        placeholder="Enter your password"
        marginBottom="mb-6"
    />
    
    <button type="submit">Sign In</button>
</form>
```

La validation Livewire fonctionne automatiquement avec `@error` dans le composant.

## Accessibilité

### Labels
- Labels toujours associés aux champs via `for` et `id`
- Labels clairs et descriptifs
- Support des labels vides (pour les champs sans label visible)

### Contraste
- Texte clair sur fond sombre : Ratio 21:1 ✅
- Placeholder : Ratio 3.2:1 (acceptable pour placeholder)
- Messages d'erreur : Couleur error avec bon contraste

### Focus
- Contour visible avec couleur primary
- Ring de 2px pour le style classique
- Bordure primary pour le style terminal
- Transition fluide

### Validation
- Messages d'erreur clairs et descriptifs
- Indication visuelle (bordure rouge)
- Messages accessibles (ARIA via `@error`)

### ARIA
Le composant génère automatiquement :
- Association `label` / `input` via `for` et `id`
- `aria-required="true"` pour les champs requis
- `aria-invalid="true"` sur les inputs en erreur
- `aria-describedby` associant les messages d'erreur aux inputs
- `role="alert"` sur les messages d'erreur pour les lecteurs d'écran
- ID unique pour chaque message d'erreur (`{fieldId}-error`)

**Exemple de structure ARIA générée** :
```html
<input 
    id="email" 
    name="email"
    aria-required="true"
    aria-invalid="true"
    aria-describedby="email-error"
/>
<p id="email-error" role="alert" class="text-error">The email field is required.</p>
```

## Notes de Design

- **Cohérence** : Utiliser le même composant pour tous les champs de formulaire
- **Flexibilité** : Support de deux variantes (classic et terminal) pour différents contextes
- **Validation** : Gestion automatique des erreurs avec Livewire
- **Accessibilité** : Labels obligatoires, focus visible, messages d'erreur descriptifs
- **Performance** : Transitions optimisées, pas de JavaScript requis

## Migration depuis les Champs Inline

### Avant

```blade
<div class="mb-4">
    <label for="email" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
        Email
    </label>
    <input
        type="email"
        id="email"
        wire:model="email"
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-surface-medium dark:border-border-dark leading-tight focus:outline-none focus:ring-2 focus:ring-space-primary focus:border-space-primary @error('email') border-error dark:border-error @enderror"
        placeholder="Enter your email"
    >
    @error('email')
        <p class="text-error text-xs italic mt-1">{{ $message }}</p>
    @enderror
</div>
```

### Après

```blade
<x-form-input
    type="email"
    name="email"
    id="email"
    label="Email"
    wireModel="email"
    placeholder="Enter your email"
    marginBottom="mb-4"
/>
```

---

**Référence** : Voir **[COMPONENT-form.md](./COMPONENT-form.md)** pour la documentation générale des formulaires et **[DESIGN-SYSTEM.md](../DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

