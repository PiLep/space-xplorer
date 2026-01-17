# ISSUE-013 : Progression onboarding - Terminal planète → OS vaisseau en orbite

## Type
Feature

## Priorité
High

## Description

Implémenter une progression narrative et visuelle pour les nouveaux joueurs : démarrer sur un terminal simple sur la planète de départ, puis recevoir son vaisseau d'exploration et décoller pour passer en orbite, avec une transition vers l'OS du vaisseau (interface différente visuellement).

Cette issue complète l'onboarding existant et le système de vaisseau (ISSUE-011, ISSUE-012) en ajoutant une dimension narrative et visuelle à la progression du joueur.

## Contexte Métier

### État Actuel du Gameplay

**Ce qui existe aujourd'hui** :
- Le joueur se connecte et arrive directement sur le dashboard
- Interface terminal avec animation de boot
- Affichage de la planète d'origine du joueur
- Système de vaisseau prévu (ISSUE-011) mais pas encore intégré dans le flow

**Problème identifié** :
- Pas de progression narrative claire pour un nouveau joueur
- Pas de distinction visuelle entre "être sur la planète" et "être dans l'espace"
- L'arrivée sur le dashboard ne raconte pas l'histoire du recrutement et du départ

### Vision Proposée

**Pour un nouveau joueur fraîchement recruté** :

1. **Phase 1 - Terminal Planète** (état initial)
   - Le joueur arrive sur un terminal simple sur sa planète de départ
   - Interface sobre, terminal basique
   - Contexte : "Vous êtes sur l'avant-poste Stellar, planète [nom]"
   - Le joueur découvre sa planète d'origine

2. **Phase 2 - Attribution du Vaisseau** (transition narrative)
   - Message dans l'inbox : "Votre vaisseau d'exploration vous attend"
   - Notification : "Votre vaisseau [nom] est prêt au hangar"
   - Le joueur peut voir son vaisseau attribué

3. **Phase 3 - Décollage et Orbite** (transition visuelle majeure)
   - Action : "Décoller" ou "Embarquer dans le vaisseau"
   - Animation de transition (décollage, sortie de l'atmosphère)
   - **Changement d'interface** : Passage à l'OS du vaisseau
   - Interface différente visuellement : plus moderne, HUD spatial, écrans multiples
   - Contexte : "Vous êtes maintenant en orbite autour de [planète]"
   - Le dashboard change pour refléter cette nouvelle position

**Valeur utilisateur** :
- **Immersion narrative** : Le joueur vit une progression claire et immersive
- **Sensation de progression** : Passage d'un état "terrestre" à un état "spatial"
- **Distinction visuelle** : L'interface change pour refléter le changement de contexte
- **Engagement** : Le décollage devient un moment marquant et mémorable
- **Clarté** : Le joueur comprend visuellement où il se trouve dans l'univers

## Critères d'Acceptation

### 1. État Initial : Terminal Planète

- [ ] **Nouveau joueur** (sans vaisseau attribué) voit :
  - Interface terminal simple et sobre
  - Contexte visuel : "Sur la planète [nom]"
  - Message d'accueil : "Bienvenue sur l'avant-poste Stellar"
  - Affichage de la planète d'origine
  - Pas encore d'option de décollage visible

- [ ] **Design visuel** :
  - Terminal basique, style "avant-poste"
  - Couleurs plus terreuses, moins futuristes
  - Interface minimaliste

### 2. Attribution du Vaisseau

- [ ] **Dépendance** : Nécessite ISSUE-011 (système de vaisseau MVP) - ✅ **En cours**

- [ ] **Moment d'attribution** :
  - Automatique lors de l'onboarding (ou juste après)
  - Ou déclenché manuellement par le joueur via un bouton "Recevoir mon vaisseau"

- [ ] **Notification et message** :
  - Message dans l'inbox : "Votre vaisseau d'exploration vous attend"
  - Notification : "Votre vaisseau [nom] est prêt"
  - Le joueur peut voir son vaisseau dans une section dédiée

### 3. Transition : Décollage

- [ ] **Action de décollage** :
  - Bouton "Décoller" ou "Embarquer dans le vaisseau" visible une fois le vaisseau attribué
  - Vérification : Le joueur doit avoir un vaisseau attribué
  - Confirmation : "Êtes-vous prêt à décoller ?"

- [ ] **Animation de transition** :
  - Animation de décollage (visuelle ou textuelle)
  - Messages de progression : "Décollage...", "Sortie de l'atmosphère...", "Mise en orbite..."
  - Durée : 3-5 secondes pour créer l'immersion

- [ ] **Changement d'état** :
  - Flag utilisateur : `is_in_orbit` (boolean)
  - Position : Passage de "sur_planete" à "en_orbite"
  - Sauvegarde de l'état dans la base de données

### 4. Nouvelle Interface : OS Vaisseau

- [ ] **Changement visuel majeur** :
  - Interface différente : style "OS vaisseau spatial"
  - HUD spatial avec écrans multiples
  - Couleurs plus futuristes (bleus, violets, verts)
  - Effets visuels : scanlines, hologrammes, etc.

- [ ] **Éléments visuels distinctifs** :
  - Header différent : "STELLAR EXPLORATION VESSEL - [nom vaisseau]"
  - Contexte affiché : "En orbite autour de [planète]"
  - Informations du vaisseau visibles (fuel, intégrité, etc.)
  - Navigation différente : options spatiales plutôt que terrestres

- [ ] **Dashboard en orbite** :
  - Vue de la planète depuis l'espace (optionnel)
  - Options d'exploration disponibles
  - Actions spatiales : "Scanner la planète", "Explorer le système", etc.

### 5. Retour sur Planète (optionnel pour MVP)

- [ ] **Action de retour** :
  - Bouton "Atterrir sur la planète" disponible depuis l'orbite
  - Retour à l'interface terminal planète
  - Changement d'état : `is_in_orbit = false`

### 6. Persistance de l'État

- [ ] **Modèle User** :
  - Ajouter `is_in_orbit` (boolean, default: false)
  - Ajouter `current_position` (enum: 'on_planet', 'in_orbit', 'in_transit')
  - Migration pour ajouter ces champs

- [ ] **Chargement de l'état** :
  - Au chargement du dashboard, vérifier l'état du joueur
  - Afficher l'interface appropriée selon l'état
  - Persister l'état entre les sessions

## Détails Techniques

### Design System - Deux Thèmes d'Interface

**Terminal Planète** :
- Style : Terminal basique, avant-poste
- Couleurs : Terreuses (bruns, ocres, gris)
- Typographie : Monospace simple
- Effets : Minimalistes, ambiance "terrestre"

**OS Vaisseau** :
- Style : HUD spatial moderne
- Couleurs : Futuristes (bleus, violets, verts néon)
- Typographie : Monospace avec effets
- Effets : Scanlines, hologrammes, particules
- Layout : Multi-panneaux, écrans multiples

### Composants Livewire

**Dashboard** :
- Détecter l'état du joueur (`is_in_orbit`)
- Afficher le composant approprié selon l'état
- Gérer la transition entre les états

**Nouveau composant : `Takeoff`** :
- Gérer l'action de décollage
- Animation de transition
- Mise à jour de l'état utilisateur

### Routes

- `/dashboard` : Affiche l'interface selon l'état (planète ou orbite)
- `/ship/takeoff` : Action de décollage (POST)
- `/ship/land` : Action d'atterrissage (POST, optionnel pour MVP)

### Événements

- `ShipTakeoff` : Déclenché lors du décollage
- `ShipLanded` : Déclenché lors de l'atterrissage (optionnel)

## Dépendances

- **ISSUE-011** : Système de vaisseau MVP (nécessaire pour l'attribution)
- **ISSUE-012** : Interface dédiée au vaisseau (peut être intégrée)
- **ISSUE-005** : Onboarding MVP (peut être intégré dans le flow)

## Questions Ouvertes

1. **Moment du décollage** :
   - Automatique après attribution du vaisseau ?
   - Ou action manuelle du joueur ?
   - **Recommandation** : Action manuelle pour donner le contrôle au joueur

2. **Retour sur planète** :
   - Permis immédiatement ?
   - Ou nécessite une raison (réparation, ravitaillement) ?
   - **Recommandation MVP** : Permis immédiatement, restrictions en Phase 2

3. **Animation de décollage** :
   - Animation visuelle complexe (canvas, vidéo) ?
   - Ou séquence textuelle avec effets ?
   - **Recommandation MVP** : Séquence textuelle avec effets CSS, animation complexe en Phase 2

4. **Vue depuis l'orbite** :
   - Affichage visuel de la planète depuis l'espace ?
   - Ou simplement changement d'interface ?
   - **Recommandation MVP** : Changement d'interface d'abord, vue visuelle en Phase 2

## Références

- **[onboarding.md](../local-brainstorming-data/onboarding.md)** : Document d'onboarding existant
- **[ISSUE-011](./ISSUE-011-implement-ship-system-mvp.md)** : Système de vaisseau MVP
- **[ISSUE-012](./ISSUE-012-implement-ship-interface.md)** : Interface dédiée au vaisseau
- **[PROJECT_BRIEF.md](../memory_bank/PROJECT_BRIEF.md)** : Vision métier du projet

## Historique

- **2024-XX-XX** : Création de l'issue suite à la réflexion sur la progression du gameplay
