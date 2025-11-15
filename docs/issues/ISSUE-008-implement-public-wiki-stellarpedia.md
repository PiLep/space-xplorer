# ISSUE-008 : Implémenter le Wiki Public Stellarpedia (MVP)

## Type
Feature

## Priorité
High

## Description

Implémenter un wiki public basique accessible à tous (joueurs et non-joueurs) pour afficher toutes les planètes découvertes dans l'univers de Stellar. Le wiki doit avoir un design superbe, style encyclopédie spatiale, et être alimenté automatiquement par les actions de jeu.

## Contexte Métier

Le Stellarpedia est le référentiel public de toutes les découvertes dans Stellar. Cette première version MVP doit être simple, élégante et accessible. Elle permet de :
- Créer un référentiel consultable par tous (même sans compte)
- Afficher toutes les planètes découvertes avec leurs caractéristiques
- Permettre aux joueurs de nommer leurs découvertes
- Générer automatiquement les descriptions via IA
- Maintenir la qualité du contenu avec validation automatique

**Philosophie MVP** : Simple, élégant, accessible. Le wiki est alimenté automatiquement par les actions de jeu, pas par des contributions manuelles complexes.

## Critères d'Acceptation

### Consultation Publique

- [ ] Le wiki est accessible publiquement sans authentification (lecture seule pour non-joueurs)
- [ ] Page d'accueil avec liste des planètes récemment découvertes
- [ ] Page d'accueil avec liste des planètes les plus consultées
- [ ] Recherche par nom de planète avec autocomplétion
- [ ] Filtres basiques : Type, Taille, Température
- [ ] Page détail planète avec toutes les caractéristiques affichées

### Création Automatique d'Articles

- [ ] Création automatique d'un article lors de `PlanetCreated` (planètes d'origine)
- [ ] Création automatique d'un article lors de `PlanetExplored` (première exploration)
- [ ] Vérification d'unicité (une planète = un article)
- [ ] Génération automatique de la description via IA basée sur les caractéristiques
- [ ] Nom de fallback technique si planète pas encore nommée (ex: "Planète Tellurique #1234")

### Nommage des Planètes

- [ ] Seul le découvreur peut nommer une planète
- [ ] Validation automatique du nom :
  - Nom unique (pas déjà utilisé)
  - Longueur : 3-50 caractères
  - Caractères autorisés : Lettres, chiffres, espaces, tirets, apostrophes
  - Pas de mots interdits
- [ ] Publication immédiate si validation réussie
- [ ] Soumission à modération admin si validation échoue
- [ ] Intégration avec l'onboarding : nommage de la planète d'origine → publication immédiate

### Contributions et Modifications

- [ ] Les joueurs ayant exploré une planète peuvent contribuer/modifier (sauf renommer)
- [ ] Validation automatique des contributions :
  - Pas de mots interdits
  - Longueur minimale : 10 caractères
  - Longueur maximale : 5000 caractères
- [ ] Publication directe si validation réussie
- [ ] Interface admin disponible pour modération si nécessaire

### Design

- [ ] Design épuré et scientifique, style encyclopédie spatiale
- [ ] Couleurs : Bleu foncé (#0a0e27), Bleu clair (#1e3a5f), Blanc (#ffffff)
- [ ] Typographie lisible et moderne
- [ ] Responsive (mobile/tablet/desktop)
- [ ] Mise en avant des caractéristiques visuelles
- [ ] Affichage de l'image de la planète (si disponible)

### Structure d'un Article

- [ ] Titre : Nom de la planète (ou fallback name)
- [ ] Auteur : Nom du joueur découvreur (si nommé par un joueur)
- [ ] Date de découverte
- [ ] Caractéristiques : Type, Taille, Température, Atmosphère, Terrain, Ressources
- [ ] Description : Texte généré automatiquement via IA
- [ ] Image de la planète (si disponible)
- [ ] Bouton "Contribuer" : Visible uniquement pour les joueurs ayant exploré la planète

## Détails Techniques

### Modèle de Données

**Table `wiki_entries`** :
- `id` (primary key)
- `planet_id` (foreign key vers `planets`, unique)
- `name` (string, nullable - nom de la planète)
- `fallback_name` (string - nom technique de fallback)
- `description` (text - texte généré automatiquement via IA)
- `discovered_by_user_id` (foreign key vers `users`, nullable)
- `is_named` (boolean - si la planète a été nommée)
- `is_public` (boolean - toujours true pour MVP)
- `created_at`, `updated_at`

**Table `wiki_contributions`** (optionnel pour MVP simplifié) :
- `id` (primary key)
- `wiki_entry_id` (foreign key vers `wiki_entries`)
- `contributor_user_id` (foreign key vers `users`)
- `content_type` (string - 'description', 'details', etc.)
- `content` (text - texte de la contribution)
- `status` (string - 'published', 'pending_review', 'rejected')
- `created_at`, `updated_at`

### Événements et Listeners

**Listener sur `PlanetCreated`** :
- Créer un article wiki pour les planètes d'origine
- Générer la description via IA
- Assigner le nom de fallback technique
- Publier immédiatement

**Listener sur `PlanetExplored`** :
- Vérifier l'existence d'un article
- Si aucun article → Créer automatiquement
- Si article existe → Pas de modification automatique

### Services

**Service `WikiService`** :
- `createEntryForPlanet(Planet $planet)` : Créer un article pour une planète
- `generateDescription(Planet $planet)` : Générer la description via IA
- `validateName(string $name)` : Valider un nom de planète
- `namePlanet(WikiEntry $entry, User $user, string $name)` : Nommer une planète

**Service `AIDescriptionService`** :
- `generatePlanetDescription(Planet $planet)` : Générer la description via IA
- Gestion des erreurs et retry
- Cache des descriptions générées

### Routes et Contrôleurs

**Routes publiques** (pas d'authentification requise) :
- `GET /wiki` : Page d'accueil du wiki
- `GET /wiki/planets` : Liste des planètes (API)
- `GET /wiki/planets/{id}` : Page détail planète
- `GET /wiki/search` : Recherche de planètes (API)

**Routes authentifiées** (pour les joueurs) :
- `POST /api/wiki/planets/{id}/name` : Nommer une planète
- `POST /api/wiki/planets/{id}/contribute` : Contribuer/modifier une page

**Routes admin** :
- `GET /admin/wiki` : Interface de modération
- `POST /admin/wiki/entries/{id}/moderate` : Modérer un article

### Migration

Créer une migration pour la table `wiki_entries` avec tous les champs nécessaires.

### Tests

- Tests unitaires pour la création d'articles
- Tests unitaires pour la validation des noms
- Tests unitaires pour la génération de descriptions IA
- Tests d'intégration pour le flux complet
- Tests de performance pour la recherche

## Notes

- **Génération IA** : Utiliser une API de génération de texte (ex: OpenAI) pour les descriptions
- **Performance** : Les recherches doivent être rapides (< 500ms)
- **Cache** : Mettre en cache les descriptions générées pour éviter les régénérations
- **Fallback** : En cas d'échec de génération IA, utiliser un template pré-écrit avec variables
- **Public** : Pas de données sensibles exposées publiquement (pas d'emails, IDs utilisateurs, etc.)
- **Onboarding** : Le wiki doit être disponible même avant l'onboarding (pour les planètes déjà créées)

## Références

- **[DRAFT-04-stellarpedia-system-MVP.md](../game-design/drafts/DRAFT-04-stellarpedia-system-MVP.md)** : Spécifications détaillées du MVP
- **[DRAFT-04-stellarpedia-system.md](../game-design/drafts/DRAFT-04-stellarpedia-system.md)** : Version complète du système (futur)
- **[ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md)** : Architecture technique
- **[EVENTS.md](../EVENTS.md)** : Documentation des événements (PlanetCreated, PlanetExplored)
- **Issue GitHub** : [#16](https://github.com/PiLep/space-xplorer/issues/16)

## Suivi et Historique

### Statut

En cours

### Historique

#### 2024-01-XX - Alex (Product Manager) - Création de l'issue
**Statut** : À faire
**Détails** : Création de l'issue pour le wiki public basique MVP après validation du DRAFT-04 MVP avec Alex
**Notes** : Version simplifiée du système Stellarpedia complet. Focus sur la consultation publique et le nommage des planètes.

#### 2025-01-XX - Sam (Lead Developer) - Création du plan
**Statut** : En cours
**Détails** : Plan de développement créé. Le plan décompose l'issue en 7 phases avec 25 tâches au total. Estimation totale : ~20h de développement.
**Fichiers modifiés** : docs/tasks/TASK-008-implement-public-wiki-stellarpedia.md
**Notes** : Focus sur MVP simplifié avec création automatique d'articles, nommage des planètes, et génération IA des descriptions. Le plan couvre les migrations, services, événements/listeners, endpoints API, composants Livewire, et tests complets.

#### 2025-01-XX - Morgan (Architect) - Review architecturale
**Statut** : En cours
**Détails** : Review architecturale complète effectuée sur le plan de développement TASK-008.
**Fichiers modifiés** : docs/reviews/ARCHITECT-REVIEW-008-implement-public-wiki-stellarpedia.md, docs/tasks/TASK-008-implement-public-wiki-stellarpedia.md
**Notes** : ⚠️ Approuvé avec recommandations. Le plan respecte l'architecture définie. Principales recommandations : clarification de l'utilisation des services par Livewire (High), utilisation explicite des ULIDs dans les migrations (High), génération IA asynchrone (High pour évolution future), rate limiting (Medium), index de performance (Medium). Le plan peut être implémenté en tenant compte des recommandations.

#### 2025-01-XX - Morgan (Architect) - Création de la branche et de l'issue GitHub
**Statut** : En cours
**Détails** : Branche Git créée (`issue/008-implement-public-wiki-stellarpedia`) et issue GitHub créée (#16).
**Fichiers modifiés** : docs/issues/ISSUE-008-implement-public-wiki-stellarpedia.md
**Notes** : Issue GitHub synchronisée avec la documentation locale. Commentaire de review architecturale ajouté sur l'issue GitHub.
**GitHub** : [#16](https://github.com/PiLep/space-xplorer/issues/16)

