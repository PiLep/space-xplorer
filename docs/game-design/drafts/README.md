# Drafts de Game Design - Stellar

Ce dossier contient les drafts de conception des mécaniques de jeu pour Stellar. Ces documents sont en attente de validation avec Alex (Product Manager) avant de devenir des spécifications officielles.

## Statut Global

**Tous les drafts sont en statut "Draft"** - En attente de validation avec Alex (Product Manager)

## Vue d'Ensemble

Ces drafts ont été créés à partir des documents de brainstorming dans `docs/local-brainstorming-data/`. Ils structurent et formalisent les idées en mécaniques de jeu complètes, équilibrées et prêtes pour l'implémentation.

## Drafts Disponibles

### 1. Système Narratif CYOA (Choose Your Own Adventure)

**Fichier** : `DRAFT-01-narrative-cyoa-system.md`

**Description** : Système narratif à choix multiples inspiré des "livres dont vous êtes le héros". Permet aux joueurs de prendre des décisions impactantes dans des micro-scénarios quotidiens.

**Points Clés** :
- 3 choix par événement (Prudent, Analytique, Audacieux)
- Événements quotidiens et événements d'expédition
- Micro-arcs narratifs sur plusieurs jours
- Intégration avec le système de gestion (vaisseau, crew, modules)
- Intégration subtile avec Le Parallaxe

**Source** : `adventure-concept.md`

---

### 2. Système de Gestion Légère

**Fichier** : `DRAFT-02-management-system.md`

**Description** : Système de gestion simple et quotidienne permettant d'améliorer la station, le vaisseau, les modules et le crew.

**Points Clés** :
- 4 axes : Station orbitale, Vaisseau, Modules, Crew
- 3 ressources : Données scientifiques, Fragments d'artefacts, Matériaux
- Sessions courtes (60-120 secondes)
- Impact réel sur le gameplay
- Progression lente mais satisfaisante

**Source** : `management-concept.md`

---

### 3. Système de Mini-Jeux Quotidiens

**Fichier** : `DRAFT-03-mini-games-system.md`

**Description** : Interactions courtes (10-90 secondes) jouées une fois par jour pour ajouter de l'interactivité à la boucle quotidienne.

**Points Clés** :
- 5 types de mini-jeux : Scan Circulaire, Décryptage, Navigation Astéroïdes, Analyse d'Échantillons, Harmonisation d'Ondes
- 1 mini-jeu quotidien recommandé
- Récompenses selon le score (0-100)
- Intégration avec le système de gestion
- Possibilité d'indices du Parallaxe

**Source** : `mini-games.md`

---

### 4. Système Stellarpedia (Codex Communautaire)

**Fichier** : `DRAFT-04-stellarpedia-system.md` (version complète)  
**Fichier MVP** : `DRAFT-04-stellarpedia-system-MVP.md` (version MVP validée)

**Description** : Système centralisé d'archives knowledge-base regroupant toutes les découvertes des explorateurs.

**Version MVP (Validée)** :
- Wiki public basique accessible à tous (joueurs et non-joueurs)
- Affichage de toutes les planètes découvertes
- Nommage des planètes par les découvreurs
- Descriptions générées automatiquement via IA
- Design superbe, style encyclopédie spatiale
- **Issue** : [ISSUE-008](../../issues/ISSUE-008-implement-public-wiki-stellarpedia.md)

**Version Complète (Futur)** :
- 9 types d'éléments archivés (planètes, systèmes, artefacts, faune, flore, etc.)
- Contributions communautaires (1/jour maximum)
- Système de votes et canonisation
- Entrées secrètes du Parallaxe (privées)
- Récompenses pour les contributeurs

**Source** : `stellarpedia.md`

---

### 5. Système d'Onboarding

**Fichier** : `DRAFT-05-onboarding-system.md`

**Description** : Première expérience du joueur dans Stellar, immersive et brève (3-5 minutes).

**Points Clés** :
- 6 étapes scénarisées
- Génération de la planète d'origine
- Introduction aux mécaniques (mini-jeu, choix narratif)
- Nomination personnalisée de la planète
- Introduction au Stellarpedia
- Premier indice du Parallaxe possible (30% chance)

**Source** : `onboarding.md`

**Note** : ISSUE-005 existe déjà pour l'onboarding MVP, ce draft complète la vision game design.

---

### 6. Le Parallaxe (Fil Rouge Narratif)

**Fichier** : `DRAFT-06-parallaxe-system.md`

**Description** : Mystère personnel et persistant qui suit chaque explorateur. Phénomène anormal se manifestant rarement sous des formes subtiles.

**Points Clés** :
- 6 types de manifestations : Signaux altérés, Découvertes impossibles, Entrées secrètes, Logs fantômes, Messages cryptés, Glitches
- Fréquence : 1 indice toutes les 10-20 sessions
- Progression non linéaire sur plusieurs mois
- Journal secret personnel
- Jamais de révélation complète

**Source** : `parallax.md`

---

## Structure des Drafts

Chaque draft suit la structure standard définie dans `design-game-mechanic.md` :

1. **Statut** : État actuel du draft
2. **Issue Associée** : Lien vers l'issue produit (si existe)
3. **Vue d'Ensemble** : Description générale et objectifs
4. **Règles de Base** : Activation, fonctionnement, conditions, interactions
5. **Équilibrage** : Probabilités, coûts, récompenses, progression, scaling
6. **Exemples et Cas d'Usage** : Exemples concrets
7. **Cas Limites** : Situations exceptionnelles à gérer
8. **Métriques à Surveiller** : Métriques de gameplay importantes
9. **Implémentation Technique** : Spécifications techniques, points d'attention, tests
10. **Historique** : Historique des modifications
11. **Références** : Liens vers les documents sources

## Prochaines Étapes

### 1. Validation avec Alex (Product Manager)

Chaque draft doit être validé avec Alex pour :
- Vérifier que les mécaniques répondent aux besoins utilisateurs
- Valider l'équilibrage et les récompenses
- S'assurer de la cohérence avec la vision produit
- Ajuster si nécessaire

### 2. Création des Issues Produit

Une fois validés, les drafts serviront de base pour créer des issues produit dans `docs/issues/` :
- Une issue par système majeur
- Référence au draft validé
- Priorisation avec Alex

### 3. Création des Plans de Développement

Sam (Lead Developer) créera des plans de développement dans `docs/tasks/` :
- Transformation des mécaniques en spécifications techniques
- Décomposition en tâches implémentables
- Estimation et priorisation

### 4. Implémentation

Jordan (Fullstack Developer) implémentera les mécaniques selon les plans :
- Code fonctionnel
- Tests
- Validation fonctionnelle

## Dépendances entre Systèmes

Voici l'ordre logique de développement basé sur les dépendances :

1. **Onboarding** (DRAFT-05) → Débloque la boucle quotidienne
2. **Système de Gestion** (DRAFT-02) → Base pour les autres systèmes
3. **Mini-Jeux** (DRAFT-03) → Intégration dans la boucle quotidienne
4. **Système Narratif CYOA** (DRAFT-01) → Utilise la gestion et les mini-jeux
5. **Stellarpedia** (DRAFT-04) → Utilise les découvertes des expéditions
6. **Le Parallaxe** (DRAFT-06) → Intègre tous les systèmes précédents

Voir `dependancies-tree.md` pour plus de détails sur les dépendances.

## Roadmap de Développement

Les drafts s'intègrent dans la roadmap définie dans `roadmap.md` :

- **Phase 1** : Onboarding, Daily Loop basique, Mini-jeux v1
- **Phase 2** : Gestion, Expéditions enrichies, Mini-jeux supplémentaires
- **Phase 3** : Stellarpedia complet, CYOA avancé, Discoveries étendus
- **Phase 4** : Parallaxe niveau 1, Micro-arcs persistants
- **Phase 5** : Parallaxe niveau 2, Events globaux

## Notes Importantes

### Équilibrage

Tous les chiffres d'équilibrage dans les drafts sont des **estimations initiales**. Ils devront être ajustés après :
- Tests avec des joueurs réels
- Analyse des métriques de gameplay
- Retours de la communauté

### Évolution

Les drafts peuvent évoluer au fil du développement :
- Ajustements d'équilibrage
- Ajouts de fonctionnalités
- Simplifications si nécessaire
- Intégration de retours utilisateurs

### Documentation

Les drafts servent de référence pour :
- L'équipe de développement
- Les tests fonctionnels
- Les ajustements d'équilibrage
- La documentation utilisateur

## Contact

Pour toute question sur les drafts :
- **Casey (Game Designer)** : Conception et équilibrage des mécaniques
- **Alex (Product Manager)** : Validation produit et priorisation
- **Sam (Lead Developer)** : Faisabilité technique et architecture

---

**Statut** : Tous les drafts sont en attente de validation

