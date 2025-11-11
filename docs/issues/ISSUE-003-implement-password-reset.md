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
- Les utilisateurs doivent contacter le support, ce qui crée de la friction et des coûts

**Valeur utilisateur** :
- Permet aux utilisateurs de récupérer leur compte facilement et rapidement
- Réduit la frustration et l'abandon de comptes
- Améliore la sécurité (les utilisateurs peuvent changer leur mot de passe s'ils pensent qu'il est compromis)
- Réduit le besoin de support manuel (gain de temps et de ressources)
- Améliore la confiance dans le système (les utilisateurs savent qu'ils peuvent récupérer leur compte)

**Impact** :
- Réduit l'abandon de comptes (les utilisateurs peuvent récupérer leur compte au lieu d'en créer un nouveau)
- Améliore la satisfaction utilisateur (expérience fluide et autonome)
- Réduit le support nécessaire (moins de demandes manuelles de réinitialisation)
- Améliore la sécurité globale du système (les utilisateurs peuvent changer leur mot de passe s'ils le souhaitent)
- Réduit les coûts opérationnels (moins d'interventions manuelles)

**Priorité** :
- Priorité **High** car fonctionnalité essentielle pour l'expérience utilisateur
- Bloquant pour une expérience utilisateur complète
- Doit être implémentée avant ou en même temps que la vérification d'email (ISSUE-004)

## Critères d'Acceptation

### Flux Utilisateur

- [ ] Ajouter un lien "Mot de passe oublié ?" sur la page de connexion (`/login`)
- [ ] Créer une route `GET /forgot-password` accessible aux utilisateurs non authentifiés
- [ ] Créer une page "Mot de passe oublié" avec formulaire simple (champ email uniquement)
- [ ] Après soumission du formulaire, afficher un message de confirmation même si l'email n'existe pas (sécurité)
- [ ] Envoyer un email avec lien de réinitialisation contenant le token
- [ ] Créer une route `GET /reset-password/{token}` pour afficher le formulaire de réinitialisation
- [ ] Le formulaire de réinitialisation doit inclure : token (hidden), email, nouveau mot de passe, confirmation du mot de passe
- [ ] Après réinitialisation réussie, rediriger vers `/login` avec message de succès
- [ ] Envoyer un email de confirmation après réinitialisation réussie

### Validation et Sécurité

- [ ] Valider que le token est valide et non expiré avant d'afficher le formulaire de réinitialisation
- [ ] Valider que le token correspond à l'email fourni
- [ ] Valider que le nouveau mot de passe respecte les règles (minimum 8 caractères)
- [ ] Valider que la confirmation du mot de passe correspond au nouveau mot de passe
- [ ] Invalider tous les tokens de réinitialisation existants pour l'utilisateur après succès
- [ ] Implémenter le rate limiting : maximum 3 demandes de réinitialisation par heure par email
- [ ] Implémenter le rate limiting : maximum 5 tentatives de réinitialisation par heure par IP

### Gestion des Erreurs

- [ ] Afficher un message d'erreur clair si le token est invalide : "Ce lien de réinitialisation est invalide."
- [ ] Afficher un message d'erreur clair si le token est expiré : "Ce lien de réinitialisation a expiré. Veuillez en demander un nouveau."
- [ ] Afficher un message d'erreur si l'email n'existe pas (sans révéler que l'email n'existe pas) : "Si cet email existe, un lien de réinitialisation vous a été envoyé."
- [ ] Afficher les erreurs de validation du formulaire de manière claire et contextuelle
- [ ] Gérer les erreurs d'envoi d'email gracieusement

### Intégration Livewire

- [ ] Créer un composant Livewire `ForgotPassword` pour le formulaire de demande (ou utiliser Blade classique)
- [ ] Créer un composant Livewire `ResetPassword` pour le formulaire de réinitialisation (ou utiliser Blade classique)
- [ ] Les composants doivent suivre le même style que `LoginTerminal` (cohérence visuelle)
- [ ] Les routes doivent être accessibles aux utilisateurs non authentifiés (middleware `guest`)

### API (Optionnel pour MVP)

- [ ] Créer `POST /api/auth/forgot-password` pour les clients externes (optionnel)
- [ ] Créer `POST /api/auth/reset-password` pour les clients externes (optionnel)
- [ ] Les endpoints API doivent retourner des réponses JSON standardisées

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
- Le service peut être utilisé par les composants Livewire et les contrôleurs API
- Suivre le même pattern que `AuthService` pour la cohérence

**Form Requests** :
- Créer `app/Http/Requests/ForgotPasswordRequest` pour valider l'email de demande
  - Validation : `email` requis, format email valide
  - Messages d'erreur personnalisés en français
- Créer `app/Http/Requests/ResetPasswordRequest` pour valider le token, email, et nouveau mot de passe
  - Validation : `token` requis, `email` requis et format valide, `password` requis avec minimum 8 caractères, `password_confirmation` requis et doit correspondre à `password`
  - Messages d'erreur personnalisés en français
  - Utiliser les règles de validation Laravel standard pour les mots de passe

**Controllers** :
- Créer `app/Http/Controllers/Auth/PasswordResetController` avec les méthodes :
  - `showForgotPasswordForm()` - Afficher le formulaire de demande (retourne la vue)
  - `sendResetLink(ForgotPasswordRequest $request)` - Envoyer le lien de réinitialisation
    - Utilise `Password::sendResetLink()` de Laravel
    - Retourne toujours un message de succès (même si l'email n'existe pas) pour la sécurité
    - Redirige vers `/forgot-password` avec message flash de succès
  - `showResetForm(Request $request, string $token)` - Afficher le formulaire de réinitialisation
    - Vérifie que le token est valide avant d'afficher le formulaire
    - Passe le token et l'email à la vue
  - `reset(ResetPasswordRequest $request)` - Réinitialiser le mot de passe
    - Utilise `Password::reset()` de Laravel
    - Invalide tous les tokens de réinitialisation après succès
    - Envoie l'email de confirmation
    - Redirige vers `/login` avec message flash de succès
- Créer `app/Http/Controllers/Api/Auth/PasswordResetController` pour les endpoints API (optionnel pour MVP)

**Livewire Components** (recommandé pour cohérence) :
- Créer `app/Livewire/ForgotPassword.php` pour le formulaire de demande
- Créer `app/Livewire/ResetPassword.php` pour le formulaire de réinitialisation
- Suivre le même style que `LoginTerminal` pour la cohérence visuelle
- Utiliser les mêmes patterns de validation et gestion d'erreurs
- Alternative : Utiliser des vues Blade classiques avec contrôleurs (plus simple mais moins cohérent)

**Emails** :
- Créer `app/Mail/ResetPasswordNotification` (Mailable Laravel)
- Template d'email (`resources/views/emails/auth/reset-password.blade.php`) avec :
  - Message d'accueil personnalisé avec le nom de l'utilisateur
  - Explication claire de la raison de l'email
  - Bouton/lien de réinitialisation bien visible
  - Lien de réinitialisation : `/reset-password/{token}?email={email}`
  - Message de sécurité expliquant que le lien expire dans 60 minutes
  - Instructions pour ignorer l'email si la demande n'a pas été faite
- Créer `app/Mail/PasswordResetConfirmation` pour l'email de confirmation après réinitialisation
- Template d'email de confirmation avec message de succès et recommandations de sécurité
- Le lien doit expirer après 60 minutes (délai par défaut de Laravel, configurable)

**Routes** :
- `GET /forgot-password` - Formulaire de demande (middleware `guest`)
- `POST /forgot-password` - Envoi du lien (middleware `guest`, rate limit)
- `GET /reset-password/{token}` - Formulaire de réinitialisation (middleware `guest`)
- `POST /reset-password` - Réinitialisation (middleware `guest`, rate limit)
- Routes API optionnelles (pour MVP) :
  - `POST /api/auth/forgot-password` - Envoi du lien (rate limit)
  - `POST /api/auth/reset-password` - Réinitialisation (rate limit)

### Frontend

**Pages** :
- Page "Mot de passe oublié" (`resources/views/auth/forgot-password.blade.php` ou composant Livewire)
  - Formulaire simple avec champ email
  - Bouton "Envoyer le lien de réinitialisation"
  - Lien "Retour à la connexion"
  - Design cohérent avec la page de connexion
- Page "Réinitialiser le mot de passe" (`resources/views/auth/reset-password.blade.php` ou composant Livewire)
  - Formulaire avec : token (hidden), email (hidden ou affiché en lecture seule), nouveau mot de passe, confirmation du mot de passe
  - Bouton "Réinitialiser le mot de passe"
  - Indicateur de force du mot de passe (optionnel mais recommandé)
  - Design cohérent avec le reste de l'application
- Utiliser le même layout que les autres pages d'authentification (`layouts/auth.blade.php` ou similaire)

**UX** :
- Lien "Mot de passe oublié ?" bien visible sur la page de connexion (sous le formulaire)
- Message de succès après envoi du lien : "Si cet email existe dans notre système, un lien de réinitialisation vous a été envoyé. Vérifiez votre boîte de réception."
- Message de succès après réinitialisation : "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter."
- Redirection vers `/login` après réinitialisation réussie avec message flash
- Indicateur de chargement pendant l'envoi du formulaire
- Validation en temps réel des champs (si Livewire)
- Messages d'erreur contextuels et clairs pour chaque cas d'erreur
- Design cohérent avec le reste de l'application (même style que LoginTerminal)

**Sécurité** :
- Le token doit être unique et sécurisé (géré automatiquement par Laravel)
- Le token doit expirer après 60 minutes (configurable dans `config/auth.php`)
- Rate limiting : maximum 3 demandes de réinitialisation par heure par email
- Rate limiting : maximum 5 tentatives de réinitialisation par heure par IP
- Ne jamais révéler si un email existe ou non dans le système (sécurité)
- Invalider tous les tokens de réinitialisation existants après succès
- Le token doit être utilisé une seule fois (invalidation après utilisation)
- Utiliser HTTPS en production pour protéger les tokens dans les URLs

### Configuration

**Mail** :
- Configurer le service d'envoi d'emails dans `.env` (SMTP, Mailgun, SendGrid, etc.)
- Vérifier que `MAIL_FROM_ADDRESS` et `MAIL_FROM_NAME` sont configurés dans `.env`
- En développement, utiliser le driver `log` pour voir les emails dans `storage/logs/laravel.log`
- En production, utiliser un service d'envoi d'emails fiable (Mailgun, SendGrid, AWS SES, etc.)
- Tester l'envoi d'emails dans les deux environnements
- Vérifier que les emails sont bien formatés et que les liens fonctionnent correctement

**Rate Limiting** :
- Limiter les demandes de réinitialisation : 3 par heure par email
- Limiter les tentatives de réinitialisation : 5 par heure par IP
- Utiliser le rate limiting Laravel natif (`RateLimiter` dans `RouteServiceProvider` ou middleware)
- Messages d'erreur clairs quand la limite est atteinte : "Trop de tentatives. Veuillez réessayer dans {minutes} minutes."

## Notes

### Technique

- Laravel fournit des fonctionnalités natives pour la réinitialisation de mot de passe
- Utiliser `Illuminate\Support\Facades\Password` pour la logique métier
- Les tokens sont stockés dans la table `password_reset_tokens` (créée automatiquement par Laravel)
- Vérifier que la migration `create_password_reset_tokens_table` existe dans `database/migrations/`
- Pour le MVP, on peut commencer avec les routes web uniquement (pas besoin d'API immédiatement)
- Les endpoints API peuvent être ajoutés dans une itération future si nécessaire

### Intégration avec l'existant

- Suivre le même pattern que `AuthService` pour la cohérence du code
- Utiliser les mêmes patterns de validation que les autres formulaires (Form Requests)
- Suivre le même style visuel que `LoginTerminal` pour la cohérence UX
- Les composants Livewire doivent utiliser les mêmes conventions de nommage et structure

### Tests

- Écrire des tests pour :
  - L'envoi du lien de réinitialisation (succès et erreurs)
  - La validation du token (valide, invalide, expiré)
  - La réinitialisation du mot de passe (succès et erreurs)
  - Le rate limiting (vérifier que les limites sont respectées)
  - Les emails envoyés (vérifier le contenu et les liens)
- Tests à créer dans `tests/Feature/Auth/PasswordResetTest.php`
- Tests Livewire dans `tests/Feature/Livewire/PasswordResetTest.php` (si composants Livewire)

### Évolutions futures

- Ajouter la possibilité de changer le mot de passe depuis le profil utilisateur (issue séparée)
- Invalider automatiquement le cookie "Remember Me" lors de la réinitialisation (amélioration sécurité)
- Ajouter des notifications push ou SMS pour la réinitialisation (optionnel)

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

#### 2025-01-XX - Alex (Product) - Amélioration de l'issue
**Statut** : À faire
**Détails** : Amélioration complète de l'issue avec :
- Critères d'acceptation détaillés et organisés par catégories (Flux Utilisateur, Validation et Sécurité, Gestion des Erreurs, Intégration Livewire, API)
- Détails techniques enrichis (chemins de fichiers complets, patterns à suivre, intégration avec l'existant)
- Messages utilisateur précis et exemples concrets
- Détails sur les emails (templates, contenu, structure)
- Section sécurité renforcée avec rate limiting détaillé
- Section tests avec cas de test à couvrir
- Section évolutions futures
- Contexte métier enrichi avec justification de la priorité
**Notes** : Issue maintenant complète et prête pour la création du plan de développement par Sam (Lead Developer)

#### 2025-01-XX - Alex (Product) - Création de l'issue GitHub
**Statut** : À faire
**Détails** : Issue GitHub créée : [#6](https://github.com/PiLep/space-xplorer/issues/6)
**Branche** : `issue/003-implement-password-reset`
**Notes** : Issue synchronisée avec GitHub, prête pour le développement

