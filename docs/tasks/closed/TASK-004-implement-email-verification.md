# TASK-004 : Implémenter la vérification d'email par code

## Issue Associée

[ISSUE-004-implement-email-verification.md](../issues/ISSUE-004-implement-email-verification.md)

## Vue d'Ensemble

Implémenter un système de vérification d'email par code à 6 chiffres pour valider que les utilisateurs possèdent bien l'adresse email qu'ils ont fournie lors de l'inscription. Après l'inscription, l'utilisateur est redirigé vers une page de vérification qui attend un code reçu par email. Si un utilisateur avec un email non vérifié tente de se connecter, il est également redirigé vers cette page de vérification. Le code doit être stocké de manière sécurisée (hashé), expirer après 15 minutes, et respecter des limitations de tentatives et de renvois.

## Suivi et Historique

### Statut

✅ Review fonctionnelle approuvée - PR créée

### Historique

#### Sam (Lead Developer) - Création du plan
**Statut** : À faire
**Détails** : Plan de développement créé pour implémenter la vérification d'email par code à 6 chiffres
**Notes** : Le plan décompose la fonctionnalité en phases logiques avec toutes les tâches nécessaires

#### 2025-11-11 - Sam (Lead Developer) - Review du code implémenté
**Statut** : En review
**Détails** : 
- Review de code complète effectuée : `CODE-REVIEW-004-email-verification.md`
- Code approuvé avec modifications mineures
- Toutes les fonctionnalités principales implémentées correctement
- 37 tests écrits et tous passent (18 unitaires, 7 intégration, 12 fonctionnels)
- Améliorations suggérées : documentation ARCHITECTURE.md, constantes dans User model, PHPDoc supplémentaires
**Notes** : Code prêt pour validation fonctionnelle par Alex (Product Manager). Les améliorations suggérées sont optionnelles et peuvent être faites dans une prochaine itération.

#### 2025-11-11 - Sam (Lead Developer) - Création de la Pull Request
**Statut** : PR créée
**Détails** : 
- Pull Request créée : [#11](https://github.com/PiLep/space-xplorer/pull/11)
- Branche : `feature/ISSUE-004-implement-email-verification` → `develop`
- Tous les fichiers committés et poussés
- Code formaté avec Pint
- Tous les tests passent (37 tests)
- Documentation ARCHITECTURE.md mise à jour
- PR prête pour review et merge
**Notes** : PR créée selon le format standardisé. Prête pour validation fonctionnelle et merge dans develop.

#### 2025-11-11 - Alex (Product Manager) - Review fonctionnelle
**Statut** : ✅ Review fonctionnelle approuvée
**Détails** : 
- Review fonctionnelle complète effectuée : `FUNCTIONAL-REVIEW-004-email-verification.md`
- Tous les critères d'acceptation de l'issue sont respectés
- Flux utilisateur fluide et intuitif
- Sécurité bien implémentée (codes hashés, expiration, limitations)
- UX excellente (formatage automatique, vérification automatique, feedback visuel)
- Design cohérent avec le style terminal
- Emails bien conçus
- Tests complets (37 tests, tous passent)
**Résultat** : ✅ Approuvé fonctionnellement
**Fichiers modifiés** :
- `docs/reviews/FUNCTIONAL-REVIEW-004-email-verification.md` (nouveau)
- `docs/tasks/TASK-004-implement-email-verification.md` (mis à jour)
**Review complète** : [FUNCTIONAL-REVIEW-004-email-verification.md](../reviews/FUNCTIONAL-REVIEW-004-email-verification.md)
**Notes** : La fonctionnalité peut être mergée en production. Aucun ajustement fonctionnel nécessaire.

## Objectifs Techniques

- Créer un service `EmailVerificationService` pour gérer la génération, validation et renvoi de codes
- Ajouter les champs nécessaires dans la table `users` pour stocker les codes de vérification (hashés) et leurs dates d'expiration
- Créer un composant Livewire `VerifyEmail` pour la page de vérification avec style terminal
- Créer un Mailable `EmailVerificationNotification` pour envoyer les codes par email
- Modifier `AuthService` pour envoyer le code après inscription et vérifier l'email lors de la connexion
- Modifier les composants Livewire `Register` et `LoginTerminal` pour rediriger vers la vérification
- Implémenter les limitations de sécurité (tentatives, renvois, expiration)
- Créer les routes web nécessaires pour la vérification

## Architecture & Design

**Approche** : Système de vérification par code à 6 chiffres au lieu d'un lien de vérification pour une meilleure UX et sécurité.

**Stockage des codes** : 
- Option 1 : Ajouter des colonnes dans la table `users` (`email_verification_code`, `email_verification_code_expires_at`, `email_verification_attempts`, `email_verification_code_sent_at`)
- Option 2 : Créer une table séparée `email_verification_codes` (plus flexible mais plus complexe)
- **Choix** : Option 1 pour la simplicité (un seul code actif par utilisateur)

**Sécurité** :
- Codes générés de manière cryptographiquement sécurisée (6 chiffres aléatoires)
- Codes hashés avant stockage (utiliser `Hash::make()`)
- Expiration après 15 minutes
- Limitation à 5 tentatives de vérification par code
- Limitation à 1 renvoi toutes les 2 minutes
- Vérification que l'utilisateur correspond bien au code

**Flux** :
1. Inscription → Génération et envoi du code → Redirection vers `/email/verify`
2. Connexion avec email non vérifié → Redirection vers `/email/verify`
3. Page de vérification → Saisie du code → Validation → Marquer email comme vérifié → Redirection vers dashboard
4. Possibilité de renvoyer le code avec limitation de fréquence

## Tâches de Développement

### Phase 1 : Base de données et Modèle

#### Tâche 1.1 : Créer la migration pour ajouter les champs de vérification d'email
- **Description** : Ajouter les colonnes nécessaires dans la table `users` pour stocker le code hashé, la date d'expiration, le nombre de tentatives, et la date d'envoi du code
- **Fichiers concernés** : `database/migrations/YYYY_MM_DD_add_email_verification_fields_to_users_table.php`
- **Estimation** : 30 min
- **Dépendances** : Aucune
- **Tests** : Vérifier la structure de la table après migration

**Colonnes à ajouter** :
- `email_verification_code` : `string|null` - Code hashé (nullable car pas toujours présent)
- `email_verification_code_expires_at` : `timestamp|null` - Date d'expiration du code
- `email_verification_attempts` : `integer` - Nombre de tentatives (défaut: 0)
- `email_verification_code_sent_at` : `timestamp|null` - Date d'envoi du dernier code (pour limitation de renvoi)

#### Tâche 1.2 : Mettre à jour le modèle User
- **Description** : Ajouter les nouveaux champs dans `$fillable` et créer des méthodes helper pour la vérification d'email
- **Fichiers concernés** : `app/Models/User.php`
- **Estimation** : 30 min
- **Dépendances** : Tâche 1.1
- **Tests** : Tests unitaires des méthodes helper

**Méthodes à ajouter** :
- `hasVerifiedEmail()` : Vérifier si `email_verified_at` est défini
- `hasPendingVerificationCode()` : Vérifier si un code est en attente et non expiré
- `canResendVerificationCode()` : Vérifier si le renvoi est autorisé (2 minutes écoulées)
- `hasExceededVerificationAttempts()` : Vérifier si les tentatives max sont atteintes (5)

### Phase 2 : Service de Vérification d'Email

#### Tâche 2.1 : Créer EmailVerificationService
- **Description** : Créer le service avec les méthodes pour générer, valider, renvoyer et vérifier les codes
- **Fichiers concernés** : `app/Services/EmailVerificationService.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 1.2
- **Tests** : Tests unitaires complets du service

**Méthodes à implémenter** :
- `generateCode(User $user): string` : Génère un code à 6 chiffres, le hash, le stocke avec expiration (15 min), réinitialise les tentatives, envoie l'email, retourne le code en clair (pour tests)
- `verifyCode(User $user, string $code): bool` : Vérifie le code, incrémente les tentatives, marque l'email comme vérifié si correct, retourne true/false
- `resendCode(User $user): void` : Vérifie les limitations, génère et envoie un nouveau code
- `isCodeValid(User $user, string $code): bool` : Vérifie si le code est valide et non expiré (sans incrémenter les tentatives)
- `clearVerificationCode(User $user): void` : Nettoie le code après vérification réussie ou expiration

**Sécurité** :
- Utiliser `random_int(100000, 999999)` pour générer le code (cryptographiquement sécurisé)
- Utiliser `Hash::check($code, $user->email_verification_code)` pour vérifier
- Utiliser `Hash::make($code)` pour stocker
- Vérifier l'expiration avant validation
- Vérifier le nombre de tentatives avant validation

#### Tâche 2.2 : Créer EmailVerificationNotification (Mailable)
- **Description** : Créer le Mailable pour envoyer le code de vérification par email
- **Fichiers concernés** : `app/Mail/EmailVerificationNotification.php`
- **Estimation** : 1h
- **Dépendances** : Aucune
- **Tests** : Tests du Mailable et de l'envoi d'email

**Caractéristiques** :
- Sujet : "Vérifiez votre adresse email - {App Name}"
- Template : `emails.auth.verify-email` (HTML) et `emails.auth.verify-email-text` (texte)
- Afficher le code à 6 chiffres de manière claire et lisible
- Design cohérent avec les autres emails (utiliser `EmailService` pour les headers)
- Inclure des instructions claires

#### Tâche 2.3 : Créer les templates d'email
- **Description** : Créer les vues Blade pour les emails de vérification (HTML et texte)
- **Fichiers concernés** : 
  - `resources/views/emails/auth/verify-email.blade.php`
  - `resources/views/emails/auth/verify-email-text.blade.php`
- **Estimation** : 1h
- **Dépendances** : Tâche 2.2
- **Tests** : Vérifier le rendu des emails

**Contenu** :
- Message d'accueil expliquant la vérification
- Code à 6 chiffres affiché de manière proéminente
- Instructions sur où saisir le code
- Lien vers la page de vérification (optionnel, pour référence)
- Design cohérent avec les autres emails du système

### Phase 3 : Composant Livewire de Vérification

#### Tâche 3.1 : Créer le composant Livewire VerifyEmail
- **Description** : Créer le composant avec les propriétés et méthodes nécessaires pour la vérification
- **Fichiers concernés** : `app/Livewire/VerifyEmail.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 2.1
- **Tests** : Tests du composant Livewire

**Propriétés** :
- `$code` : Code saisi par l'utilisateur (6 chiffres)
- `$status` : Message de statut (style terminal)
- `$attemptsRemaining` : Tentatives restantes
- `$canResend` : Booléen pour savoir si le renvoi est possible
- `$resendCooldown` : Temps restant avant de pouvoir renvoyer (en secondes)

**Méthodes** :
- `mount()` : Vérifier si l'utilisateur est authentifié, si l'email est déjà vérifié (rediriger), charger l'état initial
- `verify()` : Valider le code via `EmailVerificationService`, gérer les erreurs, rediriger vers dashboard si succès
- `resend()` : Renvoyer le code via `EmailVerificationService`, gérer les limitations, afficher les messages
- `updatedCode()` : Validation en temps réel du format (6 chiffres uniquement)
- `getAttemptsRemainingProperty()` : Calculer les tentatives restantes
- `getCanResendProperty()` : Vérifier si le renvoi est possible
- `getResendCooldownProperty()` : Calculer le temps restant avant renvoi

**Validation** :
- `code` : `required|string|size:6|regex:/^[0-9]+$/`

#### Tâche 3.2 : Créer la vue Blade pour VerifyEmail
- **Description** : Créer la vue avec style terminal cohérent avec le reste de l'application
- **Fichiers concernés** : `resources/views/livewire/verify-email.blade.php`
- **Estimation** : 1h30
- **Dépendances** : Tâche 3.1
- **Tests** : Vérifier le rendu et l'UX

**Éléments à inclure** :
- Message d'information expliquant qu'un code a été envoyé par email
- Champ de saisie pour le code à 6 chiffres (avec formatage automatique si possible)
- Bouton "Vérifier" pour soumettre le code
- Bouton "Renvoyer le code" avec indication du cooldown si applicable
- Affichage des tentatives restantes
- Messages d'erreur clairs (code incorrect, expiré, tentatives dépassées, etc.)
- Messages de succès
- Design cohérent avec le style terminal (comme LoginTerminal)

### Phase 4 : Intégration avec AuthService et Composants

#### Tâche 4.1 : Modifier AuthService pour envoyer le code après inscription
- **Description** : Intégrer l'envoi du code de vérification dans `register()` et `registerFromArray()`
- **Fichiers concernés** : `app/Services/AuthService.php`
- **Estimation** : 30 min
- **Dépendances** : Tâche 2.1
- **Tests** : Tests d'intégration de l'inscription avec envoi de code

**Modifications** :
- Après création de l'utilisateur, appeler `EmailVerificationService::generateCode($user)`
- Ne pas marquer l'email comme vérifié automatiquement
- L'utilisateur reste authentifié mais doit vérifier son email

#### Tâche 4.2 : Modifier AuthService pour vérifier l'email lors de la connexion
- **Description** : Vérifier si l'email est vérifié dans `login()` et `loginFromCredentials()`, mais ne pas bloquer la connexion (redirection gérée par les composants)
- **Fichiers concernés** : `app/Services/AuthService.php`
- **Estimation** : 30 min
- **Dépendances** : Tâche 1.2
- **Tests** : Tests de connexion avec email non vérifié

**Modifications** :
- Ajouter une méthode `isEmailVerified(User $user): bool` pour vérifier l'état
- Les composants Livewire utiliseront cette méthode pour décider de la redirection

#### Tâche 4.3 : Modifier le composant Register pour rediriger vers la vérification
- **Description** : Après inscription réussie, rediriger vers `/email/verify` au lieu de `/dashboard`
- **Fichiers concernés** : `app/Livewire/Register.php`
- **Estimation** : 15 min
- **Dépendances** : Tâche 4.1
- **Tests** : Tests fonctionnels de l'inscription avec redirection

**Modifications** :
- Changer la redirection de `route('dashboard')` vers `route('email.verify')`
- Adapter le message de succès si nécessaire

#### Tâche 4.4 : Modifier le composant LoginTerminal pour vérifier l'email et rediriger
- **Description** : Après connexion réussie, vérifier si l'email est vérifié et rediriger vers `/email/verify` si non vérifié
- **Fichiers concernés** : `app/Livewire/LoginTerminal.php`
- **Estimation** : 30 min
- **Dépendances** : Tâche 4.2
- **Tests** : Tests fonctionnels de la connexion avec redirection si email non vérifié

**Modifications** :
- Après connexion réussie, vérifier `$user->hasVerifiedEmail()`
- Si non vérifié, rediriger vers `route('email.verify')`
- Si vérifié, rediriger vers `route('dashboard')` comme avant

### Phase 5 : Routes et Middleware

#### Tâche 5.1 : Ajouter les routes web pour la vérification
- **Description** : Ajouter les routes pour la page de vérification (GET) et les actions (POST via Livewire)
- **Fichiers concernés** : `routes/web.php`
- **Estimation** : 15 min
- **Dépendances** : Tâche 3.1
- **Tests** : Vérifier l'accessibilité des routes

**Routes à ajouter** :
- `GET /email/verify` : Page de vérification (middleware `auth`, Livewire component `VerifyEmail`)
- Les actions POST sont gérées par Livewire (pas besoin de routes séparées)

**Note** : Les actions `verify()` et `resend()` sont des méthodes du composant Livewire, donc pas besoin de routes POST séparées.

### Phase 6 : Tests

#### Tâche 6.1 : Tests unitaires pour EmailVerificationService
- **Description** : Écrire des tests complets pour toutes les méthodes du service
- **Fichiers concernés** : `tests/Unit/Services/EmailVerificationServiceTest.php`
- **Estimation** : 2h
- **Dépendances** : Tâche 2.1
- **Tests** : Tests unitaires complets

**Tests à écrire** :
- Génération de code valide (6 chiffres, hashé, expiration définie)
- Vérification de code correct
- Vérification de code incorrect
- Vérification de code expiré
- Vérification avec tentatives dépassées
- Renvoi de code avec limitations
- Renvoi de code avant le cooldown (doit échouer)
- Nettoyage du code après vérification

#### Tâche 6.2 : Tests d'intégration pour la vérification d'email
- **Description** : Tests d'intégration du flux complet de vérification
- **Fichiers concernés** : `tests/Feature/EmailVerificationTest.php`
- **Estimation** : 2h
- **Dépendances** : Tâches 3.1, 4.1, 4.2
- **Tests** : Tests d'intégration complets

**Tests à écrire** :
- Inscription → Code envoyé → Redirection vers vérification
- Connexion avec email non vérifié → Redirection vers vérification
- Vérification avec code correct → Email marqué comme vérifié → Redirection dashboard
- Vérification avec code incorrect → Erreur affichée
- Vérification avec code expiré → Erreur affichée
- Tentatives dépassées → Erreur affichée
- Renvoi de code → Nouveau code envoyé
- Renvoi avant cooldown → Erreur affichée
- Connexion avec email vérifié → Pas de redirection vers vérification

#### Tâche 6.3 : Tests fonctionnels pour les composants Livewire
- **Description** : Tests fonctionnels des composants Register et LoginTerminal avec vérification
- **Fichiers concernés** : 
  - `tests/Feature/Livewire/RegisterTest.php` (mise à jour)
  - `tests/Feature/Livewire/LoginTerminalTest.php` (mise à jour)
  - `tests/Feature/Livewire/VerifyEmailTest.php` (nouveau)
- **Estimation** : 2h
- **Dépendances** : Tâches 3.1, 4.3, 4.4
- **Tests** : Tests fonctionnels complets

**Tests à écrire** :
- Register : Vérifier la redirection vers `/email/verify` après inscription
- LoginTerminal : Vérifier la redirection vers `/email/verify` si email non vérifié
- LoginTerminal : Vérifier la redirection vers `/dashboard` si email vérifié
- VerifyEmail : Vérifier l'affichage de la page
- VerifyEmail : Vérifier la soumission du code
- VerifyEmail : Vérifier le renvoi de code
- VerifyEmail : Vérifier les messages d'erreur

#### Tâche 6.4 : Tests du Mailable EmailVerificationNotification
- **Description** : Tests du rendu et de l'envoi de l'email
- **Fichiers concernés** : `tests/Feature/Mail/EmailVerificationMailTest.php`
- **Estimation** : 1h
- **Dépendances** : Tâche 2.2
- **Tests** : Tests du Mailable

**Tests à écrire** :
- Vérifier que l'email contient le code à 6 chiffres
- Vérifier le sujet de l'email
- Vérifier le rendu HTML et texte
- Vérifier l'envoi de l'email

### Phase 7 : Documentation et Finalisation

#### Tâche 7.1 : Mettre à jour ARCHITECTURE.md
- **Description** : Documenter le système de vérification d'email dans ARCHITECTURE.md
- **Fichiers concernés** : `docs/memory_bank/ARCHITECTURE.md`
- **Estimation** : 30 min
- **Dépendances** : Toutes les phases précédentes
- **Tests** : Vérifier la documentation

**Sections à ajouter/mettre à jour** :
- Section "Vérification d'email" dans Authentification
- Documenter les routes web
- Documenter le service `EmailVerificationService`
- Documenter le flux de vérification

#### Tâche 7.2 : Ajouter des commentaires dans le code
- **Description** : Ajouter des commentaires PHPDoc complets dans tous les fichiers créés/modifiés
- **Fichiers concernés** : Tous les fichiers créés/modifiés
- **Estimation** : 30 min
- **Dépendances** : Toutes les phases précédentes
- **Tests** : Vérifier la documentation du code

## Ordre d'Exécution

1. **Phase 1** : Base de données et Modèle (Tâches 1.1, 1.2)
2. **Phase 2** : Service de Vérification d'Email (Tâches 2.1, 2.2, 2.3)
3. **Phase 3** : Composant Livewire de Vérification (Tâches 3.1, 3.2)
4. **Phase 4** : Intégration avec AuthService et Composants (Tâches 4.1, 4.2, 4.3, 4.4)
5. **Phase 5** : Routes et Middleware (Tâche 5.1)
6. **Phase 6** : Tests (Tâches 6.1, 6.2, 6.3, 6.4) - Peut être fait en parallèle avec le développement
7. **Phase 7** : Documentation et Finalisation (Tâches 7.1, 7.2)

## Migrations de Base de Données

- [ ] Migration : Ajouter les champs de vérification d'email à la table users
  - `email_verification_code` (string, nullable)
  - `email_verification_code_expires_at` (timestamp, nullable)
  - `email_verification_attempts` (integer, default: 0)
  - `email_verification_code_sent_at` (timestamp, nullable)

## Endpoints API

Aucun nouvel endpoint API n'est nécessaire pour cette fonctionnalité. La vérification se fait entièrement via Livewire (routes web).

## Routes Web

### Nouvelles Routes

- `GET /email/verify` - Page de vérification d'email (middleware `auth`, Livewire component `VerifyEmail`)

### Routes Modifiées

Aucune route existante n'est modifiée. Les redirections sont gérées dans les composants Livewire.

## Événements & Listeners

Aucun nouvel événement ou listener n'est nécessaire pour cette fonctionnalité. La vérification d'email est gérée de manière synchrone dans les services et composants.

**Note** : Dans une version future, on pourra ajouter un événement `EmailVerified` pour la traçabilité et les effets de bord (notifications, analytics, etc.).

## Services & Classes

### Nouveaux Services

- `EmailVerificationService` : Service de gestion de la vérification d'email
  - Méthodes :
    - `generateCode(User $user): string` - Génère un code, le hash, le stocke, l'envoie par email
    - `verifyCode(User $user, string $code): bool` - Vérifie le code et marque l'email comme vérifié
    - `resendCode(User $user): void` - Génère et envoie un nouveau code
    - `isCodeValid(User $user, string $code): bool` - Vérifie si le code est valide (sans incrémenter tentatives)
    - `clearVerificationCode(User $user): void` - Nettoie le code après vérification

### Classes Modifiées

- `AuthService` : 
  - Ajout de l'envoi de code après inscription
  - Ajout de la vérification d'email lors de la connexion (méthode helper)
- `User` : 
  - Ajout des champs de vérification dans `$fillable`
  - Ajout de méthodes helper (`hasVerifiedEmail()`, `hasPendingVerificationCode()`, etc.)
- `Register` (Livewire) : 
  - Modification de la redirection après inscription vers `/email/verify`
- `LoginTerminal` (Livewire) : 
  - Modification de la redirection après connexion selon l'état de vérification

### Nouvelles Classes

- `EmailVerificationNotification` (Mailable) : Email contenant le code de vérification
- `VerifyEmail` (Livewire) : Composant pour la page de vérification

## Tests

### Tests Unitaires

- [ ] Test : EmailVerificationService génère un code valide (6 chiffres, hashé, expiration)
- [ ] Test : EmailVerificationService vérifie un code correct
- [ ] Test : EmailVerificationService rejette un code incorrect
- [ ] Test : EmailVerificationService rejette un code expiré
- [ ] Test : EmailVerificationService bloque après 5 tentatives
- [ ] Test : EmailVerificationService permet le renvoi après 2 minutes
- [ ] Test : EmailVerificationService bloque le renvoi avant 2 minutes
- [ ] Test : EmailVerificationService nettoie le code après vérification
- [ ] Test : User::hasVerifiedEmail() retourne true si email_verified_at est défini
- [ ] Test : User::hasPendingVerificationCode() retourne true si code non expiré
- [ ] Test : User::canResendVerificationCode() respecte le cooldown de 2 minutes
- [ ] Test : User::hasExceededVerificationAttempts() retourne true après 5 tentatives

### Tests d'Intégration

- [ ] Test : Inscription → Code envoyé → Redirection vers vérification
- [ ] Test : Connexion avec email non vérifié → Redirection vers vérification
- [ ] Test : Vérification avec code correct → Email vérifié → Redirection dashboard
- [ ] Test : Vérification avec code incorrect → Erreur affichée
- [ ] Test : Vérification avec code expiré → Erreur affichée
- [ ] Test : Tentatives dépassées → Erreur affichée
- [ ] Test : Renvoi de code → Nouveau code envoyé
- [ ] Test : Renvoi avant cooldown → Erreur affichée
- [ ] Test : Connexion avec email vérifié → Pas de redirection vers vérification

### Tests Fonctionnels

- [ ] Test : Register redirige vers `/email/verify` après inscription
- [ ] Test : LoginTerminal redirige vers `/email/verify` si email non vérifié
- [ ] Test : LoginTerminal redirige vers `/dashboard` si email vérifié
- [ ] Test : VerifyEmail affiche la page correctement
- [ ] Test : VerifyEmail soumet le code correctement
- [ ] Test : VerifyEmail affiche les erreurs correctement
- [ ] Test : VerifyEmail renvoie le code correctement
- [ ] Test : VerifyEmail affiche le cooldown correctement

### Tests du Mailable

- [ ] Test : EmailVerificationNotification contient le code à 6 chiffres
- [ ] Test : EmailVerificationNotification a le bon sujet
- [ ] Test : EmailVerificationNotification rend correctement (HTML et texte)
- [ ] Test : EmailVerificationNotification est envoyé correctement

## Documentation

- [ ] Mettre à jour ARCHITECTURE.md avec la section "Vérification d'email"
- [ ] Documenter EmailVerificationService avec PHPDoc complet
- [ ] Documenter les méthodes helper du modèle User
- [ ] Ajouter des commentaires dans le code Livewire
- [ ] Documenter les routes web dans ARCHITECTURE.md

## Notes Techniques

### Sécurité

- **Génération de code** : Utiliser `random_int(100000, 999999)` pour générer des codes cryptographiquement sécurisés
- **Stockage** : Toujours hasher les codes avec `Hash::make()` avant stockage
- **Vérification** : Utiliser `Hash::check()` pour comparer le code saisi avec le hash stocké
- **Expiration** : Codes expirant après 15 minutes (configurable via constante dans le service)
- **Limitations** : 
  - Maximum 5 tentatives de vérification par code
  - Maximum 1 renvoi toutes les 2 minutes (configurable via constante)
- **Validation** : Vérifier que l'utilisateur correspond bien au code (pas de vérification croisée entre utilisateurs)

### UX

- **Messages clairs** : Messages d'erreur explicites (code incorrect, expiré, tentatives dépassées, etc.)
- **Feedback visuel** : Afficher les tentatives restantes et le cooldown de renvoi
- **Formatage** : Champ de saisie avec validation en temps réel (6 chiffres uniquement)
- **Redirection** : Redirection automatique vers dashboard après vérification réussie
- **Design** : Style terminal cohérent avec le reste de l'application

### Configuration

- **Expiration** : 15 minutes par défaut (peut être configuré via constante dans `EmailVerificationService`)
- **Tentatives max** : 5 par défaut (peut être configuré via constante)
- **Cooldown renvoi** : 2 minutes par défaut (peut être configuré via constante)

### Comportement MVP

- **Non-bloquant** : Pour le MVP, on ne bloque pas l'accès aux fonctionnalités si l'email n'est pas vérifié (sauf redirection après login)
- **Redirection** : Redirection vers la vérification après inscription et après connexion si email non vérifié
- **Dans une version future** : On pourra bloquer certaines fonctionnalités si l'email n'est pas vérifié

### Gestion d'erreurs

- **Erreurs de génération** : Logger l'erreur mais ne pas bloquer l'inscription
- **Erreurs d'envoi** : Logger l'erreur et afficher un message à l'utilisateur
- **Erreurs de validation** : Afficher des messages d'erreur clairs et informatifs

## Références

- [ISSUE-004-implement-email-verification.md](../issues/ISSUE-004-implement-email-verification.md)
- [ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md) - Authentification et architecture
- [STACK.md](../memory_bank/STACK.md) - Stack technique
- [Laravel Email Verification](https://laravel.com/docs/authentication#email-verification) - Documentation Laravel (référence, mais notre implémentation est différente avec codes)

