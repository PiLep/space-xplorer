# ISSUE-004 : Implémenter la vérification d'email

## Type
Feature

## Priorité
Medium

## Description

Implémenter le système de vérification d'email pour valider que les utilisateurs possèdent bien l'adresse email qu'ils ont fournie lors de l'inscription. Cette fonctionnalité améliore la sécurité et la qualité des données utilisateur.

## Contexte Métier

**Problème actuel** :
- Les utilisateurs peuvent s'inscrire avec n'importe quelle adresse email sans vérification
- Pas de garantie que l'email appartient bien à l'utilisateur
- Risque de comptes avec des emails invalides ou non vérifiés

**Valeur utilisateur** :
- Améliore la sécurité du système
- Garantit la qualité des données utilisateur
- Permet d'envoyer des notifications importantes aux utilisateurs vérifiés
- Réduit les risques de comptes frauduleux ou avec emails invalides

**Impact** :
- Améliore la sécurité globale
- Améliore la qualité des données
- Permet d'envoyer des communications importantes aux utilisateurs vérifiés
- Réduit les risques de spam ou d'abus

**Note sur la priorité** :
- Priorité Medium car cette fonctionnalité n'est pas bloquante pour le MVP
- Peut être implémentée après les fonctionnalités essentielles (Remember Me, Reset Password)
- Cependant, c'est une bonne pratique à implémenter tôt pour éviter d'avoir des utilisateurs non vérifiés plus tard

## Critères d'Acceptation

- [ ] Activer l'interface `MustVerifyEmail` dans le modèle `User`
- [ ] Envoyer un email de vérification après l'inscription
- [ ] Créer une route de vérification (`/email/verify`)
- [ ] Créer une page de vérification avec message d'information
- [ ] Créer un lien de vérification dans l'email qui redirige vers la route de vérification
- [ ] Marquer l'email comme vérifié après clic sur le lien
- [ ] Afficher un message de succès après vérification
- [ ] Permettre la renvoyer l'email de vérification si nécessaire
- [ ] Bloquer l'accès à certaines fonctionnalités si l'email n'est pas vérifié (optionnel pour MVP)
- [ ] Afficher un bandeau ou message si l'email n'est pas vérifié
- [ ] Gérer les erreurs (lien expiré, email déjà vérifié, etc.)
- [ ] Fonctionner pour les utilisateurs Livewire (routes web)

## Détails Techniques

### Backend

**Modèle User** :
- Implémenter `Illuminate\Contracts\Auth\MustVerifyEmail` dans le modèle `User`
- Le champ `email_verified_at` existe déjà dans la migration

**Services** :
- Utiliser les fonctionnalités natives de Laravel pour la vérification d'email
- `sendEmailVerificationNotification()` est appelé automatiquement après l'inscription si `MustVerifyEmail` est implémenté
- Créer une notification `VerifyEmail` personnalisée si nécessaire (optionnel)

**Controllers** :
- Créer `EmailVerificationController` avec les méthodes :
  - `show()` - Afficher la page de vérification
  - `verify()` - Vérifier l'email via le lien
  - `resend()` - Renvoyer l'email de vérification
- Ou utiliser les routes Laravel natives avec Fortify/Breeze (si utilisé)

**Middleware** :
- Utiliser `Illuminate\Auth\Middleware\EnsureEmailIsVerified` pour protéger les routes
- Pour le MVP, on peut ne pas bloquer l'accès mais afficher un message d'avertissement

**Routes** :
- `GET /email/verify` - Page de vérification
- `GET /email/verify/{id}/{hash}` - Vérification via lien
- `POST /email/verification-notification` - Renvoyer l'email

**Emails** :
- Créer `VerifyEmailNotification` (Mailable Laravel) ou utiliser la notification par défaut
- Template d'email avec lien de vérification
- Le lien doit contenir l'ID utilisateur et un hash de vérification
- Le lien doit expirer après un certain temps (par défaut 60 minutes dans Laravel)

### Frontend

**Pages** :
- Page "Vérifier votre email" avec message d'information
- Lien "Renvoyer l'email de vérification" si nécessaire
- Design cohérent avec le reste de l'application

**UX** :
- Message clair expliquant pourquoi la vérification est importante
- Instructions claires sur ce qu'il faut faire
- Message de succès après vérification
- Possibilité de renvoyer l'email si nécessaire
- Bandeau ou message sur le dashboard si l'email n'est pas vérifié (non bloquant pour MVP)

**Sécurité** :
- Le lien de vérification doit être signé et expirer
- Vérifier que l'utilisateur correspond bien au lien
- Empêcher la vérification d'un email déjà vérifié

### Configuration

**Mail** :
- Configurer le service d'envoi d'emails (SMTP, Mailgun, etc.)
- Vérifier que `MAIL_FROM_ADDRESS` et `MAIL_FROM_NAME` sont configurés
- Tester l'envoi d'emails en développement (log driver) et production

**Comportement** :
- Pour le MVP, ne pas bloquer l'accès aux fonctionnalités si l'email n'est pas vérifié
- Afficher un message d'avertissement mais permettre l'utilisation
- Dans une version future, on pourra bloquer certaines fonctionnalités

## Notes

- Laravel fournit des fonctionnalités natives pour la vérification d'email
- Le champ `email_verified_at` existe déjà dans la migration
- Cette fonctionnalité peut être implémentée progressivement :
  - Phase 1 : Envoi d'email et vérification (non bloquant)
  - Phase 2 : Blocage de certaines fonctionnalités si non vérifié (futur)
- Pour le MVP, on peut commencer avec les routes web uniquement
- Cette fonctionnalité est importante pour la sécurité mais n'est pas bloquante pour le MVP

## Références

- [ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md) - Authentification
- [PROJECT_BRIEF.md](../memory_bank/PROJECT_BRIEF.md) - Parcours utilisateur
- [Laravel Email Verification](https://laravel.com/docs/authentication#email-verification)

## Suivi et Historique

### Statut

À faire

### Historique

#### 2025-01-XX - Alex (Product) - Création de l'issue
**Statut** : À faire
**Détails** : Issue créée pour améliorer la sécurité et la qualité des données avec la vérification d'email
**Notes** : Priorité medium car non bloquant pour le MVP mais bonne pratique à implémenter tôt

