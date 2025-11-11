# Architecture - Stellar

## Architecture technique

### Structure générale

- **Backend** : Laravel (MVC pattern)
- **Frontend** : Livewire 3 components
- **Database** : MySQL 8.0
- **Architecture** : Monolithique (application unique Laravel)
- **Approche** : Hybride - API REST pour clients externes, services Laravel directement pour Livewire

### Organisation des dossiers

Structure MVC Laravel standard avec gestion par événements :

```
app/
├── Console/          # Commandes Artisan
├── Events/           # Événements du domaine métier
├── Exceptions/       # Gestion des exceptions
├── Http/
│   ├── Controllers/  # Contrôleurs MVC
│   ├── Middleware/   # Middleware HTTP
│   └── Requests/     # Form Requests (validation)
├── Listeners/        # Écouteurs d'événements
├── Livewire/         # Composants Livewire
├── Models/           # Modèles Eloquent
├── Policies/         # Policies d'autorisation
├── Providers/        # Service Providers
└── Services/         # Services métier (optionnel)

routes/
├── web.php          # Routes web (Livewire)
├── api.php          # Routes API
└── channels.php     # Routes de broadcasting

database/
├── migrations/      # Migrations de base de données
├── seeders/         # Seeders
└── factories/       # Factories pour les tests

resources/
├── views/           # Vues Blade (layouts, composants)
│   └── livewire/    # Vues Livewire
├── css/             # Styles CSS (Tailwind)
└── js/              # JavaScript (Alpine.js)

tests/               # Tests unitaires et fonctionnels
```

## Modèle de données

### Entités principales

- **Users** : Gestion des utilisateurs/joueurs
- **Planets** : Planètes explorables

*Note : Le modèle de données sera étendu progressivement (systèmes stellaires, objets célestes, etc.)*

### Relations

- **Users → Planets** : Relation "planète d'origine"
  - Un joueur a une planète d'origine (générée aléatoirement à l'inscription)
  - Une planète peut être la planète d'origine de plusieurs joueurs
  - Relation : `user.home_planet_id` → `planets.id`
  - *Note : Les fonctionnalités d'exploration seront ajoutées progressivement*

### Structure simplifiée

```
Users
├── id (ULID - 26 caractères)
├── name
├── email
├── home_planet_id (ULID - foreign key → planets.id)
└── [autres champs standards Laravel]

Planets
├── id (ULID - 26 caractères)
├── name (généré aléatoirement)
├── type (type de planète : tellurique, gazeuse, etc.)
├── size (petite, moyenne, grande)
├── temperature (froide, tempérée, chaude)
├── atmosphere (respirable, toxique, inexistante)
├── terrain (rocheux, océanique, désertique, forestier, etc.)
├── resources (abondantes, modérées, rares)
├── description (générée à partir des caractéristiques)
└── created_at / updated_at
```

### Identifiants (ULIDs)

**Choix technique** : Utilisation d'ULIDs (Universally Unique Lexicographically Sortable Identifier) pour tous les IDs de tables métier.

**Avantages** :
- **URL-friendly** : Pas de caractères spéciaux, peut être utilisé directement dans les URLs
- **Triable** : Les ULIDs sont triables chronologiquement (basés sur le timestamp)
- **Non-énumérable** : Plus difficile à deviner qu'un ID auto-incrémenté
- **Meilleure sécurité** : Réduit les risques d'énumération d'IDs
- **Distribué** : Peut être généré côté client sans collision

**Format** : 26 caractères alphanumériques (ex: `01ARZ3NDEKTSV4RRFFQ69G5FAV`)

**Tables concernées** :
- `users` : ID en ULID
- `planets` : ID en ULID
- Toutes les futures tables métier utiliseront des ULIDs

**Implémentation Laravel** :
- Utilisation du trait `Illuminate\Database\Eloquent\Concerns\HasUlids` dans les modèles
- Migration avec `$table->ulid('id')->primary()` au lieu de `$table->id()`
- Les relations Eloquent fonctionnent automatiquement avec les ULIDs

## API endpoints

**Approche hybride** :
- **Livewire** : Utilise directement les services Laravel (`AuthService`, etc.) sans passer par l'API
- **API REST** : Disponible pour les clients externes (applications mobiles, SPAs distants, etc.)

### Authentification

- `POST /api/auth/register` - Inscription d'un nouveau joueur
- `POST /api/auth/login` - Connexion (retourne un token Sanctum)
- `POST /api/auth/logout` - Déconnexion
- `GET /api/auth/user` - Informations du joueur connecté

### Réinitialisation de mot de passe

**Routes Web** :
- `GET /forgot-password` - Formulaire de demande de réinitialisation (middleware `guest`)
- `POST /forgot-password` - Envoi du lien de réinitialisation (middleware `guest`, rate limit: 3/heure)
- `GET /reset-password/{token}` - Formulaire de réinitialisation (middleware `guest`)
- `POST /reset-password` - Réinitialisation du mot de passe (middleware `guest`, rate limit: 5/heure)

**Fonctionnement** :
- Utilisation des fonctionnalités natives Laravel (`Password::sendResetLink()`, `Password::reset()`)
- Tokens stockés dans la table `password_reset_tokens` (créée automatiquement par Laravel)
- Tokens expirables (60 minutes par défaut, configurable dans `config/auth.php`)
- Invalidation automatique des tokens après utilisation
- Invalidation du Remember Me et des sessions web après réinitialisation réussie (sécurité)

**Sécurité** :
- Rate limiting : 3 demandes de réinitialisation par heure par IP
- Rate limiting : 5 tentatives de réinitialisation par heure par IP
- Ne révèle jamais si un email existe dans le système (message de succès générique)
- Tokens uniques et sécurisés (gérés automatiquement par Laravel)

### Endpoints utilisateurs (MVP)

- `GET /api/users/{id}` - Détails d'un utilisateur (authentification requise)
- `PUT /api/users/{id}` - Mise à jour du profil utilisateur (authentification requise, uniquement son propre profil)
- `GET /api/users/{id}/home-planet` - Planète d'origine du joueur (authentification requise)

**Endpoints futurs** :
- `GET /api/users` - Liste des utilisateurs (avec pagination) - À implémenter selon les besoins

### Endpoints planètes (MVP)

- `GET /api/planets/{id}` - Détails d'une planète (authentification requise)

**Endpoints futurs** :
- `GET /api/planets` - Liste des planètes (avec pagination) - À implémenter selon les besoins
- `POST /api/planets/{id}/explore` - Explorer une planète (action du joueur) - À implémenter selon les besoins

### Format de réponse

Toutes les réponses API suivent un format JSON standardisé :
```json
{
  "data": { ... },
  "message": "Success message",
  "status": "success"
}
```

## Flux métier

### Flux d'inscription et génération de planète

1. **Inscription** : `POST /api/auth/register`
   - Création du compte utilisateur
   - Génération automatique d'une planète d'origine (aléatoire)
   - Attribution de la planète au joueur (`home_planet_id`)
   - Retour du token d'authentification

2. **Connexion** : `POST /api/auth/login`
   - Authentification du joueur
   - Retour du token d'authentification

3. **Affichage de la planète d'origine** : `GET /api/users/{id}/home-planet`
   - Récupération de la planète d'origine du joueur
   - Affichage des détails de la planète

### Flux d'exploration (à venir)

- [À documenter : exploration d'autres planètes, découvertes, etc.]

## Architecture événementielle

### Événements

L'application utilise une architecture événementielle complète pour découpler les actions métier et permettre une traçabilité complète des événements importants.

#### Cycle de vie utilisateur

##### `UserRegistered`

**Déclencheur** : Lors de l'inscription d'un nouveau joueur (`POST /api/auth/register` ou via `AuthService`)

**Listeners** :
- `GenerateHomePlanet` : Génère automatiquement une planète d'origine aléatoire et l'assigne au joueur
- `GenerateAvatar` : Génère automatiquement un avatar pour le joueur (en queue)

**Flux** :
1. Contrôleur API ou `AuthService` crée l'utilisateur
2. Événement `UserRegistered` est dispatché avec l'utilisateur créé
3. Listener `GenerateHomePlanet` génère une planète aléatoire et dispatch `PlanetCreated`
4. Listener `GenerateAvatar` génère un avatar (asynchrone) et dispatch `AvatarGenerated` à la fin

##### `UserLoggedIn`

**Déclencheur** : Lors de la connexion d'un joueur (`POST /api/auth/login` ou via `AuthService`)

**Listeners** :
- Aucun pour le moment (prévu pour : tracking, notifications de bienvenue, etc.)

##### `UserProfileUpdated`

**Déclencheur** : Lors de la mise à jour du profil utilisateur (`PUT /api/users/{id}`)

**Données** : Contient les attributs modifiés (anciennes et nouvelles valeurs)

**Listeners** :
- Aucun pour le moment (prévu pour : regénération d'avatar si nom changé, tracking, etc.)

##### `PasswordResetRequested`

**Déclencheur** : Lorsqu'un utilisateur demande une réinitialisation de mot de passe (`POST /forgot-password`)

**Données** : Email de l'utilisateur

**Listeners** :
- Aucun pour le moment (prévu pour : tracking, analytics, etc.)

##### `PasswordResetCompleted`

**Déclencheur** : Lorsqu'un utilisateur réinitialise son mot de passe avec succès (`POST /reset-password`)

**Données** : Utilisateur, timestamp

**Listeners** :
- Aucun pour le moment (prévu pour : notifications, analytics, invalidation sessions, etc.)

#### Cycle de vie planète

##### `PlanetCreated`

**Déclencheur** : Lors de la création d'une planète (par `PlanetGeneratorService`)

**Listeners** :
- `GeneratePlanetImage` : Génère automatiquement une image de la planète (en queue)
- `GeneratePlanetVideo` : Génère automatiquement une vidéo de la planète (en queue)

**Flux** :
1. `PlanetGeneratorService` crée la planète
2. Événement `PlanetCreated` est dispatché
3. Listeners génèrent les médias (asynchrone) et dispatchent les événements de complétion

##### `PlanetImageGenerated`

**Déclencheur** : Lorsque l'image d'une planète est générée avec succès (par `GeneratePlanetImage`)

**Données** : Planète, chemin de l'image, URL complète

**Listeners** :
- Aucun pour le moment (prévu pour : notifications utilisateur, analytics, etc.)

##### `PlanetVideoGenerated`

**Déclencheur** : Lorsque la vidéo d'une planète est générée avec succès (par `GeneratePlanetVideo`)

**Données** : Planète, chemin de la vidéo, URL complète

**Listeners** :
- Aucun pour le moment (prévu pour : notifications utilisateur, analytics, etc.)

#### Génération de médias

##### `AvatarGenerated`

**Déclencheur** : Lorsque l'avatar d'un utilisateur est généré avec succès (par `GenerateAvatar`)

**Données** : Utilisateur, chemin de l'avatar, URL complète

**Listeners** :
- Aucun pour le moment (prévu pour : notifications utilisateur, analytics, etc.)

#### Exploration (fonctionnalités futures)

##### `PlanetExplored`

**Déclencheur** : Lorsqu'un joueur explore une planète (à implémenter)

**Données** : Utilisateur, planète explorée

**Listeners** :
- Aucun pour le moment (prévu pour : tracking, attribution de points, achievements, etc.)

##### `DiscoveryMade`

**Déclencheur** : Lorsqu'un joueur fait une découverte (à implémenter)

**Données** : Utilisateur, type de découverte, données additionnelles

**Listeners** :
- Aucun pour le moment (prévu pour : tracking, achievements, notifications, etc.)

### Flux événementiel complet

```
Inscription Utilisateur
    ↓
UserRegistered
    ├─→ GenerateHomePlanet → PlanetCreated
    │                          ├─→ GeneratePlanetImage → PlanetImageGenerated
    │                          └─→ GeneratePlanetVideo → PlanetVideoGenerated
    └─→ GenerateAvatar → AvatarGenerated

Connexion Utilisateur
    ↓
UserLoggedIn

Mise à jour Profil
    ↓
UserProfileUpdated
```

### Pattern d'utilisation

**Principe** : Chaque action métier importante déclenche un événement, permettant :
- **Découplage** : Les actions métier ne dépendent pas directement des effets de bord
- **Traçabilité** : Tous les événements peuvent être loggés et analysés
- **Extensibilité** : Facile d'ajouter de nouveaux listeners sans modifier le code existant
- **Asynchrone** : Les listeners peuvent être en queue pour ne pas bloquer les requêtes utilisateur

## Authentification & Autorisation

### Authentification

**Laravel Sanctum** : Authentification par tokens pour l'API

**Fonctionnement** :
- Inscription/Connexion génère un token Sanctum
- Le token est envoyé dans le header `Authorization: Bearer {token}`
- Middleware `auth:sanctum` protège les routes API

**MVP** :
- Authentification simple : joueur connecté / non connecté
- Pas de système de rôles ou permissions pour le moment
- Tous les joueurs ont les mêmes droits d'accès

**Routes protégées** :
- Toutes les routes `/api/users/*` nécessitent l'authentification (`auth:sanctum`)
- Toutes les routes `/api/planets/*` nécessitent l'authentification (`auth:sanctum`)
- Routes `/api/auth/logout` et `/api/auth/user` nécessitent l'authentification (`auth:sanctum`)
- Routes `/api/auth/register` et `/api/auth/login` sont publiques (pas d'authentification requise)

**Autorisation** :
- Un utilisateur ne peut modifier que son propre profil (`PUT /api/users/{id}` vérifie que `auth()->id() === $user->id`)

**Remember Me (Persistance de connexion)** :

La fonctionnalité "Remember Me" permet aux utilisateurs de rester connectés même après la fermeture du navigateur.

**Comportement pour les connexions Web (Livewire)** :
- Lors de la connexion via le formulaire Livewire, l'utilisateur peut cocher la checkbox "Se souvenir de moi"
- Si cochée, Laravel crée un cookie "Remember Me" avec une durée de vie prolongée (30 jours par défaut)
- Le cookie utilise le champ `remember_token` dans la table `users` pour la validation
- La déconnexion invalide automatiquement le cookie Remember Me

**Comportement pour les connexions API (Sanctum)** :
- Pour les clients API externes utilisant Sanctum, les tokens ont déjà une durée de vie longue
- Le paramètre `remember` dans `POST /api/auth/login` affecte principalement la session web si utilisée
- Les tokens Sanctum sont indépendants du mécanisme Remember Me des sessions web
- Les tokens Sanctum persistent jusqu'à leur révocation explicite ou expiration

**Configuration de sécurité** :
- Les cookies Remember Me respectent la configuration de session dans `config/session.php` :
  - `SESSION_HTTP_ONLY` : `true` (protection XSS) - configuré par défaut
  - `SESSION_SECURE_COOKIE` : doit être `true` en production (HTTPS uniquement)
  - `SESSION_SAME_SITE` : `lax` par défaut (protection CSRF)
- La durée de vie du cookie Remember Me est gérée par Laravel (30 jours par défaut)
- Cette durée est différente de `SESSION_LIFETIME` (120 minutes pour les sessions normales)

**Réinitialisation de mot de passe** :

Lors de la réinitialisation de mot de passe réussie :
- Tous les tokens Remember Me de l'utilisateur sont invalidés
- Toutes les sessions web de l'utilisateur sont invalidées
- Un email de confirmation est envoyé à l'utilisateur
- L'événement `PasswordResetCompleted` est dispatché pour la traçabilité

**Service** : `PasswordResetService` dans `app/Services/`
- `sendResetLink(string $email)` : Envoie le lien de réinitialisation
- `reset(array $credentials)` : Réinitialise le mot de passe et invalide les sessions
- `invalidateRememberMe(User $user)` : Invalide tous les tokens Remember Me
- `invalidateSessions(User $user)` : Invalide toutes les sessions web

**Évolutions futures** :
- Système de rôles (admin, modérateur, joueur)
- Permissions granulaires
- Invalidation automatique du Remember Me lors du changement de mot de passe
- [À compléter selon les besoins]

## Génération de planètes

### Architecture de génération

**Principe** : Système de génération procédurale avec pool de types pondérés

**Composants** :
- **Service de génération** : `PlanetGeneratorService` dans `app/Services/`
- **Configuration des types** : Pool de types de planètes avec poids de probabilité dans `config/planets.php`
- **Randomisation** : Sélection aléatoire pondérée du type, puis génération des caractéristiques selon les poids du type
- **Gestion d'unicité** : Mécanisme de vérification d'unicité du nom avec gestion des collisions (max 10 tentatives, puis ajout d'un identifiant unique)

**Flux technique** :
1. Listener `GenerateHomePlanet` appelle `PlanetGeneratorService::generate()`
2. Service sélectionne un type selon les poids définis dans `config/planets.php`
3. Service génère les caractéristiques selon les distributions du type
4. Service génère un nom unique (avec gestion des collisions si nécessaire)
5. Service génère une description textuelle à partir des caractéristiques combinées
6. Service crée l'entité `Planet` en base de données
7. Planète assignée au joueur (`home_planet_id`)

**Gestion d'erreurs** :
- Le listener `GenerateHomePlanet` utilise un try-catch pour gérer les erreurs
- En cas d'erreur de génération, l'erreur est loggée mais l'inscription n'est pas bloquée
- `home_planet_id` reste null en cas d'erreur (peut être géré plus tard)

**Stockage** : Configuration des types et poids dans `config/planets.php` (fichier de configuration Laravel standard)

*Note : Les détails métier (types de planètes, caractéristiques) sont documentés dans PROJECT_BRIEF.md*

## Frontend - Livewire Components

### Architecture Frontend

**Approche hybride** :
- **Livewire** : Utilise directement les services Laravel (`AuthService`, etc.) sans passer par l'API. Les composants Livewire appellent les services directement pour une meilleure performance et simplicité.
- **API REST** : Disponible pour les clients externes (applications mobiles, SPAs distants, etc.) via Sanctum tokens.

### Authentification

**Double authentification** :
- **API** : Authentification par tokens Sanctum (`Authorization: Bearer {token}`) pour les clients externes
- **Routes Web (Livewire)** : Authentification par session Laravel (`Auth::login($user)`) pour les routes web

**Fonctionnement Livewire** :
1. Les composants Livewire utilisent directement `AuthService` pour l'inscription/connexion
2. L'utilisateur est authentifié en session (`Auth::login($user)`)
3. Les routes web utilisent le middleware `auth` pour protéger les pages
4. Pas d'appels API depuis Livewire - utilisation directe des services

**Fonctionnement API (clients externes)** :
1. Les clients externes appellent les endpoints `/api/auth/register` ou `/api/auth/login`
2. Un token Sanctum est créé et retourné dans la réponse JSON
3. Le client utilise ce token dans le header `Authorization: Bearer {token}` pour les requêtes suivantes
4. Les routes API utilisent le middleware `auth:sanctum` pour protéger les endpoints

**Services utilisés par Livewire** :
- `AuthService::register()` / `AuthService::registerFromArray()` : Inscription
- `AuthService::login()` / `AuthService::loginFromCredentials()` : Connexion
- `AuthService::logout()` : Déconnexion
- `Auth::user()` : Récupération de l'utilisateur authentifié

### Composants Livewire (MVP)

- **Register** : Formulaire d'inscription (`/register`)
- **LoginTerminal** : Formulaire de connexion avec style terminal (`/login`)
- **ForgotPassword** : Formulaire de demande de réinitialisation de mot de passe (`/forgot-password`)
- **ResetPassword** : Formulaire de réinitialisation de mot de passe avec indicateur de force (`/reset-password/{token}`)
- **Dashboard** : Affichage de la planète d'origine (`/dashboard`)
- **Profile** : Gestion du profil utilisateur (`/profile`)

**Routes Web** :
- `/` : Page d'accueil (publique)
- `/register` : Inscription (guest)
- `/login` : Connexion (guest)
- `/forgot-password` : Demande de réinitialisation de mot de passe (guest)
- `/reset-password/{token}` : Formulaire de réinitialisation (guest)
- `/dashboard` : Tableau de bord (auth)
- `/profile` : Profil utilisateur (auth)
- `POST /logout` : Déconnexion (auth)

## Aspects techniques standards

### Validation des données

- **Form Requests** : Utilisation de `FormRequest` pour valider les données API
- **Validation côté serveur** : Toutes les données sont validées avant traitement
- **Messages d'erreur** : Format JSON standardisé pour les erreurs de validation

### Gestion des erreurs

- **Exceptions** : Gestion centralisée via `app/Exceptions/Handler.php`
- **Format de réponse** : Erreurs API au format JSON standardisé
- **Codes HTTP** : Utilisation appropriée des codes de statut (200, 201, 400, 401, 404, 500, etc.)

### Pagination

- **Collections Laravel** : Utilisation de `paginate()` pour les listes
- **Format** : Pagination Laravel standard avec métadonnées (links, meta)
- **Taille par défaut** : [À définir]

### Cache

- **Redis** : Utilisation de Redis pour le cache des données fréquemment accédées
- **Stratégie** : Cache des planètes, des listes, etc. (à définir selon les besoins)

