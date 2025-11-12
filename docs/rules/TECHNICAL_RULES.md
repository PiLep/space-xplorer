# Technical Rules - Stellar

Ce document contient les règles techniques validées pour le projet Stellar. Ces règles améliorent la qualité du code et standardisent les bonnes pratiques de l'équipe.

## Processus d'Ajout

Les règles techniques sont proposées par Morgan (Architect) ou Sam (Lead Developer) via le processus décrit dans [propose-technical-rule.md](../prompts/propose-technical-rule.md).

⚠️ **Toute nouvelle règle nécessite une validation humaine avant application.**

## Règles Actuelles

### Règle 1 : Utilisation de Laravel Sail pour l'environnement de développement

**Date d'ajout** : 2025-11-09  
**Proposée par** : Jordan (Fullstack Developer)  
**Validée par** : À valider

**Description** : Toutes les commandes de développement (artisan, composer, npm, migrations, tests, etc.) doivent être exécutées via Laravel Sail (`./vendor/bin/sail`) pour garantir la cohérence avec l'environnement Docker de développement.

**Exemples** :

**Bon exemple** :
```bash
# Exécuter les migrations
./vendor/bin/sail artisan migrate

# Installer une dépendance Composer
./vendor/bin/sail composer require laravel/sanctum

# Exécuter les tests
./vendor/bin/sail artisan test

# Formater le code
./vendor/bin/sail pint

# Installer les dépendances NPM
./vendor/bin/sail npm install
```

**Mauvais exemple** :
```bash
# ❌ Ne pas utiliser directement artisan/composer/npm
php artisan migrate
composer require laravel/sanctum
npm install
```

**Justification** : 
- Garantit la cohérence de l'environnement de développement (PHP version, extensions, MySQL, Redis)
- Évite les problèmes de compatibilité entre les environnements locaux
- Simplifie le setup pour les nouveaux développeurs
- Assure que l'environnement de développement correspond à l'environnement de production
- Laravel Sail fournit un environnement isolé et reproductible

### Règle 2 : Vérification de l'application avec Chrome DevTools MCP

**Date d'ajout** : 2025-11-09  
**Proposée par** : Jordan (Fullstack Developer)  
**Validée par** : À valider

**Description** : Après avoir lancé l'application ou implémenté une nouvelle fonctionnalité, utiliser Chrome DevTools MCP pour vérifier visuellement que l'application fonctionne correctement. Cela permet de détecter les problèmes d'affichage, d'interaction, ou d'erreurs JavaScript avant de continuer.

**Quand utiliser** :
- Après avoir lancé l'application pour la première fois
- Après avoir implémenté une nouvelle fonctionnalité frontend
- Après avoir modifié des routes ou des contrôleurs qui affectent l'affichage
- Avant de marquer une phase comme terminée
- Lorsqu'on suspecte un problème d'affichage ou d'interaction

**Exemples** :

**Bon exemple** :
```bash
# 1. Lancer l'application
./vendor/bin/sail up -d

# 2. Vérifier avec Chrome DevTools MCP
# - Prendre un snapshot de la page
# - Vérifier les erreurs dans la console
# - Vérifier les requêtes réseau
# - Tester les interactions utilisateur si nécessaire
```

**Mauvais exemple** :
```bash
# ❌ Ne pas vérifier visuellement l'application
# Se contenter de vérifier que les migrations passent
# Ignorer les problèmes d'affichage ou d'interaction
```

**Justification** : 
- Détecte les problèmes visuels et d'interaction avant qu'ils ne soient découverts par les utilisateurs
- Permet de vérifier que les modifications fonctionnent comme prévu dans un vrai navigateur
- Identifie les erreurs JavaScript ou CSS qui ne seraient pas détectées par les tests backend
- Assure une meilleure qualité globale de l'application
- Chrome DevTools MCP permet une vérification automatisée et reproductible

**Outils disponibles** :
- `take_snapshot` : Prendre un snapshot de la page pour voir la structure HTML
- `list_console_messages` : Vérifier les erreurs JavaScript
- `list_network_requests` : Vérifier les requêtes réseau et leurs statuts
- `take_screenshot` : Capturer une image de la page pour documentation
- `evaluate_script` : Exécuter du JavaScript pour tester des interactions

### Règle 3 : Utilisation obligatoire d'un layout pour toutes les vues Blade

**Date d'ajout** : 2025-01-27  
**Proposée par** : Alex (Product)  
**Validée par** : À valider

**Description** : Toutes les vues Blade (`resources/views/**/*.blade.php`) qui sont rendues directement par un contrôleur doivent utiliser un layout approprié via `@extends('layouts.app')` ou un autre layout spécifique. Les composants Livewire utilisent l'attribut `#[Layout('layouts.app')]` dans leur classe PHP, mais les vues Blade classiques doivent toujours étendre un layout.

**Quand appliquer** :
- Lors de la création d'une nouvelle vue Blade rendue par un contrôleur
- Lors de la modification d'une vue Blade existante
- Lors de la review de code d'une nouvelle fonctionnalité
- Lors de la vérification visuelle avec Chrome DevTools MCP

**Exemples** :

**Bon exemple** :
```php
// resources/views/auth/reset-password.blade.php
@extends('layouts.app')

@section('content')
    <livewire:reset-password :token="$token" :email="$email" />
@endsection
```

```php
// app/Livewire/ResetPassword.php
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ResetPassword extends Component
{
    // ...
}
```

**Mauvais exemple** :
```php
// ❌ Vue Blade sans layout - CSS ne sera pas chargé
@php
    // This view is used by the controller to pass token and email to Livewire component
@endphp

<livewire:reset-password :token="$token" :email="$email" />
```

**Justification** : 
- Garantit que le CSS et les assets JavaScript sont toujours chargés correctement
- Assure une cohérence visuelle sur toutes les pages de l'application
- Évite les problèmes d'affichage où les styles ne sont pas appliqués
- Le layout `layouts.app` inclut les directives `@vite` nécessaires pour charger les assets
- Les composants Livewire peuvent utiliser l'attribut `#[Layout]` mais les vues Blade classiques doivent utiliser `@extends`
- Permet de maintenir une structure HTML cohérente (head, body, scripts) sur toutes les pages

**Exceptions** :
- Les composants Blade (`resources/views/components/**/*.blade.php`) n'ont pas besoin d'étendre un layout car ils sont inclus dans d'autres vues
- Les partials Blade (`resources/views/partials/**/*.blade.php`) n'ont pas besoin d'étendre un layout car ils sont inclus dans d'autres vues
- Les emails (`resources/views/emails/**/*.blade.php`) peuvent utiliser un layout spécifique pour les emails

**Vérification** :
- Lors de la review de code, vérifier que toutes les vues Blade rendues par des contrôleurs utilisent `@extends`
- Lors de la vérification visuelle avec Chrome DevTools MCP, vérifier que le CSS est chargé (inspecter les éléments et vérifier les styles appliqués)
- Si une page s'affiche sans styles, vérifier immédiatement si le layout est utilisé

### Règle 4 : Utilisation des attributs PHP 8 de Livewire 3 pour la validation

**Date d'ajout** : 2025-01-27  
**Proposée par** : Morgan (Architect)  
**Validée par** : À valider

**Description** : Utiliser les attributs PHP 8 `#[Validate]` de Livewire 3.6 pour définir les règles de validation directement sur les propriétés, plutôt que d'utiliser `protected $rules`.

**Quand appliquer** :
- Lors de la création d'un nouveau composant Livewire
- Lors de la modification d'un composant Livewire existant
- Lors de la migration de composants Livewire 2 vers Livewire 3

**Exemples** :

**Bon exemple** :
```php
use Livewire\Attributes\Validate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Register extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255|unique:users')]
    public string $email = '';

    #[Validate('required|string|min:8|confirmed')]
    public string $password = '';

    public function register()
    {
        $this->validate();
        // ...
    }
}
```

**Mauvais exemple** :
```php
class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ];
}
```

**Justification** : 
- Syntaxe moderne et déclarative avec les attributs PHP 8
- Code plus lisible : les règles de validation sont directement sur les propriétés
- Cohérence avec les standards Livewire 3.6
- Meilleure intégration avec l'IDE pour l'autocomplétion et la validation
- Réduction de la duplication de code

**Exceptions** :
- Les règles de validation complexes avec logique conditionnelle peuvent toujours utiliser `protected $rules` avec des méthodes dynamiques
- Les règles de validation qui dépendent d'autres propriétés peuvent nécessiter une validation manuelle dans les méthodes

### Règle 5 : Utilisation de `wire:key` pour les listes dans les vues Livewire

**Date d'ajout** : 2025-01-27  
**Proposée par** : Morgan (Architect)  
**Validée par** : À valider

**Description** : Toujours utiliser l'attribut `wire:key` pour les éléments dans les boucles (`@foreach`) dans les vues Livewire pour optimiser les performances et éviter les bugs de synchronisation du DOM.

**Quand appliquer** :
- Lors du rendu de listes dans les vues Livewire
- Lors de l'utilisation de `@foreach` avec des données dynamiques
- Lors de la création de composants Livewire qui affichent des collections

**Exemples** :

**Bon exemple** :
```blade
@foreach($planets as $planet)
    <div wire:key="planet-{{ $planet->id }}">
        <h3>{{ $planet->name }}</h3>
        <p>{{ $planet->description }}</p>
    </div>
@endforeach
```

```blade
@foreach($users as $index => $user)
    <div wire:key="user-{{ $user->id }}-{{ $index }}">
        {{ $user->name }}
    </div>
@endforeach
```

**Mauvais exemple** :
```blade
@foreach($planets as $planet)
    <div>
        <h3>{{ $planet->name }}</h3>
        <p>{{ $planet->description }}</p>
    </div>
@endforeach
```

**Justification** : 
- Aide Livewire à identifier les éléments lors des mises à jour du DOM
- Optimise les re-renders en ne mettant à jour que les éléments modifiés
- Évite les bugs de synchronisation du DOM (éléments mal associés après mise à jour)
- Améliore les performances en réduisant les manipulations DOM inutiles
- Standard recommandé par la documentation officielle Livewire

**Format recommandé** :
- Utiliser un identifiant unique : `wire:key="type-{{ $item->id }}"`
- Pour les listes sans ID, utiliser l'index : `wire:key="item-{{ $index }}"`
- Combiner ID et index si nécessaire : `wire:key="item-{{ $item->id }}-{{ $index }}"`

### Règle 6 : Utilisation de `wire:model.debounce` pour les champs de saisie fréquents

**Date d'ajout** : 2025-01-27  
**Proposée par** : Morgan (Architect)  
**Validée par** : À valider

**Description** : Utiliser `wire:model.debounce` pour les champs de saisie où l'utilisateur tape fréquemment (recherche, filtres, etc.) pour réduire le nombre de requêtes serveur et améliorer les performances.

**Quand appliquer** :
- Pour les champs de recherche
- Pour les filtres en temps réel
- Pour les champs de saisie qui déclenchent des requêtes serveur
- Pour les champs qui n'ont pas besoin de validation immédiate

**Exemples** :

**Bon exemple** :
```blade
<input type="text" wire:model.debounce.500ms="searchQuery" placeholder="Search planets...">
```

```blade
<input type="text" wire:model.debounce.300ms="filterName" placeholder="Filter by name...">
```

**Mauvais exemple** :
```blade
<input type="text" wire:model="searchQuery" placeholder="Search planets...">
```

**Justification** : 
- Réduit significativement le nombre de requêtes serveur (une requête toutes les 500ms au lieu de chaque frappe)
- Améliore les performances de l'application en réduisant la charge serveur
- Améliore l'expérience utilisateur en évitant les lag pendant la saisie
- Évite les requêtes inutiles pendant que l'utilisateur tape encore
- Standard recommandé pour les champs de recherche dans Livewire

**Délais recommandés** :
- Recherche : `debounce.500ms` (500 millisecondes)
- Filtres : `debounce.300ms` (300 millisecondes)
- Validation en temps réel : utiliser `wire:model` sans debounce

**Exceptions** :
- Les champs qui nécessitent une validation immédiate (ex: vérification de disponibilité d'email) doivent utiliser `wire:model` sans debounce
- Les champs de formulaire simples peuvent utiliser `wire:model.lazy` pour validation au blur

### Règle 7 : Utilisation de `#[Computed]` pour les propriétés calculées

**Date d'ajout** : 2025-01-27  
**Proposée par** : Morgan (Architect)  
**Validée par** : À valider

**Description** : Utiliser l'attribut `#[Computed]` pour les propriétés calculées qui nécessitent un calcul coûteux ou qui sont utilisées plusieurs fois dans le composant. Livewire met automatiquement en cache la valeur calculée pour la durée de la requête.

**Quand appliquer** :
- Pour les propriétés dérivées qui nécessitent un calcul (ex: concaténation, comptage, agrégation)
- Pour les propriétés qui sont utilisées plusieurs fois dans le composant ou la vue
- Pour les propriétés qui font des requêtes à la base de données
- Pour les propriétés qui effectuent des calculs coûteux

**Exemples** :

**Bon exemple** :
```php
use Livewire\Attributes\Computed;

class Dashboard extends Component
{
    public User $user;

    #[Computed]
    public function fullName(): string
    {
        return "{$this->user->first_name} {$this->user->last_name}";
    }

    #[Computed]
    public function planetCount(): int
    {
        return $this->user->planets()->count();
    }

    #[Computed]
    public function recentDiscoveries(): Collection
    {
        return $this->user->discoveries()
            ->latest()
            ->take(5)
            ->get();
    }
}
```

**Mauvais exemple** :
```php
class Dashboard extends Component
{
    public User $user;

    public function getFullNameProperty(): string
    {
        return "{$this->user->first_name} {$this->user->last_name}";
    }

    public function getPlanetCountProperty(): int
    {
        return $this->user->planets()->count();
    }
}
```

**Justification** : 
- Cache automatique : la valeur est calculée une seule fois par requête, même si utilisée plusieurs fois
- Performance améliorée pour les calculs coûteux (requêtes DB, agrégations)
- Syntaxe moderne et déclarative avec les attributs PHP 8
- Réduction de la charge serveur en évitant les recalculs inutiles
- Cohérence avec les standards Livewire 3.6

**Utilisation dans les vues** :
```blade
<div>
    <h1>{{ $this->fullName }}</h1>
    <p>Planets discovered: {{ $this->planetCount }}</p>
</div>
```

**Note** : Utiliser `$this->propertyName` dans les vues pour accéder aux propriétés calculées.

### Règle 8 : Séparation des responsabilités : logique métier dans les services, pas dans les composants Livewire

**Date d'ajout** : 2025-01-27  
**Proposée par** : Morgan (Architect)  
**Validée par** : À valider

**Description** : Les composants Livewire doivent être minces et se concentrer uniquement sur la gestion de l'état de l'interface et des interactions utilisateur. Toute la logique métier doit être déléguée aux services Laravel.

**Quand appliquer** :
- Lors de la création d'un nouveau composant Livewire
- Lors de la modification d'un composant Livewire existant
- Lors de la review de code de composants Livewire

**Exemples** :

**Bon exemple** :
```php
use App\Services\PlanetService;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $planet = null;
    public $loading = true;

    public function mount(PlanetService $planetService)
    {
        $this->loadPlanet($planetService);
    }

    public function loadPlanet(PlanetService $planetService)
    {
        $this->planet = $planetService->getHomePlanet(Auth::user());
        $this->loading = false;
    }
}
```

**Mauvais exemple** :
```php
class Dashboard extends Component
{
    public $planet = null;

    public function mount()
    {
        // ❌ Logique métier directement dans le composant
        $this->planet = Planet::where('user_id', Auth::id())
            ->with('resources')
            ->with('discoveries')
            ->with('explorations')
            ->first();
    }
}
```

**Justification** : 
- Séparation claire des responsabilités : présentation vs logique métier
- Réutilisabilité : la logique métier peut être réutilisée dans d'autres contextes (API, commandes, etc.)
- Testabilité : les services sont plus faciles à tester unitairement
- Maintenabilité : modifications de la logique métier sans toucher aux composants
- Cohérence avec l'architecture API-first du projet
- Les composants Livewire appellent directement les services (pas d'API interne)

**Structure recommandée** :
- **Composants Livewire** : Gèrent l'état de l'interface (`$loading`, `$error`, etc.) et les interactions utilisateur
- **Services** : Contiennent toute la logique métier (requêtes DB, calculs, validations métier)
- **Modèles** : Gèrent les relations Eloquent et les scopes

**Injection de dépendances** :
- Utiliser l'injection de dépendances dans les méthodes (`mount()`, méthodes publiques)
- Les services sont automatiquement résolus par le conteneur Laravel

---

## Format d'une Règle

Chaque règle doit suivre ce format :

```markdown
### Règle X : [Titre de la règle]

**Date d'ajout** : YYYY-MM-DD  
**Proposée par** : [Morgan | Sam]  
**Validée par** : [Nom du validateur]

**Description** : [Description de la règle]

**Exemples** :

**Bon exemple** :
```php
// Code conforme
```

**Mauvais exemple** :
```php
// Code non conforme
```

**Justification** : [Pourquoi cette règle est importante]
```

## Références

- [propose-technical-rule.md](../prompts/propose-technical-rule.md) : Guide pour proposer une nouvelle règle
- [HUMAN_VALIDATION.md](./HUMAN_VALIDATION.md) : Points de validation humaine
- [proposals/](./proposals/) : Propositions en attente de validation

