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

Les composants de base sont les éléments fondamentaux réutilisables dans toute l'application.

### Button

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

### Form

Voir **[COMPONENT-form.md](./components/COMPONENT-form.md)** pour la documentation complète.

**Éléments** :
- Inputs : Champs de saisie avec validation
- Labels : Labels clairs et accessibles
- Validation : Messages d'erreur visuels
- États : Focus, disabled, error

### Form Input

Voir **[COMPONENT-form-input.md](./components/COMPONENT-form-input.md)** pour la documentation complète.

**Usage** : Composant réutilisable pour les champs de formulaire avec label, input, validation et messages d'erreur

**Variantes** :
- Classic : Style standard avec label au-dessus
- Terminal : Style terminal avec prompt système

**Exemple** :
```blade
<x-form-input
    type="email"
    name="email"
    label="Email"
    wireModel="email"
    placeholder="Enter your email"
/>
```

### Form Card

Voir **[COMPONENT-form-card.md](./components/COMPONENT-form-card.md)** pour la documentation complète.

**Usage** : Conteneur standardisé pour les formulaires avec fond, ombre, bordures et effet scan

**Variantes** :
- Standard : Titre intégré au-dessus du formulaire
- Header Séparé : Titre dans un header distinct avec bordure

**Exemple** :
```blade
<x-form-card title="Sign In">
    <form>
        <!-- Form fields -->
    </form>
</x-form-card>
```

### Form Link

Voir **[COMPONENT-form-link.md](./components/COMPONENT-form-link.md)** pour la documentation complète.

**Usage** : Lien de navigation entre formulaires avec texte descriptif et lien stylisé

**Caractéristiques** :
- Texte descriptif en gris avec lien en couleur secondary
- Centrage automatique du texte
- Espacement configurable

**Exemple** :
```blade
<x-form-link
    text="Don't have an account?"
    linkText="Register"
    :href="route('register')"
/>
```

### Page Header

Voir **[COMPONENT-page-header.md](./components/COMPONENT-page-header.md)** pour la documentation complète.

**Usage** : En-tête de page standardisé avec titre et description optionnelle

**Caractéristiques** :
- Titre H1 avec taille et style standardisés
- Description optionnelle
- Espacement configurable

**Exemple** :
```blade
<x-page-header
    title="Profile Settings"
    description="Manage your account information and preferences."
/>
```

### Card

Voir **[COMPONENT-card.md](./components/COMPONENT-card.md)** pour la documentation complète.

**Usage** : Conteneurs génériques pour afficher des informations

**Exemple** :
```html
<div class="bg-surface-dark border border-border-dark rounded-lg p-6 mb-6">
  <h3 class="text-2xl font-semibold text-white mb-4">Titre</h3>
  <p class="text-gray-300">Contenu</p>
</div>
```

### Alert

Voir **[COMPONENT-alert.md](./components/COMPONENT-alert.md)** pour la documentation complète.

**Usage** : Messages d'alerte et notifications

**Variantes** :
- Success : Confirmations et succès
- Error : Erreurs critiques
- Warning : Avertissements
- Info : Informations

**Exemple** :
```blade
<x-alert type="error" message="Failed to load planet data" />
```

## Composants Terminal

Les composants terminal offrent une interface de type console pour créer une expérience immersive dans le thème spatial.

### Terminal Prompt

Voir **[COMPONENT-terminal-prompt.md](./components/COMPONENT-terminal-prompt.md)** pour la documentation complète.

**Usage** : Ligne de commande terminal avec prompt système

**Caractéristiques** :
- Style monospace avec prompt système
- Format `SYSTEM@SPACE-XPLORER:~$ command`
- Support des couleurs du design system

**Exemple** :
```blade
<x-terminal-prompt command="load_user_session" />
```

### Terminal Boot

Voir **[COMPONENT-terminal-boot.md](./components/COMPONENT-terminal-boot.md)** pour la documentation complète.

**Usage** : Séquence de messages de démarrage système avec animations

**Spécifications** :
- Messages avec couleurs selon le type ([OK], [ERROR], etc.)
- Animation de fade-out pour les anciens messages
- Support du polling Livewire
- Curseur clignotant pendant le chargement

**Exemple** :
```blade
<x-terminal-boot 
    :bootMessages="$bootMessages" 
    :terminalBooted="$terminalBooted"
    :pollMethod="'nextBootStep'"
/>
```

### Terminal Message

Voir **[COMPONENT-terminal-message.md](./components/COMPONENT-terminal-message.md)** pour la documentation complète.

**Usage** : Messages système avec style terminal et détection automatique du type

**Caractéristiques** :
- Détection automatique du type basée sur le préfixe ([OK], [ERROR], [INFO], etc.)
- Couleurs adaptées selon le type (vert pour success, rouge pour error, etc.)
- Espacement configurable

**Exemple** :
```blade
<x-terminal-message message="[OK] System initialized" />
```

### Terminal Link

Voir **[COMPONENT-terminal-link.md](./components/COMPONENT-terminal-link.md)** pour la documentation complète.

**Usage** : Lien avec style terminal pour les interfaces terminal

**Caractéristiques** :
- Style monospace avec format de commande terminal
- Couleur secondary avec effet hover
- Bordure supérieure optionnelle

**Exemple** :
```blade
<x-terminal-link
    href="{{ route('register') }}"
    text="> REGISTER_NEW_USER"
/>
```

## Composants Spécialisés

Les composants spécialisés sont conçus pour des cas d'usage spécifiques du projet.

### Planet Card

Voir **[COMPONENT-planet-card.md](./components/COMPONENT-planet-card.md)** pour la documentation complète.

**Usage** : Affichage des caractéristiques d'une planète

**Spécifications** :
- Layout horizontal avec image (optionnelle)
- Header avec nom et type
- Description de la planète
- Liste de caractéristiques avec format terminal

**Exemple** :
```blade
<x-planet-card :planet="$planet" />
```

### Loading Spinner

Voir **[COMPONENT-loading-spinner.md](./components/COMPONENT-loading-spinner.md)** pour la documentation complète.

**Usage** : Indicateur de chargement avec style terminal

**Variantes** :
- **Terminal** (défaut) : Avec message terminal et style monospace
- **Simple** : Spinner uniquement, sans message, style minimaliste

**Tailles** :
- Small (`sm`) : 32px × 32px
- Medium (`md`) : 48px × 48px (défaut)
- Large (`lg`) : 56px × 56px (avec bordure plus épaisse)

**Exemple** :
```blade
<x-loading-spinner message="[LOADING] Accessing planetary database..." />
<x-loading-spinner variant="simple" size="md" :showMessage="false" />
```

## Composants Utilitaires

Les composants utilitaires facilitent l'organisation et la mise en page des autres composants.

### Button Group

Voir **[COMPONENT-button-group.md](./components/COMPONENT-button-group.md)** pour la documentation complète.

**Usage** : Grouper plusieurs boutons d'action ensemble avec un layout cohérent

**Variantes** :
- Alignement : `center` (défaut), `left`, `right`
- Espacement : `sm`, `md` (défaut), `lg`
- Largeur : `full-width` pour prendre toute la largeur

**Exemple** :
```blade
<x-button-group align="center" spacing="md">
    <button>Action 1</button>
    <button>Action 2</button>
</x-button-group>
```

### Navigation

Voir **[COMPONENT-navigation.md](./components/COMPONENT-navigation.md)** pour la documentation complète.

**Usage** : Navigation principale de l'application avec style rétro-futuriste

**Variantes** :
- Sidebar : Navigation latérale fixe ou sticky
- Top : Menu de navigation horizontal en haut de la page
- Terminal : Barre de navigation fixe en bas de l'écran avec style terminal

**Exemple** :
```blade
<x-navigation variant="sidebar" :items="$navItems" />
```

### Modal

Voir **[COMPONENT-modal.md](./components/COMPONENT-modal.md)** pour la documentation complète.

**Usage** : Dialogs pour les interactions importantes, confirmations, ou contenus nécessitant l'attention

**Variantes** :
- Standard : Modal standard pour la plupart des dialogues
- Confirmation : Modal de confirmation pour actions destructives
- Form : Modal contenant un formulaire

**Exemple** :
```blade
<x-modal show="true" title="Confirmation" variant="confirmation">
    <p>Êtes-vous sûr de vouloir continuer ?</p>
</x-modal>
```

## Composants à Venir

Les composants suivants sont prévus pour de futures versions :

- **Badge** : Indicateurs de statut et labels
- **Empty State** : États vides (pas de données)

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
├── COMPONENT-button.md          (Base)
├── COMPONENT-form.md             (Base)
├── COMPONENT-form-input.md       (Base)
├── COMPONENT-form-card.md        (Base)
├── COMPONENT-form-link.md        (Base)
├── COMPONENT-page-header.md      (Base)
├── COMPONENT-card.md             (Base)
├── COMPONENT-alert.md            (Base)
├── COMPONENT-terminal-prompt.md  (Terminal)
├── COMPONENT-terminal-boot.md    (Terminal)
├── COMPONENT-terminal-message.md (Terminal)
├── COMPONENT-terminal-link.md    (Terminal)
├── COMPONENT-planet-card.md      (Spécialisé)
├── COMPONENT-loading-spinner.md  (Spécialisé)
├── COMPONENT-button-group.md     (Utilitaires)
├── COMPONENT-navigation.md       (Utilitaires)
├── COMPONENT-modal.md            (Utilitaires)
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

