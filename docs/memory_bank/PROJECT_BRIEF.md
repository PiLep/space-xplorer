# Project Brief - Space Xplorer

## Vision métier

Space Xplorer est un jeu d'exploration de l'univers. Les joueurs peuvent découvrir et explorer différents systèmes stellaires, planètes et objets célestes dans un univers virtuel.

**MVP (Version initiale)** : 
- Chaque joueur commence avec une planète d'origine générée aléatoirement
- Le joueur peut visualiser les caractéristiques de sa planète d'origine
- Les fonctionnalités d'exploration seront ajoutées progressivement

## Fonctionnalités

### Fonctionnalités principales (MVP)

- **Inscription/Connexion** : Création de compte et authentification
- **Génération de planète d'origine** : À l'inscription, chaque joueur reçoit automatiquement une planète d'origine générée aléatoirement
- **Visualisation de la planète** : Affichage des caractéristiques de la planète d'origine du joueur
- **Profil utilisateur** : Gestion du profil du joueur

### Fonctionnalités futures

- Exploration d'autres planètes
- Découverte de systèmes stellaires
- Système de progression et d'achievements
- Interactions entre joueurs
- [À compléter selon l'évolution du projet]

## Personas

### Persona principal

**Nom** : Explorateur Spatial

**Profil** : 
- Joueur curieux intéressé par l'espace et l'exploration
- Aime découvrir de nouveaux mondes et leurs caractéristiques
- Recherche une expérience de jeu immersive et progressive

**Objectifs** : 
- Découvrir sa planète d'origine et ses caractéristiques
- Explorer progressivement l'univers
- Comprendre les différents types de planètes et leurs particularités

**Besoins** : 
- Interface claire et intuitive
- Informations détaillées sur les planètes
- Progression graduelle dans la découverte
- Expérience visuelle attrayante

## Flux utilisateurs

### Parcours d'inscription et découverte (MVP)

1. **Arrivée sur le site**
   - Page d'accueil avec présentation du jeu
   - Option d'inscription ou de connexion

2. **Inscription**
   - Formulaire d'inscription (nom, email, mot de passe)
   - Validation et création du compte
   - Génération automatique d'une planète d'origine (aléatoire)
   - Redirection vers le tableau de bord

3. **Connexion**
   - Formulaire de connexion
   - Authentification et obtention du token
   - Redirection vers le tableau de bord

4. **Tableau de bord**
   - Affichage de la planète d'origine du joueur
   - Visualisation des caractéristiques de la planète
   - Informations sur le profil du joueur

### Parcours d'exploration (à venir)

- [À documenter : exploration d'autres planètes, découvertes, etc.]

## Système de planètes

### Caractéristiques des planètes

Chaque planète possède les caractéristiques suivantes :

- **Type** : Type de planète (tellurique, gazeuse, glacée, désertique, océanique)
- **Taille** : Petite / Moyenne / Grande
- **Température** : Froide / Tempérée / Chaude
- **Atmosphère** : Respirable / Toxique / Inexistante
- **Terrain** : Rocheux / Océanique / Désertique / Forestier / Urbain / Mixte / Glacé
- **Ressources** : Abondantes / Modérées / Rares
- **Nom** : Généré aléatoirement (ex: "Kepler-452b", "Proxima Centauri c")
- **Description** : Texte descriptif généré à partir des caractéristiques combinées

### Types de planètes

Le système utilise un pool de types de planètes avec des poids de probabilité pour la génération :

1. **Planète Tellurique** (40% de chance)
   - Planète rocheuse similaire à la Terre
   - Caractéristiques variées selon les distributions de probabilité

2. **Planète Gazeuse** (25% de chance)
   - Planète géante gazeuse
   - Grande taille, atmosphère toxique

3. **Planète Glacée** (15% de chance)
   - Planète recouverte de glace
   - Température froide, ressources souvent rares

4. **Planète Désertique** (10% de chance)
   - Planète aride et chaude
   - Terrain désertique, ressources limitées

5. **Planète Océanique** (10% de chance)
   - Planète principalement recouverte d'eau
   - Grande taille, atmosphère souvent respirable

*Note : Chaque type a ses propres distributions de probabilité pour les caractéristiques (taille, température, etc.)*

