# ISSUE-003 : Implémenter la réinitialisation de mot de passe

## Type
Feature

## Priorité
High

## Description

Implémenter le système de réinitialisation de mot de passe pour permettre aux utilisateurs de récupérer leur compte en cas d'oubli de mot de passe. Cette fonctionnalité est essentielle pour l'expérience utilisateur et réduit le support nécessaire.

## Contexte Métier

**Problème actuel** :
- Les utilisateurs qui oublient leur mot de passe ne peuvent pas récupérer leur compte
- Aucun moyen de réinitialiser le mot de passe sans intervention manuelle
- Cela peut mener à l'abandon du compte et à la création de nouveaux comptes

**Valeur utilisateur** :
- Permet aux utilisateurs de récupérer leur compte facilement
- Réduit la frustration et l'abandon
- Améliore la sécurité (les utilisateurs peuvent changer leur mot de passe s'ils pensent qu'il est compromis)
- Réduit le besoin de support manuel

**Impact** :
- Réduit l'abandon de comptes
- Améliore la satisfaction utilisateur
- Réduit le support nécessaire
- Améliore la sécurité globale du système

## Critères d'Acceptation

- [ ] Créer une route "Mot de passe oublié" (`/forgot-password`) accessible depuis la page de connexion
- [ ] Créer un formulaire de demande de réinitialisation (email uniquement)
- [ ] Implémenter l'envoi d'email avec lien de réinitialisation
- [ ] Créer une route de réinitialisation (`/reset-password/{token}`)
- [ ] Créer un formulaire de réinitialisation (token, email, nouveau mot de passe, confirmation)
- [ ] Valider le token et permettre la réinitialisation
- [ ] Envoyer un email de confirmation après réinitialisation réussie
- [ ] Invalider tous les tokens de réinitialisation après succès
- [ ] Gérer les erreurs (token invalide, expiré, email non trouvé)
- [ ] Afficher des messages d'erreur clairs et utiles
- [ ] Fonctionner pour les utilisateurs Livewire (routes web)
- [ ] Créer les endpoints API correspondants pour les clients externes (optionnel pour MVP)

## Détails Techniques

### Backend

**Migrations** :
- La table `password_reset_tokens` devrait être créée automatiquement par Laravel
- Vérifier que la migration existe, sinon créer `create_password_reset_tokens_table`

**Services** :
- Utiliser les fonctionnalités natives de Laravel pour la réinitialisation de mot de passe
- `Password::sendResetLink()` pour envoyer le lien
- `Password::reset()` pour réinitialiser le mot de passe
- Créer un service `PasswordResetService` si nécessaire pour encapsuler la logique

**Form Requests** :
- Créer `ForgotPasswordRequest` pour valider l'email de demande
- Créer `ResetPasswordRequest` pour valider le token, email, et nouveau mot de passe
- Validation du mot de passe : minimum 8 caractères, confirmation requise

**Controllers** :
- Créer `PasswordResetController` avec les méthodes :
  - `showForgotPasswordForm()` - Afficher le formulaire
  - `sendResetLink()` - Envoyer le lien de réinitialisation
  - `showResetForm($token)` - Afficher le formulaire de réinitialisation
  - `reset()` - Réinitialiser le mot de passe

**Livewire Components** (optionnel) :
- Créer `ForgotPassword.php` pour le formulaire de demande
- Créer `ResetPassword.php` pour le formulaire de réinitialisation
- Ou utiliser des vues Blade classiques (plus simple pour MVP)

**Emails** :
- Créer `ResetPasswordNotification` (Mailable Laravel)
- Template d'email avec lien de réinitialisation
- Le lien doit contenir le token et l'email
- Le lien doit expirer après un certain temps (par défaut 60 minutes dans Laravel)

**Routes** :
- `GET /forgot-password` - Formulaire de demande
- `POST /forgot-password` - Envoi du lien
- `GET /reset-password/{token}` - Formulaire de réinitialisation
- `POST /reset-password` - Réinitialisation

### Frontend

**Pages** :
- Page "Mot de passe oublié" avec formulaire simple (email)
- Page "Réinitialiser le mot de passe" avec formulaire (token, email, nouveau mot de passe, confirmation)
- Design cohérent avec le reste de l'application

**UX** :
- Lien "Mot de passe oublié ?" sur la page de connexion
- Messages d'erreur clairs et utiles
- Messages de succès après envoi du lien et après réinitialisation
- Redirection vers la page de connexion après réinitialisation réussie

**Sécurité** :
- Le token doit être unique et sécurisé
- Le token doit expirer après un délai raisonnable
- Limiter le nombre de tentatives de réinitialisation par email (rate limiting)

### Configuration

**Mail** :
- Configurer le service d'envoi d'emails (SMTP, Mailgun, etc.)
- Vérifier que `MAIL_FROM_ADDRESS` et `MAIL_FROM_NAME` sont configurés
- Tester l'envoi d'emails en développement (log driver) et production

**Rate Limiting** :
- Limiter les demandes de réinitialisation (par exemple, 3 par heure par email)
- Utiliser le rate limiting Laravel natif

## Notes

- Laravel fournit des fonctionnalités natives pour la réinitialisation de mot de passe
- Utiliser `Illuminate\Support\Facades\Password` pour la logique métier
- Les tokens sont stockés dans la table `password_reset_tokens`
- Cette fonctionnalité est essentielle pour l'expérience utilisateur et doit être implémentée tôt
- Pour le MVP, on peut commencer avec les routes web uniquement (pas besoin d'API immédiatement)

## Références

- [ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md) - Authentification
- [PROJECT_BRIEF.md](../memory_bank/PROJECT_BRIEF.md) - Parcours utilisateur
- [Laravel Password Reset](https://laravel.com/docs/authentication#password-reset)

## Suivi et Historique

### Statut

À faire

### Historique

#### 2025-01-XX - Alex (Product) - Création de l'issue
**Statut** : À faire
**Détails** : Issue créée pour permettre aux utilisateurs de récupérer leur compte en cas d'oubli de mot de passe
**Notes** : Priorité haute car fonctionnalité essentielle pour l'expérience utilisateur

