# Technical Rules - Space Xplorer

Ce document contient les règles techniques validées pour le projet Space Xplorer. Ces règles améliorent la qualité du code et standardisent les bonnes pratiques de l'équipe.

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

