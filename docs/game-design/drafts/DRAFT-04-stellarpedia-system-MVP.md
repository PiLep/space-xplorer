# DRAFT MVP - Wiki Public Stellarpedia

## Statut
**MVP Validé** - Version simplifiée pour le wiki public basique

## Issue Associée
[ISSUE-008-implement-public-wiki-stellarpedia.md](../../issues/ISSUE-008-implement-public-wiki-stellarpedia.md)

## Vue d'Ensemble

Le Stellarpedia MVP est un **wiki public basique** accessible à tous (joueurs et non-joueurs). Il affiche toutes les planètes découvertes dans l'univers de Stellar avec leurs caractéristiques et descriptions générées automatiquement.

**Objectifs MVP** :
- Créer un référentiel public consultable par tous
- Afficher toutes les planètes découvertes (y compris planètes d'origine)
- Permettre aux joueurs de nommer leurs découvertes
- Générer automatiquement les descriptions via IA
- Maintenir la qualité du contenu avec validation automatique

**Philosophie MVP** : Simple, élégant, accessible. Le wiki est alimenté automatiquement par les actions de jeu, pas par des contributions manuelles complexes.

## Règles de Base

### Consultation

- **Accès public** : Le wiki est accessible à tous, même sans compte (lecture seule)
- **Pas de login requis** : Consultation libre pour tous les visiteurs
- **Design superbe** : Interface épurée et scientifique, style encyclopédie spatiale

### Création d'Articles

**Déclenchement automatique** :
- Lors de la création d'une planète d'origine (`PlanetCreated` pour planète d'origine)
- Lors de l'exploration d'une planète pour la première fois (`PlanetExplored`)

**Processus** :
1. Vérification de l'existence d'un article pour cette planète
2. Si aucun article n'existe, création automatique d'un article
3. Génération automatique de la description via IA (basée sur les caractéristiques)
4. Nom de fallback technique si pas encore nommée (ex: "Planète Tellurique #1234")

### Nommage des Planètes

**Qui peut nommer** :
- **Le découvreur** : Seul le joueur qui découvre une planète en premier peut la nommer
- **Autres joueurs** : Les joueurs qui explorent une planète déjà découverte peuvent contribuer/modifier mais **ne peuvent pas renommer**

**Processus de nommage** :
1. Le découvreur propose un nom lors de la découverte
2. Validation automatique :
   - Nom unique (pas déjà utilisé)
   - Respecte les règles de nommage (longueur, caractères autorisés)
   - Pas de mots interdits
3. Si validation réussie → Publication immédiate
4. Si validation échoue → Soumission à modération admin

**Planètes d'origine** :
- Via l'onboarding, le joueur nomme sa planète d'origine
- Publication immédiate dans le wiki après validation automatique

### Contributions et Modifications

**Qui peut contribuer** :
- **Joueurs ayant exploré la planète** : Tout joueur qui a exploré une planète peut contribuer/modifier la page
- **Limitations** :
  - Ne peuvent pas renommer la planète (réservé au découvreur)
  - Peuvent modifier les sections de contenu (description, détails, etc.)

**Validation** :
- **Publication directe** : Si validation automatique réussie (pas de mots interdits, format correct)
- **Modération admin** : Interface admin disponible pour modérer si nécessaire
- **Pas de validation obligatoire** : Le système privilégie la publication directe avec validation automatique

### Descriptions Automatiques

**Génération IA** :
- Les descriptions sont générées automatiquement via IA
- Basées sur les caractéristiques de la planète (type, taille, température, atmosphère, terrain, ressources)
- Format : Texte narratif + données scientifiques
- Mise à jour automatique si les caractéristiques changent (futur)

## Structure des Données

### Articles (Wiki Entries)

```sql
wiki_entries
- id
- planet_id (foreign key vers planets)
- name (nom de la planète, peut être null si pas encore nommée)
- fallback_name (nom technique de fallback, ex: "Planète Tellurique #1234")
- description (texte généré automatiquement via IA)
- discovered_by_user_id (joueur découvreur, nullable)
- is_named (bool - si la planète a été nommée)
- is_public (bool - toujours true pour MVP)
- created_at
- updated_at
```

### Contributions (Contributions)

```sql
wiki_contributions
- id
- wiki_entry_id (foreign key vers wiki_entries)
- contributor_user_id (joueur qui contribue)
- content_type ('description', 'details', etc.)
- content (texte de la contribution)
- status ('published', 'pending_review', 'rejected')
- created_at
- updated_at
```

**Note** : Pour le MVP, on peut simplifier et ne pas avoir de table de contributions séparée si les modifications sont directes sur l'article.

## Fonctionnalités MVP

### Affichage

**Page d'accueil** :
- Liste des planètes récemment découvertes
- Liste des planètes les plus consultées
- Recherche par nom de planète
- Filtres basiques : Type, Taille, Température

**Page Planète** :
- **Titre** : Nom de la planète (ou fallback name)
- **Auteur** : Nom du joueur découvreur (si nommé par un joueur)
- **Date de découverte** : Date de première découverte
- **Caractéristiques** :
  - Type (Tellurique, Gazeuse, etc.)
  - Taille (Petite, Moyenne, Grande)
  - Température (Froide, Tempérée, Chaude)
  - Atmosphère (Respirable, Toxique, etc.)
  - Terrain (Océanique, Désertique, etc.)
  - Ressources (Liste)
- **Description** : Texte généré automatiquement via IA
- **Image** : Image de la planète (si disponible)
- **Bouton "Contribuer"** : Visible uniquement pour les joueurs ayant exploré la planète

### Recherche et Navigation

- **Recherche** : Barre de recherche avec autocomplétion par nom
- **Filtres** : Par type, taille, température
- **Navigation** : Liste des planètes, catégories

### Design

**Style visuel** :
- Design épuré et scientifique, inspiré d'une encyclopédie spatiale
- Couleurs : Bleu foncé (#0a0e27), Bleu clair (#1e3a5f), Blanc (#ffffff)
- Typographie : Police lisible et moderne pour les longs textes
- Icônes : Style minimaliste pour les catégories et actions

**Responsive** :
- Interface adaptée mobile/tablet/desktop
- Navigation simplifiée sur petits écrans

## Intégration avec le Système Existant

### Événements

**PlanetCreated** (pour planètes d'origine) :
- Création automatique d'un article wiki
- Génération de la description via IA
- Nom de fallback technique assigné
- Publication immédiate (is_public = true)

**PlanetExplored** :
- Vérification de l'existence d'un article
- Si aucun article → Création automatique
- Si article existe → Pas de modification automatique (le joueur peut contribuer manuellement)

### Onboarding

- Lors de l'onboarding, le joueur nomme sa planète d'origine
- Validation automatique du nom
- Publication immédiate dans le wiki

## Modération et Qualité

### Validation Automatique

**Pour les noms** :
- Nom unique (pas déjà utilisé)
- Longueur : 3-50 caractères
- Caractères autorisés : Lettres, chiffres, espaces, tirets, apostrophes
- Pas de mots interdits
- Format correct

**Pour les contributions** :
- Pas de mots interdits
- Longueur minimale : 10 caractères
- Longueur maximale : 5000 caractères
- Format correct

### Modération Admin

**Interface admin disponible** :
- Liste des articles
- Modération des noms rejetés
- Modération des contributions signalées
- Suppression/édition de contenu si nécessaire

**Cas de modération** :
- Nom rejeté par la validation automatique
- Contenu signalé par un joueur
- Contenu inapproprié détecté

## Ce qui n'est PAS dans le MVP

- Systèmes stellaires (pas assez avancé sur l'exploration)
- Système de votes communautaires
- Canonisation d'entrées
- Tags et catégories avancées
- Entrées Parallaxe
- Récompenses et progression
- Rôles de contributeurs (planétologue, etc.) - à venir plus tard
- Autres catégories (artefacts, faune, flore, etc.)

## Évolution Future

### Phase 2 - Contributions Communautaires
- Système de votes
- Canonisation automatique
- Contributions multiples par article
- Tags et recherche avancée

### Phase 3 - Systèmes Stellaires
- Articles pour systèmes stellaires
- Critères d'exploration suffisante
- Intégration avec le système d'exploration

### Phase 4 - Autres Catégories
- Artefacts
- Faune et flore
- Anomalies
- Événements historiques

### Phase 5 - Rôles et Progression
- Rôles de contributeurs (planétologue, astrobiologiste, etc.)
- Récompenses pour contributions
- Progression et badges

## Métriques à Surveiller (MVP)

### Engagement
- Nombre de consultations par jour
- Nombre d'articles créés par jour
- Temps moyen passé sur une page
- Taux de recherche vs navigation

### Qualité
- Taux d'acceptation des noms (acceptés / soumis)
- Taux de rejet avec raison
- Nombre de contributions par article
- Longueur moyenne des descriptions

### Technique
- Temps de génération des descriptions IA
- Performance de la recherche
- Temps de chargement des pages

## Implémentation Technique

### Spécifications

**Création d'articles** :
- Listener sur `PlanetCreated` et `PlanetExplored`
- Vérification d'unicité (une planète = un article)
- Génération automatique de la description via IA
- Création du nom de fallback technique

**Génération de descriptions IA** :
- Service dédié pour la génération IA
- Prompt basé sur les caractéristiques de la planète
- Cache des descriptions générées
- Retry en cas d'échec

**Nommage** :
- Validation automatique des noms
- Vérification d'unicité
- Filtrage des mots interdits
- Publication directe si validation OK

**Recherche** :
- Index de recherche sur les noms
- Autocomplétion
- Filtres par caractéristiques

### Points d'Attention

1. **Performance** : Les recherches doivent être rapides (< 500ms)
2. **IA** : Gestion des erreurs de génération IA (fallback, retry)
3. **Qualité** : Validation automatique efficace
4. **Public** : Pas de données sensibles exposées publiquement

### Tests à Prévoir

1. **Tests unitaires** :
   - Création d'articles
   - Validation des noms
   - Génération de descriptions IA

2. **Tests d'intégration** :
   - Flux complet de création d'article
   - Nommage via onboarding
   - Recherche et filtres

3. **Tests de performance** :
   - Recherche dans le wiki
   - Génération de descriptions IA
   - Affichage des listes

## Historique

- **2024-01-XX** : Création du draft MVP simplifié basé sur les discussions avec Alex (Product Manager)
- **2024-01-XX** : Validation de l'approche MVP - Wiki public basique avec design superbe

## Références

- **[DRAFT-04-stellarpedia-system.md](./DRAFT-04-stellarpedia-system.md)** : Version complète du système Stellarpedia (futur)
- **[ISSUE-008-implement-public-wiki-stellarpedia.md](../../issues/ISSUE-008-implement-public-wiki-stellarpedia.md)** : Issue produit pour l'implémentation MVP
- **[PRODUCT.md](../../agents/PRODUCT.md)** : Documentation de l'agent Product Manager

