# Stellarpedia – Documentation Complète

## 1. Présentation
Le Stellarpedia est le système centralisé d’archives knowledge-base de Stellar. Il regroupe toutes les découvertes faites par les explorateurs : planètes, systèmes stellaires, artefacts, faune, flore, anomalies, événements historiques et espèces intelligentes. Le contenu du Stellarpedia est majoritairement alimenté par les joueurs eux-mêmes, ce qui en fait un outil communautaire vivant et évolutif.

## 2. Objectifs du Stellarpedia
- Construire un univers riche basé sur les découvertes réelles des joueurs.
- Garder un lore cohérent tout en laissant place à la créativité.
- Offrir un référentiel public et consultable par tous.
- Encourager les joueurs à revenir quotidiennement pour enrichir leurs découvertes.
- Intégrer le fil rouge narratif (Le Parallaxe) via des entrées spéciales.

## 3. Types d’éléments archivés
Le Stellarpedia indexe les catégories suivantes :
- **Planètes**
- **Systèmes stellaires**
- **Artefacts**
- **Faune**
- **Flore**
- **Espèces intelligentes**
- **Anomalies et phénomènes**
- **Fragments d’histoires ou événements historiques**
- **Entrées cryptiques liées au Parallaxe (privées)**

Chaque catégorie correspond à un objet réel du monde persistant.

## 4. Structure des données

### 4.1 Articles
Chaque objet découvert possède un article unique.
```sql
codex_articles
- id
- discoverable_type   -- 'planet', 'system', 'artifact', etc.
- discoverable_id
- slug
- canonical_entry_id  -- entrée publiée par défaut
- created_at
- updated_at
```

### 4.2 Entrées
Les entrées sont les propositions des joueurs.
```sql
codex_entries
- id
- codex_article_id
- author_player_id
- title
- short_label
- body_lore
- body_scientific
- body_biological
- body_cultural
- status            -- 'draft', 'pending_review', 'published', 'rejected'
- is_official       -- bool
- created_at
- updated_at
- reviewed_by_admin_id
- rejected_reason
```

### 4.3 Votes
```sql
codex_entry_votes
- id
- codex_entry_id
- voter_player_id
- value (+1 ou -1)
- created_at
```

### 4.4 Tags
```sql
codex_tags
- id
- name

codex_entry_tags
- id
- codex_entry_id
- codex_tag_id
```

## 5. Fonctionnement du flux de contribution

### 5.1 Création d’un article
Lorsqu’un joueur découvre un objet pour la première fois :
1. Vérification de l’existence d’un article.
2. Création automatique d’un `codex_article` si nécessaire.
3. Le joueur obtient l’autorisation de proposer la fiche initiale.

### 5.2 Proposer une nouvelle entrée
Conditions :
- avoir découvert l’objet
- ne pas avoir dépassé la limite quotidienne (1 contribution/jour)

Étapes :
1. Le joueur rédige son entrée.
2. Ajoute des tags.
3. Envoie en modération (`pending_review`).

### 5.3 Validation
Trois modes possibles :

**A. Validation admin**  
L’administrateur accepte ou rejette l’entrée.

**B. Vote communautaire**  
Les entrées très votées peuvent devenir canonique automatiquement.

**C. Canonisation manuelle**  
L’administrateur choisit l’entrée canonique d’un article.

## 6. Consultation

### 6.1 Vue Article
Affiche :
- Nom canonique
- Description officielle
- Image(s)
- Caractéristiques essentielles
- Entrées alternatives
- Auteur(s)
- Tags

### 6.2 Vue Entrée
Affiche :
- Titre et corps
- Statut
- Votes
- Auteur
- Date

## 7. Intégration au gameplay
- Chaque expédition peut débloquer un objet du Stellarpedia.
- L’explorateur qui découvre un objet peut en nommer la version officielle.
- Les joueurs peuvent enrichir le contenu selon leurs découvertes.
- Le Stellarpedia augmente la valeur narrative du jeu.

## 8. Récompenses
- XP d’exploration pour chaque contribution acceptée.
- Titres honorifiques (ex. "Archiviste Stellaire").
- Badges profil.
- Classements mensuels.

## 9. Gestion du contenu sensible
- Filtrage automatique des mots interdits.
- Revue admin obligatoire pour les premières versions.
- Possibilité de signaler une entrée.

## 10. Les entrées du Parallaxe
Les entrées liées au Parallaxe :
- ne sont pas publiques
- n’apparaissent que pour le joueur concerné
- prennent la forme de fragments cryptiques
- ne peuvent pas être votées ou modifiées

Ces entrées participent au fil rouge narratif.

## 11. Objectifs long terme
- Faire du Stellarpedia la mémoire vivante du jeu.
- Centraliser toutes les découvertes.
- Encourager une implication modérée mais régulière.
- Permettre à la communauté de construire un univers riche et cohérent.

