# DRAFT - Système d'Onboarding

## Statut
**Draft** - En attente de validation avec Alex (Product Manager)

## Issue Associée
À créer après validation du draft (Note: ISSUE-005 existe déjà pour l'onboarding MVP)

## Vue d'Ensemble

L'onboarding est la première expérience du joueur dans Stellar. Il doit être immersif, bref (2-5 minutes selon la phase), et donner une raison claire de revenir le lendemain.

**Objectifs généraux** :
- Immerger immédiatement le joueur dans l'univers
- Introduire les mécaniques sans surcharge
- Générer de l'attachement via une découverte unique
- Laisser une raison claire de revenir le lendemain

**Note importante** : Ce draft définit deux phases d'onboarding :
- **Phase 1 (MVP)** : Onboarding minimal sans dépendances, peut être implémenté immédiatement
- **Phase 2 (Complet)** : Onboarding enrichi nécessitant les systèmes de base (mini-jeux, CYOA, Stellarpedia)

## Règles de Base

### Activation (Commun aux deux phases)

- Déclenchement automatique lors de la première connexion
- Un seul passage possible par joueur
- Progression sauvegardée à chaque étape
- Possibilité de reprendre si interruption

### Conditions (Commun aux deux phases)

- Le joueur doit avoir créé son compte
- L'onboarding n'est accessible qu'une seule fois
- Chaque étape doit être complétée avant de passer à la suivante
- Possibilité de revenir en arrière (sauf pour certaines étapes critiques)

---

## Phase 1 : Onboarding MVP (Sans Dépendances)

**Statut** : Prêt pour implémentation immédiate  
**Issue** : ISSUE-005 existe déjà pour cette phase  
**Durée cible** : 2-3 minutes  
**Dépendances** : Aucune (système de planètes déjà disponible)

### Vue d'Ensemble Phase 1

Onboarding minimal permettant de créer une première connexion émotionnelle avec l'univers Stellar sans nécessiter de systèmes complexes. Version simplifiée alignée avec ISSUE-005.

**Objectifs Phase 1** :
- Immerger le joueur dans l'univers Stellar
- Introduire le nom du personnage
- Découvrir la planète d'origine
- Présenter l'interface terminal
- Donner une raison de revenir

### Fonctionnement Phase 1

L'onboarding MVP se déroule en **4 étapes essentielles** :

1. **Arrivée/Recrutement Stellar** (30-45 secondes)
2. **Définition du nom du personnage** (30-60 secondes)
3. **Découverte de la planète d'origine** (30-45 secondes)
4. **Présentation du terminal** (20-30 secondes)

**Total** : ~2-3 minutes de contenu actif

### Structure Détaillée Phase 1

#### Étape 1 : Arrivée/Recrutement Stellar (30-60 secondes)

**Objectif** : Introduire le ton du jeu et créer une immersion immédiate avec mystère et intrigue

**Éléments** :
- Écran noir avec "Booting…" (animation lente, typewriter effect)
- Interface terminal instable ou glitchée (effet visuel subtil)
- Message cryptique et mystérieux présentant Stellar comme une entité énigmatique
- Ambiance Alien, texte narratif minimaliste dans l'interface terminal
- Pas d'explication claire du rôle ou de la mission (laisser des questions en suspens)

**Séquence narrative** :

1. **Initialisation** (5-8 secondes)
   - Écran noir
   - "Booting…" apparaît progressivement
   - Sons de système (optionnel, subtil)

2. **Révélation progressive** (15-25 secondes)
   - Texte apparaissant ligne par ligne (typewriter effect)
   - Messages cryptiques et ambigus

**Exemples de textes (variations possibles)** :

*Version 1 - Minimaliste* :
> Système activé.  
> Contrat Stellar #█████ validé.  
> Réveil programmé.  
> [Pause]  
> Vous êtes en ligne.

*Version 2 - Plus mystérieuse* :
> [Glitch visuel]  
> Signal détecté.  
> Stellar.  
> [Pause]  
> Vous avez été sélectionné.  
> Raison : Classifiée.

*Version 3 - Ambiance Alien* :
> Émergence d'hibernation...  
> Système de survie : Opérationnel.  
> Mission : Non spécifiée.  
> Destination : Inconnue.  
> Stellar vous attend.

**Principe de design** :
- **Moins d'informations** : Ne pas expliquer qui est Stellar, pourquoi le joueur a été sélectionné, quelle est sa mission exacte
- **Plus d'ambiance** : Créer une atmosphère mystérieuse et intrigante
- **Questions en suspens** : Laisser le joueur se demander "Qu'est-ce que Stellar ?", "Pourquoi moi ?", "Quelle est ma mission ?"
- **Ton sérieux et professionnel** : Comme dans Alien, ambiance de grande entreprise avec des intentions floues

**Éléments visuels recommandés** :
- Animation de booting (texte qui apparaît progressivement)
- Effets de glitch subtils (occasionnels, pas trop fréquents)
- Ambiance sombre avec texte terminal vert/cyan
- Sons de système (optionnel, très subtils)

**Action** : Bouton "Continuer" (apparaît après le texte complet)

**Résultat** : Transition vers l'étape suivante avec une atmosphère de mystère établie

#### Étape 2 : Définition du Nom du Personnage (30-60 secondes)

**Objectif** : Permettre au joueur de définir son identité dans le jeu avec un contexte narratif immersif

**Éléments** :
- Contexte narratif cohérent avec l'ambiance Stellar
- Formulaire permettant au joueur de définir le nom de son personnage
- Interface terminal cohérente avec le reste du jeu
- Champ de saisie avec validation en temps réel

**Contexte narratif** :

Le système demande un identifiant pour l'activation. Le message doit être cohérent avec l'ambiance mystérieuse :

*Version 1 - Minimaliste* :
> Identifiant requis pour l'activation du système.  
> [Champ de saisie]

*Version 2 - Plus narrative* :
> Système d'identification...  
> Quel identifiant souhaitez-vous utiliser pour ce contrat ?  
> [Champ de saisie]

*Version 3 - Ambiance Alien* :
> Enregistrement de l'identité opérationnelle.  
> Nom d'identification :  
> [Champ de saisie]

**Contraintes** :
- Validation du nom (longueur : 3-30 caractères)
- Caractères autorisés : lettres, chiffres, espaces, tirets
- Vérification de sécurité (filtrage des mots inappropriés)
- Message d'erreur discret en cas d'invalidité

**Action** : Saisie du nom + Bouton "Valider" (ou "Confirmer")

**Résultat** :
- Nom sauvegardé dans `character_name`
- Confirmation visuelle discrète ("Identifiant enregistré" ou similaire)
- Transition vers l'étape suivante

**Note importante** : Le nom du personnage ne doit **plus** être demandé lors du processus d'inscription (register). Cette étape d'onboarding remplace cette fonctionnalité. L'inscription doit uniquement demander : email, mot de passe (et confirmation), et éventuellement nom d'utilisateur pour l'authentification si nécessaire.

#### Étape 3 : Découverte de la Planète d'Origine (30-60 secondes)

**Objectif** : Créer un moment magique de découverte et générer de l'attachement

**Éléments** :
- Scène de révélation progressive avec animation
- Présentation de la planète d'origine (déjà générée à l'inscription via `home_planet_id`)
- Affichage des caractéristiques de la planète de manière immersive
- Moment de révélation cinématique

**Séquence de révélation** :

1. **Phase 1 : Initialisation du scan** (5-8 secondes)
   - Écran sombre avec texte terminal
   - "Initialisation du scanner longue portée..."
   - Animation de chargement/scan progressif
   - Effet visuel de scan (lignes, grille, etc.)

2. **Phase 2 : Détection** (5-8 secondes)
   - "Signal détecté..."
   - "Analyse des données..."
   - Animation de traitement des données
   - Effet de zoom progressif vers l'espace

3. **Phase 3 : Révélation** (10-15 secondes)
   - Apparition progressive de la planète (fade-in ou zoom)
   - Visualisation de la planète (image générée ou placeholder stylisé)
   - Texte narratif court apparaissant progressivement :
     > Planète identifiée : **[Nom généré]**  
     > Classification : **[Type]**  
     > Atmosphère : **[Type]** | Température : **[Valeur]**  
     > Distance : **[Valeur]** années-lumière  
     > Statut : **Planète d'origine assignée**

4. **Phase 4 : Confirmation** (5-10 secondes)
   - Message de confirmation
   - "Cette planète est maintenant votre point de départ."
   - Option de visualiser plus de détails (optionnel)

**Éléments visuels recommandés** :
- Animation de scan (lignes animées, grille, radar)
- Transition progressive vers la visualisation de la planète
- Effet de révélation (fade-in, zoom, ou apparition progressive)
- Style cohérent avec l'interface terminal
- Ambiance spatiale immersive

**Action** : Bouton "Continuer" (apparaît après la révélation complète)

**Résultat** : Transition vers l'étape finale

**Note technique** : L'animation peut être réalisée avec CSS/JavaScript simple, sans dépendre de systèmes complexes. Utiliser l'image de planète déjà générée ou un placeholder stylisé.

#### Étape 4 : Présentation du Terminal (20-30 secondes)

**Objectif** : Terminer l'onboarding et donner une raison de revenir, sans sur-guider

**Éléments** :
- Message simple de transition vers le terminal
- Indication du prochain événement disponible
- Pas de tutoriel détaillé (le joueur découvre par lui-même)

**Exemple** :
> Votre terminal est maintenant opérationnel.  
> Un scan longue portée sera disponible dans 23 heures.  
> Explorez à votre rythme.

**Principe de design** : Ne pas sur-guider. Le joueur doit découvrir les fonctionnalités par lui-même. L'onboarding introduit l'univers et les bases, mais laisse de la place à l'exploration et à la curiosité.

**Action** : Bouton "Accéder au terminal"

**Résultat** :
- Onboarding complété
- Flag `onboarding_completed_at` défini
- `first_daily_event_due_at` défini (24h après)
- `first_expedition_available_at` défini (24h après)
- Redirection vers le dashboard/terminal principal

### Interactions Phase 1

**Avec le système de planètes** :
- Utilisation de la planète d'origine déjà générée (`user.home_planet_id`)
- Affichage des caractéristiques existantes

**Pas d'interactions avec** :
- Mini-jeux (non implémentés)
- Système narratif CYOA (non implémenté)
- Stellarpedia (non implémenté)
- Le Parallaxe (non implémenté)

### Équilibrage Phase 1

**Durée** :
- **Cible** : 2-3 minutes
- **Maximum** : 4 minutes
- **Minimum** : 1m30 (si toutes les étapes sont complétées rapidement)

**Progression** :
- **Étape 1** : 30-60s (introduction mystérieuse avec typewriter effect)
- **Étape 2** : 30-60s (nomination)
- **Étape 3** : 30-60s (découverte avec animation de révélation)
- **Étape 4** : 20-30s (terminal)

**Récompenses** :
- **Planète d'origine** : Découverte unique et personnalisée
- **Nom personnalisé** : Attachement émotionnel
- **Accès au jeu** : Déblocage de la première session quotidienne

---

## Phase 2 : Onboarding Complet (Avec Tous les Systèmes)

**Statut** : Vision future, nécessite les systèmes de base  
**Dépendances** : Mini-jeux v1, Système narratif CYOA basique, Stellarpedia core  
**Durée cible** : 3-5 minutes

### Vue d'Ensemble Phase 2

Onboarding enrichi intégrant tous les systèmes de base pour introduire complètement le joueur aux mécaniques de jeu. Cette phase nécessite que les systèmes suivants soient implémentés :
- Système de mini-jeux (au moins le "Scan Circulaire")
- Système narratif CYOA (choix narratifs basiques)
- Stellarpedia (création et consultation d'entrées)
- Le Parallaxe (introduction subtile du fil rouge)

### Fonctionnement Phase 2

L'onboarding complet se déroule en **7 étapes enrichies** (basées sur Phase 1 + systèmes additionnels) :

1. **Réveil du joueur** (30-60 secondes) - Identique à Phase 1
2. **Définition du nom du personnage** (30-60 secondes) - Identique à Phase 1
3. **Scan initial et mini-jeu d'introduction** (10-20 secondes) - Nouveau avec mini-jeu
4. **Premier micro-choix narratif** (20-30 secondes) - Nouveau avec CYOA
5. **Nomination de la planète d'origine** (30-60 secondes) - Enrichi
6. **Introduction au Codex Stellaire** (10-20 secondes) - Nouveau avec Stellarpedia
7. **Projection vers la prochaine session** (10-15 secondes) - Identique à Phase 1

**Total** : ~3-5 minutes de contenu actif

### Structure Détaillée Phase 2

#### Étape 1 : Réveil du Joueur (30-60 secondes)

**Objectif** : Introduire le ton du jeu et créer une immersion immédiate avec mystère et intrigue

**Éléments** :
- Écran noir avec "Booting…" (animation lente, typewriter effect)
- Interface instable ou glitchée (effet visuel subtil)
- Message cryptique et mystérieux (cohérent avec Phase 1)
- Pas d'explication claire (laisser des questions en suspens)

**Séquence narrative** (identique à Phase 1 pour cohérence) :

1. **Initialisation** (5-8 secondes)
   - Écran noir
   - "Booting…" apparaît progressivement
   - Sons de système (optionnel, subtil)

2. **Révélation progressive** (15-25 secondes)
   - Texte apparaissant ligne par ligne (typewriter effect)
   - Messages cryptiques et ambigus

**Exemples de textes** (variations possibles, cohérentes avec Phase 1) :

*Version 1 - Minimaliste* :
> Système activé.  
> Contrat Stellar #█████ validé.  
> Réveil programmé.  
> [Pause]  
> Vous êtes en ligne.

*Version 2 - Plus mystérieuse* :
> [Glitch visuel]  
> Signal détecté.  
> Stellar.  
> [Pause]  
> Vous avez été sélectionné.  
> Raison : Classifiée.

*Version 3 - Ambiance Alien* :
> Émergence d'hibernation...  
> Système de survie : Opérationnel.  
> Mission : Non spécifiée.  
> Destination : Inconnue.  
> Stellar vous attend.

**Principe de design** : Cohérent avec Phase 1 - moins d'informations, plus d'ambiance, questions en suspens.

**Action** : Bouton "Continuer" (apparaît après le texte complet)

**Résultat** : Transition vers l'étape suivante avec une atmosphère de mystère établie

#### Étape 2 : Définition du Nom du Personnage (30-60 secondes)

**Objectif** : Permettre au joueur de définir son identité dans le jeu avec un contexte narratif immersif

**Note** : Identique à la Phase 1, étape 2. Voir la section Phase 1 pour les détails complets.

**Résumé** :
- Contexte narratif cohérent avec l'ambiance Stellar
- Formulaire permettant au joueur de définir le nom de son personnage
- Validation : 3-30 caractères, lettres, chiffres, espaces, tirets
- Nom sauvegardé dans `character_name`

### Étape 3 : Scan Initial et Mini-Jeu (10-20 secondes)

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

#### Étape 4 : Premier Choix Narratif (20-30 secondes)

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

#### Étape 5 : Nomination de la Planète d'Origine (30-60 secondes)

**Objectif** : Renforcer l'attachement émotionnel en permettant au joueur de nommer sa planète d'origine

**Note** : Dans la Phase 2, le nom du personnage a déjà été défini à l'étape 2 (comme dans Phase 1). Cette étape permet de nommer la planète d'origine elle-même.

**Éléments** :
- Affichage de la planète générée (déjà découverte à l'étape 2)
- Contexte narratif pour la nomination
- Champ de saisie pour le nom de la planète
- Suggestion automatique de noms si nécessaire (optionnel)
- Validation simple et rapide

**Contexte narratif** :
> Planète identifiée.  
> Souhaitez-vous lui donner un nom ?  
> [Champ de saisie]

**Contraintes** :
- Vérification de sécurité (filtrage et longueur : 3-30 caractères)
- Vérification d'unicité (optionnel, peut permettre les doublons)
- Caractères autorisés : lettres, chiffres, espaces, tirets

**Résultat** :
- Nom de la planète sauvegardé (dans la table `planets` ou `planet_properties`)
- Planète personnalisée
- Transition vers l'étape suivante

#### Étape 6 : Introduction au Codex Stellaire (10-20 secondes)

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

#### Étape 7 : Projection vers Demain (10-15 secondes)

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

### Interactions Phase 2

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

### Intégration du Fil Rouge : Le Parallaxe (Phase 2 uniquement)

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

### Équilibrage Phase 2

**Durée** :
- **Cible** : 3 minutes
- **Maximum** : 5 minutes
- **Minimum** : 2 minutes (si toutes les étapes sont complétées rapidement)

**Progression** :
- **Étape 1** : 30-60s (introduction mystérieuse avec typewriter effect)
- **Étape 2** : 30-60s (nomination personnage)
- **Étape 3** : 10-20s (mini-jeu)
- **Étape 4** : 20-30s (choix narratif)
- **Étape 5** : 30-60s (nomination planète)
- **Étape 6** : 10-20s (codex)
- **Étape 7** : 10-15s (projection)

**Récompenses** :
- **Planète d'origine** : Découverte unique et personnalisée
- **Premier choix** : 50-100 données selon le choix
- **Nom personnalisé** : Attachement émotionnel
- **Entrée Stellarpedia** : Contribution automatique
- **Premier indice Parallaxe** : Intrigue et mystère (30% chance)

## Points Clés de Design (Commun aux deux phases)

### Principes

1. **Ne jamais bloquer le joueur** : Toutes les étapes sont skippables (sauf critiques)
2. **Aucun texte long** : Maximum 3 phrases par écran
3. **Aucune mécanique complexe** : Introduction progressive uniquement
4. **Maximum une seule action** : Par étape
5. **Onboarding terminé rapidement** : Objectif 2-3 minutes (Phase 1) ou 3-5 minutes (Phase 2)
6. **Préparation de la première session quotidienne** : Dès la fin
7. **Découverte par soi-même** : Ne pas sur-guider, laisser de la place à l'exploration et à la curiosité. L'onboarding introduit l'univers et les bases, mais le joueur doit découvrir les fonctionnalités par lui-même.

### UX Guidelines

- **Feedback immédiat** : Chaque action donne un feedback visuel
- **Progression visible** : Indicateur de progression (ex: "Étape 2/4" pour Phase 1, "Étape 2/7" pour Phase 2)
- **Tutoriel contextuel** : Tooltips légers si nécessaire
- **Pas de pression** : Le joueur peut prendre son temps

## Flags et États Techniques (Commun aux deux phases)

### Tables de Base de Données

```sql
users (ou players selon le modèle)
- id
- onboarding_completed (bool, default false)
- onboarding_step (int, nullable - dernière étape complétée)
  - Phase 1 : valeurs 1-4
  - Phase 2 : valeurs 1-6
- onboarding_completed_at (datetime, nullable)
- character_name (string, nullable) - Nom du personnage
- first_daily_event_due_at (datetime, nullable)
- first_expedition_available_at (datetime, nullable)
- created_at
- updated_at

onboarding_progress (optionnel, pour tracking détaillé)
- id
- user_id
- step (int, 1-4 pour Phase 1, 1-7 pour Phase 2)
- completed_at (datetime)
- data (JSON - stores choices, planet name, etc.)
- created_at
```

### Flags Importants

- `onboarding_completed` : Détermine si le joueur peut accéder au jeu complet
- `onboarding_step` : Permet de reprendre l'onboarding si interruption
  - Phase 1 : 1-4 (4 étapes)
  - Phase 2 : 1-7 (7 étapes)
- `character_name` : Nom du personnage défini par le joueur
- `first_daily_event_due_at` : Déclenche le premier événement quotidien
- `first_expedition_available_at` : Déclenche la première expédition

## Logs et Métriques

### Métriques Utiles (Commun aux deux phases)

- **Temps passé dans l'onboarding** : Durée totale
- **Temps par étape** : Identifier les étapes trop longues
- **Abandon potentiel** : Pourcentage d'abandons par étape
- **Nom du personnage** : Statistiques sur les noms choisis
- **Choix narratif effectué** (Phase 2 uniquement) : Distribution des choix
- **Performance mini-jeu** (Phase 2 uniquement) : Scores et réussites

### Logs à Enregistrer

- Début de l'onboarding
- Complétion de chaque étape
- Nom du personnage choisi
- Choix narratif sélectionné (Phase 2 uniquement)
- Score mini-jeu (Phase 2 uniquement)
- Indice Parallaxe détecté (Phase 2 uniquement)
- Fin de l'onboarding
- Abandon (si applicable)

## Exemples et Cas d'Usage

### Exemple 1 : Onboarding Phase 1 (MVP)

**Joueur** : Nouveau joueur, première connexion

**Déroulement** :
1. Arrivée/Recrutement : 45s (regarde l'animation de booting, lit le texte mystérieux ligne par ligne)
2. Nom du personnage : 45s (saisit "Alex Nova")
3. Découverte planète : 50s (regarde l'animation de révélation progressive, lit les caractéristiques)
4. Présentation terminal : 25s (lit le message)

**Total** : ~2m45s

**Résultat** : Onboarding MVP complété, joueur prêt pour la première session quotidienne

### Exemple 2 : Onboarding Phase 2 (Complet) - Standard

**Joueur** : Nouveau joueur, première connexion

**Déroulement** :
1. Réveil : 35s (lit le texte mystérieux, appuie sur continuer)
2. Nom du personnage : 40s (saisit "Alex Nova")
3. Scan + mini-jeu : 15s (complète le mini-jeu avec succès)
4. Choix narratif : 25s (choisit "Analyser", obtient +50 données)
5. Nomination planète : 45s (choisit "Nova Prime")
6. Codex : 15s (consulte rapidement)
7. Projection : 10s (lit le message)

**Total** : ~3m25s

**Résultat** : Onboarding complet terminé, joueur prêt pour la première session quotidienne

### Exemple 3 : Onboarding Phase 2 (Complet) - Avec Parallaxe

**Joueur** : Nouveau joueur, première connexion

**Déroulement** :
1. Réveil : 40s (remarque un léger glitch visuel, lit le texte mystérieux)
2. Nom du personnage : 45s (saisit "Echo")
3. Scan + mini-jeu : 18s (voit "Anomalie non identifiée" brièvement, complète le mini-jeu)
4. Choix narratif : 30s (choisit "Amplifier", obtient +100 données)
5. Nomination planète : 50s (choisit "Echo Station")
6. Codex : 12s (consulte rapidement)
7. Projection : 12s (lit le message)

**Total** : ~3m47s

**Résultat** : Onboarding complet terminé avec premier indice Parallaxe, joueur intrigué

## Cas Limites (Commun aux deux phases)

1. **Interruption** : Le joueur peut reprendre à la dernière étape complétée
2. **Erreur de nom** : Validation côté client et serveur
3. **Mini-jeu échoué** (Phase 2 uniquement) : Pas de pénalité, progression normale
4. **Choix non fait** (Phase 2 uniquement) : Choix par défaut (Prudent) si timeout

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

### Spécifications Communes

**Système de progression** :
- Sauvegarde automatique à chaque étape
- Possibilité de reprendre si interruption
- Validation des étapes avant progression
- Architecture modulaire permettant d'ajouter facilement de nouvelles étapes

**Système de génération de planète** :
- Utilisation de la planète d'origine déjà générée (`user.home_planet_id`)
- Affichage des caractéristiques existantes
- Pas de génération supplémentaire nécessaire

### Spécifications Phase 1 (MVP)

**Systèmes nécessaires** :
- Système de planètes (déjà disponible)
- Système d'authentification (déjà disponible)
- Interface terminal (à créer ou déjà disponible)

**Pas de dépendances** :
- Mini-jeux (non nécessaires)
- Système narratif CYOA (non nécessaire)
- Stellarpedia (non nécessaire)
- Le Parallaxe (non nécessaire)

**Modifications requises au système d'inscription** :
- **Retirer** la demande de nom du personnage du processus d'inscription (register)
- L'inscription doit uniquement demander : email, mot de passe (et confirmation)
- Le nom du personnage sera défini lors de l'étape 2 de l'onboarding
- Vérifier que le champ `character_name` dans la table `users` est bien nullable

### Spécifications Phase 2 (Complet)

**Systèmes nécessaires** :
- Tous les systèmes de la Phase 1
- Système de mini-jeux (au moins "Scan Circulaire")
- Système narratif CYOA (choix narratifs basiques)
- Stellarpedia (création et consultation d'entrées)
- Le Parallaxe (introduction subtile du fil rouge)

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
- **Restructuration** : Division en deux phases distinctes (MVP sans dépendances, Complet avec tous les systèmes) pour clarifier l'ordre de développement

## Références

- **[onboarding.md](../local-brainstorming-data/onboarding.md)** : Document source du brainstorming
- **[ISSUE-005-implement-onboarding-mvp.md](../issues/ISSUE-005-implement-onboarding-mvp.md)** : Issue existante pour l'onboarding
- **[GAME-DESIGNER.md](../agents/GAME-DESIGNER.md)** : Documentation de l'agent Game Designer
- **[design-game-mechanic.md](../prompts/design-game-mechanic.md)** : Guide pour concevoir des mécaniques

