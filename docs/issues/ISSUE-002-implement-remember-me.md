# ISSUE-002 : Implémenter la persistence de connexion (Remember Me)

## Type
Feature

## Priorité
High

## Description

Implémenter la fonctionnalité "Remember Me" pour permettre aux utilisateurs de rester connectés même après la fermeture du navigateur. Cette fonctionnalité améliore grandement l'expérience utilisateur en réduisant la friction de reconnexion.

## Contexte Métier

**Problème actuel** :
- Les utilisateurs doivent se reconnecter à chaque fois qu'ils ferment leur navigateur
- Les sessions expirent après 120 minutes d'inactivité
- Cela crée une friction inutile pour les utilisateurs réguliers

**Valeur utilisateur** :
- Réduit la friction de reconnexion
- Améliore l'engagement des utilisateurs
- Permet une expérience plus fluide et agréable
- Les utilisateurs peuvent revenir facilement sur le jeu sans avoir à se reconnecter

**Impact** :
- Augmente la probabilité que les utilisateurs reviennent sur le jeu
- Réduit l'abandon lors de la reconnexion
- Améliore la satisfaction globale de l'expérience utilisateur

## Critères d'Acceptation

- [ ] Ajouter une checkbox "Se souvenir de moi" sur le formulaire de connexion (Livewire et API)
- [ ] Implémenter la logique "Remember Me" dans `AuthService::login()` et `AuthService::loginFromCredentials()`
- [ ] Utiliser `Auth::login($user, $remember)` avec le paramètre `$remember` basé sur la checkbox
- [ ] Le cookie de session doit persister au-delà de la fermeture du navigateur quand "Remember Me" est coché
- [ ] La durée de vie du cookie "Remember Me" doit être configurable (par défaut 30 jours)
- [ ] Fonctionner pour les connexions via Livewire (routes web)
- [ ] Fonctionner pour les connexions via API (tokens Sanctum - le token persiste déjà, mais documenter le comportement)
- [ ] Tester que la déconnexion invalide bien le cookie "Remember Me"
- [ ] Tester que le changement de mot de passe invalide les sessions "Remember Me" (à faire dans une issue future)

## Détails Techniques

### Backend

**Service AuthService** :
- Modifier `login()` et `loginFromCredentials()` pour accepter un paramètre `$remember` (booléen)
- Utiliser `Auth::login($user, $remember)` au lieu de `Auth::login($user)`
- Le champ `remember_token` existe déjà dans la table `users` (migration déjà créée)

**Form Requests** :
- Ajouter le champ `remember` (optionnel, booléen) dans `LoginRequest`
- Validation : `'remember' => 'sometimes|boolean'`

**Livewire Components** :
- Ajouter une checkbox dans `LoginTerminal.php` pour "Se souvenir de moi"
- Passer la valeur à `AuthService::loginFromCredentials()`

**API** :
- Ajouter le champ `remember` (optionnel) dans la requête de login API
- Documenter que pour l'API, les tokens Sanctum ont déjà une durée de vie longue, mais le comportement de session peut être différent

### Frontend

**Formulaire de connexion** :
- Ajouter une checkbox avec le label "Se souvenir de moi" ou "Remember me"
- Positionner la checkbox de manière intuitive (sous le champ mot de passe, avant le bouton de connexion)

**UX** :
- La checkbox doit être claire et facilement accessible
- Le texte doit être explicite sur ce que fait cette option

### Configuration

**Session** :
- Vérifier la configuration dans `config/session.php`
- La durée de vie du cookie "Remember Me" est gérée par Laravel automatiquement
- Par défaut, Laravel utilise `SESSION_LIFETIME` pour les sessions normales et une durée plus longue pour "Remember Me"

**Sécurité** :
- Le cookie "Remember Me" doit être sécurisé (httpOnly, secure en production)
- Vérifier que les paramètres de sécurité sont corrects dans `config/session.php`

## Notes

- Le champ `remember_token` existe déjà dans la migration `create_users_table.php`
- Laravel gère automatiquement la génération et la validation du token "Remember Me"
- Cette fonctionnalité est essentielle pour l'expérience utilisateur et doit être implémentée tôt
- Pour l'API avec Sanctum, les tokens ont déjà une durée de vie longue, mais cette fonctionnalité améliore l'expérience pour les utilisateurs Livewire

## Références

- [ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md) - Authentification et sessions
- [PROJECT_BRIEF.md](../memory_bank/PROJECT_BRIEF.md) - Parcours utilisateur
- [Laravel Authentication - Remember Me](https://laravel.com/docs/authentication#remembering-users)

## Suivi et Historique

### Statut

À faire

### GitHub

- **Issue GitHub** : [#4](https://github.com/PiLep/space-xplorer/issues/4)
- **Branche** : `issue/002-remember-me`

### Historique

#### 2025-01-XX - Alex (Product) - Création de l'issue
**Statut** : À faire
**Détails** : Issue créée pour améliorer l'expérience utilisateur avec la persistence de connexion
**Notes** : Priorité haute car impact direct sur l'engagement utilisateur
**GitHub** : Issue créée sur GitHub (#4) et branche dédiée créée (`issue/002-remember-me`)

