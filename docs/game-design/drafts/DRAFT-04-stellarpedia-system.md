# DRAFT - Système Stellarpedia (Codex Communautaire)

## Statut
**Draft** - En attente de validation avec Alex (Product Manager)

## Issue Associée
À créer après validation du draft

## Vue d'Ensemble

Le Stellarpedia est le système centralisé d'archives knowledge-base de Stellar. Il regroupe toutes les découvertes faites par les explorateurs : planètes, systèmes stellaires, artefacts, faune, flore, anomalies, événements historiques et espèces intelligentes.

**Objectifs** :
- Construire un univers riche basé sur les découvertes réelles des joueurs
- Garder un lore cohérent tout en laissant place à la créativité
- Offrir un référentiel public et consultable par tous
- Encourager les joueurs à revenir quotidiennement pour enrichir leurs découvertes
- Intégrer le fil rouge narratif (Le Parallaxe) via des entrées spéciales

**Philosophie** : Le contenu du Stellarpedia est majoritairement alimenté par les joueurs eux-mêmes, ce qui en fait un outil communautaire vivant et évolutif.

## Règles de Base

### Activation

- Un article est créé automatiquement lorsqu'un joueur découvre un objet pour la première fois
- Le joueur découvreur obtient l'autorisation de proposer la fiche initiale
- Les autres joueurs peuvent contribuer après avoir découvert l'objet
- Consultation libre pour tous les joueurs

### Fonctionnement

**Création d'un article** :
1. Un joueur découvre un objet (planète, artefact, etc.)
2. Vérification de l'existence d'un article
3. Création automatique d'un `codex_article` si nécessaire
4. Le joueur découvreur peut proposer la fiche initiale

**Proposer une nouvelle entrée** :
1. Le joueur doit avoir découvert l'objet
2. Vérification de la limite quotidienne (1 contribution/jour maximum)
3. Le joueur rédige son entrée
4. Ajout de tags optionnels
5. Envoi en modération (`pending_review`)

**Validation** :
- Trois modes possibles : Validation admin, Vote communautaire, Canonisation manuelle

**Consultation** :
- Vue Article : Affiche l'entrée canonique et les alternatives
- Vue Entrée : Affiche une entrée spécifique avec votes et statut

### Conditions

- **Limite quotidienne** : 1 contribution/jour maximum par joueur
- **Découverte requise** : Le joueur doit avoir découvert l'objet pour contribuer
- **Modération** : Toutes les premières entrées nécessitent une validation admin
- **Filtrage** : Filtrage automatique des mots interdits

### Interactions

**Avec les expéditions** :
- Chaque expédition peut débloquer un objet du Stellarpedia
- L'explorateur qui découvre un objet peut en nommer la version officielle

**Avec le système narratif CYOA** :
- Les découvertes peuvent créer des entrées dans le Stellarpedia
- Les fragments de lore enrichissent le Stellarpedia

**Avec Le Parallaxe** :
- Les entrées liées au Parallaxe sont privées et cryptiques
- Elles n'apparaissent que pour le joueur concerné

## Types d'Éléments Archivés

Le Stellarpedia indexe les catégories suivantes :

1. **Planètes** : Caractéristiques, composition, habitabilité
2. **Systèmes stellaires** : Configuration, étoiles, orbites
3. **Artefacts** : Objets découverts, fonction, origine
4. **Faune** : Espèces animales découvertes
5. **Flore** : Espèces végétales découvertes
6. **Espèces intelligentes** : Civilisations rencontrées (rares)
7. **Anomalies et phénomènes** : Événements étranges, anomalies spatiales
8. **Fragments d'histoires ou événements historiques** : Lore et histoire
9. **Entrées cryptiques liées au Parallaxe** : Privées, visibles uniquement par le joueur

Chaque catégorie correspond à un objet réel du monde persistant.

## Structure des Données

### Articles

Chaque objet découvert possède un article unique.

```sql
codex_articles
- id
- discoverable_type ('planet', 'system', 'artifact', 'fauna', 'flora', 'species', 'anomaly', 'event')
- discoverable_id (id de l'objet découvert)
- slug (URL-friendly identifier)
- canonical_entry_id (id de l'entrée publiée par défaut, nullable)
- first_discovered_by_player_id (joueur qui a découvert en premier)
- created_at
- updated_at
```

### Entrées

Les entrées sont les propositions des joueurs.

```sql
codex_entries
- id
- codex_article_id
- author_player_id
- title
- short_label (résumé court, max 100 caractères)
- body_lore (texte narratif, lore)
- body_scientific (données scientifiques)
- body_biological (informations biologiques, si applicable)
- body_cultural (informations culturelles, si applicable)
- status ('draft', 'pending_review', 'published', 'rejected', 'canonical')
- is_official (bool - entrée officielle/canonique)
- votes_count (int, cached)
- votes_score (int, cached - sum of votes)
- created_at
- updated_at
- reviewed_by_admin_id (nullable)
- rejected_reason (nullable)
```

### Votes

```sql
codex_entry_votes
- id
- codex_entry_id
- voter_player_id
- value (+1 ou -1)
- created_at
- unique(codex_entry_id, voter_player_id)
```

### Tags

```sql
codex_tags
- id
- name (unique)
- category (optional)
- created_at

codex_entry_tags
- id
- codex_entry_id
- codex_tag_id
- unique(codex_entry_id, codex_tag_id)
```

## Fonctionnement du Flux de Contribution

### Création d'un Article

Lorsqu'un joueur découvre un objet pour la première fois :
1. Vérification de l'existence d'un article (`discoverable_type` + `discoverable_id`)
2. Création automatique d'un `codex_article` si nécessaire
3. Le joueur obtient l'autorisation de proposer la fiche initiale
4. Notification au joueur découvreur

### Proposer une Nouvelle Entrée

**Conditions** :
- Avoir découvert l'objet (vérification dans `player_discoveries`)
- Ne pas avoir dépassé la limite quotidienne (1 contribution/jour)
- L'article doit exister

**Étapes** :
1. Le joueur rédige son entrée (titre, corps, sections optionnelles)
2. Ajoute des tags (optionnel)
3. Envoie en modération (`pending_review`)
4. Notification à l'admin (si première entrée de l'article)

### Validation

Trois modes possibles :

**A. Validation Admin**
- L'administrateur accepte ou rejette l'entrée
- Statut : `published` ou `rejected`
- Raison de rejet si applicable

**B. Vote Communautaire**
- Les entrées très votées peuvent devenir canonique automatiquement
- Seuil : Score de +10 votes avec ratio 70% positif
- Statut : `canonical` automatique

**C. Canonisation Manuelle**
- L'administrateur choisit l'entrée canonique d'un article
- Statut : `canonical`
- L'ancienne entrée canonique devient `published`

## Consultation

### Vue Article

Affiche :
- **Nom canonique** : Titre de l'entrée canonique
- **Description officielle** : Corps de l'entrée canonique
- **Image(s)** : Images associées à l'objet
- **Caractéristiques essentielles** : Stats et données clés
- **Entrées alternatives** : Autres entrées publiées (avec votes)
- **Auteur(s)** : Crédits des contributeurs
- **Tags** : Tags associés
- **Statistiques** : Nombre de vues, votes, contributions

### Vue Entrée

Affiche :
- **Titre et corps** : Contenu complet
- **Statut** : published, canonical, pending_review, etc.
- **Votes** : Score et nombre de votes
- **Auteur** : Nom du joueur auteur
- **Date** : Date de création et dernière modification
- **Tags** : Tags associés

## Intégration au Gameplay

### Découvertes

- Chaque expédition peut débloquer un objet du Stellarpedia
- L'explorateur qui découvre un objet peut en nommer la version officielle
- Les joueurs peuvent enrichir le contenu selon leurs découvertes

### Récompenses

- **XP d'exploration** : +50 XP pour chaque contribution acceptée
- **Titres honorifiques** : Ex. "Archiviste Stellaire" (après 10 contributions)
- **Badges profil** : Badges pour contributions exceptionnelles
- **Classements mensuels** : Top contributeurs du mois

### Progression

- **Première découverte** : Droit de nommer l'objet
- **Contributions** : XP et réputation
- **Votes positifs** : Réputation supplémentaire
- **Entrée canonique** : Badge spécial + réputation majeure

## Gestion du Contenu Sensible

### Filtrage Automatique

- Filtrage automatique des mots interdits
- Validation avant publication si mots suspects détectés

### Modération

- **Revue admin obligatoire** : Pour les premières versions d'un article
- **Signalement** : Possibilité de signaler une entrée
- **Révision** : Les admins peuvent réviser ou supprimer du contenu

### Règles de Contenu

- Pas de contenu offensant ou inapproprié
- Respect du lore établi (cohérence)
- Qualité minimale requise (pas de spam)

## Les Entrées du Parallaxe

Les entrées liées au Parallaxe :
- **Ne sont pas publiques** : Visibles uniquement par le joueur concerné
- **N'apparaissent que pour le joueur concerné** : Pas dans les recherches publiques
- **Prennent la forme de fragments cryptiques** : Textes ambigus et mystérieux
- **Ne peuvent pas être votées ou modifiées** : Statut spécial `parallaxe`

Ces entrées participent au fil rouge narratif et créent du mystère personnel.

## Équilibrage

### Limites Quotidiennes

- **1 contribution/jour maximum** : Évite le spam et encourage la qualité
- **Pas de limite de votes** : Les votes sont illimités
- **Pas de limite de consultation** : Consultation libre

### Récompenses

**Contributions acceptées** :
- +50 XP par contribution
- +10 réputation par contribution
- Titre "Archiviste" après 10 contributions
- Badge spécial après 50 contributions

**Entrées canoniques** :
- +200 XP
- +50 réputation
- Badge "Canoniste"
- Mention spéciale dans le classement

**Votes positifs** :
- +5 réputation pour l'auteur par vote positif
- Pas de pénalité pour les votes négatifs (sauf si abus)

### Progression

- **Niveaux 1-5** : Contributions simples, récompenses de base
- **Niveaux 6-15** : Contributions avancées, possibilité d'entrées canoniques
- **Niveaux 16+** : Contributions expertes, réputation majeure

## Exemples et Cas d'Usage

### Exemple 1 : Découverte d'une Planète

**Situation** : Joueur découvre une planète tellurique pour la première fois

**Processus** :
1. Article créé automatiquement
2. Joueur propose la fiche initiale :
   - Titre : "Korvath Prime"
   - Lore : "Une planète verdoyante avec des océans bleu profond..."
   - Scientifique : "Type tellurique, atmosphère respirable, température moyenne 15°C..."
3. Validation admin (première entrée)
4. Publication et canonisation automatique
5. +50 XP + réputation pour le joueur

### Exemple 2 : Contribution Alternative

**Situation** : Autre joueur découvre la même planète et propose une version alternative

**Processus** :
1. Joueur propose une entrée alternative :
   - Titre : "Korvath Prime - La Planète des Tempêtes"
   - Lore : Version alternative avec focus sur les tempêtes magnétiques
2. Mise en modération
3. Publication après validation
4. Votes communautaires déterminent la popularité
5. Si score élevé, possibilité de devenir canonique

### Exemple 3 : Entrée Parallaxe

**Situation** : Joueur reçoit un indice du Parallaxe

**Processus** :
1. Entrée créée automatiquement avec statut `parallaxe`
2. Contenu cryptique : "Cette planète connaît ton passage. L'autre version de toi a hésité."
3. Visible uniquement par le joueur concerné
4. Pas de votes ni modifications possibles
5. Ajout au journal secret du joueur

## Cas Limites

1. **Article déjà existant** : Le joueur peut contribuer mais ne peut pas créer un nouvel article
2. **Limite quotidienne atteinte** : Le joueur doit attendre le lendemain
3. **Contenu rejeté** : Le joueur peut proposer une nouvelle version après corrections
4. **Entrée canonique remplacée** : L'ancienne devient `published`, la nouvelle devient `canonical`

## Métriques à Surveiller

### Métriques d'Engagement
- Nombre de contributions par jour
- Nombre de consultations par jour
- Taux de participation (joueurs actifs)
- Taux de votes

### Métriques de Qualité
- Taux d'acceptation des contributions
- Score moyen des votes
- Nombre d'entrées canoniques créées

### Métriques Communautaires
- Distribution des contributeurs (top 10%, 50%, etc.)
- Taux de réutilisation des tags
- Popularité des catégories

## Implémentation Technique

### Spécifications

**Système de création d'articles** :
- Déclenchement automatique lors d'une découverte
- Vérification d'unicité (`discoverable_type` + `discoverable_id`)
- Génération automatique du slug

**Système de contributions** :
- Vérification de la limite quotidienne
- Validation du contenu (filtrage)
- Mise en modération automatique

**Système de votes** :
- Calcul en temps réel du score
- Cache des compteurs pour performance
- Validation des votes (un vote par joueur par entrée)

**Système de canonisation** :
- Algorithme automatique basé sur les votes
- Possibilité de canonisation manuelle par admin
- Mise à jour de l'article avec la nouvelle entrée canonique

### Points d'Attention

1. **Performance** : Les recherches doivent être rapides (< 500ms)
2. **Modération** : Système de modération efficace et réactif
3. **Spam** : Protection contre le spam et les abus
4. **Qualité** : Encouragement de contributions de qualité

### Tests à Prévoir

1. **Tests unitaires** :
   - Création d'articles
   - Validation des contributions
   - Calcul des votes

2. **Tests d'intégration** :
   - Flux complet de contribution
   - Système de modération
   - Canonisation automatique

3. **Tests de performance** :
   - Recherche dans le Stellarpedia
   - Affichage des articles populaires
   - Gestion des votes

## Historique

- Création du draft initial basé sur `stellarpedia.md`

## Références

- **[stellarpedia.md](../local-brainstorming-data/stellarpedia.md)** : Document source du brainstorming
- **[GAME-DESIGNER.md](../agents/GAME-DESIGNER.md)** : Documentation de l'agent Game Designer
- **[design-game-mechanic.md](../prompts/design-game-mechanic.md)** : Guide pour concevoir des mécaniques

