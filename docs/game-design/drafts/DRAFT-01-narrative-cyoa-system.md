# DRAFT - Système Narratif CYOA (Choose Your Own Adventure)

## Statut
**Draft** - En attente de validation avec Alex (Product Manager)

## Issue Associée
À créer après validation du draft

## Vue d'Ensemble

Le système narratif à choix multiples (CYOA) est inspiré des "livres dont vous êtes le héros". Il permet aux joueurs de prendre des décisions courtes mais impactantes dans des micro-scénarios quotidiens. Ces choix influencent les événements, les récompenses, le ton narratif, et peuvent parfois déclencher des indices liés au Parallaxe.

**Objectifs** :
- Offrir des moments narratifs intenses mais brefs (30-90 secondes)
- Renforcer la sensation d'exploration et d'inconnu
- Proposer des conséquences réelles sans complexité excessive
- Construire des micro-histoires cohérentes sur plusieurs jours
- Donner une structure narrative aux expéditions
- Intégrer subtilement des éléments du Parallaxe

## Règles de Base

### Activation

Un événement narratif peut se déclencher lorsque :
- Le joueur lance une expédition
- Le jeu génère l'événement quotidien (une fois par jour)
- Un palier important est atteint (milestone)
- Une anomalie du Parallaxe interfère (très rare)

### Fonctionnement

**Étape 1 : Déclenchement**
- Vérification des conditions de déclenchement
- Sélection d'un template d'événement selon la rareté et le contexte
- Génération d'une instance d'événement pour le joueur

**Étape 2 : Présentation**
Le joueur voit :
- Un résumé court de la situation (2-3 phrases)
- Une ambiance visuelle ou sonore (optionnelle)
- Trois choix clairs et distincts

**Étape 3 : Choix**
Toujours **trois options** :
- **Prudent** : faible risque, faible reward
- **Analytique** : équilibre risque/reward
- **Audacieux** : risque élevé, reward élevé

**Étape 4 : Résolution**
Calcul du résultat à partir de :
- Taux de base de réussite du choix
- Bonus/malus liés au vaisseau (modules, stats)
- Bonus/malus liés au crew (rôles, traits)
- Modules installés (déblocage de choix supplémentaires)
- Variables du Parallaxe (très rares)

Types d'issues :
- **Succès** : Récompense complète
- **Issue mitigée** : Récompense partielle, conséquences mineures
- **Échec** : Conséquences négatives, récompense minimale ou nulle

**Étape 5 : Conséquences**
- Ressources (données, matériaux, fragments)
- Découvertes (planètes, artefacts, entrées Stellarpedia)
- Dégâts (coque, fuel, intégrité)
- Fragments de lore
- Indices cryptiques du Parallaxe

**Étape 6 : Suite (optionnelle)**
Si `can_chain = true` :
- Un événement lié peut apparaître le lendemain ou plusieurs jours plus tard
- Réservé aux joueurs ayant pris une certaine branche

### Conditions

- Un joueur ne peut avoir qu'un seul événement narratif actif à la fois
- Un événement doit être résolu avant qu'un nouveau ne puisse apparaître
- Les événements quotidiens sont garantis une fois par jour (si le joueur est actif)
- Les événements d'expédition sont déclenchés lors du lancement d'une expédition

### Interactions

**Avec le système de gestion** :
- Les stats du vaisseau influencent les chances de réussite
- Les modules débloquent des choix supplémentaires ou améliorent les résultats
- Le crew apporte des bonus selon leurs rôles

**Avec les mini-jeux** :
- Les résultats des mini-jeux peuvent influencer les événements narratifs
- Certains événements peuvent déclencher des mini-jeux

**Avec le Stellarpedia** :
- Les découvertes peuvent créer des entrées dans le Stellarpedia
- Les fragments de lore enrichissent le Stellarpedia

**Avec Le Parallaxe** :
- Certains événements peuvent contenir des indices du Parallaxe
- Les branches secrètes peuvent révéler des éléments du mystère

## Équilibrage

### Probabilités

**Distribution des événements** :
- Événements autonomes : 70% (résolus en 1 interaction)
- Micro-arcs (2-3 événements) : 25%
- Arcs persistants (plusieurs semaines) : 4%
- Échos du Parallaxe : 1%

**Taux de réussite de base** :
- Choix Prudent : 80% succès, 15% mitigé, 5% échec
- Choix Analytique : 60% succès, 30% mitigé, 10% échec
- Choix Audacieux : 40% succès, 40% mitigé, 20% échec

**Modificateurs** :
- Vaisseau amélioré : +5% à +15% selon le niveau
- Crew compétent : +5% à +10% selon le rôle
- Modules spécialisés : +10% à +20% selon le module

### Coûts et Récompenses

**Coûts** :
- Temps : 30-90 secondes par événement
- Risques : Dégâts au vaisseau, perte de ressources (en cas d'échec)

**Récompenses** :
- Données scientifiques : 50-200 selon le succès
- Fragments d'artefacts : 1-3 selon la rareté
- Matériaux : 10-50 selon le succès
- Découvertes : Planètes, artefacts, entrées Stellarpedia
- Lore : Fragments narratifs
- Indices Parallaxe : Très rares

### Progression

- Les événements deviennent plus complexes avec la progression du joueur
- Les récompenses augmentent légèrement avec le niveau
- Les arcs persistants deviennent plus fréquents aux niveaux élevés
- Les indices du Parallaxe deviennent plus fréquents après certaines découvertes

### Scaling

- Niveau 1-5 : Événements simples, récompenses de base
- Niveau 6-15 : Événements modérés, micro-arcs possibles
- Niveau 16+ : Événements complexes, arcs persistants possibles

## Styles Narratifs Supportés

### 1. Événements Autonomes
Micro-histoires résolues en 1 seule interaction. Majorité des événements.

### 2. Micro-Arcs
Chaînes de 2 à 3 événements répartis sur plusieurs jours. Créent une continuité narrative.

### 3. Arcs Persistants
Branches rares pouvant durer plusieurs semaines. Réservés aux joueurs avancés.

### 4. Échos du Parallaxe
Branches cachées, incohérentes ou légèrement altérées. Très rares, créent du mystère.

## Influence du Joueur

### Vaisseau
- **Moteurs** : Bonus aux issues d'évasion
- **Scanners** : Déblocage d'options d'analyse
- **Coque/Boucliers** : Réduction des risques physiques

### Crew
- **Scientifique** : Bonus analyse (+10% succès choix analytiques)
- **Navigateur** : Bonus déplacement (+10% succès choix prudents)
- **Sécurité** : Réduction des dangers (-15% dégâts en cas d'échec)

### Modules
Certains modules débloquent :
- Choix supplémentaires (ex: "Utiliser le module de scan avancé")
- Outcome amélioré (+15% récompenses)
- Accès à une branche rare

## Types de Récompenses

1. **Données scientifiques** : Ressource principale
2. **Matériaux** : Ressource rare
3. **Fragments d'artefacts** : Pour débloquer des modules
4. **Modules uniques** : Récompenses exceptionnelles
5. **Entrées du Stellarpedia** : Découvertes documentables
6. **Indices du Parallaxe** : Fragments cryptiques
7. **Buffs temporaires** : Bonus pour la prochaine expédition
8. **Dégâts subis** : Conséquences négatives

## Règles de Conception des Choix

**Contraintes** :
- 3 choix maximum (jamais plus, jamais moins)
- Textes courts (maximum 10 mots par choix)
- Risque/reward clair pour chaque choix
- Style distinct pour chaque option

**Bon exemple** :
- "Enquêter prudemment"
- "Sécuriser la zone"
- "Ignorer et avancer"

**Mauvais exemple** :
- 6 choix confus
- Paragraphes trop longs
- Résultats identiques

## Intégration dans la Boucle Quotidienne

Ordre recommandé :
1. Événement quotidien (CYOA)
2. Mini-jeu
3. Événement d'expédition (CYOA)
4. Mise à jour du Stellarpedia

Le CYOA est l'ancrage narratif de la session.

## Intégration avec le Parallaxe

Le système permet :
- Branches cachées (non visibles dans les choix normaux)
- Textes distordus (légèrement altérés)
- Contradictions (événements qui ne devraient pas exister)
- Logs impossibles (horodatages incorrects)
- Issues non logiques (résultats impossibles)

Ces éléments restent ultra rares (moins de 1% des événements).

## Exemples et Cas d'Usage

### Exemple 1 : Événement Autonome - Signal Inconnu

**Situation** :
> Un signal faible est détecté à la périphérie du système. Sa source est inconnue.

**Choix** :
1. **Prudent** : "Ignorer le signal" (80% succès, récompense minimale)
2. **Analytique** : "Analyser le signal" (60% succès, données + fragments)
3. **Audacieux** : "Amplifier et répondre" (40% succès, récompense élevée ou dégâts)

**Résolution** :
- Succès Analytique : +150 données, +1 fragment d'artefact, entrée Stellarpedia "Signal mystérieux"
- Échec Audacieux : -50 intégrité vaisseau, +50 données (signal partiellement décodé)

### Exemple 2 : Micro-Arc - Découverte d'Anomalie

**Jour 1** :
> Une anomalie gravitationnelle est détectée. Elle semble artificielle.

**Choix** :
1. "Approcher prudemment"
2. "Scanner à distance"
3. "Ignorer et continuer"

**Jour 2** (si choix 1 ou 2) :
> L'anomalie révèle une structure ancienne. Des signaux faibles émanent de l'intérieur.

**Choix** :
1. "Explorer la structure"
2. "Documenter et partir"
3. "Signaler aux autorités"

**Jour 3** (si choix 1) :
> À l'intérieur, vous découvrez des artefacts inconnus. Ils semblent réagir à votre présence.

**Résolution finale** : Module unique débloqué, entrée Stellarpedia majeure, +500 données

## Cas Limites

1. **Joueur inactif** : Les événements quotidiens s'accumulent (maximum 3 en attente)
2. **Événement non résolu** : Le joueur ne peut pas déclencher de nouvel événement jusqu'à résolution
3. **Échec critique** : Possibilité de blesser le crew ou endommager gravement le vaisseau
4. **Parallaxe** : Certains événements peuvent créer des états persistants étranges

## Métriques à Surveiller

### Métriques d'Engagement
- Temps moyen passé sur un événement narratif
- Taux de complétion des événements
- Taux d'abandon avant résolution

### Métriques de Choix
- Distribution des choix (Prudent vs Analytique vs Audacieux)
- Taux de réussite par type de choix
- Satisfaction des récompenses

### Métriques Narratives
- Fréquence des micro-arcs complétés
- Fréquence des arcs persistants déclenchés
- Fréquence des indices du Parallaxe

### Métriques d'Équilibrage
- Distribution des récompenses
- Impact des modificateurs (vaisseau, crew, modules)
- Taux de satisfaction des joueurs

## Implémentation Technique

### Spécifications

**Tables de base de données** :
```sql
event_templates
- id
- code (unique identifier)
- title
- description
- rarity (common, uncommon, rare, epic, legendary)
- danger_level (1-5)
- category (exploration, anomaly, encounter, etc.)
- can_chain (bool)
- created_at
- updated_at

event_choices
- id
- event_template_id
- label (short text, max 10 words)
- description (optional longer text)
- base_success_rate (0-100)
- risk_physical (0-100)
- risk_mental (0-100)
- reward_type (data, materials, fragments, discovery, etc.)
- reward_payload (JSON)
- created_at
- updated_at

event_instances
- id
- player_id
- event_template_id
- status ('pending', 'resolved')
- context (JSON - stores player state, modifiers, etc.)
- rolled_danger (actual danger level rolled)
- created_at
- updated_at
- resolved_at

event_resolutions
- id
- event_instance_id
- event_choice_id
- success (bool)
- success_score (0-100)
- result_summary (text)
- sanity_delta (if applicable)
- hull_delta (if applicable)
- reward_type
- reward_payload (JSON)
- created_at
- updated_at

event_chains
- id
- parent_event_instance_id
- child_event_template_id
- trigger_choice_id (which choice triggers this chain)
- delay_days (how many days before next event)
- created_at
```

**Modificateurs** :
- Calcul des bonus/malus basé sur les stats du vaisseau
- Application des bonus du crew selon leurs rôles
- Vérification des modules pour débloquer des choix supplémentaires

**Système de résolution** :
1. Calcul du taux de réussite final (base + modificateurs)
2. Roll aléatoire (1-100)
3. Détermination du résultat (succès/mitigé/échec)
4. Application des conséquences
5. Génération de la récompense
6. Vérification des chaînes d'événements

### Points d'Attention

1. **Performance** : Les événements doivent se charger rapidement (< 500ms)
2. **Variété** : Assez de templates pour éviter la répétition
3. **Équilibrage** : Les récompenses doivent être équilibrées avec les autres systèmes
4. **Narratif** : Les textes doivent être engageants mais courts
5. **Parallaxe** : L'intégration doit être subtile et rare

### Tests à Prévoir

1. **Tests unitaires** :
   - Calcul des taux de réussite avec modificateurs
   - Génération des récompenses
   - Vérification des chaînes d'événements

2. **Tests d'intégration** :
   - Déclenchement des événements quotidiens
   - Déclenchement des événements d'expédition
   - Application des conséquences sur le vaisseau/crew

3. **Tests d'équilibrage** :
   - Distribution des récompenses
   - Taux de réussite par type de choix
   - Fréquence des événements rares

## Historique

- Création du draft initial basé sur `adventure-concept.md`

## Références

- **[adventure-concept.md](../local-brainstorming-data/adventure-concept.md)** : Document source du brainstorming
- **[GAME-DESIGNER.md](../agents/GAME-DESIGNER.md)** : Documentation de l'agent Game Designer
- **[design-game-mechanic.md](../prompts/design-game-mechanic.md)** : Guide pour concevoir des mécaniques

