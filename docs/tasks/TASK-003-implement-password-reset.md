# TASK-003 : Implémenter la réinitialisation de mot de passe

## Issue Associée

[ISSUE-003-implement-password-reset.md](../issues/ISSUE-003-implement-password-reset.md)

## Vue d'Ensemble

Implémenter le système complet de réinitialisation de mot de passe pour permettre aux utilisateurs de récupérer leur compte en cas d'oubli. Le système utilise les fonctionnalités natives de Laravel (`Password::sendResetLink()` et `Password::reset()`) avec des composants Livewire pour la cohérence visuelle. L'implémentation inclut l'invalidation du Remember Me lors de la réinitialisation (sécurité) et des événements pour la traçabilité.

## Suivi et Historique

### Statut

✅ Terminée

### Historique

#### 2025-01-27 - Sam (Lead Dev) - Création du plan
**Statut** : À faire
**Détails** : Plan de développement créé pour l'implémentation de la réinitialisation de mot de passe. Le plan intègre toutes les recommandations architecturales (invalidation Remember Me, événements `PasswordResetRequested` et `PasswordResetCompleted`) et les recommandations design (style terminal, composants design system, indicateur de force du mot de passe). Le plan décompose l'issue en 8 phases avec 20 tâches au total.
**Fichiers modifiés** : docs/tasks/TASK-003-implement-password-reset.md
**Notes** : Estimation totale : ~12h de développement. Le plan est prêt pour la review architecturale par Morgan (Architect).

#### 2025-01-27 - Morgan (Architect) - Review architecturale
**Statut** : En cours
**Détails** : Review architecturale complète effectuée sur le plan de développement. Le plan est approuvé avec recommandations. Le plan intègre correctement toutes les recommandations architecturales de la review pré-planification et suit les patterns établis dans le projet.
**Résultat** : ✅ Approuvé avec recommandations
**Points positifs** :
- Intégration complète des recommandations architecturales
- Architecture événementielle bien pensée
- Sécurité bien couverte (rate limiting, invalidation Remember Me, tokens sécurisés)
- Tests complets prévus
- Documentation prévue
**Recommandations principales** :
- 🟡 Medium Priority : Clarifier l'invalidation des sessions web en plus du Remember Me
- 🟡 Medium Priority : Vérifier la configuration de sécurité des cookies
- 🟢 Low Priority : Clarifier la structure des données des événements dans les tâches
- 🟢 Low Priority : Préciser l'approche technique pour le rate limiting
**Fichiers modifiés** :
- `docs/reviews/ARCHITECT-REVIEW-003-TASK-password-reset.md` (nouveau)
- `docs/tasks/TASK-003-implement-password-reset.md` (mis à jour)
- `docs/issues/ISSUE-003-implement-password-reset.md` (mis à jour)
**Notes** : Le plan est prêt pour l'implémentation par Jordan (Fullstack Developer) en tenant compte des recommandations Medium priority.

#### 2025-01-27 - Jordan (Fullstack Dev) - Implémentation complète
**Statut** : ✅ Terminée
**Détails** : Toutes les phases du plan ont été implémentées avec succès. L'implémentation inclut :
- Tous les événements, services, Form Requests, contrôleurs et composants Livewire
- Templates d'emails avec style terminal
- Routes configurées avec rate limiting
- Tests complets (51 tests, 127 assertions) - tous passent
- Documentation ARCHITECTURE.md mise à jour
**Fichiers créés** : 24 nouveaux fichiers (événements, services, contrôleurs, composants Livewire, Mailables, templates, tests)
**Fichiers modifiés** : 4 fichiers (User.php, routes/web.php, login-terminal.blade.php, ARCHITECTURE.md)
**Commit** : `cd161c0` - feat: implement password reset functionality
**Tests** : ✅ 51 tests passent (127 assertions)
**Notes** : Toutes les recommandations architecturales ont été intégrées. L'implémentation est complète et prête pour la review fonctionnelle et visuelle.

#### 2025-01-27 - Sam (Lead Dev) - Review de l'implémentation
**Statut** : ✅ Approuvé avec modifications mineures
**Détails** : Review technique complète effectuée sur l'implémentation de la réinitialisation de mot de passe. Le code respecte le plan, suit les conventions Laravel, et intègre toutes les recommandations architecturales.
**Résultat** : ✅ Approuvé avec modifications mineures
**Points positifs** :
- Excellent respect du plan, toutes les tâches sont complétées
- Code propre et bien structuré, suit les conventions Laravel
- Tests complets et qui passent (51 tests, 127 assertions)
- Bonne utilisation de l'architecture événementielle
- Services bien encapsulés
- Sécurité bien couverte (invalidation Remember Me et sessions, rate limiting)
- Style terminal cohérent avec le reste de l'application
**Recommandations principales** :
- 🟢 Low Priority : Documenter que le rate limiting est par IP pour le MVP (acceptable)
- 🟢 Low Priority : Simplifier la gestion d'erreur dans ForgotPassword (optionnel)
- 🟢 Low Priority : Clarifier le nommage de `invalidateRememberMe()` (optionnel)
**Fichiers modifiés** :
- `docs/reviews/CODE-REVIEW-003-password-reset.md` (nouveau)
- `docs/tasks/TASK-003-implement-password-reset.md` (mis à jour)
- `docs/issues/ISSUE-003-implement-password-reset.md` (mis à jour)
**Review complète** : [CODE-REVIEW-003-password-reset.md](../reviews/CODE-REVIEW-003-password-reset.md)
**Notes** : Le code est approuvé techniquement et peut être mergé en production après review fonctionnelle et visuelle. Les améliorations suggérées sont optionnelles.

#### 2025-01-27 - Alex (Product) - Review fonctionnelle
**Statut** : ✅ Terminée - Review fonctionnelle approuvée
**Détails** : Review fonctionnelle complète effectuée sur l'implémentation de la réinitialisation de mot de passe. La fonctionnalité a été testée comme un utilisateur final avec Chrome DevTools MCP.
**Résultat** : ✅ Approuvé fonctionnellement
**Points positifs** :
- Tous les critères d'acceptation de l'issue sont respectés
- Expérience utilisateur fluide et agréable
- Style terminal cohérent sur toutes les pages (connexion, demande, réinitialisation)
- Messages clairs et informatifs en français avec format terminal
- Sécurité bien gérée (messages de sécurité, rate limiting, invalidation Remember Me)
- Email de réinitialisation bien formaté avec l'identité visuelle
- Navigation intuitive avec liens de retour bien positionnés
- Feedback visuel pendant le traitement (boutons avec [PROCESSING])
**Tests fonctionnels effectués** :
- Lien "Mot de passe oublié ?" présent et fonctionnel sur la page de connexion
- Formulaire de demande de réinitialisation fonctionnel avec message de sécurité
- Email de réinitialisation envoyé avec style terminal et lien fonctionnel
- Formulaire de réinitialisation affiché correctement avec token valide
- Validation du token invalide/expiré avec messages d'erreur clairs
**Suggestions d'amélioration** :
- 🟢 Low Priority : Vérifier que l'indicateur de force du mot de passe s'affiche correctement (optionnel)
**Fichiers modifiés** :
- `docs/reviews/FUNCTIONAL-REVIEW-003-password-reset.md` (nouveau)
- `docs/tasks/TASK-003-implement-password-reset.md` (mis à jour)
- `docs/issues/ISSUE-003-implement-password-reset.md` (mis à jour)
**Review complète** : [FUNCTIONAL-REVIEW-003-password-reset.md](../reviews/FUNCTIONAL-REVIEW-003-password-reset.md)
**Notes** : La fonctionnalité répond parfaitement aux besoins métier et peut être approuvée pour la production. Tous les critères d'acceptation sont respectés, l'expérience utilisateur est excellente, et la sécurité est bien gérée. Le plan est complet et toutes les phases ont été validées.

## Objectifs Techniques

- Implémenter les endpoints web pour la demande et la réinitialisation de mot de passe
- Créer les composants Livewire avec style terminal pour la cohérence visuelle
- Intégrer l'invalidation du Remember Me lors de la réinitialisation (sécurité)
- Ajouter des événements pour la traçabilité (`PasswordResetRequested`, `PasswordResetCompleted`)
- Implémenter le rate limiting pour éviter les abus
- Créer les templates d'emails avec l'identité visuelle du projet
- Ajouter un lien "Mot de passe oublié ?" sur la page de connexion

## Architecture & Design

- **Approche** : Utilisation des fonctionnalités natives Laravel (`Password::sendResetLink()`, `Password::reset()`)
- **Composants** : Livewire avec style terminal pour cohérence avec `LoginTerminal`
- **Design System** : Réutilisation des composants existants (`terminal-prompt`, `terminal-message`, `form-input`, `button`, `terminal-link`)
- **Sécurité** : Rate limiting (3/heure par email, 5/heure par IP), invalidation Remember Me, tokens expirables (60 min)
- **Architecture événementielle** : Événements `PasswordResetRequested` et `PasswordResetCompleted` pour traçabilité
- **Migration** : La table `password_reset_tokens` existe déjà dans la migration `0001_01_01_000000_create_users_table.php`

## Tâches de Développement

### Phase 1 : Événements et Services

#### Tâche 1.1 : Créer l'événement PasswordResetRequested
- **Description** : Créer l'événement qui sera dispatché lorsqu'un utilisateur demande une réinitialisation de mot de passe
- **Fichiers concernés** : `app/Events/PasswordResetRequested.php`
- **Estimation** : 30 min
- **Dépendances** : Aucune
- **Tests** : Tests unitaires de l'événement

#### Tâche 1.2 : Créer l'événement PasswordResetCompleted
- **Description** : Créer l'événement qui sera dispatché lorsqu'un utilisateur réinitialise son mot de passe avec succès
- **Fichiers concernés** : `app/Events/PasswordResetCompleted.php`
- **Estimation** : 30 min
- **Dépendances** : Aucune
- **Tests** : Tests unitaires de l'événement

#### Tâche 1.3 : Créer PasswordResetService (optionnel)
- **Description** : Créer un service pour encapsuler la logique de réinitialisation si nécessaire. Pour le MVP, l'utilisation directe de `Password::` peut être suffisante, mais créer le service pour la cohérence avec `AuthService` et faciliter l'ajout de logique métier future
- **Fichiers concernés** : `app/Services/PasswordResetService.php`
- **Estimation** : 1h
- **Dépendances** : Tâche 1.1, Tâche 1.2
- **Tests** : Tests unitaires du service

### Phase 2 : Form Requests et Validation

#### Tâche 2.1 : Créer ForgotPasswordRequest
- **Description** : Créer le FormRequest pour valider l'email de demande de réinitialisation
- **Fichiers concernés** : `app/Http/Requests/ForgotPasswordRequest.php`
- **Estimation** : 30 min
- **Dépendances** : Aucune
- **Tests** : Tests de validation

#### Tâche 2.2 : Créer ResetPasswordRequest
- **Description** : Créer le FormRequest pour valider le token, email, et nouveau mot de passe
- **Fichiers concernés** : `app/Http/Requests/ResetPasswordRequest.php`
- **Estimation** : 45 min
- **Dépendances** : Aucune
- **Tests** : Tests de validation (token, email, password, password_confirmation)

### Phase 3 : Contrôleurs Web

#### Tâche 3.1 : Créer PasswordResetController
- **Description** : Créer le contrôleur avec les méthodes `showForgotPasswordForm()`, `sendResetLink()`, `showResetForm()`, et `reset()`
- **Fichiers concernés** : `app/Http/Controllers/Auth/PasswordResetController.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 1.3, Tâche 2.1, Tâche 2.2
- **Tests** : Tests d'intégration des méthodes

#### Tâche 3.2 : Implémenter l'invalidation du Remember Me
- **Description** : Dans la méthode `reset()`, invalider tous les tokens Remember Me de l'utilisateur après réinitialisation réussie (sécurité)
- **Fichiers concernés** : `app/Http/Controllers/Auth/PasswordResetController.php`
- **Estimation** : 30 min
- **Dépendances** : Tâche 3.1
- **Tests** : Test que le Remember Me est invalidé après réinitialisation

### Phase 4 : Composants Livewire

#### Tâche 4.1 : Créer le composant ForgotPassword
- **Description** : Créer le composant Livewire pour le formulaire de demande de réinitialisation avec style terminal
- **Fichiers concernés** : `app/Livewire/ForgotPassword.php`, `resources/views/livewire/forgot-password.blade.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 3.1
- **Tests** : Tests Livewire du composant

#### Tâche 4.2 : Créer le composant ResetPassword
- **Description** : Créer le composant Livewire pour le formulaire de réinitialisation avec style terminal et indicateur de force du mot de passe
- **Fichiers concernés** : `app/Livewire/ResetPassword.php`, `resources/views/livewire/reset-password.blade.php`
- **Estimation** : 2h30
- **Dépendances** : Tâche 3.1
- **Tests** : Tests Livewire du composant

#### Tâche 4.3 : Ajouter le lien "Mot de passe oublié ?" sur LoginTerminal
- **Description** : Ajouter le lien vers `/forgot-password` sur la page de connexion avec le style terminal
- **Fichiers concernés** : `resources/views/livewire/login-terminal.blade.php`
- **Estimation** : 30 min
- **Dépendances** : Tâche 4.1
- **Tests** : Test visuel de la présence du lien

### Phase 5 : Emails

#### Tâche 5.1 : Créer ResetPasswordNotification (Mailable)
- **Description** : Créer le Mailable pour l'email de réinitialisation avec template cohérent avec l'identité visuelle
- **Fichiers concernés** : `app/Mail/ResetPasswordNotification.php`
- **Estimation** : 1h
- **Dépendances** : Aucune
- **Tests** : Tests du Mailable

#### Tâche 5.2 : Créer le template d'email reset-password
- **Description** : Créer le template Blade pour l'email de réinitialisation avec style terminal et identité visuelle
- **Fichiers concernés** : `resources/views/emails/auth/reset-password.blade.php`
- **Estimation** : 1h30
- **Dépendances** : Tâche 5.1
- **Tests** : Test visuel du template

#### Tâche 5.3 : Créer PasswordResetConfirmation (Mailable)
- **Description** : Créer le Mailable pour l'email de confirmation après réinitialisation réussie
- **Fichiers concernés** : `app/Mail/PasswordResetConfirmation.php`
- **Estimation** : 1h
- **Dépendances** : Aucune
- **Tests** : Tests du Mailable

#### Tâche 5.4 : Créer le template d'email password-reset-confirmation
- **Description** : Créer le template Blade pour l'email de confirmation avec style cohérent
- **Fichiers concernés** : `resources/views/emails/auth/password-reset-confirmation.blade.php`
- **Estimation** : 1h
- **Dépendances** : Tâche 5.3
- **Tests** : Test visuel du template

### Phase 6 : Routes et Rate Limiting

#### Tâche 6.1 : Ajouter les routes web
- **Description** : Ajouter les routes `/forgot-password` (GET, POST) et `/reset-password/{token}` (GET, POST) avec middleware `guest` et rate limiting
- **Fichiers concernés** : `routes/web.php`
- **Estimation** : 30 min
- **Dépendances** : Tâche 3.1, Tâche 4.1, Tâche 4.2
- **Tests** : Tests des routes

#### Tâche 6.2 : Configurer le rate limiting
- **Description** : Configurer le rate limiting dans `app/Providers/RouteServiceProvider.php` ou via middleware : 3 demandes/heure par email, 5 tentatives/heure par IP
- **Fichiers concernés** : `app/Providers/RouteServiceProvider.php` ou middleware personnalisé
- **Estimation** : 1h
- **Dépendances** : Tâche 6.1
- **Tests** : Tests du rate limiting

### Phase 7 : Tests

#### Tâche 7.1 : Tests unitaires des événements
- **Description** : Écrire les tests unitaires pour `PasswordResetRequested` et `PasswordResetCompleted`
- **Fichiers concernés** : `tests/Unit/Events/PasswordResetRequestedTest.php`, `tests/Unit/Events/PasswordResetCompletedTest.php`
- **Estimation** : 1h
- **Dépendances** : Tâche 1.1, Tâche 1.2
- **Tests** : Tests unitaires

#### Tâche 7.2 : Tests d'intégration du contrôleur
- **Description** : Écrire les tests d'intégration pour toutes les méthodes du `PasswordResetController`
- **Fichiers concernés** : `tests/Feature/Auth/PasswordResetTest.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 3.1, Tâche 3.2
- **Tests** : Tests d'intégration (envoi lien, validation token, réinitialisation, invalidation Remember Me)

#### Tâche 7.3 : Tests Livewire
- **Description** : Écrire les tests Livewire pour `ForgotPassword` et `ResetPassword`
- **Fichiers concernés** : `tests/Feature/Livewire/ForgotPasswordTest.php`, `tests/Feature/Livewire/ResetPasswordTest.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 4.1, Tâche 4.2
- **Tests** : Tests Livewire (validation, soumission, gestion erreurs)

#### Tâche 7.4 : Tests du rate limiting
- **Description** : Écrire les tests pour vérifier que le rate limiting fonctionne correctement
- **Fichiers concernés** : `tests/Feature/Auth/PasswordResetRateLimitTest.php`
- **Estimation** : 1h
- **Dépendances** : Tâche 6.2
- **Tests** : Tests du rate limiting

#### Tâche 7.5 : Tests des emails
- **Description** : Écrire les tests pour vérifier que les emails sont envoyés avec le bon contenu
- **Fichiers concernés** : `tests/Feature/Mail/PasswordResetMailTest.php`
- **Estimation** : 1h
- **Dépendances** : Tâche 5.1, Tâche 5.3
- **Tests** : Tests des emails (contenu, liens, destinataires)

### Phase 8 : Documentation

#### Tâche 8.1 : Mettre à jour ARCHITECTURE.md
- **Description** : Mettre à jour ARCHITECTURE.md avec les nouveaux endpoints (si API ajoutée), les événements dans la section "Architecture événementielle", et la configuration de réinitialisation dans la section "Authentification"
- **Fichiers concernés** : `docs/memory_bank/ARCHITECTURE.md`
- **Estimation** : 1h
- **Dépendances** : Toutes les phases précédentes
- **Tests** : Vérification de la documentation

## Ordre d'Exécution

1. Phase 1 : Événements et Services (Tâches 1.1, 1.2, 1.3)
2. Phase 2 : Form Requests et Validation (Tâches 2.1, 2.2)
3. Phase 3 : Contrôleurs Web (Tâches 3.1, 3.2)
4. Phase 4 : Composants Livewire (Tâches 4.1, 4.2, 4.3)
5. Phase 5 : Emails (Tâches 5.1, 5.2, 5.3, 5.4)
6. Phase 6 : Routes et Rate Limiting (Tâches 6.1, 6.2)
7. Phase 7 : Tests (Tâches 7.1, 7.2, 7.3, 7.4, 7.5)
8. Phase 8 : Documentation (Tâche 8.1)

## Migrations de Base de Données

- [x] Migration : La table `password_reset_tokens` existe déjà dans `0001_01_01_000000_create_users_table.php`

## Endpoints API

### Endpoints Web (MVP)

- `GET /forgot-password` - Formulaire de demande de réinitialisation (middleware `guest`)
- `POST /forgot-password` - Envoi du lien de réinitialisation (middleware `guest`, rate limit)
- `GET /reset-password/{token}` - Formulaire de réinitialisation (middleware `guest`)
- `POST /reset-password` - Réinitialisation du mot de passe (middleware `guest`, rate limit)

### Endpoints API (Optionnel pour MVP)

Les endpoints API peuvent être ajoutés dans une itération future si nécessaire :
- `POST /api/auth/forgot-password` - Envoi du lien (rate limit)
- `POST /api/auth/reset-password` - Réinitialisation (rate limit)

## Événements & Listeners

### Nouveaux Événements

- `PasswordResetRequested` : Déclenché lorsqu'un utilisateur demande une réinitialisation de mot de passe
  - Déclenché quand : Un utilisateur soumet le formulaire de demande de réinitialisation
  - Données : Email de l'utilisateur
  - Listeners : Aucun pour le moment (prévu pour : tracking, analytics, etc.)

- `PasswordResetCompleted` : Déclenché lorsqu'un utilisateur réinitialise son mot de passe avec succès
  - Déclenché quand : Un utilisateur réinitialise son mot de passe avec succès
  - Données : Utilisateur, timestamp
  - Listeners : Aucun pour le moment (prévu pour : notifications, analytics, invalidation sessions, etc.)

### Listeners

Aucun listener n'est prévu pour le MVP. Les listeners peuvent être ajoutés dans des itérations futures pour :
- Tracking et analytics
- Notifications additionnelles
- Invalidation de toutes les sessions (au-delà du Remember Me)
- Logs de sécurité

## Services & Classes

### Nouveaux Services

- `PasswordResetService` : Service pour encapsuler la logique de réinitialisation de mot de passe
  - Méthodes :
    - `sendResetLink(string $email): string` : Envoie le lien de réinitialisation et retourne le statut
    - `reset(array $credentials): string` : Réinitialise le mot de passe et retourne le statut
    - `invalidateRememberMe(User $user): void` : Invalide tous les tokens Remember Me de l'utilisateur

### Classes Modifiées

- `LoginTerminal` : Ajout du lien "Mot de passe oublié ?" dans la vue

## Tests

### Tests Unitaires

- [ ] Test : `PasswordResetRequested` est correctement dispatché avec les bonnes données
- [ ] Test : `PasswordResetCompleted` est correctement dispatché avec les bonnes données
- [ ] Test : `PasswordResetService::sendResetLink()` envoie le lien correctement
- [ ] Test : `PasswordResetService::reset()` réinitialise le mot de passe correctement
- [ ] Test : `PasswordResetService::invalidateRememberMe()` invalide les tokens Remember Me

### Tests d'Intégration

- [ ] Test : `POST /forgot-password` envoie le lien de réinitialisation
- [ ] Test : `POST /forgot-password` retourne toujours un message de succès (même si l'email n'existe pas)
- [ ] Test : `GET /reset-password/{token}` affiche le formulaire si le token est valide
- [ ] Test : `GET /reset-password/{token}` redirige si le token est invalide
- [ ] Test : `GET /reset-password/{token}` redirige si le token est expiré
- [ ] Test : `POST /reset-password` réinitialise le mot de passe avec succès
- [ ] Test : `POST /reset-password` invalide le Remember Me après réinitialisation
- [ ] Test : `POST /reset-password` envoie l'email de confirmation
- [ ] Test : Le rate limiting fonctionne pour les demandes (3/heure par email)
- [ ] Test : Le rate limiting fonctionne pour les tentatives (5/heure par IP)

### Tests Livewire

- [ ] Test : `ForgotPassword` valide correctement l'email
- [ ] Test : `ForgotPassword` envoie le lien de réinitialisation
- [ ] Test : `ForgotPassword` affiche les messages d'erreur correctement
- [ ] Test : `ResetPassword` valide correctement les champs
- [ ] Test : `ResetPassword` réinitialise le mot de passe avec succès
- [ ] Test : `ResetPassword` affiche l'indicateur de force du mot de passe
- [ ] Test : `ResetPassword` gère les erreurs de token invalide/expiré

### Tests des Emails

- [ ] Test : `ResetPasswordNotification` contient le bon lien de réinitialisation
- [ ] Test : `ResetPasswordNotification` contient les bonnes informations utilisateur
- [ ] Test : `PasswordResetConfirmation` est envoyé après réinitialisation réussie
- [ ] Test : Les templates d'emails sont correctement formatés

## Documentation

- [ ] Mettre à jour ARCHITECTURE.md avec les nouveaux événements dans la section "Architecture événementielle"
- [ ] Mettre à jour ARCHITECTURE.md avec la configuration de réinitialisation dans la section "Authentification"
- [ ] Ajouter des commentaires dans le code pour expliquer la logique métier
- [ ] Documenter l'invalidation du Remember Me dans ARCHITECTURE.md

## Notes Techniques

### Sécurité

- **Rate Limiting** : 
  - 3 demandes de réinitialisation par heure par email
  - 5 tentatives de réinitialisation par heure par IP
  - Utiliser le rate limiting Laravel natif

- **Invalidation Remember Me** : 
  - Implémenter dès le MVP pour la sécurité
  - Invalider tous les tokens Remember Me après réinitialisation réussie
  - Utiliser `DB::table('sessions')->where('user_id', $user->id)->delete()` pour invalider les sessions web si nécessaire

- **Tokens** :
  - Tokens expirables (60 minutes par défaut, configurable dans `config/auth.php`)
  - Tokens uniques et sécurisés (gérés automatiquement par Laravel)
  - Invalidation après utilisation

- **Non-révélation d'informations** :
  - Toujours retourner un message de succès même si l'email n'existe pas
  - Ne pas révéler si un email existe dans le système

### Design & UX

- **Style Terminal** : Utiliser le style terminal pour maintenir la cohérence avec `LoginTerminal`
- **Composants Design System** : Réutiliser les composants existants (`terminal-prompt`, `terminal-message`, `form-input`, `button`, `terminal-link`)
- **Messages de statut** : Utiliser le format terminal (`[SUCCESS]`, `[ERROR]`, `[INFO]`, `[PROCESSING]`)
- **Indicateur de force du mot de passe** : Implémenter dès le MVP pour améliorer l'UX et la sécurité
- **Animations** : Utiliser les animations existantes du design system (`wireLoading`, transitions CSS)

### Configuration Mail

- Vérifier que `MAIL_FROM_ADDRESS` et `MAIL_FROM_NAME` sont configurés dans `.env`
- En développement, utiliser le driver `log` pour voir les emails dans `storage/logs/laravel.log`
- En production, utiliser un service d'envoi d'emails fiable (Mailgun, SendGrid, AWS SES, etc.)

### Intégration avec l'Existant

- Suivre le même pattern que `AuthService` pour la cohérence du code
- Utiliser les mêmes patterns de validation que les autres formulaires (Form Requests)
- Suivre le même style visuel que `LoginTerminal` pour la cohérence UX
- Les composants Livewire doivent utiliser les mêmes conventions de nommage et structure

## Références

- [ISSUE-003-implement-password-reset.md](../issues/ISSUE-003-implement-password-reset.md) - Issue produit
- [ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md) - Architecture événementielle, Authentification, Remember Me
- [ARCHITECT-REVIEW-003-password-reset.md](../reviews/ARCHITECT-REVIEW-003-password-reset.md) - Review architecturale avec recommandations
- [VISUAL-REVIEW-003-password-reset.md](../reviews/VISUAL-REVIEW-003-password-reset.md) - Review visuelle avec recommandations design
- [STACK.md](../memory_bank/STACK.md) - Stack technique
- [Laravel Password Reset Documentation](https://laravel.com/docs/authentication#password-reset) - Documentation Laravel officielle

