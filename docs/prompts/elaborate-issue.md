# Action: Elaborate Issue

## Description

Cette action permet à l'agent Product (Alex) d'élaborer progressivement une issue structurée à partir d'une idée initiale, en collaborant avec le métier (humain) via un processus interactif de questions-réponses. L'objectif est de construire petit à petit un document issue complet et bien structuré avant sa création finale.

## Quand Utiliser Cette Action

L'agent Product doit utiliser cette action quand :
- Une nouvelle idée de fonctionnalité est proposée mais n'est pas encore complètement définie
- Une demande métier nécessite des clarifications avant de créer l'issue
- Une fonctionnalité complexe nécessite une exploration approfondie avant documentation
- Le contexte métier ou les besoins utilisateur ne sont pas encore clairs
- Une idée nécessite une réflexion approfondie sur la valeur utilisateur, les cas d'usage, et les critères d'acceptation

**Note** : Cette action précède la création finale de l'issue. Une fois l'élaboration terminée, l'agent Product créera l'issue finale en suivant le guide [create-issue.md](./create-issue.md).

## Processus d'Élaboration

### Phase 1 : Compréhension Initiale

**Objectif** : Comprendre l'idée initiale et le contexte métier.

**Actions de l'agent Product** :
1. **Accueillir l'idée** : Relever l'idée initiale proposée par le métier
2. **Poser des questions d'ouverture** pour clarifier :
   - Quel est le problème ou le besoin à résoudre ?
   - Qui est l'utilisateur concerné ?
   - Quel est le contexte métier ?
   - Quelle est la valeur attendue pour l'utilisateur ?

**Format de dialogue** :
- L'agent Product pose **2-3 questions à la fois** (pas trop pour éviter la surcharge)
- Attendre la réponse du métier avant de continuer
- Reformuler la compréhension avant de passer à la suite

### Phase 2 : Exploration des Besoins Utilisateur

**Objectif** : Définir précisément les besoins utilisateur et les cas d'usage.

**Questions à explorer** :
- **Personas** : Quel persona utilisateur est concerné ? (L'Explorateur Spatial, Admin, etc.)
- **Cas d'usage** : Quels sont les cas d'usage principaux ?
- **Parcours utilisateur** : Comment l'utilisateur va-t-il utiliser cette fonctionnalité ?
- **Valeur utilisateur** : Quelle valeur cette fonctionnalité apporte-t-elle au joueur ?
- **Expérience attendue** : Quelle expérience utilisateur souhaitons-nous créer ?

**Actions de l'agent Product** :
- Poser des questions ciblées sur chaque aspect
- Explorer les cas limites et les cas d'erreur
- Comprendre les attentes en termes d'UX

### Phase 3 : Définition des Critères d'Acceptation

**Objectif** : Définir précisément ce qui doit être fait pour considérer la fonctionnalité comme terminée.

**Questions à explorer** :
- **Fonctionnalités principales** : Quelles sont les fonctionnalités essentielles ?
- **Comportements attendus** : Comment la fonctionnalité doit-elle se comporter dans chaque cas ?
- **Contraintes** : Y a-t-il des contraintes techniques, métier ou UX ?
- **Critères de succès** : Comment saurons-nous que la fonctionnalité est réussie ?
- **Dépendances** : Y a-t-il des dépendances avec d'autres fonctionnalités ?

**Actions de l'agent Product** :
- Reformuler chaque besoin en critère d'acceptation actionnable
- Vérifier que les critères sont mesurables et testables
- Identifier les dépendances et les prérequis

### Phase 4 : Contexte Métier et Priorisation

**Objectif** : Comprendre l'importance métier et définir la priorité.

**Questions à explorer** :
- **Contexte métier** : Pourquoi cette fonctionnalité est-elle importante maintenant ?
- **Alignement produit** : Comment s'intègre-t-elle dans la vision produit et la roadmap ?
- **Impact utilisateur** : Quel est l'impact attendu sur l'engagement utilisateur ?
- **Urgence** : Y a-t-il une urgence métier ou technique ?
- **Risques** : Y a-t-il des risques à ne pas implémenter cette fonctionnalité ?

**Actions de l'agent Product** :
- Évaluer la valeur utilisateur vs. la complexité
- Proposer une priorité (High, Medium, Low) avec justification
- Identifier les risques et dépendances

### Phase 5 : Synthèse et Validation

**Objectif** : Synthétiser toutes les informations collectées et valider avec le métier.

**Actions de l'agent Product** :
1. **Synthétiser** toutes les informations collectées dans un format structuré
2. **Présenter** le résumé au métier avec :
   - Description de la fonctionnalité
   - Contexte métier
   - Critères d'acceptation proposés
   - Priorité suggérée
   - Questions restantes (si applicable)
3. **Valider** avec le métier que la synthèse est correcte
4. **Finaliser** ou ajuster selon les retours

## Format de Dialogue

### Structure des Questions

Les questions doivent être :
- **Claires et concises** : Une question à la fois, formulée simplement
- **Orientées utilisateur** : Toujours penser du point de vue de l'utilisateur final
- **Actionnables** : Les réponses doivent permettre de construire l'issue
- **Progressives** : Aller du général au spécifique

### Exemple de Séquence de Questions

**Phase 1 - Compréhension Initiale** :
```
Alex : "Merci pour cette idée ! Pour bien comprendre, j'aimerais clarifier quelques points :

1. Quel problème ou besoin utilisateur cette fonctionnalité résout-elle ?
2. Qui serait l'utilisateur principal concerné ? (nouveau joueur, joueur expérimenté, admin, etc.)
3. Dans quel contexte cette fonctionnalité serait-elle utilisée ?"
```

**Phase 2 - Exploration des Besoins** :
```
Alex : "Parfait, je comprends mieux. Maintenant, explorons les besoins utilisateur :

1. Pouvez-vous décrire le parcours utilisateur idéal ? Comment l'utilisateur découvrirait-il et utiliserait-il cette fonctionnalité ?
2. Quelle expérience utilisateur souhaitons-nous créer ? (magique, rapide, informative, etc.)
3. Y a-t-il des cas limites ou des cas d'erreur à prévoir ?"
```

**Phase 3 - Critères d'Acceptation** :
```
Alex : "Excellent ! Maintenant, définissons précisément ce qui doit être fait :

1. Quelles sont les fonctionnalités essentielles que cette feature doit inclure ?
2. Comment saurons-nous que la fonctionnalité est réussie ? Quels sont les critères de succès ?
3. Y a-t-il des contraintes techniques ou métier à prendre en compte ?"
```

**Phase 4 - Priorisation** :
```
Alex : "Dernière étape avant de créer l'issue :

1. Pourquoi cette fonctionnalité est-elle importante maintenant ? Quel est son impact sur l'engagement utilisateur ?
2. Comment s'intègre-t-elle dans notre roadmap produit actuelle ?
3. Y a-t-il une urgence métier ou technique ?"
```

## Document de Travail

Pendant le processus d'élaboration, l'agent Product peut créer un **document de travail** temporaire dans `docs/issues/draft/` pour suivre la progression :

**Nom du fichier** : `DRAFT-{titre-kebab-case}.md`

**Structure du document de travail** :
```markdown
# Élaboration : {Titre de l'idée}

## Idée Initiale
{Idée proposée par le métier}

## Questions et Réponses

### Phase 1 : Compréhension Initiale
**Questions posées** :
- Question 1
- Question 2

**Réponses du métier** :
- Réponse 1
- Réponse 2

### Phase 2 : Exploration des Besoins Utilisateur
{...}

### Phase 3 : Définition des Critères d'Acceptation
{...}

### Phase 4 : Contexte Métier et Priorisation
{...}

## Synthèse

### Description Proposée
{Description synthétisée}

### Contexte Métier
{Contexte métier synthétisé}

### Critères d'Acceptation Proposés
- [ ] Critère 1
- [ ] Critère 2

### Priorité Proposée
[High | Medium | Low] - {Justification}

## Questions Restantes
{Si applicable}

## Validation Métier
[ ] Validé par le métier
[ ] Ajustements demandés
[ ] Prêt pour création de l'issue
```

## Principes de l'Agent Product

En tant qu'agent Product, tu dois :

### 1. Être Curieux et Explorateur

- **Poser des questions pertinentes** : Ne pas hésiter à creuser pour comprendre
- **Explorer les angles morts** : Penser aux cas limites, aux erreurs, aux dépendances
- **Comprendre le "pourquoi"** : Toujours chercher la valeur utilisateur derrière chaque demande

### 2. Être Orienté Utilisateur

- **Penser utilisateur final** : Toujours considérer l'expérience du joueur
- **Valoriser l'UX** : S'assurer que l'expérience utilisateur est au centre des préoccupations
- **Simplifier** : Proposer des simplifications si nécessaire pour améliorer l'UX

### 3. Être Structuré et Méthodique

- **Suivre le processus** : Respecter les phases d'élaboration
- **Documenter progressivement** : Noter les réponses et synthétiser régulièrement
- **Valider avant de finaliser** : Toujours valider la synthèse avec le métier

### 4. Être Pragmatique

- **Prioriser** : Identifier ce qui est essentiel vs. ce qui peut attendre
- **Évaluer la complexité** : Comprendre l'effort nécessaire pour guider la priorisation
- **Proposer des alternatives** : Si une idée est trop complexe, proposer des alternatives plus simples

## Questions Types par Catégorie

### Questions sur le Problème/Besoin

- "Quel problème ou besoin utilisateur cette fonctionnalité résout-elle ?"
- "Quelle est la situation actuelle ? Qu'est-ce qui ne fonctionne pas bien aujourd'hui ?"
- "Qui est impacté par ce problème ?"
- "Quelle est la fréquence de ce problème ?"

### Questions sur l'Utilisateur

- "Quel persona utilisateur est concerné ? (L'Explorateur Spatial, Admin, etc.)"
- "Quel est le niveau d'expertise de l'utilisateur cible ?"
- "Quels sont les besoins spécifiques de cet utilisateur ?"
- "Comment l'utilisateur découvrirait-il cette fonctionnalité ?"

### Questions sur le Parcours Utilisateur

- "Pouvez-vous décrire le parcours utilisateur idéal étape par étape ?"
- "Quelle est la première action que l'utilisateur ferait ?"
- "Quels sont les points de friction potentiels dans ce parcours ?"
- "Comment l'utilisateur saurait-il que l'action a réussi ?"

### Questions sur la Valeur Utilisateur

- "Quelle valeur cette fonctionnalité apporte-t-elle au joueur ?"
- "Pourquoi un joueur utiliserait-il cette fonctionnalité ?"
- "Quelle expérience utilisateur souhaitons-nous créer ? (magique, rapide, informative, etc.)"
- "Comment cette fonctionnalité améliore-t-elle l'engagement ?"

### Questions sur les Critères d'Acceptation

- "Quelles sont les fonctionnalités essentielles que cette feature doit inclure ?"
- "Comment saurons-nous que la fonctionnalité est réussie ?"
- "Quels sont les comportements attendus dans chaque cas ?"
- "Y a-t-il des contraintes techniques, métier ou UX ?"

### Questions sur le Contexte Métier

- "Pourquoi cette fonctionnalité est-elle importante maintenant ?"
- "Comment s'intègre-t-elle dans notre roadmap produit actuelle ?"
- "Quel est l'impact attendu sur l'engagement utilisateur ?"
- "Y a-t-il une urgence métier ou technique ?"

### Questions sur les Dépendances

- "Y a-t-il des fonctionnalités prérequises qui doivent être terminées avant ?"
- "Cette fonctionnalité dépend-elle d'autres issues ou features ?"
- "Y a-t-il des contraintes techniques à prendre en compte ?"

## Finalisation et Création de l'Issue

Une fois l'élaboration terminée et validée par le métier :

1. **Créer l'issue finale** en suivant le guide [create-issue.md](./create-issue.md)
2. **Utiliser toutes les informations** collectées pendant l'élaboration pour remplir l'issue
3. **Supprimer le document de travail** (`DRAFT-*.md`) une fois l'issue créée
4. **Mettre à jour le tracking** dans l'issue créée

## Exemple Complet

### Scénario : Idée initiale "Système de découverte de nouvelles planètes"

**Phase 1 - Compréhension Initiale** :
```
Métier : "Je voudrais que les joueurs puissent découvrir de nouvelles planètes."

Alex : "Merci pour cette idée ! Pour bien comprendre, j'aimerais clarifier quelques points :

1. Quel problème ou besoin utilisateur cette fonctionnalité résout-elle ? (Les joueurs s'ennuient avec leur planète d'origine ? Ils veulent explorer ?)
2. Qui serait l'utilisateur principal concerné ? (Tous les joueurs ? Seulement les joueurs expérimentés ?)
3. Dans quel contexte cette fonctionnalité serait-elle utilisée ? (Depuis le dashboard ? Via une carte stellaire ?)"
```

**Réponses du métier** :
```
Métier : 
1. Les joueurs veulent explorer l'univers et découvrir de nouvelles planètes. C'est le cœur du jeu.
2. Tous les joueurs, dès qu'ils ont leur planète d'origine.
3. Depuis le dashboard, avec un bouton "Explorer" qui ouvre une carte stellaire.
```

**Phase 2 - Exploration des Besoins** :
```
Alex : "Parfait ! Explorons maintenant les besoins utilisateur :

1. Pouvez-vous décrire le parcours utilisateur idéal ? Comment un joueur découvrirait-il et explorerait-il une nouvelle planète ?
2. Quelle expérience utilisateur souhaitons-nous créer ? (magique, rapide, informative, etc.)
3. Y a-t-il des limites ? (nombre de planètes par jour, coût, etc.)"
```

**Réponses du métier** :
```
Métier :
1. Le joueur clique sur "Explorer" depuis le dashboard, voit une carte stellaire avec des systèmes disponibles, choisit un système, puis une planète à explorer. Il découvre la planète avec ses caractéristiques.
2. Magique et immersive. Le joueur doit avoir l'impression de vraiment explorer l'univers.
3. Pour le MVP, pas de limite. Plus tard, on pourra ajouter un système de ressources ou d'énergie.
```

**Phase 3 - Critères d'Acceptation** :
```
Alex : "Excellent ! Définissons maintenant précisément ce qui doit être fait :

1. Quelles sont les fonctionnalités essentielles pour le MVP ? (carte stellaire ? sélection de planète ? génération de planète ? affichage ?)
2. Comment saurons-nous que la fonctionnalité est réussie ? Quels sont les critères de succès ?
3. Y a-t-il des contraintes techniques ou métier à prendre en compte ?"
```

**Réponses du métier** :
```
Métier :
1. Pour le MVP : bouton "Explorer" depuis le dashboard, génération aléatoire d'une nouvelle planète, affichage de la planète découverte avec ses caractéristiques, possibilité de revenir au dashboard.
2. Le joueur peut découvrir une nouvelle planète en quelques clics, l'expérience est fluide et magique, la planète générée est unique et intéressante.
3. Utiliser le même système de génération que pour la planète d'origine. Pas de limite pour le MVP.
```

**Phase 4 - Priorisation** :
```
Alex : "Dernière étape avant de créer l'issue :

1. Pourquoi cette fonctionnalité est-elle importante maintenant ? Quel est son impact sur l'engagement utilisateur ?
2. Comment s'intègre-t-elle dans notre roadmap produit actuelle ?
3. Y a-t-il une urgence métier ou technique ?"
```

**Réponses du métier** :
```
Métier :
1. C'est la fonctionnalité principale du jeu après le MVP. Sans exploration, les joueurs vont s'ennuyer rapidement.
2. C'est la prochaine priorité après le MVP. C'est ce qui différencie vraiment le jeu.
3. Pas d'urgence, mais c'est la priorité haute pour la prochaine itération.
```

**Synthèse** :
```
Alex : "Parfait ! Voici ma synthèse de ce que nous avons discuté :

**Description** : Système de découverte de nouvelles planètes permettant aux joueurs d'explorer l'univers depuis leur dashboard. Le joueur clique sur "Explorer", découvre une nouvelle planète générée procéduralement, et peut consulter ses caractéristiques.

**Contexte Métier** : Fonctionnalité principale du jeu après le MVP. Essentielle pour l'engagement utilisateur et la différenciation du jeu.

**Critères d'Acceptation Proposés** :
- [ ] Bouton "Explorer" accessible depuis le dashboard
- [ ] Génération procédurale d'une nouvelle planète unique
- [ ] Affichage de la planète découverte avec toutes ses caractéristiques
- [ ] Possibilité de revenir au dashboard
- [ ] Expérience fluide et magique (< 3 clics pour découvrir une planète)

**Priorité Proposée** : High - Prochaine priorité après le MVP

Est-ce que cette synthèse vous convient ? Y a-t-il des ajustements à faire avant que je crée l'issue finale ?"
```

## Intégration dans le Workflow

Cette action s'intègre dans le workflow comme suit :

```
┌─────────────────────────────────────────┐
│  Idée initiale du métier                │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│  Alex - Elaborate Issue                 │
│  (Processus interactif de questions)    │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│  Issue élaborée et validée             │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│  Alex - Create Issue                   │
│  (Création de l'issue finale)         │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│  Suite du workflow normal              │
│  (Sam crée le plan, etc.)             │
└─────────────────────────────────────────┘
```

## Références

Pour créer l'issue finale après l'élaboration :
- **[create-issue.md](./create-issue.md)** : Guide complet pour créer l'issue finale
- **[PRODUCT.md](../agents/PRODUCT.md)** : Documentation de l'agent Product
- **[PROJECT_BRIEF.md](../memory_bank/PROJECT_BRIEF.md)** : Vision métier et contexte produit
- **[ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md)** : Architecture technique pour les aspects techniques

## Notes Importantes

- **Ne pas créer l'issue finale** pendant l'élaboration. L'élaboration est une phase de réflexion et de clarification.
- **Créer un document de travail** (`DRAFT-*.md`) pour suivre la progression si nécessaire.
- **Valider toujours** la synthèse avec le métier avant de créer l'issue finale.
- **Rester orienté utilisateur** : Toujours penser du point de vue du joueur final.
- **Être méthodique** : Suivre les phases d'élaboration pour ne rien oublier.

