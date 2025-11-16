# DRAFT - Système de Mini-Jeux Quotidiens

## Statut
**Draft** - En attente de validation avec Alex (Product Manager)

## Issue Associée
À créer après validation du draft

## Vue d'Ensemble

Les mini-jeux sont des interactions courtes (10–90 secondes) jouées une fois par jour. Ils remplissent plusieurs fonctions dans la boucle quotidienne de Stellar.

**Objectifs** :
- Ajouter un moment d'interactivité dans la session quotidienne
- Donner un sentiment de progression rapide
- Offrir des récompenses variées (données, fragments, découvertes)
- Introduire de la tension et du risque léger
- Servir parfois de support à un indice du Parallaxe
- Rester suffisamment simples pour ne jamais devenir chronophages

## Règles de Base

### Activation

- **1 mini-jeu quotidien** recommandé par défaut
- Des versions spéciales peuvent apparaître lors d'événements majeurs
- Le mini-jeu est disponible après la collecte des ressources quotidiennes
- Un seul essai par jour (pas de retry)

### Fonctionnement

**Déroulement** :
1. Sélection aléatoire d'un type de mini-jeu (selon la progression)
2. Présentation du mini-jeu avec instructions courtes
3. Exécution du mini-jeu (10-90 secondes)
4. Calcul du score et détermination du résultat
5. Attribution des récompenses selon le score
6. Affichage des résultats

### Conditions

- Le joueur doit avoir collecté ses ressources quotidiennes
- Un seul mini-jeu par jour (reset à minuit)
- Les mini-jeux sont toujours accessibles (pas de prérequis)

### Interactions

**Avec le système de gestion** :
- Les statistiques du vaisseau influencent la difficulté
- Les modules améliorent les scores possibles
- Les améliorations débloquent des bonus rares

**Avec les expéditions** :
- Les récompenses peuvent inclure des coordonnées partiellement révélées
- Les découvertes peuvent déclencher des mini-jeux spéciaux

**Avec le Stellarpedia** :
- Les mini-jeux peuvent révéler des entrées cachées
- Les fragments de lore enrichissent le Stellarpedia

**Avec Le Parallaxe** :
- Certains mini-jeux peuvent contenir des anomalies impossibles
- Les signaux altérés peuvent apparaître

## Caractéristiques Générales

### Durée

- **Minimum** : 10 secondes
- **Maximum** : 90 secondes
- **Cible** : 30-60 secondes pour la majorité

### Nombre par Jour

- **1 mini-jeu quotidien** recommandé par défaut
- Des versions spéciales peuvent apparaître lors d'événements majeurs
- Pas de limite si événement spécial

### Difficulté

- **Légère, jamais punitive** : Le joueur peut toujours obtenir une récompense minimale
- Basée sur la précision, la rapidité ou la logique simple
- Adaptation dynamique selon la progression du joueur

### Récompenses Possibles

- Données scientifiques (base : 50-200)
- Fragments d'artefacts (1-3 selon le score)
- Informations partielles (coordonnées, découvertes)
- Découvertes rares (planètes, artefacts)
- Bonus temporaires (buff pour la prochaine expédition)
- Potentiels indices du Parallaxe (très rares)

### Risques

Léger mais existant :
- Petite perte de ressources si échec critique (score < 25)
- Bruit parasite dans les scanners (réduction temporaire de précision)
- Altération mineure du vaisseau (réduction de 5-10% d'une stat)

## Types de Mini-Jeux

### 1. Scan Circulaire

**Principe** : Un radar affiche des signaux éphémères. Le joueur doit cliquer au bon moment pour les "verrouiller".

**Mécanique** :
- Signaux apparaissent et disparaissent rapidement (1-3 secondes)
- Le joueur doit cliquer quand le signal est dans la zone optimale
- 5-10 signaux à verrouiller
- Score basé sur la précision et le nombre de signaux verrouillés

**Objectifs** :
- Précision
- Observation
- Réflexes légers

**Récompenses** :
- Score 0-25 : 50 données
- Score 25-60 : 100 données + 1 fragment
- Score 60-85 : 150 données + 2 fragments
- Score 85-100 : 200 données + 3 fragments + découverte rare

**Intégration Parallaxe** : Un signal impossible à verrouiller peut apparaître (très rare, < 1%)

**Durée** : 30-60 secondes

### 2. Décryptage de Signaux

**Principe** : Un message est bruité. Le joueur doit déplacer, aligner ou filtrer certaines parties pour révéler une portion lisible.

**Mécanique** :
- Message affiché avec du bruit visuel
- Le joueur doit ajuster des filtres ou déplacer des éléments
- 3-5 ajustements nécessaires
- Score basé sur la précision des ajustements

**Objectifs** :
- Logique simple
- Exploration narrative
- Reconnaissance de patterns

**Récompenses** :
- Score 0-25 : 50 données + fragment de lore
- Score 25-60 : 100 données + 1 fragment + coordonnées partielles
- Score 60-85 : 150 données + 2 fragments + coordonnées complètes
- Score 85-100 : 200 données + 3 fragments + découverte + entrée Stellarpedia

**Intégration Parallaxe** : Symboles inconnus ou distorsions brèves (rare, < 2%)

**Durée** : 45-90 secondes

### 3. Navigation dans un Champ d'Astéroïdes

**Principe** : L'écran comporte 3 zones verticales. Le joueur déplace le vaisseau pour éviter des obstacles qui descendent.

**Mécanique** :
- Vaisseau contrôlable dans 3 voies
- Astéroïdes descendent à vitesse variable
- 30-60 secondes de navigation
- Score basé sur le nombre d'obstacles évités et la durée

**Objectifs** :
- Réflexe léger
- Gestion du risque
- Coordination main-œil

**Récompenses** :
- Score 0-25 : 50 données (dégâts mineurs : -5 intégrité)
- Score 25-60 : 100 données (intégrité conservée)
- Score 60-85 : 150 données + 1 fragment (intégrité conservée)
- Score 85-100 : 200 données + 2 fragments + données sur densité locale

**Risques** : Légers dégâts si collision (-5 à -15 intégrité)

**Durée** : 30-60 secondes

### 4. Analyse d'Échantillons

**Principe** : Le joueur doit associer des motifs ou structures pour identifier un échantillon inconnu.

**Mécanique** :
- Échantillon affiché avec caractéristiques visibles
- Le joueur doit associer avec des motifs connus
- 3-5 associations nécessaires
- Score basé sur la précision des associations

**Objectifs** :
- Reconnaissance visuelle
- Logique de classification
- Mémoire des patterns

**Récompenses** :
- Score 0-25 : 50 données
- Score 25-60 : 100 données + élément biologique
- Score 60-85 : 150 données + 1 fragment + données faune/flore
- Score 85-100 : 200 données + 2 fragments + objet codex + entrée Stellarpedia

**Durée** : 45-75 secondes

### 5. Harmonisation d'Ondes

**Principe** : Plusieurs courbes d'ondes sont affichées. Le joueur doit ajuster les fréquences pour les aligner.

**Mécanique** :
- 3-5 ondes affichées avec fréquences différentes
- Le joueur ajuste des sliders pour aligner les fréquences
- Score basé sur la précision de l'alignement
- Temps limité (60 secondes)

**Objectifs** :
- Précision
- Sens du rythme
- Coordination fine

**Récompenses** :
- Score 0-25 : 50 données
- Score 25-60 : 100 données + amplification de scan temporaire
- Score 60-85 : 150 données + 1 fragment + signaux rares révélés
- Score 85-100 : 200 données + 2 fragments + signal exceptionnel + module possible

**Intégration Parallaxe** : Une onde impossible à stabiliser (rare, < 1%)

**Durée** : 45-90 secondes

## Difficulté et Scoring

### Difficulté Dynamique

La difficulté peut évoluer selon :
- **Progression du joueur** : +5% difficulté par niveau (max +50%)
- **Modules installés** : -10% à -30% difficulté selon les modules
- **Événements récents** : Modifications temporaires (+/- 10%)

### Calcul du Score

**Paramètres possibles** :
- Précision (pourcentage de réussite)
- Vitesse (temps de complétion)
- Nombre d'erreurs (pénalités)
- Alignement optimal (pour harmonisation)

**Formule générale** :
```
Score = (Précision × 0.6) + (Vitesse × 0.2) + (Qualité × 0.2) - (Erreurs × 5)
```

### Interprétation du Score

| Score | Résultat | Récompense Base |
|-------|----------|-----------------|
| 0-25  | Échec léger | 50 données |
| 25-60 | Réussite minimale | 100 données + 1 fragment |
| 60-85 | Bonne performance | 150 données + 2 fragments |
| 85-100| Réussite exceptionnelle | 200 données + 3 fragments + bonus |

## Récompenses Détaillées

### Récompenses Fixes

- **Données scientifiques** : Toujours attribuées selon le score
- **Fragments simples** : À partir de score 25+

### Récompenses Variables

- **Signaux rares** : Score 60+ (10% chance)
- **Découvertes d'un objet du Stellarpedia** : Score 85+ (5% chance)
- **Coordonnées partiellement révélées** : Score 60+ (15% chance)

### Récompenses Exceptionnelles

- **Artefact mineur** : Score 100 (1% chance)
- **Entrée codex cachée** : Score 95+ (2% chance)
- **Message cryptique lié au Parallaxe** : Score 90+ (0.5% chance)

## Intégration au Cycle Quotidien

Les mini-games s'intègrent à la boucle journalière :

1. **Événement du jour** (CYOA)
2. **Mini-jeu** ← Ici
3. **Expédition**
4. **Mise à jour du Codex**

Chaque mini-jeu apporte une variation subtile sans rallonger la session.

## Intégration Technique

### Tables de Stockage

```sql
mini_game_attempts
- id
- player_id
- type ('scan_circular', 'signal_decrypt', 'asteroid_field', 'sample_analysis', 'wave_harmony')
- score (0-100)
- success (bool)
- context (JSON - stores game state, modifiers, etc.)
- duration_ms (int)
- rewards (JSON - stores rewards granted)
- created_at

mini_game_types
- id
- code (string, unique)
- name (string)
- description (text)
- min_duration_seconds (int)
- max_duration_seconds (int)
- base_difficulty (int, 1-100)
- enabled (bool)
- created_at
- updated_at
```

### Points d'Intégration

- **Événements** : Certains événements peuvent déclencher des mini-jeux spéciaux
- **Expéditions** : Les mini-jeux peuvent révéler des informations sur les expéditions
- **Récompenses** : Les récompenses sont intégrées dans le système de ressources
- **Progression** : Les scores influencent la progression globale

## Valeur Narrative

Les mini-jeux servent également :
- À introduire des fragments de lore
- À renforcer l'ambiance du jeu
- À faire apparaître des indices cryptiques
- À créer des moments d'étrangeté liés au Parallaxe

## Équilibrage

### Distribution des Types

- Scan Circulaire : 30%
- Décryptage de Signaux : 25%
- Navigation Astéroïdes : 20%
- Analyse d'Échantillons : 15%
- Harmonisation d'Ondes : 10%

### Récompenses Moyennes

- Score moyen attendu : 55-65
- Récompense moyenne : 120 données + 1.5 fragments
- Temps moyen : 45 secondes

### Progression

- Niveaux 1-5 : Mini-jeux simples, récompenses de base
- Niveaux 6-15 : Mini-jeux modérés, récompenses améliorées
- Niveaux 16+ : Mini-jeux complexes, récompenses exceptionnelles possibles

## Exemples et Cas d'Usage

### Exemple 1 : Scan Circulaire Réussi

**Situation** : Joueur niveau 5, Scanner qualité 60

**Déroulement** :
- 8 signaux à verrouiller
- 7 signaux verrouillés avec précision moyenne
- 1 signal manqué
- Temps : 42 secondes

**Score** : 72/100

**Récompenses** :
- 150 données scientifiques
- 2 fragments d'artefacts
- Coordonnées partiellement révélées (nouveau système stellaire)

### Exemple 2 : Harmonisation avec Anomalie Parallaxe

**Situation** : Joueur niveau 10, score exceptionnel (98/100)

**Déroulement** :
- 5 ondes à aligner
- Toutes alignées avec précision maximale
- Une onde impossible à stabiliser apparaît brièvement (anomalie Parallaxe)
- Temps : 38 secondes

**Score** : 98/100

**Récompenses** :
- 200 données scientifiques
- 3 fragments d'artefacts
- Signal exceptionnel décodé
- Indice cryptique du Parallaxe ajouté au journal secret

## Cas Limites

1. **Échec critique** : Score < 25, perte de ressources mineures
2. **Temps écoulé** : Score basé sur la progression jusqu'au timeout
3. **Anomalie Parallaxe** : Mini-jeu peut se terminer de manière inattendue
4. **Modules spéciaux** : Certains modules peuvent modifier les règles

## Métriques à Surveiller

### Métriques d'Engagement
- Temps moyen passé sur un mini-jeu
- Taux de complétion des mini-jeux
- Taux d'abandon avant la fin

### Métriques de Performance
- Distribution des scores par type de mini-jeu
- Temps moyen de complétion
- Taux de réussite par niveau de joueur

### Métriques d'Équilibrage
- Distribution des récompenses
- Impact des modificateurs (vaisseau, modules)
- Satisfaction des joueurs

## Implémentation Technique

### Spécifications

**Système de sélection** :
- Algorithme de sélection aléatoire pondéré
- Éviter la répétition immédiate (pas le même mini-jeu 2 jours de suite)
- Adaptation selon la progression

**Système de scoring** :
- Calcul en temps réel pendant le mini-jeu
- Affichage du score progressif (optionnel)
- Validation finale à la fin

**Système de récompenses** :
- Calcul des récompenses selon le score
- Application des bonus de modules
- Génération des récompenses rares (probabilités)

### Points d'Attention

1. **Performance** : Les mini-jeux doivent être fluides (60 FPS)
2. **Accessibilité** : Contrôles simples et clairs
3. **Variété** : Assez de types pour éviter la répétition
4. **Équilibrage** : Les récompenses doivent être équilibrées

### Tests à Prévoir

1. **Tests unitaires** :
   - Calcul des scores
   - Génération des récompenses
   - Application des modificateurs

2. **Tests d'intégration** :
   - Cycle quotidien complet avec mini-jeu
   - Intégration des récompenses
   - Intégration avec le système de gestion

3. **Tests d'équilibrage** :
   - Distribution des scores
   - Temps de complétion
   - Satisfaction des joueurs

## Historique

- Création du draft initial basé sur `mini-games.md`

## Références

- **[mini-games.md](../local-brainstorming-data/mini-games.md)** : Document source du brainstorming
- **[GAME-DESIGNER.md](../agents/GAME-DESIGNER.md)** : Documentation de l'agent Game Designer
- **[design-game-mechanic.md](../prompts/design-game-mechanic.md)** : Guide pour concevoir des mécaniques

