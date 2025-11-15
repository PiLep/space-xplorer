# DRAFT - Système d'Onboarding

## Statut
**Draft** - En attente de validation avec Alex (Product Manager)

## Issue Associée
À créer après validation du draft (Note: ISSUE-005 existe déjà pour l'onboarding MVP)

## Vue d'Ensemble

L'onboarding est la première expérience du joueur dans Stellar. Il doit être immersif, bref (3-5 minutes), et donner une raison claire de revenir le lendemain.

**Objectifs** :
- Immerger immédiatement le joueur dans l'univers
- Introduire les mécaniques sans surcharge
- Donner une première action interactive
- Générer de l'attachement via une découverte unique
- Introduire subtilement le fil rouge (Le Parallaxe)
- Laisser une raison claire de revenir le lendemain

**Durée cible** : 3 à 5 minutes maximum

## Règles de Base

### Activation

- Déclenchement automatique lors de la première connexion
- Un seul passage possible par joueur
- Progression sauvegardée à chaque étape
- Possibilité de reprendre si interruption

### Fonctionnement

L'onboarding se déroule en **6 étapes courtes et scénarisées** :

1. **Réveil du joueur** (30-45 secondes)
2. **Scan initial et mini-jeu d'introduction** (10-20 secondes)
3. **Premier micro-choix narratif** (20-30 secondes)
4. **Nomination de la planète d'origine** (30-60 secondes)
5. **Introduction au Codex Stellaire** (10-20 secondes)
6. **Projection vers la prochaine session** (10-15 secondes)

**Total** : ~2-3 minutes de contenu actif

### Conditions

- Le joueur doit avoir créé son compte
- L'onboarding n'est accessible qu'une seule fois
- Chaque étape doit être complétée avant de passer à la suivante
- Possibilité de revenir en arrière (sauf pour certaines étapes critiques)

### Interactions

**Avec le système de planètes** :
- Génération de la planète d'origine du joueur
- Caractéristiques uniques et mémorables

**Avec le système narratif CYOA** :
- Introduction du système de choix
- Premier événement narratif simplifié

**Avec le Stellarpedia** :
- Création automatique de l'entrée de la planète d'origine
- Introduction au système de contribution

**Avec Le Parallaxe** :
- Premier indice subtil du mystère
- Introduction discrète du fil rouge

## Structure Détaillée

### Étape 1 : Réveil du Joueur (30-45 secondes)

**Objectif** : Introduire le ton du jeu et créer une immersion immédiate

**Éléments** :
- Écran noir avec "Booting…" (animation)
- Interface instable ou glitchée (effet visuel)
- Texte court de contextualisation

**Exemple de texte** :
> Vous émergez d'hibernation.  
> Le système de survie vous a réveillé 34 minutes plus tôt que prévu.  
> Une anomalie a été détectée à proximité.

**Action** : Bouton "Continuer"

**Résultat** : Transition vers l'étape suivante

### Étape 2 : Scan Initial et Mini-Jeu (10-20 secondes)

**Objectif** : Première action interactive simple

**Éléments** :
- Génération instantanée de la planète d'origine du joueur
- Affichage partiel des informations (données brutes)
- Mini-jeu introductif très court (scan rapide simplifié)

**Mini-jeu** :
- Version simplifiée du "Scan Circulaire"
- 3 signaux à verrouiller (au lieu de 8)
- Temps limité : 15 secondes
- Pas de pénalité en cas d'échec

**Résultat** :
- Informations supplémentaires sur la planète révélées
- Premiers éléments narratifs atmosphériques
- Transition vers l'étape suivante

### Étape 3 : Premier Choix Narratif (20-30 secondes)

**Objectif** : Introduire la mécanique de choix et leur impact

**Situation** :
> Un signal faible est détecté.  
> Sa source est inconnue.

**Choix** :
1. **"Analyser"** (Analytique) : +50 données, fragment de lore
2. **"Ignorer"** (Prudent) : Pas de récompense, pas de risque
3. **"Amplifier"** (Audacieux) : +100 données ou -20 intégrité vaisseau

**Résolution** :
- Affichage immédiat des conséquences
- Explication courte du système de choix
- Transition vers l'étape suivante

**Intégration Parallaxe** : Légère anomalie dans le signal (optionnel, 20% chance)

### Étape 4 : Nomination de la Planète d'Origine (30-60 secondes)

**Objectif** : Renforcer l'attachement émotionnel

**Éléments** :
- Affichage de la planète générée
- Champ de saisie pour le nom
- Suggestion automatique de noms si nécessaire (optionnel)
- Validation simple et rapide

**Contraintes** :
- Vérification de sécurité (filtrage et longueur : 3-30 caractères)
- Vérification d'unicité (optionnel, peut permettre les doublons)
- Pas de caractères spéciaux interdits

**Résultat** :
- Nom sauvegardé
- Planète personnalisée
- Transition vers l'étape suivante

### Étape 5 : Introduction au Codex Stellaire (10-20 secondes)

**Objectif** : Présenter le système de lore et de contributions futures

**Éléments** :
- Affichage de l'entrée de la planète dans le Stellarpedia
- Court texte généré automatiquement
- Mention indiquant qu'il pourra contribuer plus tard

**Exemple** :
> Votre planète a été enregistrée dans le Stellarpedia, l'archive centrale de toutes les découvertes.  
> Vous pourrez enrichir cette entrée plus tard avec vos observations.

**Action** : Bouton "Accéder au Codex" (facultatif, ouvre le Stellarpedia)

**Résultat** : Transition vers l'étape finale

### Étape 6 : Projection vers Demain (10-15 secondes)

**Objectif** : Donner une raison claire de revenir

**Éléments** :
- Message de projection temporelle
- Indication du prochain événement disponible

**Exemple** :
> Un scan longue portée sera disponible dans 23 heures.  
> Revenez pour votre premier briefing d'exploration.

**Action** : Bouton "Terminer l'activation"

**Résultat** :
- Onboarding complété
- Flag `onboarding_completed = true`
- `first_daily_event_due_at` défini (24h après)
- `first_expedition_available_at` défini (24h après)
- Redirection vers le dashboard principal

## Intégration du Fil Rouge : Le Parallaxe

**Objectif** : Introduire discrètement le mystère dès l'onboarding

**Recommandations** :
- Insérer un léger glitch visuel ou audio (étape 1 ou 2)
- Ajouter une phrase courte et ambiguë lors du mini-jeu ou du choix :
  - "Anomalie non identifiée."
  - "Valeur attendue manquante."
  - "Interférence… source indéterminée."

**Règles** :
- Ne jamais expliquer
- Ne jamais répéter immédiatement
- Fréquence : 1 indice maximum pendant l'onboarding
- Probabilité : 30% de chance d'apparition

## Points Clés de Design

### Principes

1. **Ne jamais bloquer le joueur** : Toutes les étapes sont skippables (sauf critiques)
2. **Aucun texte long** : Maximum 3 phrases par écran
3. **Aucune mécanique complexe** : Introduction progressive uniquement
4. **Maximum une seule action** : Par étape
5. **Onboarding terminé en moins de 5 minutes** : Objectif 3 minutes
6. **Préparation de la première session quotidienne** : Dès la fin

### UX Guidelines

- **Feedback immédiat** : Chaque action donne un feedback visuel
- **Progression visible** : Indicateur de progression (ex: "Étape 2/6")
- **Tutoriel contextuel** : Tooltips légers si nécessaire
- **Pas de pression** : Le joueur peut prendre son temps

## Flags et États Techniques

### Tables de Base de Données

```sql
players
- id
- onboarding_completed (bool, default false)
- onboarding_step (int, nullable - dernière étape complétée)
- onboarding_completed_at (datetime, nullable)
- first_daily_event_due_at (datetime, nullable)
- first_expedition_available_at (datetime, nullable)
- created_at
- updated_at

onboarding_progress
- id
- player_id
- step (int, 1-6)
- completed_at (datetime)
- data (JSON - stores choices, planet name, etc.)
- created_at
```

### Flags Importants

- `onboarding_completed` : Détermine si le joueur peut accéder au jeu complet
- `onboarding_step` : Permet de reprendre l'onboarding si interruption
- `first_daily_event_due_at` : Déclenche le premier événement quotidien
- `first_expedition_available_at` : Déclenche la première expédition

## Logs et Métriques

### Métriques Utiles

- **Temps passé dans l'onboarding** : Durée totale
- **Temps par étape** : Identifier les étapes trop longues
- **Abandon potentiel** : Pourcentage d'abandons par étape
- **Choix narratif effectué** : Distribution des choix
- **Nom de planète** : Statistiques sur les noms choisis

### Logs à Enregistrer

- Début de l'onboarding
- Complétion de chaque étape
- Choix narratif sélectionné
- Nom de planète choisi
- Fin de l'onboarding
- Abandon (si applicable)

## Équilibrage

### Durée

- **Cible** : 3 minutes
- **Maximum** : 5 minutes
- **Minimum** : 2 minutes (si toutes les étapes sont complétées rapidement)

### Progression

- **Étape 1** : 30-45s (introduction)
- **Étape 2** : 10-20s (mini-jeu)
- **Étape 3** : 20-30s (choix)
- **Étape 4** : 30-60s (nomination)
- **Étape 5** : 10-20s (codex)
- **Étape 6** : 10-15s (projection)

### Récompenses

- **Planète d'origine** : Découverte unique et personnalisée
- **Premier choix** : 50-100 données selon le choix
- **Nom personnalisé** : Attachement émotionnel
- **Entrée Stellarpedia** : Contribution automatique

## Exemples et Cas d'Usage

### Exemple 1 : Onboarding Standard

**Joueur** : Nouveau joueur, première connexion

**Déroulement** :
1. Réveil : 35s (lit le texte, appuie sur continuer)
2. Scan : 15s (complète le mini-jeu avec succès)
3. Choix : 25s (choisit "Analyser", obtient +50 données)
4. Nomination : 45s (choisit "Nova Prime")
5. Codex : 15s (consulte rapidement)
6. Projection : 10s (lit le message)

**Total** : ~2m45s

**Résultat** : Onboarding complété, joueur prêt pour la première session quotidienne

### Exemple 2 : Onboarding avec Parallaxe

**Joueur** : Nouveau joueur, première connexion

**Déroulement** :
1. Réveil : 40s (remarque un léger glitch visuel)
2. Scan : 18s (voit "Anomalie non identifiée" brièvement)
3. Choix : 30s (choisit "Amplifier", obtient +100 données)
4. Nomination : 50s (choisit "Echo Station")
5. Codex : 12s (consulte rapidement)
6. Projection : 12s (lit le message)

**Total** : ~3m02s

**Résultat** : Onboarding complété avec premier indice Parallaxe, joueur intrigué

## Cas Limites

1. **Interruption** : Le joueur peut reprendre à la dernière étape complétée
2. **Erreur de nom** : Validation côté client et serveur
3. **Mini-jeu échoué** : Pas de pénalité, progression normale
4. **Choix non fait** : Choix par défaut (Prudent) si timeout

## Métriques à Surveiller

### Métriques d'Engagement
- Taux de complétion de l'onboarding
- Temps moyen de complétion
- Taux d'abandon par étape

### Métriques de Qualité
- Distribution des choix narratifs
- Temps moyen par étape
- Satisfaction des joueurs (survey optionnel)

### Métriques Techniques
- Taux d'erreurs techniques
- Performance de chargement
- Taux de reprise après interruption

## Implémentation Technique

### Spécifications

**Système de progression** :
- Sauvegarde automatique à chaque étape
- Possibilité de reprendre si interruption
- Validation des étapes avant progression

**Système de génération de planète** :
- Génération procédurale lors de l'étape 2
- Caractéristiques uniques et mémorables
- Sauvegarde dans `planets` avec `is_origin = true`

**Système de mini-jeu** :
- Version simplifiée du système complet
- Pas de pénalité en cas d'échec
- Récompenses garanties

**Système de choix** :
- Version simplifiée du système CYOA
- 3 choix toujours disponibles
- Résolution immédiate

### Points d'Attention

1. **Performance** : Chargement rapide (< 1s entre étapes)
2. **Accessibilité** : Contrôles simples et clairs
3. **Immersion** : Ambiance visuelle et sonore cohérente
4. **Progression** : Sauvegarde fiable à chaque étape

### Tests à Prévoir

1. **Tests unitaires** :
   - Génération de planète d'origine
   - Validation du nom
   - Calcul des récompenses

2. **Tests d'intégration** :
   - Flux complet d'onboarding
   - Sauvegarde et reprise
   - Intégration avec les autres systèmes

3. **Tests utilisateurs** :
   - Temps de complétion
   - Compréhension des mécaniques
   - Satisfaction globale

## Historique

- Création du draft initial basé sur `onboarding.md`
- **Note** : ISSUE-005 existe déjà pour l'onboarding MVP, ce draft complète la vision game design

## Références

- **[onboarding.md](../local-brainstorming-data/onboarding.md)** : Document source du brainstorming
- **[ISSUE-005-implement-onboarding-mvp.md](../issues/ISSUE-005-implement-onboarding-mvp.md)** : Issue existante pour l'onboarding
- **[GAME-DESIGNER.md](../agents/GAME-DESIGNER.md)** : Documentation de l'agent Game Designer
- **[design-game-mechanic.md](../prompts/design-game-mechanic.md)** : Guide pour concevoir des mécaniques

