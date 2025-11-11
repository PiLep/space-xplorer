# TASK-002 : Implémenter la persistence de connexion (Remember Me)

## Issue Associée

[ISSUE-002-implement-remember-me.md](../issues/ISSUE-002-implement-remember-me.md)

## Vue d'Ensemble

Implémenter la fonctionnalité "Remember Me" pour permettre aux utilisateurs de rester connectés même après la fermeture du navigateur. Cette fonctionnalité améliore l'expérience utilisateur en réduisant la friction de reconnexion. L'implémentation doit fonctionner pour les connexions via Livewire (routes web) et documenter le comportement pour les connexions via API (tokens Sanctum).

## Suivi et Historique

### Statut

À faire

### Historique

#### 2025-01-XX - Sam (Lead Dev) - Création du plan
**Statut** : À faire
**Détails** : Plan de développement créé pour implémenter la fonctionnalité "Remember Me". Le plan décompose l'issue en 3 phases avec 8 tâches au total.
**Fichiers modifiés** : docs/tasks/TASK-002-implement-remember-me.md
**Notes** : Estimation totale : ~4h de développement. Le champ `remember_token` existe déjà dans la migration users.

## Objectifs Techniques

- Ajouter une checkbox "Se souvenir de moi" sur le formulaire de connexion (Livewire et API)
- Implémenter la logique "Remember Me" dans `AuthService::login()` et `AuthService::loginFromCredentials()`
- Utiliser `Auth::login($user, $remember)` avec le paramètre `$remember` basé sur la checkbox
- Vérifier que le cookie de session persiste au-delà de la fermeture du navigateur quand "Remember Me" est coché
- Documenter le comportement pour les connexions API (tokens Sanctum)
- Tester que la déconnexion invalide bien le cookie "Remember Me"

## Architecture & Design

- **Service AuthService** : Modifier les méthodes `login()` et `loginFromCredentials()` pour accepter un paramètre `$remember` (booléen)
- **Form Request** : Ajouter le champ `remember` (optionnel, booléen) dans `LoginRequest`
- **Livewire Component** : Ajouter une checkbox dans `LoginTerminal.php` pour "Se souvenir de moi"
- **API Controller** : Passer le paramètre `remember` à `AuthService::login()` depuis `AuthController`
- **Vue Livewire** : Ajouter la checkbox dans le formulaire de connexion
- **Configuration** : Vérifier la configuration de session dans `config/session.php` (déjà configurée par Laravel)

## Tâches de Développement

### Phase 1 : Backend - Service et Validation

#### Tâche 1.1 : Modifier LoginRequest pour accepter le champ remember
- **Description** : Ajouter la validation du champ `remember` (optionnel, booléen) dans `LoginRequest`
- **Fichiers concernés** : `app/Http/Requests/LoginRequest.php`
- **Estimation** : 15 min
- **Dépendances** : Aucune
- **Tests** : Tests de validation du champ remember

#### Tâche 1.2 : Modifier AuthService::login() pour accepter le paramètre remember
- **Description** : Modifier la méthode `login()` pour accepter un paramètre `$remember` (booléen, par défaut false) et l'utiliser dans `Auth::login($user, $remember)`
- **Fichiers concernés** : `app/Services/AuthService.php`
- **Estimation** : 20 min
- **Dépendances** : Tâche 1.1
- **Tests** : Tests unitaires pour vérifier que le paramètre remember est bien passé à Auth::login()

#### Tâche 1.3 : Modifier AuthService::loginFromCredentials() pour accepter le paramètre remember
- **Description** : Modifier la méthode `loginFromCredentials()` pour accepter un paramètre `$remember` (booléen, par défaut false) et l'utiliser dans `Auth::login($user, $remember)`
- **Fichiers concernés** : `app/Services/AuthService.php`
- **Estimation** : 20 min
- **Dépendances** : Tâche 1.2
- **Tests** : Tests unitaires pour vérifier que le paramètre remember est bien passé à Auth::login()

### Phase 2 : Frontend - Composants Livewire

#### Tâche 2.1 : Ajouter la propriété remember dans LoginTerminal
- **Description** : Ajouter une propriété publique `$remember = false` dans le composant `LoginTerminal.php` et l'ajouter aux règles de validation
- **Fichiers concernés** : `app/Livewire/LoginTerminal.php`
- **Estimation** : 15 min
- **Dépendances** : Aucune
- **Tests** : Vérifier que la propriété est bien définie

#### Tâche 2.2 : Modifier la méthode login() de LoginTerminal pour passer remember
- **Description** : Modifier la méthode `login()` pour passer le paramètre `$this->remember` à `AuthService::loginFromCredentials()`
- **Fichiers concernés** : `app/Livewire/LoginTerminal.php`
- **Estimation** : 15 min
- **Dépendances** : Tâche 1.3, Tâche 2.1
- **Tests** : Tests fonctionnels pour vérifier que le paramètre est bien passé

#### Tâche 2.3 : Ajouter la checkbox dans la vue login-terminal.blade.php
- **Description** : Ajouter une checkbox "Se souvenir de moi" dans le formulaire de connexion, positionnée entre le champ password et le bouton de soumission
- **Fichiers concernés** : `resources/views/livewire/login-terminal.blade.php`
- **Estimation** : 30 min
- **Dépendances** : Tâche 2.1
- **Tests** : Vérifier visuellement que la checkbox s'affiche correctement

### Phase 3 : API et Tests

#### Tâche 3.1 : Modifier AuthController::login() pour passer remember
- **Description** : Modifier la méthode `login()` dans `AuthController` pour récupérer le champ `remember` de la requête et le passer à `AuthService::login()`
- **Fichiers concernés** : `app/Http/Controllers/Api/AuthController.php`
- **Estimation** : 20 min
- **Dépendances** : Tâche 1.2
- **Tests** : Tests d'intégration pour vérifier que le paramètre est bien passé

#### Tâche 3.2 : Écrire les tests pour Remember Me
- **Description** : Écrire des tests pour vérifier que :
  - Le cookie "Remember Me" persiste après la fermeture du navigateur (simulation)
  - La déconnexion invalide bien le cookie "Remember Me"
  - Le champ remember est bien validé dans LoginRequest
  - Les méthodes AuthService passent bien le paramètre remember
- **Fichiers concernés** : 
  - `tests/Feature/Api/AuthControllerTest.php`
  - `tests/Feature/Livewire/LoginTerminalTest.php` (si existe)
  - `tests/Unit/Services/AuthServiceTest.php` (si existe)
- **Estimation** : 1h30
- **Dépendances** : Tâches 1.1, 1.2, 1.3, 2.2, 3.1
- **Tests** : Tests unitaires et d'intégration complets

## Ordre d'Exécution

1. Phase 1 : Backend - Service et Validation (Tâches 1.1, 1.2, 1.3)
2. Phase 2 : Frontend - Composants Livewire (Tâches 2.1, 2.2, 2.3)
3. Phase 3 : API et Tests (Tâches 3.1, 3.2)

## Migrations de Base de Données

- [x] Migration : Le champ `remember_token` existe déjà dans la table users (migration `0001_01_01_000000_create_users_table.php`)

## Endpoints API

### Endpoints Modifiés

- `POST /api/auth/login` - Ajout du champ optionnel `remember` (booléen)
  - Request body : 
    ```json
    {
      "email": "string",
      "password": "string",
      "remember": true  // optionnel, booléen
    }
    ```
  - Response : Inchangée (même format qu'avant)
  - Validation : `remember` => `sometimes|boolean`
  - Note : Pour l'API avec Sanctum, les tokens ont déjà une durée de vie longue. Le paramètre `remember` affecte principalement la session web si utilisée, mais la documentation doit clarifier que pour les clients API externes, les tokens Sanctum sont déjà persistants.

## Événements & Listeners

Aucun nouvel événement ou listener nécessaire. Les événements existants (`UserLoggedIn`) continuent de fonctionner normalement.

## Services & Classes

### Classes Modifiées

- `AuthService` : 
  - Méthode `login()` : Ajout du paramètre `$remember` (booléen, par défaut false)
  - Méthode `loginFromCredentials()` : Ajout du paramètre `$remember` (booléen, par défaut false)
  
- `LoginRequest` : 
  - Ajout de la règle de validation pour le champ `remember` (optionnel, booléen)
  
- `LoginTerminal` (Livewire) : 
  - Ajout de la propriété `$remember`
  - Modification de la méthode `login()` pour passer le paramètre
  
- `AuthController` : 
  - Modification de la méthode `login()` pour récupérer et passer le paramètre `remember`

## Tests

### Tests Unitaires

- [ ] Test : LoginRequest valide le champ remember (optionnel, booléen)
- [ ] Test : AuthService::login() passe bien le paramètre remember à Auth::login()
- [ ] Test : AuthService::loginFromCredentials() passe bien le paramètre remember à Auth::login()
- [ ] Test : AuthService::login() utilise false par défaut si remember n'est pas fourni

### Tests d'Intégration

- [ ] Test : POST /api/auth/login avec remember=true crée un cookie Remember Me
- [ ] Test : POST /api/auth/login avec remember=false ne crée pas de cookie Remember Me
- [ ] Test : POST /api/auth/login sans remember utilise false par défaut
- [ ] Test : La déconnexion invalide bien le cookie Remember Me

### Tests Fonctionnels

- [ ] Test : Connexion via Livewire avec remember=true persiste après fermeture du navigateur (simulation)
- [ ] Test : Connexion via Livewire avec remember=false expire à la fermeture du navigateur
- [ ] Test : La checkbox "Se souvenir de moi" fonctionne correctement dans le formulaire Livewire

## Documentation

- [ ] Documenter le comportement "Remember Me" pour l'API dans ARCHITECTURE.md
- [ ] Ajouter des commentaires dans le code expliquant le fonctionnement
- [ ] Documenter la durée de vie du cookie Remember Me (30 jours par défaut dans Laravel)

## Notes Techniques

- **Laravel Remember Me** : Laravel gère automatiquement la génération et la validation du token "Remember Me" via le champ `remember_token` dans la table users
- **Durée de vie** : Par défaut, Laravel utilise une durée de vie plus longue pour les cookies "Remember Me" (généralement 30 jours) comparé aux sessions normales (120 minutes)
- **Sécurité** : Le cookie "Remember Me" doit être sécurisé (httpOnly, secure en production) - vérifier `config/session.php`
- **API Sanctum** : Pour les clients API externes utilisant Sanctum, les tokens ont déjà une durée de vie longue. Le paramètre `remember` affecte principalement la session web si utilisée. Documenter ce comportement.
- **Configuration** : Vérifier que `config/session.php` a les bonnes configurations de sécurité (httpOnly, secure, sameSite)
- **Tests** : Pour tester la persistence après fermeture du navigateur, simuler en vérifiant que le cookie a la bonne expiration

## Références

- [ISSUE-002-implement-remember-me.md](../issues/ISSUE-002-implement-remember-me.md)
- [ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md) - Authentification et sessions
- [STACK.md](../memory_bank/STACK.md) - Stack technique
- [Laravel Authentication - Remember Me](https://laravel.com/docs/authentication#remembering-users)

