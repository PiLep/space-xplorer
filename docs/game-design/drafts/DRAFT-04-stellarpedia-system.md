# DRAFT - Système Stellarpedia (Codex Communautaire)

## Statut
**Draft** - Version complète du système Stellarpedia (futur)

**Note** : Une version MVP simplifiée a été validée : **[DRAFT-04-stellarpedia-system-MVP.md](./DRAFT-04-stellarpedia-system-MVP.md)**

## Issue Associée
- **MVP** : [ISSUE-008-implement-public-wiki-stellarpedia.md](../../issues/ISSUE-008-implement-public-wiki-stellarpedia.md)
- **Version complète** : À créer après implémentation du MVP

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

**Avec le système de découverte existant (Planètes)** :
- Lorsqu'un joueur explore une planète pour la première fois (événement `PlanetExplored`), un article Stellarpedia est créé automatiquement
- Le joueur découvreur reçoit une notification lui permettant de proposer la fiche initiale
- Les caractéristiques de la planète (type, taille, température, atmosphère, terrain, ressources) sont pré-remplies dans le formulaire de contribution
- Les autres joueurs qui découvrent la même planète peuvent proposer des entrées alternatives
- Les planètes d'origine (home planets) sont automatiquement ajoutées au Stellarpedia lors de leur création

**Avec les expéditions** :
- Chaque expédition peut débloquer un objet du Stellarpedia (planète, système stellaire, artefact, etc.)
- L'explorateur qui découvre un objet peut en nommer la version officielle
- Les découvertes rares (planètes océaniques, désertiques) génèrent des notifications spéciales encourageant la contribution

**Avec le système narratif CYOA** :
- Les découvertes faites lors des événements narratifs peuvent créer des entrées dans le Stellarpedia
- Les fragments de lore collectés enrichissent automatiquement les sections `body_lore` des entrées
- Certains choix narratifs peuvent débloquer des informations supplémentaires pour les entrées

**Avec le système de gestion** :
- Les modules de laboratoire améliorent la qualité des données scientifiques pré-remplies
- Les améliorations de la station débloquent des catégories supplémentaires (faune, flore, espèces intelligentes)
- Le niveau du joueur influence la longueur maximale des contributions acceptées

**Avec les mini-jeux** :
- Les résultats des mini-jeux peuvent révéler des informations supplémentaires sur les objets découverts
- Ces informations peuvent être automatiquement ajoutées aux sections scientifiques des entrées

**Avec Le Parallaxe** :
- Les entrées liées au Parallaxe sont privées et cryptiques
- Elles n'apparaissent que pour le joueur concerné
- Elles peuvent référencer des objets réels mais avec des descriptions altérées

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

**Déclenchement** :
- Événement de découverte (ex: `PlanetExplored`, `DiscoveryMade`)
- Vérification de l'existence d'un article (`discoverable_type` + `discoverable_id`)
- Si aucun article n'existe, création automatique d'un `codex_article`

**Création de l'article** :
1. Génération automatique du `slug` basé sur le nom ou l'ID de l'objet
2. Enregistrement du `first_discovered_by_player_id`
3. `canonical_entry_id` reste `null` jusqu'à la première entrée validée
4. Création d'une entrée de base avec les données de l'objet (si disponibles)

**Notification au joueur découvreur** :
- Notification in-game : "Vous avez découvert [Objet] ! Proposez la première entrée du Stellarpedia."
- Message dans la boîte de réception avec lien direct vers le formulaire de contribution
- Badge temporaire "Premier découvreur" visible sur l'article pendant 7 jours

**Cas particuliers** :
- **Planète d'origine** : Création automatique lors de la génération de la planète d'origine du joueur
- **Découvertes multiples simultanées** : Le premier joueur à compléter la découverte devient le découvreur officiel
- **Objets générés procéduralement** : Les caractéristiques sont pré-remplies dans le formulaire de contribution

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
- **Seuil de canonisation automatique** :
  - Score minimum : +10 votes nets (votes positifs - votes négatifs)
  - Ratio minimum : 70% de votes positifs
  - Nombre minimum de votes : 15 votes au total
  - Délai minimum : 7 jours après publication (pour éviter les manipulations)
- **Processus** :
  - Vérification quotidienne des entrées publiées
  - Si seuil atteint, changement automatique de statut à `canonical`
  - L'ancienne entrée canonique (si existe) devient `published`
  - Notification à l'auteur de la nouvelle entrée canonique
  - Notification à l'auteur de l'ancienne entrée canonique (si différente)

**C. Canonisation Manuelle**
- L'administrateur choisit l'entrée canonique d'un article
- Statut : `canonical`
- L'ancienne entrée canonique devient `published`

## Consultation

### Vue Article (Page principale d'un objet)

**En-tête** :
- **Nom canonique** : Titre de l'entrée canonique (grand, mis en avant)
- **Badge de découverte** : "Découvert par [Nom du joueur]" avec date
- **Catégorie** : Badge visuel indiquant le type (Planète, Système, Artefact, etc.)
- **Image principale** : Image générée ou uploadée de l'objet

**Contenu principal** :
- **Description officielle** : Corps de l'entrée canonique avec sections (Lore, Scientifique, Biologique, Culturel)
- **Caractéristiques essentielles** : Stats et données clés dans un tableau formaté
  - Pour les planètes : Type, Taille, Température, Atmosphère, Terrain, Ressources
  - Pour les systèmes : Type d'étoile, Nombre de planètes, Coordonnées
  - Pour les artefacts : Fonction présumée, Origine, État

**Section communautaire** :
- **Entrées alternatives** : Liste des autres entrées publiées avec :
  - Titre et résumé court (short_label)
  - Score de votes et nombre de votes
  - Auteur et date
  - Bouton "Voir l'entrée complète"
- **Bouton "Contribuer"** : Visible uniquement si le joueur a découvert l'objet et n'a pas atteint la limite quotidienne

**Métadonnées** :
- **Tags** : Tags associés (cliquables pour recherche)
- **Statistiques** : Nombre de vues, votes totaux, nombre de contributions
- **Auteur(s)** : Crédits des contributeurs avec liens vers leurs profils
- **Historique** : Date de première découverte, dernière modification

### Vue Entrée (Page d'une entrée spécifique)

**En-tête** :
- **Titre** : Titre de l'entrée
- **Statut** : Badge visuel (published, canonical, pending_review, etc.)
- **Auteur** : Nom du joueur auteur avec lien vers profil
- **Date** : Date de création et dernière modification

**Contenu** :
- **Sections** : Affichage formaté de toutes les sections (Lore, Scientifique, Biologique, Culturel)
- **Tags** : Tags associés

**Interactions** :
- **Votes** : 
  - Score actuel et nombre de votes
  - Boutons "+1" et "-1" (un seul vote par joueur)
  - Indication si le joueur a déjà voté
- **Actions** :
  - "Signaler" (si contenu inapproprié)
  - "Voir l'article complet" (retour à la vue Article)

### Recherche et Navigation

**Page d'accueil du Stellarpedia** :
- **Recherche** : Barre de recherche avec autocomplétion
- **Filtres** : Par catégorie, par tags, par popularité, par date
- **Articles récents** : Derniers articles créés
- **Articles populaires** : Articles les plus consultés
- **Top contributeurs** : Classement des contributeurs du mois

**Navigation** :
- **Catégories** : Navigation par type d'objet
- **Tags populaires** : Nuage de tags
- **Mes contributions** : Page personnelle listant les contributions du joueur
- **Mes découvertes** : Liste des objets découverts par le joueur avec statut de contribution

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
- **XP d'exploration** : +50 XP par contribution acceptée
- **Réputation** : +10 réputation par contribution acceptée
- **Première contribution d'un article** : Bonus de +25 XP supplémentaires (total +75 XP)
- **Titres honorifiques** :
  - "Archiviste" : Après 10 contributions acceptées
  - "Archiviste Stellaire" : Après 25 contributions acceptées
  - "Grand Archiviste" : Après 50 contributions acceptées
  - "Maître Archiviste" : Après 100 contributions acceptées
- **Badges profil** :
  - Badge "Contributeur" : Après 5 contributions
  - Badge "Archiviste" : Après 10 contributions
  - Badge "Archiviste Stellaire" : Après 25 contributions
  - Badge "Grand Archiviste" : Après 50 contributions

**Entrées canoniques** :
- **XP** : +200 XP (bonus unique, non cumulable si déjà reçu)
- **Réputation** : +50 réputation
- **Badge spécial** : Badge "Canoniste" permanent sur le profil
- **Mention spéciale** : Nom affiché en évidence sur l'article avec badge "Entrée canonique"
- **Classement** : Points bonus dans le classement mensuel des contributeurs

**Votes positifs** :
- **Réputation pour l'auteur** : +5 réputation par vote positif reçu
- **Pas de pénalité** : Les votes négatifs ne retirent pas de réputation (sauf en cas d'abus détecté)
- **Limite de réputation par entrée** : Maximum +100 réputation par entrée (20 votes positifs)

**Découverte première** :
- **Badge "Premier Découvreur"** : Visible sur l'article pendant 7 jours
- **Réputation** : +25 réputation pour la première découverte d'un objet
- **Droit de nommer** : Possibilité de proposer le nom officiel de l'objet

### Progression

**Niveaux 1-5 (Débutant)** :
- Contributions simples acceptées
- Longueur maximale : 500 caractères pour le lore, 300 pour les sections scientifiques
- Récompenses de base (+50 XP, +10 réputation)
- Accès aux catégories de base (Planètes, Systèmes)

**Niveaux 6-10 (Intermédiaire)** :
- Contributions avancées acceptées
- Longueur maximale : 1000 caractères pour le lore, 500 pour les sections scientifiques
- Possibilité de proposer des entrées canoniques
- Accès aux catégories intermédiaires (Artefacts, Anomalies)
- Déblocage des tags personnalisés

**Niveaux 11-15 (Avancé)** :
- Contributions expertes acceptées
- Longueur maximale : 2000 caractères pour le lore, 1000 pour les sections scientifiques
- Accès aux catégories avancées (Faune, Flore)
- Possibilité de proposer plusieurs tags par contribution (jusqu'à 5)

**Niveaux 16+ (Expert)** :
- Contributions expertes avec sections multiples complètes
- Longueur maximale : 3000 caractères pour le lore, 1500 pour les sections scientifiques
- Accès à toutes les catégories (Espèces intelligentes, Événements historiques)
- Possibilité de proposer jusqu'à 10 tags par contribution
- Accès prioritaire à la modération communautaire (vote sur les contributions d'autres joueurs)
- Badge "Expert Archiviste" visible sur le profil

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

1. **Article déjà existant** :
   - Le joueur peut contribuer mais ne peut pas créer un nouvel article
   - Le joueur découvreur original conserve son statut de "Premier découvreur"
   - Les nouvelles contributions sont traitées comme des entrées alternatives

2. **Limite quotidienne atteinte** :
   - Le joueur doit attendre le lendemain (reset à minuit UTC)
   - Message clair : "Vous avez atteint votre limite quotidienne de contributions. Revenez demain !"
   - La limite est globale (pas par article)

3. **Contenu rejeté** :
   - Le joueur peut proposer une nouvelle version après corrections
   - La raison du rejet est communiquée clairement
   - Pas de pénalité de réputation pour un rejet (sauf en cas de spam répété)
   - Le compteur quotidien n'est pas réinitialisé (la contribution compte comme utilisée)

4. **Entrée canonique remplacée** :
   - L'ancienne entrée canonique devient automatiquement `published`
   - L'auteur de l'ancienne entrée reçoit une notification
   - Les votes de l'ancienne entrée sont conservés
   - L'historique de canonisation est conservé pour référence

5. **Découverte simultanée** :
   - Si deux joueurs découvrent le même objet simultanément, le premier à compléter la découverte devient le découvreur officiel
   - Le second joueur peut toujours contribuer mais n'obtient pas le badge "Premier découvreur"

6. **Article sans entrée canonique** :
   - Un article peut exister sans entrée canonique (toutes les entrées en attente de modération)
   - L'article affiche "En attente de première contribution validée"
   - Les joueurs peuvent toujours consulter les entrées en attente

7. **Suppression d'une entrée canonique** :
   - Si une entrée canonique est supprimée (abus, contenu inapproprié), l'article revient à l'état "sans entrée canonique"
   - L'entrée publiée avec le meilleur score devient automatiquement la nouvelle entrée canonique (si seuil atteint)
   - Sinon, l'article attend une nouvelle canonisation manuelle ou automatique

8. **Joueur banni** :
   - Les contributions d'un joueur banni sont marquées comme "Auteur banni" mais restent visibles
   - Les entrées canoniques d'un joueur banni peuvent être remplacées par un admin
   - Les votes d'un joueur banni sont conservés mais ne comptent plus dans les calculs

## Métriques à Surveiller

### Métriques d'Engagement

**Volume d'activité** :
- Nombre de contributions par jour (moyenne, médiane, pic)
- Nombre de consultations par jour (moyenne, médiane, pic)
- Nombre d'articles créés par jour
- Nombre de votes par jour

**Participation** :
- Taux de participation (joueurs actifs / joueurs totaux)
- Nombre de contributeurs uniques par semaine/mois
- Nombre de contributeurs réguliers (> 1 contribution/semaine)
- Taux de rétention des contributeurs (joueurs qui contribuent plusieurs fois)

**Interactions** :
- Taux de votes (votes / consultations)
- Ratio contributions / découvertes (combien de joueurs contribuent après découverte)
- Temps moyen passé sur une page d'article
- Taux de clic sur les entrées alternatives

### Métriques de Qualité

**Acceptation** :
- Taux d'acceptation des contributions (acceptées / soumises)
- Taux de rejet avec raison (spam, contenu inapproprié, qualité insuffisante)
- Temps moyen de modération (de soumission à validation)

**Qualité du contenu** :
- Score moyen des votes par contribution
- Longueur moyenne des contributions acceptées
- Nombre de sections complétées par contribution (lore, scientifique, etc.)
- Taux d'utilisation des tags

**Canonisation** :
- Nombre d'entrées canoniques créées par semaine/mois
- Ratio canonisation automatique / canonisation manuelle
- Temps moyen avant canonisation automatique
- Nombre de remplacements d'entrées canoniques

### Métriques Communautaires

**Distribution** :
- Distribution des contributeurs (top 1%, 10%, 50%, etc.)
- Nombre de contributions par contributeur (moyenne, médiane, max)
- Ratio contributeurs actifs / contributeurs occasionnels

**Tags** :
- Taux de réutilisation des tags existants vs création de nouveaux tags
- Tags les plus populaires
- Distribution des tags par catégorie

**Catégories** :
- Popularité des catégories (nombre d'articles par catégorie)
- Taux de contribution par catégorie
- Catégories avec le plus de contributions alternatives

### Métriques de Progression

**Récompenses** :
- Distribution des niveaux des contributeurs
- Nombre de badges distribués par type
- Réputation moyenne des contributeurs actifs

**Découvertes** :
- Nombre de premières découvertes par joueur (moyenne, médiane)
- Taux de contribution après première découverte
- Temps moyen entre découverte et première contribution

### Métriques de Modération

**Contenu sensible** :
- Nombre de contributions filtrées automatiquement
- Nombre de signalements par jour/semaine
- Taux de faux positifs (signalements non justifiés)
- Temps de réponse moyen aux signalements

**Abus** :
- Nombre de comptes suspendus pour abus du système
- Nombre de contributions supprimées pour spam
- Nombre de votes suspects détectés (manipulation)

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

## Expérience Utilisateur et Design

### Parcours Utilisateur Type

**Découverte d'un objet** :
1. Joueur explore une planète lors d'une expédition
2. Notification : "Nouvelle découverte ! Proposez la première entrée du Stellarpedia"
3. Clic sur la notification → Redirection vers le formulaire de contribution
4. Formulaire pré-rempli avec les caractéristiques de la planète
5. Joueur complète les sections (Lore, Scientifique)
6. Soumission → Confirmation "Votre contribution est en modération"
7. Après validation → Notification "Votre contribution a été acceptée ! +50 XP"

**Consultation du Stellarpedia** :
1. Joueur accède au Stellarpedia depuis le menu principal
2. Page d'accueil avec recherche et filtres
3. Clic sur un article → Vue Article avec entrée canonique
4. Scroll vers le bas → Entrées alternatives avec votes
5. Clic sur une entrée alternative → Vue Entrée détaillée
6. Vote sur l'entrée → Confirmation "+1 vote enregistré"

**Contribution alternative** :
1. Joueur découvre une planète déjà documentée
2. Consultation de l'article existant
3. Clic sur "Proposer une entrée alternative"
4. Formulaire avec sections à compléter
5. Soumission → Vérification de la limite quotidienne
6. Si OK → Contribution en modération
7. Après validation → Publication et notification

### Design de l'Interface

**Style visuel** :
- Design épuré et scientifique, inspiré d'une encyclopédie spatiale
- Couleurs : Bleu foncé (#0a0e27), Bleu clair (#1e3a5f), Blanc (#ffffff)
- Typographie : Police lisible et moderne pour les longs textes
- Icônes : Style minimaliste pour les catégories et actions

**Éléments clés** :
- **Badge "Premier découvreur"** : Badge doré visible sur l'article
- **Badge "Entrée canonique"** : Badge vert avec icône de couronne
- **Barre de votes** : Barre visuelle montrant le ratio positif/négatif
- **Tags** : Pills colorées cliquables pour navigation
- **Statistiques** : Affichage discret mais visible (vues, votes, contributions)

**Responsive** :
- Interface adaptée mobile/tablette/desktop
- Formulaire de contribution optimisé pour mobile
- Navigation simplifiée sur petits écrans

### Feedback Utilisateur

**Notifications** :
- Notification in-game pour chaque étape importante
- Email optionnel pour les contributions acceptées/rejetées
- Notification push (si activée) pour les votes sur ses contributions

**Messages de confirmation** :
- "Contribution soumise avec succès"
- "Votre vote a été enregistré"
- "Limite quotidienne atteinte - Revenez demain !"
- "Merci pour votre contribution ! +50 XP"

**Indicateurs visuels** :
- Barre de progression pour la limite quotidienne
- Badge "Nouveau" sur les articles récemment créés
- Badge "Populaire" sur les articles avec beaucoup de votes
- Indicateur "Vous avez déjà voté" sur les entrées votées

## Historique

- **2024-01-XX** : Création du draft initial basé sur `stellarpedia.md`
- **2024-01-XX** : Amélioration avec détails sur l'intégration, l'expérience utilisateur, l'équilibrage et les métriques

## Références

- **[stellarpedia.md](../local-brainstorming-data/stellarpedia.md)** : Document source du brainstorming
- **[GAME-DESIGNER.md](../agents/GAME-DESIGNER.md)** : Documentation de l'agent Game Designer
- **[design-game-mechanic.md](../prompts/design-game-mechanic.md)** : Guide pour concevoir des mécaniques

