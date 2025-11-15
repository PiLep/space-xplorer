# ISSUE-005 : Implémenter l'onboarding MVP Phase 1

## Type
Feature

## Priorité
High

## Description

Implémenter un système d'onboarding immersif qui accueille le joueur après la vérification de son email. L'onboarding doit créer une expérience mémorable dans l'univers Stellar (ambiance Alien, compagnie mystérieuse) et guider le joueur dans ses premières actions.

**MVP Phase 1** : Onboarding minimal avec 4 étapes essentielles, permettant de convaincre le joueur de rester et de créer une connexion émotionnelle avec l'univers du jeu. Durée cible : 2-3 minutes.

**Principe de design** : Moins d'informations, plus d'ambiance. Créer du mystère et de l'intrigue plutôt que d'expliquer clairement qui est Stellar et quelle est la mission du joueur.

## Contexte Métier

L'onboarding est le **premier contact critique** du joueur avec le jeu après son inscription. C'est un moment décisif pour :
- **Créer l'engagement** : Convaincre le joueur de rester et de continuer à jouer
- **Immersion** : Plonger le joueur dans l'univers Stellar (ambiance Alien, compagnie mystérieuse)
- **Guidage** : Accompagner le joueur dans ses premières actions sans sur-guider
- **Définition du personnage** : Permettre au joueur de définir son identité dans le jeu (nom du personnage)

**Moment d'affichage** : Juste après la vérification de l'email, première entrée dans le jeu.

**Ambiance** : Immersive dans un monde à la Alien, grosse compagnie mystérieuse (Stellar) dont on ne connait pas les intentions. Le joueur vient pour l'appel de l'aventure. **Ne pas expliquer** qui est Stellar, pourquoi le joueur a été sélectionné, quelle est sa mission exacte. Laisser des questions en suspens.

**Reprise automatique** : L'onboarding peut être interrompu (fermeture de page, crash navigateur) et doit reprendre automatiquement à l'étape où le joueur s'est arrêté.

**Changement important** : Le nom du personnage ne doit **plus** être demandé lors du processus d'inscription (register). Cette étape d'onboarding remplace cette fonctionnalité. L'inscription doit uniquement demander : email, mot de passe (et confirmation), et éventuellement nom d'utilisateur pour l'authentification si nécessaire.

## Critères d'Acceptation

### MVP Phase 1 - Étapes essentielles

#### Étape 1 : Arrivée/Recrutement Stellar (30-60 secondes)

- [ ] **Écran d'initialisation**
  - Écran noir avec "Booting…" (animation lente, typewriter effect)
  - Interface terminal instable ou glitchée (effet visuel subtil)
  - Sons de système (optionnel, subtil)

- [ ] **Message cryptique et mystérieux**
  - Texte apparaissant ligne par ligne (typewriter effect)
  - Messages cryptiques et ambigus présentant Stellar comme une entité énigmatique
  - Ambiance Alien, texte narratif minimaliste dans l'interface terminal
  - Pas d'explication claire du rôle ou de la mission (laisser des questions en suspens)
  - Exemples de textes fournis dans les détails techniques

- [ ] **Éléments visuels**
  - Animation de booting (texte qui apparaît progressivement)
  - Effets de glitch subtils (occasionnels, pas trop fréquents)
  - Ambiance sombre avec texte terminal vert/cyan
  - Sons de système (optionnel, très subtils)

- [ ] **Navigation**
  - Bouton "Continuer" (apparaît après le texte complet)
  - Transition vers l'étape suivante avec une atmosphère de mystère établie

#### Étape 2 : Définition du Nom du Personnage (30-60 secondes)

- [ ] **Contexte narratif**
  - Message cohérent avec l'ambiance mystérieuse Stellar
  - Demande d'identifiant pour l'activation du système
  - Exemples de textes fournis dans les détails techniques

- [ ] **Formulaire de saisie**
  - Champ de saisie avec validation en temps réel
  - Interface terminal cohérente avec le reste du jeu
  - Validation du nom (longueur : 3-30 caractères)
  - Caractères autorisés : lettres, chiffres, espaces, tirets
  - Vérification de sécurité (filtrage des mots inappropriés)
  - Message d'erreur discret en cas d'invalidité

- [ ] **Sauvegarde**
  - Nom sauvegardé dans `character_name` (nouveau champ dans `users`)
  - Confirmation visuelle discrète ("Identifiant enregistré" ou similaire)
  - Transition vers l'étape suivante

- [ ] **Modification du système d'inscription**
  - Retirer la demande de nom du personnage du processus d'inscription (register)
  - L'inscription doit uniquement demander : email, mot de passe (et confirmation)
  - Vérifier que le champ `character_name` dans la table `users` est bien nullable

#### Étape 3 : Découverte de la Planète d'Origine (30-60 secondes)

- [ ] **Phase 1 : Initialisation du scan** (5-8 secondes)
  - Écran sombre avec texte terminal
  - "Initialisation du scanner longue portée..."
  - Animation de chargement/scan progressif
  - Effet visuel de scan (lignes, grille, etc.)

- [ ] **Phase 2 : Détection** (5-8 secondes)
  - "Signal détecté..."
  - "Analyse des données..."
  - Animation de traitement des données
  - Effet de zoom progressif vers l'espace

- [ ] **Phase 3 : Révélation** (10-15 secondes)
  - Apparition progressive de la planète (fade-in ou zoom)
  - Visualisation de la planète (image générée ou placeholder stylisé)
  - Texte narratif court apparaissant progressivement avec les caractéristiques :
    - Planète identifiée : **[Nom généré]**
    - Classification : **[Type]**
    - Atmosphère : **[Type]** | Température : **[Valeur]**
    - Distance : **[Valeur]** années-lumière
    - Statut : **Planète d'origine assignée**

- [ ] **Phase 4 : Confirmation** (5-10 secondes)
  - Message de confirmation
  - "Cette planète est maintenant votre point de départ."
  - Option de visualiser plus de détails (optionnel)

- [ ] **Éléments visuels**
  - Animation de scan (lignes animées, grille, radar)
  - Transition progressive vers la visualisation de la planète
  - Effet de révélation (fade-in, zoom, ou apparition progressive)
  - Style cohérent avec l'interface terminal
  - Ambiance spatiale immersive

- [ ] **Navigation**
  - Bouton "Continuer" (apparaît après la révélation complète)
  - Transition vers l'étape finale

**Note technique** : L'animation peut être réalisée avec CSS/JavaScript simple, sans dépendre de systèmes complexes. Utiliser l'image de planète déjà générée ou un placeholder stylisé.

#### Étape 4 : Présentation du Terminal (20-30 secondes)

- [ ] **Message de transition**
  - Message simple de transition vers le terminal
  - Indication du prochain événement disponible
  - Pas de tutoriel détaillé (le joueur découvre par lui-même)

- [ ] **Exemple de message** :
  > Votre terminal est maintenant opérationnel.  
  > Un scan longue portée sera disponible dans 23 heures.  
  > Explorez à votre rythme.

- [ ] **Principe de design**
  - Ne pas sur-guider. Le joueur doit découvrir les fonctionnalités par lui-même.
  - L'onboarding introduit l'univers et les bases, mais laisse de la place à l'exploration et à la curiosité.

- [ ] **Navigation**
  - Bouton "Accéder au terminal"

- [ ] **Finalisation**
  - Onboarding complété
  - Flag `onboarding_completed_at` défini
  - `first_daily_event_due_at` défini (24h après) - **Note** : Nécessite une issue dédiée pour le système d'événements quotidiens
  - `first_expedition_available_at` défini (24h après) - **Note** : Nécessite une issue dédiée pour le système d'expéditions
  - Redirection vers le dashboard/terminal principal

### Fonctionnalités techniques

- [ ] **Sauvegarde de progression**
  - Sauvegarde de l'étape en cours dans la base de données (nouveau champ `onboarding_step` dans la table `users`)
  - Reprise automatique à l'étape où le joueur s'est arrêté lors de la reconnexion
  - Gestion des cas d'interruption (fermeture page, crash navigateur)

- [ ] **Interface et expérience utilisateur**
  - Interface terminal cohérente avec le reste du jeu
  - Texte narratif progressif avec transitions subtiles
  - Boutons de navigation (Continuer, Précédent si applicable)
  - Expérience fluide et immersive (< 3 minutes pour le MVP)
  - Indicateur de progression (ex: "Étape 2/4")
  - Feedback immédiat pour chaque action

- [ ] **Extensibilité**
  - Architecture permettant d'ajouter facilement de nouvelles étapes
  - Système de gestion des étapes modulaire et extensible
  - Préparation pour les phases futures (avatar, contenu narratif enrichi, mini-jeux, CYOA, etc.)

## Détails Techniques

### Modèle de données

**Ajout dans la table `users`** :
- `onboarding_step` (integer, nullable) : Numéro de l'étape en cours (1, 2, 3, 4)
- `onboarding_completed_at` (timestamp, nullable) : Date de complétion de l'onboarding
- `character_name` (string, nullable) : Nom du personnage défini par le joueur (distinct du champ `name` utilisé pour l'authentification)
- `first_daily_event_due_at` (timestamp, nullable) : Date du premier événement quotidien (24h après complétion onboarding) - **Note** : Nécessite une issue dédiée
- `first_expedition_available_at` (timestamp, nullable) : Date de disponibilité de la première expédition (24h après complétion onboarding) - **Note** : Nécessite une issue dédiée

**Migration à créer** :
```php
Schema::table('users', function (Blueprint $table) {
    $table->integer('onboarding_step')->nullable()->after('email_verified_at');
    $table->timestamp('onboarding_completed_at')->nullable()->after('onboarding_step');
    $table->string('character_name')->nullable()->after('onboarding_completed_at');
    $table->timestamp('first_daily_event_due_at')->nullable()->after('character_name');
    $table->timestamp('first_expedition_available_at')->nullable()->after('first_daily_event_due_at');
});
```

**Modification du système d'inscription** :
- Retirer le champ `name` du formulaire d'inscription (ou le renommer en `username` pour l'authentification uniquement)
- Le champ `name` dans `users` peut rester pour l'authentification, mais ne doit plus être demandé à l'inscription
- Le `character_name` sera défini lors de l'étape 2 de l'onboarding

### Flux utilisateur

1. **Après vérification email** → Redirection vers l'onboarding (si `onboarding_completed_at` est null)
2. **Vérification de l'étape** → Reprendre à l'étape sauvegardée (`onboarding_step`)
3. **Progression** → Sauvegarder l'étape après chaque action
4. **Fin de l'onboarding** → Marquer comme complété (`onboarding_completed_at`) et rediriger vers le dashboard

### Architecture suggérée

- **Livewire Component** : `Onboarding.php` pour gérer l'état et la navigation entre les étapes
- **Migration** : Ajouter les champs nécessaires dans la table `users`
- **Service** (optionnel) : `OnboardingService` pour la logique métier de progression
- **Vues** : Une vue par étape ou une vue unique avec gestion d'état
- **Animations** : CSS/JavaScript pour les effets de typewriter, glitch, scan, révélation

### Exemples de textes pour l'étape 1

**Version 1 - Minimaliste** :
> Système activé.  
> Contrat Stellar #█████ validé.  
> Réveil programmé.  
> [Pause]  
> Vous êtes en ligne.

**Version 2 - Plus mystérieuse** :
> [Glitch visuel]  
> Signal détecté.  
> Stellar.  
> [Pause]  
> Vous avez été sélectionné.  
> Raison : Classifiée.

**Version 3 - Ambiance Alien** :
> Émergence d'hibernation...  
> Système de survie : Opérationnel.  
> Mission : Non spécifiée.  
> Destination : Inconnue.  
> Stellar vous attend.

**Principe** : Moins d'informations, plus d'ambiance. Créer du mystère et de l'intrigue.

### Exemples de textes pour l'étape 2

**Version 1 - Minimaliste** :
> Identifiant requis pour l'activation du système.  
> [Champ de saisie]

**Version 2 - Plus narrative** :
> Système d'identification...  
> Quel identifiant souhaitez-vous utiliser pour ce contrat ?  
> [Champ de saisie]

**Version 3 - Ambiance Alien** :
> Enregistrement de l'identité opérationnelle.  
> Nom d'identification :  
> [Champ de saisie]

### Intégration avec l'existant

- Utiliser la planète d'origine déjà générée (`user.home_planet_id`)
- S'intégrer avec le système d'authentification existant
- Respecter l'architecture API-first et event-driven
- Utiliser les layouts existants (`layouts.app`)

## Notes

### Scope MVP Phase 1

- **Avatar** : Non inclus dans le MVP (peut être ajouté en Phase 2)
- **Contenu narratif** : Textes simples pour le MVP, peut être enrichi en Phase 2
- **Animations** : Transitions subtiles pour le MVP, animations plus poussées en Phase 2
- **Skip** : Non prévu pour le MVP (peut être ajouté si nécessaire)
- **Mini-jeux** : Non inclus dans le MVP (Phase 2)
- **Système narratif CYOA** : Non inclus dans le MVP (Phase 2)
- **Stellarpedia** : Non inclus dans le MVP (Phase 2)
- **Le Parallaxe** : Non inclus dans le MVP (Phase 2)

### Durée cible

- **Cible** : 2-3 minutes
- **Maximum** : 4 minutes
- **Minimum** : 1m30 (si toutes les étapes sont complétées rapidement)

### Principes de design

1. **Ne jamais bloquer le joueur** : Toutes les étapes sont skippables (sauf critiques)
2. **Aucun texte long** : Maximum 3 phrases par écran
3. **Aucune mécanique complexe** : Introduction progressive uniquement
4. **Maximum une seule action** : Par étape
5. **Onboarding terminé rapidement** : Objectif 2-3 minutes
6. **Préparation de la première session quotidienne** : Dès la fin
7. **Découverte par soi-même** : Ne pas sur-guider, laisser de la place à l'exploration et à la curiosité

### Issues liées à créer

Cette issue va probablement nécessiter la création d'autres issues :
- Système de terminal (interface principale du jeu)
- Système d'événements quotidiens (pour `first_daily_event_due_at`)
- Système d'expéditions (pour `first_expedition_available_at`)
- Système de personnage/avatar (Phase 2 de l'onboarding)

### Extensibilité

L'architecture doit permettre d'ajouter facilement :
- De nouvelles étapes d'onboarding
- Du contenu narratif enrichi
- Des interactions plus complexes
- Des animations et transitions
- Les systèmes de Phase 2 (mini-jeux, CYOA, Stellarpedia, Parallaxe)

### Expérience utilisateur

- **Durée cible** : 2-3 minutes pour le MVP
- **Rythme** : Contrôlé par le joueur (bouton "Continuer")
- **Immersion** : Interface terminal, texte narratif, ambiance Alien/Stellar
- **Guidage** : Progression claire et guidée, mais sans sur-guider
- **Mystère** : Laisser des questions en suspens pour créer de l'intrigue

## Références

- **[PROJECT_BRIEF.md](../memory_bank/PROJECT_BRIEF.md)** - Vision métier et personas
- **[ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md)** - Architecture technique et modèle de données
- **[STACK.md](../memory_bank/STACK.md)** - Stack technique (Laravel, Livewire)
- **[DRAFT-05-onboarding-system.md](../game-design/drafts/DRAFT-05-onboarding-system.md)** - Draft de Game Design complet

## Suivi et Historique

### Statut

À faire

### Historique

#### 2025-01-27 - Alex (Product) - Création de l'issue
**Statut** : À faire
**Détails** : Issue créée suite à l'élaboration avec le métier. MVP Phase 1 défini avec 4 étapes essentielles : Arrivée/Recrutement Stellar, Définition du nom du personnage, Découverte de la planète d'origine, Présentation du terminal. Architecture extensible prévue pour les phases futures.
**Notes** : Issue prioritaire pour améliorer l'engagement et la rétention des nouveaux joueurs.

#### 2025-01-27 - Alex (Product) - Enrichissement avec Game Design
**Statut** : À faire
**Détails** : Issue enrichie avec les détails du Game Design DRAFT-05. Ajouts principaux :
- Détails précis sur chaque étape avec exemples de textes concrets
- Principe de design "moins d'informations, plus d'ambiance" clarifié
- Changement important : le nom du personnage n'est plus demandé à l'inscription
- Animations et effets visuels détaillés pour chaque étape
- Flags supplémentaires pour les systèmes futurs (`first_daily_event_due_at`, `first_expedition_available_at`)
- Durée cible clarifiée (2-3 minutes) avec répartition par étape
- Principes de design UX détaillés
**Notes** : Cette version enrichie apporte des clarifications importantes sur l'ambiance, le ton du jeu et les détails d'implémentation. Prête pour la création du plan de développement par Sam (Lead Developer).
