# Système Narratif à Choix – Documentation
Système de narration interactive inspiré des « livres dont vous êtes le héros » pour Stellar.

---

# 1. Vue d’ensemble
Ce document présente le système narratif à embranchements de Stellar. Inspiré des formats "livre dont vous êtes le héros", il permet au joueur de prendre des décisions courtes mais impactantes dans des micro-scénarios quotidiens. Ces choix influencent les événements, les récompenses, le ton narratif, et peuvent parfois déclencher des indices liés au Parallaxe.

---

# 2. Objectifs du système
- Offrir des moments narratifs intenses mais brefs.
- Renforcer la sensation d’exploration et d’inconnu.
- Proposer des conséquences réelles sans complexité excessive.
- Construire des micro-histoires cohérentes sur plusieurs jours.
- Donner une structure narrative aux expéditions.
- Intégrer subtilement des éléments du Parallaxe.

---

# 3. Structure d’un événement narratif
Un événement à choix multiples comprend :

1. **Un template d’événement** (scénario de base)
2. **Une situation** (prompt narratif)
3. **Trois choix** (jamais plus, jamais moins)
4. **Résolutions** (succès, mitigé, échec)
5. **Conséquences** (récompenses, risques, lore)
6. **Éventuels événements suivants** (micro-arcs)
7. **Modificateurs** (vaisseau, modules, crew)

### 3.1 Structure en base de données
```sql
event_templates
- id
- code
- title
- description
- rarity
- danger_level
- category
- can_chain (bool)
- created_at
- updated_at

event_choices
- id
- event_template_id
- label
- description
- base_success_rate
- risk_physical
- risk_mental
- reward_type
- reward_payload (JSON)
- created_at
- updated_at

event_instances
- id
- player_id
- event_template_id
- status        -- 'pending', 'resolved'
- context JSON
- rolled_danger
- created_at
- updated_at

event_resolutions
- id
- event_instance_id
- event_choice_id
- success
- success_score
- result_summary
- sanity_delta
- hull_delta
- reward_type
- reward_payload (JSON)
- created_at
- updated_at
```

---

# 4. Déroulement d’un événement

## Étape 1 : Déclencheur
Un événement peut se déclencher lorsque :
- Le joueur lance une expédition.
- Le jeu génère l’événement quotidien.
- Un palier important est atteint.
- Une anomalie du Parallaxe interfère.

## Étape 2 : Présentation
Le joueur voit :
- un résumé court de la situation
- une ambiance visuelle ou sonore (
- trois choix clairs

## Étape 3 : Choix
Toujours trois options :
- **Prudent** : faible risque, faible reward
- **Analytique** : équilibre
- **Audacieux** : risque élevé, reward élevé

## Étape 4 : Résolution
Calcul du résultat à partir de :
- taux de base
- bonus/malus liés au vaisseau
- bonus/malus liés au crew
- modules installés
- variables du Parallaxe (très rares)

Types d’issues :
- **Succès**
- **Issue mitigée**
- **Échec**

## Étape 5 : Conséquences
Exemples :
- ressources
- découvertes
- dégâts
- fragments de lore
- indices cryptiques

## Étape 6 : Suite (optionnelle)
Si `can_chain = true` :
- un événement lié peut apparaître le lendemain ou plusieurs jours plus tard
- réservé aux joueurs ayant pris une certaine branche

---

# 5. Styles narratifs supportés

## 5.1 Événements autonomes
Micro-histoires résolues en 1 seule interaction.

## 5.2 Micro-arcs
Chaînes de 2 à 3 événements répartis sur plusieurs jours.

## 5.3 Arcs persistants
Branches rares pouvant durer plusieurs semaines.

## 5.4 Échos du Parallaxe
Branches cachées, incohérentes ou légèrement altérées. Très rares.

---

# 6. Influence du joueur
Les choix sont influencés par :

### 6.1 Vaisseau
- moteurs : issues d’évasion
- scanners : options d’analyse
- coque/boucliers : réduction de risques

### 6.2 Crew
- scientifique : bonus analyse
- navigateur : bonus déplacement
- sécurité : réduction des dangers

### 6.3 Modules
Certains modules débloquent :
- choix supplémentaires
- outcome amélioré
- accès à une branche rare

---

# 7. Types de récompenses
- données
- matériaux
- fragments d’artefacts
- modules uniques
- entrées du Stellarpedia
- indices du Parallaxe
- buffs temporaires
- dégâts subis

---

# 8. Règles de conception des choix
- 3 choix maximum
- textes courts
- risque / reward pour chaque choix
- style distinct pour chaque option

Bon exemple :
- enquêter
- sécuriser
- ignorer et avancer

Mauvais exemple :
- 6 choix confus
- paragraphes trop longs
- résultats identiques

---

# 9. Intégration dans la boucle quotidienne
Ordre recommandé :
1. Événement quotidien
2. Mini-jeu
3. Événement d’expédition (CYOA)
4. Mise à jour du Stellarpedia

Le CYOA est l’ancrage narratif de la session.

---

# 10. Intégration avec le Parallaxe
Le système permet :
- branches cachées
- textes distordus
- contradictions
- logs impossibles
- issues non logiques

Ces éléments restent ultra rares.

---

# 11. Extensions futures
- arcs centrés sur le crew
- arcs liés à certains secteurs
- événements globaux synchronisés
- possibles fins alternatives (optionnel)

---

# 12. Résumé
Le système narratif à choix de Stellar fournit des histoires courtes, impactantes et rejouables. Il équilibre exploration, risque, ambiance et mystère, tout en servant de vecteur naturel pour les interactions avec le Parallaxe et la progression quotidienne du joueur.
