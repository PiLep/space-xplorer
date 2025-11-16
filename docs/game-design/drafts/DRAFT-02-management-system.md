# DRAFT - Système de Gestion Légère

## Statut
**Draft** - En attente de validation avec Alex (Product Manager)

## Issue Associée
À créer après validation du draft

## Vue d'Ensemble

Le système de gestion de Stellar introduit une couche stratégique simple, jouable en quelques minutes par jour. Il permet aux joueurs d'améliorer leur station, leur vaisseau, leur équipage et leurs capacités d'exploration, sans créer une boucle de grind ou une obligation de présence prolongée.

**Objectifs** :
- Offrir une progression douce, lisible, et liée à l'exploration
- Sessions courtes : toutes les actions réalisables en moins de 2 minutes
- Impact réel : chaque élément influence le gameplay
- Simplicité : aucune micro-gestion complexe
- Progression lente mais satisfaisante

## Principes Fondamentaux

1. **Simplicité** : Aucune micro-gestion complexe
2. **Impact réel** : Chaque élément de gestion influence le gameplay
3. **Sessions courtes** : Toutes les actions réalisables en moins de 2 minutes
4. **Progression lente mais satisfaisante** : La gestion accompagne la boucle quotidienne
5. **Intégration au lore** : Technologies, modules et crew s'inscrivent dans l'univers

## Règles de Base

### Activation

Le système de gestion est toujours actif. Le joueur peut :
- Collecter les ressources quotidiennes (automatique ou manuel)
- Vérifier l'état de son vaisseau
- Effectuer des réparations si nécessaire
- Améliorer sa station, son vaisseau, ou installer des modules
- Gérer son équipage (attribution, récupération)

### Fonctionnement

**Cycle quotidien de gestion** (60 à 120 secondes) :
1. Collecte des ressources quotidiennes
2. Vérification de l'intégrité du vaisseau
3. Réparation rapide si nécessaire
4. Option d'amélioration disponible
5. Attribution automatique ou manuelle du crew
6. Impact des améliorations sur la prochaine expédition

### Conditions

- Les ressources quotidiennes sont générées automatiquement
- Les améliorations nécessitent des ressources suffisantes
- Les réparations nécessitent des matériaux
- Le crew doit être en bonne santé pour être assigné

### Interactions

**Avec les mini-jeux** :
- Les statistiques du vaisseau influencent la difficulté
- Les modules améliorent les scores possibles
- Les améliorations débloquent des bonus rares

**Avec les expéditions** :
- La gestion contrôle la distance maximale
- Les stats du vaisseau influencent la rareté des découvertes
- Les modules réduisent les risques

**Avec le Stellarpedia** :
- Certaines améliorations débloquent de nouvelles catégories
- Les modules améliorent les analyses des découvertes

**Avec le système narratif CYOA** :
- Les stats du vaisseau influencent les chances de réussite
- Le crew apporte des bonus selon leurs rôles
- Les modules débloquent des choix supplémentaires

## Éléments du Système

### 1. Station Orbitale (Base du Joueur)

La station sert de hub principal du joueur.

**Fonctionnalités améliorables** :
- **Laboratoire** : Augmente les données produites quotidiennement
- **Antenne longue portée** : Améliore les scans et mini-jeux de détection
- **Hangar** : Débloque des options pour le vaisseau
- **Module de stabilisation** : Réduit les risques d'événements négatifs

**Améliorations** :
- Niveaux simples : 1 → 5
- Coût exponentiel léger : Niveau N = Base × (1.5^N)
- Impact linéaire : +20% par niveau

**Exemple** :
- Laboratoire Niveau 1 : +50 données/jour
- Laboratoire Niveau 2 : +75 données/jour (+50%)
- Laboratoire Niveau 3 : +112 données/jour (+50%)

### 2. Vaisseau

Le vaisseau est le principal outil d'exploration.

**Stats principales** :
- **Fuel** : Capacité de carburant (détermine la portée)
- **Intégrité de la coque** : Résistance aux dégâts (0-100)
- **Puissance du moteur** : Vitesse et portée d'expédition
- **Qualité des scanners** : Précision des scans et mini-jeux
- **Boucliers** : Protection contre les dangers

**Améliorations** :
- Chaque stat peut être améliorée indépendamment
- Coût en matériaux et données scientifiques
- Impact direct sur le gameplay

**Influence sur le gameplay** :
- **Fuel** : Distance maximale d'expédition
- **Intégrité** : Résistance aux dégâts dans les événements
- **Moteur** : Réduction du temps d'expédition
- **Scanners** : Bonus aux mini-jeux et découvertes
- **Boucliers** : Réduction des risques physiques

### 3. Modules

Équipements installables sur la station ou le vaisseau.

**Types de modules** :
- **Scanner avancé** : +15% précision mini-jeux scan
- **Module d'analyse biologique** : Découvertes biologiques améliorées
- **Amplificateur de signal** : +20% portée des scans
- **Blindage léger** : -10% dégâts subis
- **Purificateur d'atmosphère** : Réduction des risques d'événements négatifs

**Obtention** :
- Fragments d'artefacts (assemblage)
- Récompenses d'événements rares
- Récompenses de mini-jeux exceptionnels

**Impact** :
- Influencent directement les probabilités de réussite
- Débloquent des interactions ou résultats rares
- Améliorent les capacités du vaisseau/station

### 4. Crew (Optionnel et Léger)

Le joueur peut posséder jusqu'à **3 membres d'équipage**.

**Caractéristiques** :
- **Rôle** : Navigation, Science, Sécurité
- **Trait** : Calme, Instable, Méthodique, Audacieux, etc.
- **État** : Stable → Blessé → Rétabli

**Impact sur le gameplay** :
- **Scientifique** : +10% succès choix analytiques (CYOA), +5% données
- **Navigateur** : +10% succès choix prudents (CYOA), +10% portée
- **Sécurité** : -15% dégâts en cas d'échec (CYOA), +5% intégrité

**Gestion** :
- Attribution automatique ou manuelle
- Récupération après blessure (24-48h)
- Possibilité de remplacer un membre (coût en ressources)

## Ressources Principales

### 1. Données Scientifiques

**Génération** :
- Production quotidienne automatique (base : 100/jour)
- Bonus du Laboratoire (niveau)
- Récompenses d'expéditions, mini-jeux, événements

**Utilisation** :
- Améliorations de la station
- Améliorations du vaisseau
- Certains modules

### 2. Fragments d'Artefacts

**Obtention** :
- Expéditions (découvertes rares)
- Mini-jeux (scores élevés)
- Événements narratifs (choix audacieux)

**Utilisation** :
- Assemblage de modules uniques
- Améliorations spéciales

### 3. Matériaux

**Obtention** :
- Expéditions (planètes riches)
- Événements narratifs (succès)
- Récompenses exceptionnelles

**Utilisation** :
- Améliorations majeures
- Réparations importantes
- Modules avancés

## Système d'Amélioration

### Règles Générales

- Coût clair et limité
- Pas de timers multiples
- Un seul bouton : "Améliorer"
- Progression linéaire ou légèrement exponentielle
- Feedback immédiat

### Modèle de Coût

**Station** :
- Niveau N : Coût = Base × (1.5^N) en données scientifiques
- Impact : +20% par niveau

**Vaisseau** :
- Stat N : Coût = Base × (1.3^N) en données + matériaux
- Impact : +15% par niveau

**Modules** :
- Coût fixe en fragments d'artefacts
- Impact unique selon le module

## Équilibrage

### Progression

**Courbe de progression** :
- Niveaux 1-3 : Accessibles rapidement (1-3 jours)
- Niveaux 4-5 : Progression modérée (1-2 semaines)
- Modules : Progression lente (2-4 semaines)

**Impact sur le gameplay** :
- Niveaux bas : Impact modéré mais visible
- Niveaux moyens : Impact significatif
- Niveaux élevés : Impact majeur mais rare

### Coûts et Récompenses

**Coûts d'amélioration** :
- Station Niveau 1 : 100 données
- Station Niveau 2 : 150 données
- Station Niveau 3 : 225 données
- Station Niveau 4 : 337 données
- Station Niveau 5 : 506 données

**Récompenses quotidiennes** :
- Base : 100 données/jour
- Avec Laboratoire Niveau 3 : +112 données/jour
- Total : 212 données/jour

**Temps de récupération** :
- Niveau 1 → 2 : 1 jour
- Niveau 2 → 3 : 1.5 jours
- Niveau 3 → 4 : 2 jours
- Niveau 4 → 5 : 3 jours

### Scaling

- Les améliorations deviennent plus coûteuses mais restent accessibles
- L'impact reste proportionnel au coût
- Pas de "mur de progression" infranchissable

## Cycle Quotidien de Gestion

**Durée totale** : 60 à 120 secondes

1. **Collecte** (5-10s) : Clic sur "Collecter les ressources"
2. **Vérification** (10-20s) : Vérification automatique de l'état
3. **Réparation** (10-30s si nécessaire) : Réparation rapide si dégâts
4. **Amélioration** (30-60s si ressources suffisantes) : Choix d'amélioration
5. **Crew** (10-20s) : Attribution automatique ou manuelle
6. **Impact** : Affichage des bonus pour la prochaine expédition

**Principe** : La gestion ne doit jamais ralentir la session.

## Intégration avec les Autres Systèmes

### Avec les Mini-Games

Les statistiques du vaisseau influencent :
- Difficulté des mini-jeux (-10% à -30% selon le niveau)
- Score maximal (+10% à +30% selon le niveau)
- Possibilités d'apparition d'un bonus rare (+5% à +15%)

### Avec les Expéditions

La gestion contrôle :
- Distance maximale (Fuel + Moteur)
- Rareté des découvertes (Scanners)
- Risques subis (Intégrité + Boucliers)
- Chances d'indices du Parallaxe (Modules spécialisés)

### Avec le Codex

Certaines améliorations débloquent :
- Nouvelles catégories (Module d'analyse biologique)
- Meilleures analyses (Scanners avancés)
- Versions plus complètes des découvertes (Laboratoire niveau élevé)

### Avec le Système Narratif CYOA

La gestion contrôle :
- Chances de réussite (Stats du vaisseau)
- Choix disponibles (Modules)
- Récompenses (Crew)

## Progression Long Terme

Le système de gestion doit soutenir la durée de vie du jeu :

- **Amélioration de station** → Boost passif quotidien
- **Amélioration de vaisseau** → Exploration plus profonde
- **Modules** → Personnalisation et spécialisation
- **Crew** → Variété et ambiance

**Objectif** : Garder une boucle de progression douce et intéressante sans créer de pression.

## Exemples et Cas d'Usage

### Exemple 1 : Amélioration du Laboratoire

**Situation** : Joueur niveau 3, Laboratoire niveau 2

**Coût** : 225 données scientifiques
**Impact** : +37 données/jour (de 150 à 187/jour)
**Temps de récupération** : ~1.2 jours

**Résultat** : Le joueur peut améliorer son laboratoire tous les 1-2 jours, créant une progression régulière et satisfaisante.

### Exemple 2 : Installation d'un Module

**Situation** : Joueur a collecté 5 fragments d'artefacts

**Choix** : Assembler un "Scanner avancé"
**Coût** : 5 fragments d'artefacts
**Impact** : +15% précision mini-jeux scan, déblocage de choix supplémentaires dans les événements

**Résultat** : Le joueur se spécialise dans l'exploration, améliorant ses capacités de découverte.

### Exemple 3 : Gestion du Crew

**Situation** : Joueur a un scientifique blessé et un navigateur disponible

**Choix** : Remplacer temporairement le scientifique par le navigateur
**Impact** : Perte du bonus scientifique, gain du bonus navigation
**Récupération** : Le scientifique récupère en 24h

**Résultat** : Le joueur adapte sa stratégie selon les besoins de l'expédition.

## Cas Limites

1. **Ressources insuffisantes** : Le joueur ne peut pas améliorer, mais peut toujours jouer
2. **Vaisseau endommagé** : Réparation automatique possible si matériaux suffisants
3. **Crew tous blessés** : Le joueur peut continuer sans crew (pas de bonus)
4. **Améliorations maximales** : Niveau 5 atteint, possibilité de modules supplémentaires

## Métriques à Surveiller

### Métriques de Progression
- Temps moyen pour atteindre chaque niveau
- Distribution des niveaux des joueurs
- Taux d'amélioration par type (station vs vaisseau vs modules)

### Métriques d'Engagement
- Fréquence d'interaction avec le système de gestion
- Temps moyen passé sur la gestion quotidienne
- Taux d'utilisation des améliorations

### Métriques d'Équilibrage
- Distribution des ressources collectées
- Taux de satisfaction des coûts
- Impact réel des améliorations sur le gameplay

## Implémentation Technique

### Spécifications

**Tables de base de données** :
```sql
player_upgrades
- id
- player_id
- target_type ('station', 'ship', 'module')
- target_id (id de la station/ship/module)
- level (1-5 pour station/ship, 1 pour modules)
- created_at
- updated_at

player_resources
- id
- player_id
- data_scientific (int)
- artifact_fragments (int)
- materials (int)
- updated_at

player_ships
- id
- player_id
- fuel (int, 0-100)
- hull_integrity (int, 0-100)
- engine_power (int, 1-100)
- scanner_quality (int, 1-100)
- shields (int, 0-100)
- created_at
- updated_at

player_modules
- id
- player_id
- module_type (string)
- installed_on ('station', 'ship')
- created_at

crew_members
- id
- player_id
- role ('navigation', 'science', 'security')
- trait (string)
- status ('stable', 'injured', 'recovering')
- injured_until (datetime, nullable)
- created_at
- updated_at

daily_productions
- id
- player_id
- data_scientific (int)
- produced_at (date)
- collected (bool)
- created_at
```

**Système de production quotidienne** :
- Tâche programmée (Laravel Schedule) génère les ressources
- Collection manuelle ou automatique après 24h
- Calcul des bonus selon les améliorations

**Système d'amélioration** :
- Vérification des ressources disponibles
- Calcul du coût selon le niveau
- Application immédiate de l'amélioration
- Mise à jour des stats

### Points d'Attention

1. **Performance** : Les calculs doivent être rapides (< 200ms)
2. **Équilibrage** : Les coûts doivent être testés et ajustés
3. **UX** : Interface simple et claire
4. **Progression** : Pas de mur de progression infranchissable

### Tests à Prévoir

1. **Tests unitaires** :
   - Calcul des coûts d'amélioration
   - Calcul de la production quotidienne
   - Application des bonus

2. **Tests d'intégration** :
   - Cycle quotidien complet
   - Améliorations et leurs impacts
   - Gestion du crew

3. **Tests d'équilibrage** :
   - Temps de récupération des ressources
   - Impact réel des améliorations
   - Satisfaction des joueurs

## Historique

- Création du draft initial basé sur `management-concept.md`

## Références

- **[management-concept.md](../local-brainstorming-data/management-concept.md)** : Document source du brainstorming
- **[GAME-DESIGNER.md](../agents/GAME-DESIGNER.md)** : Documentation de l'agent Game Designer
- **[design-game-mechanic.md](../prompts/design-game-mechanic.md)** : Guide pour concevoir des mécaniques

