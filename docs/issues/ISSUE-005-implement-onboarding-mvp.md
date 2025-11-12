# ISSUE-005 : Implémenter l'onboarding MVP Phase 1

## Type
Feature

## Priorité
High

## Description

Implémenter un système d'onboarding immersif qui accueille le joueur après la vérification de son email. L'onboarding doit créer une expérience mémorable dans l'univers Stellar (ambiance Alien, compagnie mystérieuse) et guider le joueur dans ses premières actions.

**MVP Phase 1** : Onboarding minimal avec 3-4 étapes essentielles, permettant de convaincre le joueur de rester et de créer une connexion émotionnelle avec l'univers du jeu.

## Contexte Métier

L'onboarding est le **premier contact critique** du joueur avec le jeu après son inscription. C'est un moment décisif pour :
- **Créer l'engagement** : Convaincre le joueur de rester et de continuer à jouer
- **Immersion** : Plonger le joueur dans l'univers Stellar (ambiance Alien, compagnie mystérieuse)
- **Guidage** : Accompagner le joueur dans ses premières actions
- **Définition du personnage** : Permettre au joueur de définir son identité dans le jeu

**Moment d'affichage** : Juste après la vérification de l'email, première entrée dans le jeu.

**Ambiance** : Immersive dans un monde à la Alien, grosse compagnie mystérieuse (Stellar) dont on ne connait pas les intentions. Le joueur vient pour l'appel de l'aventure.

**Reprise automatique** : L'onboarding peut être interrompu (fermeture de page, crash navigateur) et doit reprendre automatiquement à l'étape où le joueur s'est arrêté.

## Critères d'Acceptation

### MVP Phase 1 - Étapes essentielles

- [ ] **Étape 1 : Arrivée/Recrutement Stellar**
  - Message de bienvenue immersif présentant Stellar comme une compagnie mystérieuse
  - Ambiance Alien, texte narratif dans l'interface terminal
  - Présentation du rôle du joueur (nouveau contrat/poste chez Stellar)
  - Bouton "Continuer" pour passer à l'étape suivante

- [ ] **Étape 2 : Définition du nom du personnage**
  - Formulaire permettant au joueur de définir le nom de son personnage
  - Validation du nom (longueur, caractères autorisés)
  - Sauvegarde du nom dans le profil utilisateur
  - Interface terminal cohérente avec le reste du jeu

- [ ] **Étape 3 : Découverte de la planète d'origine**
  - Présentation de la planète d'origine (déjà générée à l'inscription)
  - Affichage des caractéristiques de la planète de manière immersive
  - Moment magique de découverte
  - Bouton "Continuer" pour passer à l'étape suivante

- [ ] **Étape 4 : Présentation du terminal**
  - Introduction à l'interface terminal (interface principale du jeu)
  - Explication des fonctionnalités de base disponibles
  - Accès au dashboard/terminal principal
  - Fin de l'onboarding

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

- [ ] **Extensibilité**
  - Architecture permettant d'ajouter facilement de nouvelles étapes
  - Système de gestion des étapes modulaire et extensible
  - Préparation pour les phases futures (avatar, contenu narratif enrichi, etc.)

## Détails Techniques

### Modèle de données

**Ajout dans la table `users`** :
- `onboarding_step` (integer, nullable) : Numéro de l'étape en cours (1, 2, 3, 4)
- `onboarding_completed_at` (timestamp, nullable) : Date de complétion de l'onboarding
- `character_name` (string, nullable) : Nom du personnage défini par le joueur

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

### Issues liées à créer

Cette issue va probablement nécessiter la création d'autres issues :
- Système de terminal (interface principale du jeu)
- Inbox (système de messages)
- Système de personnage/avatar (Phase 2 de l'onboarding)

### Extensibilité

L'architecture doit permettre d'ajouter facilement :
- De nouvelles étapes d'onboarding
- Du contenu narratif enrichi
- Des interactions plus complexes
- Des animations et transitions

### Expérience utilisateur

- **Durée cible** : 2-3 minutes pour le MVP
- **Rythme** : Contrôlé par le joueur (bouton "Continuer")
- **Immersion** : Interface terminal, texte narratif, ambiance Alien/Stellar
- **Guidage** : Progression claire et guidée

## Références

- [PROJECT_BRIEF.md](../memory_bank/PROJECT_BRIEF.md) - Vision métier et personas
- [ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md) - Architecture technique et modèle de données
- [STACK.md](../memory_bank/STACK.md) - Stack technique (Laravel, Livewire)

## Suivi et Historique

### Statut

À faire

### Historique

#### 2025-01-27 - Alex (Product) - Création de l'issue
**Statut** : À faire
**Détails** : Issue créée suite à l'élaboration avec le métier. MVP Phase 1 défini avec 4 étapes essentielles : Arrivée/Recrutement Stellar, Définition du nom du personnage, Découverte de la planète d'origine, Présentation du terminal. Architecture extensible prévue pour les phases futures.
**Notes** : Issue prioritaire pour améliorer l'engagement et la rétention des nouveaux joueurs.

