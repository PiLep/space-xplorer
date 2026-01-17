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
- **StarSystems** : Systèmes stellaires contenant des étoiles et planètes
- **Planets** : Planètes explorables appartenant à un système stellaire
- **PlanetProperties** : Propriétés détaillées des planètes (type, taille, température, etc.)
- **WikiEntry** : Articles du Codex (encyclopédie spatiale) pour les planètes découvertes
- **WikiContribution** : Contributions des joueurs aux articles du Codex

### Relations

- **Users → Planets** : Relation "planète d'origine"
  - Un joueur a une planète d'origine (générée aléatoirement à l'inscription)
  - Une planète peut être la planète d'origine de plusieurs joueurs
  - Relation : `user.home_planet_id` → `planets.id`

- **StarSystems → Planets** : Relation "système contient planètes"
  - Un système stellaire contient plusieurs planètes (1 à 7 planètes)
  - Une planète appartient à un système stellaire
  - Relation : `planets.star_system_id` → `star_systems.id`

- **Planets → PlanetProperties** : Relation "propriétés planétaires"
  - Une planète a des propriétés détaillées (type, taille, température, etc.)
  - Relation : `planet_properties.planet_id` → `planets.id`

- **Planets → WikiEntry** : Relation "article Codex"
  - Une planète a un article dans le Codex (encyclopédie spatiale)
  - Relation : `wiki_entries.planet_id` → `planets.id` (unique)
  - Un article peut être nommé par le découvreur

- **Users → WikiEntry** : Relation "découvreur"
  - Un utilisateur peut découvrir des planètes
  - Relation : `wiki_entries.discovered_by_user_id` → `users.id`

- **WikiEntry → WikiContribution** : Relation "contributions"
  - Un article peut avoir plusieurs contributions de joueurs
  - Relation : `wiki_contributions.wiki_entry_id` → `wiki_entries.id`

### Structure simplifiée

```
Users
├── id (ULID - 26 caractères)
├── name
├── email
├── home_planet_id (ULID - foreign key → planets.id)
└── [autres champs standards Laravel]

StarSystems
├── id (ULID - 26 caractères)
├── name (ex: "Alpha Centauri-42")
├── x, y, z (coordonnées 3D dans la galaxie)
├── star_type (yellow_dwarf, red_dwarf, etc.)
├── planet_count (nombre de planètes)
├── discovered (boolean)
└── created_at / updated_at

Planets
├── id (ULID - 26 caractères)
├── name (généré aléatoirement)
├── star_system_id (ULID - foreign key → star_systems.id)
├── x, y, z (coordonnées absolues 3D)
├── orbital_distance, orbital_angle, orbital_inclination (coordonnées orbitales)
├── image_url, video_url (médias générés)
└── created_at / updated_at

PlanetProperties
├── id (ULID - 26 caractères)
├── planet_id (ULID - foreign key → planets.id)
├── type (tellurique, gazeuse, glacée, désertique, océanique)
├── size (petite, moyenne, grande)
├── temperature (froide, tempérée, chaude)
├── atmosphere (respirable, toxique, inexistante)
├── terrain (rocheux, océanique, désertique, forestier, etc.)
├── resources (abondantes, modérées, rares)
├── description (générée à partir des caractéristiques)
└── created_at / updated_at

WikiEntry
├── id (ULID - 26 caractères)
├── planet_id (ULID - foreign key → planets.id, unique)
├── name (nullable, nom donné par le joueur)
├── fallback_name (nom technique auto-généré)
├── description (text, description générée par IA)
├── discovered_by_user_id (ULID - foreign key → users.id, nullable)
├── is_named (boolean, si la planète a été nommée)
├── is_public (boolean, visibilité publique)
└── created_at / updated_at

WikiContribution
├── id (ULID - 26 caractères)
├── wiki_entry_id (ULID - foreign key → wiki_entries.id)
├── contributor_user_id (ULID - foreign key → users.id)
├── content_type (string, type de contribution)
├── content (text, contenu de la contribution)
├── status (enum: pending, approved, rejected)
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
- `wiki_entries` : ID en ULID
- `wiki_contributions` : ID en ULID
- Toutes les futures tables métier utiliseront des ULIDs

**Implémentation Laravel** :
- Utilisation du trait `Illuminate\Database\Eloquent\Concerns\HasUlids` dans les modèles
- Migration avec `$table->ulid('id')->primary()` au lieu de `$table->id()`
- Les relations Eloquent fonctionnent automatiquement avec les ULIDs

## Organisation spatiale : Systèmes stellaires et planètes

### Architecture spatiale

L'univers de Stellar est organisé en **systèmes stellaires** contenant des **planètes**. Chaque système stellaire possède une étoile centrale autour de laquelle orbitent plusieurs planètes.

### Hiérarchie spatiale

```
Galaxie (espace 3D)
  └── Systèmes stellaires (StarSystem)
        ├── Étoile centrale (star_type)
        └── Planètes (Planet)
              └── Propriétés planétaires (PlanetProperty)
```

### Systèmes stellaires (StarSystem)

**Modèle** : `App\Models\StarSystem`

**Structure de la table `star_systems`** :

```sql
star_systems
├── id (ULID) - Identifiant unique
├── name (string) - Nom du système (ex: "Alpha Centauri-42")
├── x (decimal 15,2) - Position X dans la galaxie
├── y (decimal 15,2) - Position Y dans la galaxie
├── z (decimal 15,2) - Position Z dans la galaxie
├── star_type (string, nullable) - Type d'étoile
├── planet_count (integer) - Nombre de planètes dans le système
├── discovered (boolean) - Système découvert ou non
└── timestamps
```

**Types d'étoiles** (probabilités définies dans `StarSystemGeneratorService`) :
- `yellow_dwarf` : 35% (comme le Soleil)
- `red_dwarf` : 40% (très commun)
- `orange_dwarf` : 15% (type K)
- `red_giant` : 5% (étoiles évoluées)
- `blue_giant` : 3% (rare)
- `white_dwarf` : 2% (très rare)

**Distribution du nombre de planètes par système** :
- 1 planète : 10%
- 2 planètes : 15%
- 3 planètes : 25%
- 4 planètes : 25%
- 5 planètes : 15%
- 6 planètes : 8%
- 7 planètes : 2%

**Coordonnées spatiales** :
- Les systèmes stellaires sont positionnés dans un espace 3D avec des coordonnées `(x, y, z)`
- Distance minimale entre systèmes : 50 unités (configurable dans `config/star-systems.php`)
- Rayon d'exploration par défaut : 200 unités
- Coordonnées générées aléatoirement dans une sphère (distance minimale depuis l'origine : 100 unités)

**Relations** :
- `planets()` : HasMany → Toutes les planètes du système

**Méthodes utilitaires** :
- `distanceTo(StarSystem $other): float` - Calcule la distance à un autre système
- `nearby(float $x, float $y, float $z, float $radius): Collection` - Trouve les systèmes proches

### Planètes (Planet)

**Modèle** : `App\Models\Planet`

**Structure de la table `planets`** :

```sql
planets
├── id (ULID) - Identifiant unique
├── name (string) - Nom de la planète (généré aléatoirement)
├── image_url (string, nullable) - URL de l'image générée
├── video_url (string, nullable) - URL de la vidéo générée
├── image_generating (boolean) - Image en cours de génération
├── video_generating (boolean) - Vidéo en cours de génération
├── star_system_id (ULID, nullable) - Système stellaire parent (FK)
├── x (decimal 15,2, nullable) - Position X absolue dans la galaxie
├── y (decimal 15,2, nullable) - Position Y absolue dans la galaxie
├── z (decimal 15,2, nullable) - Position Z absolue dans la galaxie
├── orbital_distance (decimal 10,2, nullable) - Distance à l'étoile (unités arbitraires)
├── orbital_angle (decimal 8,4, nullable) - Angle orbital (0-360°)
├── orbital_inclination (decimal 6,2, nullable) - Inclinaison orbitale (-90 à +90°)
└── timestamps
```

**Propriétés planétaires** (table séparée `planet_properties`) :
- `type` : Type de planète (tellurique, gazeuse, glacée, désertique, océanique)
- `size` : Taille (petite, moyenne, grande)
- `temperature` : Température (froide, tempérée, chaude)
- `atmosphere` : Atmosphère (respirable, toxique, inexistante)
- `terrain` : Terrain (rocheux, océanique, désertique, forestier, urbain, mixte, glacé)
- `resources` : Ressources (abondantes, modérées, rares)
- `description` : Description textuelle générée

**Coordonnées spatiales** :

Les planètes utilisent un système de coordonnées à deux niveaux :

1. **Coordonnées orbitales** (relatives au système) :
   - `orbital_distance` : Distance à l'étoile centrale (5.0 à 50.0 unités)
   - `orbital_angle` : Angle dans le plan orbital (0-360°)
   - `orbital_inclination` : Inclinaison du plan orbital (-15° à +15°)

2. **Coordonnées absolues** (dans la galaxie) :
   - `x`, `y`, `z` : Position 3D absolue calculée depuis les coordonnées orbitales et la position du système
   - Calcul : `position_absolue = position_système + conversion_orbitales_vers_3D(orbital_distance, orbital_angle, orbital_inclination)`

**Conversion orbitales → absolues** :

```php
// Formule de conversion (dans StarSystemGeneratorService)
$x = $orbitalDistance * cos($angleRad);
$y = $orbitalDistance * sin($angleRad) * cos($inclinationRad);
$z = $orbitalDistance * sin($angleRad) * sin($inclinationRad);

// Position absolue
$absoluteX = $systemX + $x;
$absoluteY = $systemY + $y;
$absoluteZ = $systemZ + $z;
```

**Relations** :
- `starSystem()` : BelongsTo → Système stellaire parent
- `users()` : HasMany → Utilisateurs ayant cette planète comme planète d'origine
- `properties()` : HasOne → Propriétés planétaires détaillées

**Méthodes utilitaires** :
- `distanceTo(Planet $other): float` - Calcule la distance à une autre planète
- `travelTimeTo(Planet $other, float $speed = 1.0): float` - Calcule le temps de voyage (en heures)
- `nearby(float $x, float $y, float $z, float $radius): Collection` - Trouve les planètes proches
- `hasImage(): bool` - Vérifie si l'image est disponible
- `hasVideo(): bool` - Vérifie si la vidéo est disponible

**Attributs calculés** (via `PlanetProperty`) :
- `type`, `size`, `temperature`, `atmosphere`, `terrain`, `resources`, `description`

### Génération des systèmes stellaires

**Service** : `App\Services\StarSystemGeneratorService`

**Méthode principale** : `generateSystem(?float $x = null, ?float $y = null, ?float $z = null, float $minDistance = 100): StarSystem`

**Processus de génération** :

1. **Génération des coordonnées** :
   - Si non fournies, génère des coordonnées aléatoires dans une sphère
   - Distance minimale depuis l'origine : 100 unités (par défaut)

2. **Sélection du type d'étoile** :
   - Sélection pondérée selon les probabilités définies

3. **Génération du nom** :
   - Format : `"{prefix} {suffix}-{number}"`
   - Exemples : "Alpha Centauri-42", "Beta Orionis-789"

4. **Création du système** :
   - Création de l'entité `StarSystem` avec `discovered = false` et `planet_count = 0`

5. **Génération des planètes** :
   - Sélection du nombre de planètes selon la distribution
   - Pour chaque planète :
     - Génération via `PlanetGeneratorService`
     - Calcul des coordonnées orbitales (distance, angle, inclinaison)
     - Conversion en coordonnées absolues
     - Assignation au système

6. **Mise à jour du compteur** :
   - Mise à jour de `planet_count` avec le nombre réel de planètes

**Génération de systèmes proches** :

`generateNearbySystem(float $x, float $y, float $z, float $minDistance = 50, float $maxDistance = 200): StarSystem`

- Génère un système dans un rayon autour d'une position donnée
- Utilisé pour l'exploration (génération de systèmes découverts)

### Planètes d'origine (Home Planets)

**Principe** : Chaque joueur possède une planète d'origine unique générée à l'inscription.

**Organisation** :
- Chaque planète d'origine obtient son **propre système stellaire dédié**
- Un système = une planète d'origine (pas de partage entre joueurs)
- Le système est créé automatiquement lors de la génération de la planète d'origine

**Flux** :
1. Inscription utilisateur → Événement `UserRegistered`
2. Listener `GenerateHomePlanet` :
   - Génère un système stellaire complet
   - Supprime les planètes générées automatiquement
   - Génère une planète d'origine unique
   - Assigne la planète au système (position orbitale : distance 10.0, angle 0°, inclinaison 0°)
   - Met à jour `planet_count = 1`
   - Assigne la planète au joueur (`home_planet_id`)

**Migration des planètes existantes** :

> **Note** : La migration des planètes existantes vers le nouveau système de coordonnées a été effectuée. La commande `planets:migrate-coordinates` a été supprimée car il s'agissait d'une migration ponctuelle qui ne sera plus utilisée.

### Recherches spatiales

**Recherche de systèmes proches** :

```php
StarSystem::nearby($x, $y, $z, $radius);
```

- Utilise un index spatial sur `(x, y, z)`
- Filtre avec `whereBetween` puis calcule la distance réelle
- Retourne les systèmes dans le rayon spécifié

**Recherche de planètes proches** :

```php
Planet::nearby($x, $y, $z, $radius);
```

- Même principe que pour les systèmes
- Utilise l'index spatial sur `(x, y, z)`

**Calcul de distance** :

- Formule euclidienne 3D : `sqrt((x1-x2)² + (y1-y2)² + (z1-z2)²)`
- Implémentée dans les méthodes `distanceTo()` des modèles

### Configuration

**Fichiers de configuration** :

- `config/star-systems.php` :
  - `generation.min_distance_between_systems` : 50.0
  - `generation.exploration_radius` : 200.0
  - `generation.max_nearby_systems` : 10
  - `travel.base_speed` : 1.0 (unités par heure)
  - `travel.speed_multiplier` : Multiplicateurs selon le type d'étoile

- `config/planets.php` :
  - Types de planètes avec probabilités et caractéristiques
  - Préfixes et suffixes pour la génération de noms

### Index de performance

**Index spatiaux** :
- `star_systems` : Index sur `(x, y, z)` et `discovered`
- `planets` : Index sur `(x, y, z)` et `star_system_id`

**Optimisations** :
- Les recherches spatiales utilisent `whereBetween` pour filtrer rapidement
- Le calcul de distance exacte se fait ensuite en mémoire sur le résultat filtré
- Le compteur `planet_count` évite de compter les planètes à chaque requête

### Évolutions futures

**Fonctionnalités prévues** :
- Exploration de systèmes stellaires (découverte progressive)
- Voyage entre planètes (calcul de temps de trajet basé sur la distance)
- Systèmes multi-étoiles (binaires, ternaires)
- Objets célestes additionnels (astéroïdes, lunes, stations spatiales)
- Animation orbitale (mouvement des planètes autour de l'étoile)
- Zones d'influence des systèmes stellaires

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

### Vérification d'email

**Routes Web** :
- `GET /email/verify` - Page de vérification d'email (middleware `auth`, composant Livewire `VerifyEmail`)

**Fonctionnement** :
- Après l'inscription, un code de vérification à 6 chiffres est généré et envoyé par email
- L'utilisateur est redirigé vers `/email/verify` pour saisir le code
- Lors de la connexion, si l'email n'est pas vérifié, l'utilisateur est redirigé vers `/email/verify`
- Le code est hashé avant stockage dans la base de données (sécurité)
- Le code expire après 15 minutes
- Maximum 5 tentatives de vérification par code
- Cooldown de 2 minutes entre les renvois de code

**Sécurité** :
- Codes générés de manière cryptographiquement sécurisée (`random_int(100000, 999999)`)
- Codes hashés avant stockage (utiliser `Hash::make()`)
- Vérification avec `Hash::check()` pour comparer le code saisi avec le hash stocké
- Expiration après 15 minutes
- Limitation à 5 tentatives de vérification par code
- Limitation à 1 renvoi toutes les 2 minutes
- Vérification que l'utilisateur correspond bien au code (pas de vérification croisée entre utilisateurs)

**Service** :
- `EmailVerificationService` : Service de gestion de la vérification d'email
  - `generateCode(User $user): string` - Génère un code, le hash, le stocke, l'envoie par email
  - `verifyCode(User $user, string $code): bool` - Vérifie le code et marque l'email comme vérifié
  - `resendCode(User $user): void` - Génère et envoie un nouveau code
  - `isCodeValid(User $user, string $code): bool` - Vérifie si le code est valide (sans incrémenter tentatives)
  - `clearVerificationCode(User $user): void` - Nettoie le code après vérification

**Modèle User** :
- Champs ajoutés pour la vérification d'email :
  - `email_verification_code` (string, nullable) - Code hashé
  - `email_verification_code_expires_at` (timestamp, nullable) - Date d'expiration
  - `email_verification_attempts` (integer, default: 0) - Nombre de tentatives
  - `email_verification_code_sent_at` (timestamp, nullable) - Date d'envoi du dernier code
- Méthodes helper :
  - `hasVerifiedEmail()` - Vérifie si l'email est vérifié
  - `hasPendingVerificationCode()` - Vérifie si un code est en attente et non expiré
  - `canResendVerificationCode()` - Vérifie si le renvoi est autorisé (2 minutes écoulées)
  - `hasExceededVerificationAttempts()` - Vérifie si les tentatives max sont atteintes (5)

**Composant Livewire** :
- `VerifyEmail` : Composant pour la page de vérification avec style terminal
  - Formatage automatique du code (6 chiffres uniquement)
  - Messages d'erreur spécifiques selon les cas
  - Feedback visuel des tentatives avec couleurs adaptées
  - Compteur de cooldown pour le renvoi
  - Email masqué pour la sécurité

**Mailable** :
- `EmailVerificationNotification` : Email contenant le code de vérification à 6 chiffres
  - Template HTML et texte avec style terminal cohérent
  - Code affiché de manière proéminente
  - Instructions claires sur où saisir le code

**Flux** :
1. Inscription → Génération et envoi du code → Redirection vers `/email/verify`
2. Connexion avec email non vérifié → Redirection vers `/email/verify`
3. Page de vérification → Saisie du code → Validation → Marquer email comme vérifié → Redirection vers dashboard
4. Possibilité de renvoyer le code avec limitation de fréquence (cooldown de 2 minutes)

**Comportement MVP** :
- Pour le MVP, on ne bloque pas l'accès aux fonctionnalités si l'email n'est pas vérifié (sauf redirection après login)
- Redirection vers la vérification après inscription et après connexion si email non vérifié
- Dans une version future, on pourra bloquer certaines fonctionnalités si l'email n'est pas vérifié

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

### Endpoints Codex (Stellarpedia) - Public

- `GET /api/codex/planets` - Liste paginée des planètes du Codex (public, rate limit: 60/min)
  - Query params : `page`, `per_page`, `type`, `size`, `temperature`, `search`
  - Retourne les articles wiki avec pagination

- `GET /api/codex/planets/{id}` - Détails d'un article Codex (public, rate limit: 60/min)
  - Retourne les détails complets d'une planète avec caractéristiques et description

- `GET /api/codex/search` - Recherche avec autocomplétion (public, rate limit: 60/min)
  - Query params : `q` (query string, min 2 caractères)
  - Retourne les résultats de recherche par nom/fallback_name

### Endpoints Codex (Stellarpedia) - Authentifiés

- `POST /api/codex/planets/{id}/name` - Nommer une planète (authentification requise, rate limit: 5/min)
  - Request body : `{ "name": "string" }`
  - Validation : longueur 3-50 caractères, caractères autorisés, unicité, mots interdits
  - Seul le découvreur peut nommer sa planète

- `POST /api/codex/planets/{id}/contribute` - Contribuer à un article (authentification requise, rate limit: 5/min)
  - Request body : `{ "content": "string" }`
  - Validation : longueur 10-5000 caractères
  - Crée une contribution en statut "pending" (modération requise)

### Format de réponse

Toutes les réponses API suivent un format JSON standardisé :
```json
{
  "data": { ... },
  "message": "Success message",
  "status": "success"
}
```

## Systèmes de communication

### Inbox (Boîte mail Stellar)

**Rôle** : Boîte mail Stellar (employé) - Messages narratifs de la compagnie Stellar

**Modèle** : `App\Models\Message`

**Structure de la table `messages`** :
- `id` (ULID) - Identifiant unique
- `sender_id` (ULID, nullable) - Expéditeur (null pour messages système)
- `recipient_id` (ULID) - Destinataire (FK → users.id)
- `type` (string) - Type de message (welcome, discovery, mission, alert, system)
- `subject` (string) - Sujet du message
- `content` (text) - Contenu narratif complet
- `is_read` (boolean) - Statut de lecture
- `read_at` (timestamp, nullable) - Date de lecture
- `is_important` (boolean) - Message important
- `metadata` (JSON, nullable) - Métadonnées additionnelles

**Service** : `App\Services\MessageService`

**Composant Livewire** : `App\Livewire\Inbox`

**Routes** :
- `GET /inbox` - Page de la boîte mail (middleware `auth`)

**Fonctionnalités** :
- Affichage des messages avec filtres (all, unread, read)
- Marquer comme lu/non lu
- Supprimer des messages
- Messages importants mis en évidence

### Notifications (Système d'événements du jeu)

**Rôle** : Système d'événements du jeu - Alertes courtes qui pointent vers les systèmes concernés (vaisseau, inventaire, missions, inbox)

**Modèle** : `App\Models\Notification`

**Structure de la table `notifications`** :
- `id` (ULID) - Identifiant unique
- `user_id` (ULID) - Destinataire (FK → users.id, onDelete cascade)
- `type` (string) - Type de notification (`message_important`, `ship_assigned`, `resource_added`, etc.)
- `title` (string) - Titre court de la notification
- `message` (text) - Message court (max 200 caractères)
- `data` (JSON, nullable) - Données additionnelles (ID vaisseau, quantité ressources, lien vers message, etc.)
- `is_read` (boolean, default: false) - Statut de lecture
- `read_at` (timestamp, nullable) - Date de lecture
- `created_at`, `updated_at` - Timestamps

**Index** :
- `user_id` - Pour requêtes par utilisateur
- `is_read` - Pour filtres unread/read
- `type` - Pour filtres par type
- `created_at` - Pour tri chronologique
- `(user_id, is_read)` - Index composite pour requêtes combinées
- `(user_id, created_at)` - Index composite pour notifications triées par date

**Service** : `App\Services\NotificationService`

**Méthodes du service** :
- `create(User $user, string $type, string $title, string $message, array $data = []): Notification` - Créer une notification
- `markAsRead(Notification $notification): bool` - Marquer comme lue
- `markAllAsRead(User $user): int` - Marquer toutes comme lues
- `getUnreadCount(User $user): int` - Compter les notifications non lues
- `getNotificationsForUser(User $user, int $limit = 20): Collection` - Récupérer les notifications récentes
- `getUnreadNotificationsForUser(User $user, int $limit = 10): Collection` - Récupérer les non lues

**Scopes du modèle** :
- `scopeUnread(Builder $query)` - Notifications non lues
- `scopeRead(Builder $query)` - Notifications lues
- `scopeForUser(Builder $query, User $user)` - Notifications d'un utilisateur (sécurité)
- `scopeByType(Builder $query, string $type)` - Filtrer par type

**Méthodes du modèle** :
- `markAsRead(): bool` - Marquer comme lue
- `markAsUnread(): bool` - Marquer comme non lue

**Configuration** : `config/notifications.php`
- Types de notifications autorisés avec métadonnées
- Longueur maximale des messages (200 caractères par défaut)
- Limites par défaut (20 pour page, 10 pour dropdown)

**Composants Livewire** :
- `App\Livewire\NotificationBadge` - Badge dans la navigation avec compteur
- `App\Livewire\Notifications` - Page complète de notifications avec filtres et pagination

**Routes** :
- `GET /notifications` - Page de notifications (middleware `auth`)

**Génération automatique** :
- **Listener** : `App\Listeners\CreateNotificationOnImportantMessage`
- **Événement déclencheur** : `MessageReceived` (si `is_important = true`)
- **Comportement** : Crée une notification courte qui pointe vers l'inbox (`/inbox?message={id}`) sans dupliquer le contenu du message

**Types de notifications MVP** :
- `message_important` : Message important reçu dans l'inbox
  - Redirige vers `/inbox?message={message_id}`
  - Données : `message_id`, `inbox_url`
- `ship_assigned` : Vaisseau attribué (futur)
  - Redirige vers `/ships/{ship_id}`
- `resource_added` : Nouvelles ressources ajoutées (futur)
  - Redirige vers `/inventory`

**Distinction Notifications vs Inbox** :
- **Inbox** : Messages narratifs détaillés, consultables à tout moment, statut de lecture indépendant
- **Notifications** : Alertes courtes (max 200 caractères) qui pointent vers les systèmes du jeu, statut de lecture indépendant
- Les notifications ne dupliquent pas le contenu des messages, elles sont des alertes qui redirigent vers le système concerné

**Performance** :
- Utilisation de `#[Computed]` dans Livewire pour cache automatique
- Polling optionnel toutes les 30 secondes pour le badge
- Index optimisés pour requêtes fréquentes
- Limites par défaut pour éviter la surcharge

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

**📚 Documentation complète** : Voir [`docs/EVENTS.md`](../../EVENTS.md) pour la liste complète de tous les événements disponibles et leur utilisation.

**Événements principaux** :

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

#### Communication et messages

##### `MessageReceived`

**Déclencheur** : Lorsqu'un message est créé et envoyé à un utilisateur (via `MessageService`)

**Données** : Message, User (recipient)

**Listeners** :
- `CreateNotificationOnImportantMessage` : Crée une notification si le message est important (`is_important = true`)
  - Crée une notification de type `message_important` qui pointe vers l'inbox
  - La notification est une alerte courte (max 200 caractères) qui ne duplique pas le contenu du message
  - Redirige vers `/inbox?message={message_id}` pour ouvrir le message dans l'inbox
  - Gère les erreurs gracieusement sans bloquer la création du message

**Flux** :
1. `MessageService` crée un message et dispatch `MessageReceived`
2. Si le message est important (`is_important = true`), le listener `CreateNotificationOnImportantMessage` crée une notification
3. La notification apparaît dans le badge de notifications et redirige vers l'inbox

#### Cycle de vie planète

##### `PlanetCreated`

**Déclencheur** : Lors de la création d'une planète (par `PlanetGeneratorService`)

**Listeners** :
- `GeneratePlanetImage` : Génère automatiquement une image de la planète (en queue)
- `GeneratePlanetVideo` : Génère automatiquement une vidéo de la planète (en queue)
- `CreateWikiEntryOnPlanetCreated` : Crée automatiquement un article Codex pour la planète
  - Génère un nom de fallback basé sur le type de planète
  - Génère une description via IA (AIDescriptionService)
  - Assigne le découvreur si c'est une planète d'origine (via home_planet_id)

**Flux** :
1. `PlanetGeneratorService` crée la planète
2. Événement `PlanetCreated` est dispatché
3. Listeners génèrent les médias (asynchrone) et dispatchent les événements de complétion
4. Listener crée l'article Codex avec description IA

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

**Déclencheur** : Lorsqu'un joueur explore une planète

**Données** : Utilisateur, planète explorée

**Listeners** :
- `CreateWikiEntryOnPlanetExplored` : Crée un article Codex si inexistant
  - Vérifie l'existence d'un article avant de créer
  - Génère une description via IA si nécessaire
  - Assigne le découvreur (utilisateur qui explore)
- `SendPlanetDiscoveryMessage` : Envoie un message de découverte au joueur

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

**Principe** : Système de génération procédurale avec pool de types pondérés, intégré dans le système de systèmes stellaires.

**Composants** :
- **Service de génération de planètes** : `PlanetGeneratorService` dans `app/Services/`
- **Service de génération de systèmes** : `StarSystemGeneratorService` dans `app/Services/`
- **Configuration des types** : Pool de types de planètes avec poids de probabilité dans `config/planets.php`
- **Randomisation** : Sélection aléatoire pondérée du type, puis génération des caractéristiques selon les poids du type
- **Gestion d'unicité** : Mécanisme de vérification d'unicité du nom avec gestion des collisions (max 10 tentatives, puis ajout d'un identifiant unique)

**Flux technique pour planète d'origine** :
1. Listener `GenerateHomePlanet` appelle `StarSystemGeneratorService::generateSystem()`
2. Service génère un système stellaire complet avec ses planètes
3. Service supprime les planètes générées automatiquement
4. Service génère une planète d'origine unique via `PlanetGeneratorService::generate()`
5. Service calcule les coordonnées orbitales (distance 10.0, angle 0°, inclinaison 0°)
6. Service convertit les coordonnées orbitales en coordonnées absolues 3D
7. Service assigne la planète au système (`star_system_id`, coordonnées)
8. Service met à jour `planet_count = 1` du système
9. Planète assignée au joueur (`home_planet_id`)

**Flux technique pour planètes dans un système** :
1. `StarSystemGeneratorService::generateSystem()` sélectionne le nombre de planètes (1-7)
2. Pour chaque planète :
   - Appel de `PlanetGeneratorService::generate()`
   - Sélection d'un type selon les poids définis dans `config/planets.php`
   - Génération des caractéristiques selon les distributions du type
   - Génération d'un nom unique (avec gestion des collisions si nécessaire)
   - Génération d'une description textuelle à partir des caractéristiques combinées
   - Calcul des coordonnées orbitales (distance, angle, inclinaison)
   - Conversion en coordonnées absolues 3D
   - Création de l'entité `Planet` avec assignation au système

**Génération de planète isolée** (`PlanetGeneratorService::generate()`) :
1. Service sélectionne un type selon les poids définis dans `config/planets.php`
2. Service génère les caractéristiques selon les distributions du type
3. Service génère un nom unique (avec gestion des collisions si nécessaire)
4. Service génère une description textuelle à partir des caractéristiques combinées
5. Service crée l'entité `Planet` en base de données (sans coordonnées ni système)
6. Les coordonnées et l'assignation au système sont gérées séparément

**Gestion d'erreurs** :
- Le listener `GenerateHomePlanet` utilise un try-catch pour gérer les erreurs
- En cas d'erreur de génération, l'erreur est loggée mais l'inscription n'est pas bloquée
- `home_planet_id` reste null en cas d'erreur (peut être géré plus tard)

**Stockage** : Configuration des types et poids dans `config/planets.php` (fichier de configuration Laravel standard)

**Intégration avec systèmes stellaires** :
- Les planètes sont toujours générées dans le contexte d'un système stellaire
- Les coordonnées spatiales (orbitales et absolues) sont calculées automatiquement
- Voir la section "Organisation spatiale" pour plus de détails sur les systèmes stellaires

*Note : Les détails métier (types de planètes, caractéristiques) sont documentés dans PROJECT_BRIEF.md*

## Codex (Stellarpedia) - Système Wiki

### Architecture du Codex

**Principe** : Encyclopédie spatiale publique accessible à tous (joueurs et non-joueurs) pour afficher toutes les planètes découvertes dans l'univers Stellar.

**Composants** :
- **Service Wiki** : `WikiService` dans `app/Services/` - Gestion des articles Codex
- **Service Génération IA** : `AIDescriptionService` dans `app/Services/` - Génération de descriptions via IA
- **Configuration** : `config/wiki.php` - Règles de validation des noms et contributions
- **Configuration IA** : `config/text-generation.php` - Configuration pour la génération de texte IA

### Services

#### WikiService

**Méthodes principales** :
- `createEntryForPlanet(Planet $planet, ?User $discoverer = null)` : Crée un article Codex avec génération de description IA
- `generateFallbackName(Planet $planet)` : Génère un nom de fallback technique (ex: "Planète Tellurique #1234")
- `validateName(string $name)` : Valide un nom selon les règles (unicité, longueur, caractères, mots interdits)
- `namePlanet(WikiEntry $entry, User $user, string $name)` : Nomme une planète avec validation complète
- `canUserNamePlanet(WikiEntry $entry, User $user)` : Vérifie si l'utilisateur peut nommer (découvreur uniquement)
- `canUserContribute(WikiEntry $entry, User $user)` : Vérifie si l'utilisateur peut contribuer
- `getEntries(array $filters = [], int $perPage = 20)` : Liste paginée avec filtres (type, size, temperature, search)
- `searchEntries(string $query, int $limit = 10)` : Recherche avec autocomplétion

**Validation des noms** :
- Longueur : 3-50 caractères
- Caractères autorisés : Lettres (a-z, A-Z, accents), chiffres (0-9), espaces, tirets (-), apostrophes (')
- Unicité : Vérification que le nom n'est pas déjà utilisé
- Mots interdits : Liste dans `config/wiki.php` (validation case-insensitive)

#### AIDescriptionService

**Méthodes principales** :
- `generatePlanetDescription(Planet $planet, ?string $provider = null)` : Génère une description basée sur les caractéristiques
- `buildPrompt(Planet $planet)` : Construit le prompt pour l'IA à partir des caractéristiques
- `isProviderConfigured(string $provider)` : Vérifie si un provider est configuré
- `getAvailableProviders()` : Liste des providers disponibles

**Fonctionnalités** :
- Cache des descriptions générées (TTL configurable)
- Retry logic avec exponential backoff
- Fallback vers template pré-écrit en cas d'échec IA
- Support de multiples providers (OpenAI GPT par défaut)

**Configuration** :
- Provider par défaut : OpenAI GPT (`gpt-4o-mini`)
- Cache : Activé par défaut (TTL 24h)
- Retry : 3 tentatives avec délai exponentiel

### Création automatique d'articles

**Listeners** :
- `CreateWikiEntryOnPlanetCreated` : Crée un article lors de la création d'une planète
  - Vérifie si c'est une planète d'origine (via `home_planet_id`)
  - Assigne le découvreur si disponible
- `CreateWikiEntryOnPlanetExplored` : Crée un article lors de l'exploration d'une planète
  - Vérifie l'existence d'un article avant de créer
  - Assigne le découvreur (utilisateur qui explore)

**Flux** :
1. Planète créée/explorée → Événement `PlanetCreated`/`PlanetExplored`
2. Listener crée l'article Codex via `WikiService::createEntryForPlanet()`
3. Service génère le nom de fallback
4. Service génère la description via IA (avec fallback si échec)
5. Article créé avec `is_public = true` par défaut

### Routes Web

- `/codex` : Page d'accueil du Codex (public)
- `/codex/planets/{id}` : Détails d'une planète dans le Codex (public)

**Composants Livewire** :
- `WikiIndex` : Liste des planètes avec recherche et filtres
- `WikiPlanet` : Détails d'une planète avec caractéristiques et description
- `NamePlanet` : Modal pour nommer une planète (authentifié)
- `ContributeToWiki` : Modal pour contribuer (authentifié)

**Note** : Les composants Livewire utilisent directement `WikiService` plutôt que les endpoints API pour une meilleure performance.

## Frontend - Livewire Components

### Architecture Frontend

**Version** : Livewire 3.6 (compatible Laravel 12, PHP 8.2+)

**Approche hybride** :
- **Livewire** : Utilise directement les services Laravel (`AuthService`, etc.) sans passer par l'API. Les composants Livewire appellent les services directement pour une meilleure performance et simplicité.
- **API REST** : Disponible pour les clients externes (applications mobiles, SPAs distants, etc.) via Sanctum tokens.

### Attributs PHP 8 de Livewire 3.6

Livewire 3.6 utilise les attributs PHP 8 pour une syntaxe moderne et déclarative :

- **`#[Layout('layouts.app')]`** : Définit le layout Blade pour le composant (utilisé dans tous les composants)
- **`#[Validate('rules')]`** : Définit les règles de validation directement sur les propriétés (à privilégier au lieu de `protected $rules`)
- **`#[Computed]`** : Marque une méthode comme propriété calculée avec cache automatique
- **`#[On('event')]`** : Écoute un événement Livewire ou Laravel
- **`#[Locked]`** : Empêche la modification d'une propriété depuis le frontend

**Exemple de validation moderne** :
```php
use Livewire\Attributes\Validate;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Register extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255|unique:users')]
    public string $email = '';
}
```

### Performance et Optimisation

**Utilisation de `wire:key`** :
- Toujours utiliser `wire:key` pour les listes dans les vues Livewire
- Format : `wire:key="item-{{ $item->id }}"`
- Optimise les re-renders en identifiant les éléments modifiés

**Debounce pour les champs de saisie** :
- Utiliser `wire:model.debounce.500ms` pour les champs de recherche/saisie fréquents
- Réduit le nombre de requêtes serveur et améliore les performances

**Lazy Loading** :
- Utiliser `wire:model.lazy` pour les champs qui n'ont pas besoin de validation en temps réel
- La validation se déclenche uniquement lors du blur du champ

**Propriétés calculées** :
- Utiliser `#[Computed]` pour les propriétés dérivées coûteuses
- Cache automatique : la valeur est calculée une seule fois par requête

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
- `WikiService::getEntries()` : Liste paginée des articles Codex
- `WikiService::searchEntries()` : Recherche d'articles Codex
- `WikiService::namePlanet()` : Nommer une planète
- `WikiService::canUserNamePlanet()` : Vérifier les permissions de nommage
- `WikiService::canUserContribute()` : Vérifier les permissions de contribution

### Structure des Composants

**Séparation des responsabilités** :
- **Composants Livewire** : Gèrent uniquement l'état de l'interface et les interactions utilisateur
- **Services** : Contiennent toute la logique métier (appelés directement depuis les composants)
- **Modèles** : Gèrent les relations et les requêtes Eloquent

**Organisation** :
- Composants dans `app/Livewire/` (classes PHP)
- Vues dans `resources/views/livewire/` (templates Blade)
- Services dans `app/Services/` (logique métier)

**Principe** : Les composants Livewire sont minces et délèguent la logique métier aux services.

### Composants Livewire (MVP)

- **Register** : Formulaire d'inscription (`/register`)
- **LoginTerminal** : Formulaire de connexion avec style terminal (`/login`)
- **ForgotPassword** : Formulaire de demande de réinitialisation de mot de passe (`/forgot-password`)
- **ResetPassword** : Formulaire de réinitialisation de mot de passe avec indicateur de force (`/reset-password/{token}`)
- **VerifyEmail** : Page de vérification d'email avec code à 6 chiffres (`/email/verify`)
- **Dashboard** : Affichage de la planète d'origine (`/dashboard`)
- **Profile** : Gestion du profil utilisateur (`/profile`)
- **WikiIndex** : Page d'accueil du Codex avec liste des planètes (`/codex`)
- **WikiPlanet** : Page détail d'une planète dans le Codex (`/codex/planets/{id}`)
- **NamePlanet** : Modal pour nommer une planète (composant enfant)
- **ContributeToWiki** : Modal pour contribuer à un article (composant enfant)

**Routes Web** :
- `/` : Page d'accueil (publique)
- `/register` : Inscription (guest)
- `/login` : Connexion (guest)
- `/forgot-password` : Demande de réinitialisation de mot de passe (guest)
- `/reset-password/{token}` : Formulaire de réinitialisation (guest)
- `/email/verify` : Vérification d'email (auth)
- `/dashboard` : Tableau de bord (auth)
- `/profile` : Profil utilisateur (auth)
- `/codex` : Codex - Encyclopédie spatiale (public)
- `/codex/planets/{id}` : Détails d'une planète dans le Codex (public)
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

