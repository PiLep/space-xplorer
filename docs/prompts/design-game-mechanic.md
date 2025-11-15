# Action: Design Game Mechanic

## Description

Cette action permet à l'agent Game Designer (Casey) de concevoir de nouvelles mécaniques de jeu. La conception inclut la définition des règles, l'équilibrage, les interactions, et la documentation pour guider l'implémentation.

## Quand Utiliser Cette Action

L'agent Game Designer doit utiliser cette action quand :
- Une nouvelle fonctionnalité nécessite des mécaniques de jeu
- Une mécanique existante doit être améliorée ou étendue
- Un nouveau système de jeu doit être conçu
- Des règles de gameplay doivent être définies avant la création d'une issue
- L'équilibrage d'une mécanique doit être ajusté

## Processus de Conception

### Étape 1 : Comprendre le Besoin

1. **Lire le contexte** :
   - Si une issue existe déjà : Lire l'issue produit créée par Alex
   - Si aucune issue n'existe : Collaborer avec Alex pour comprendre le besoin
   - Comprendre les objectifs utilisateurs et produit

2. **Analyser l'existant** :
   - Examiner les mécaniques de jeu existantes
   - Comprendre comment la nouvelle mécanique s'intègre
   - Identifier les dépendances avec d'autres systèmes

### Étape 2 : Concevoir la Mécanique

1. **Définir les règles** :
   - Règles de base de la mécanique
   - Conditions d'activation
   - Interactions possibles
   - Cas limites et exceptions

2. **Définir l'équilibrage** :
   - Probabilités et distributions
   - Coûts et récompenses
   - Temps et ressources nécessaires
   - Progression et scaling

3. **Définir les interactions** :
   - Comment la mécanique interagit avec les autres systèmes
   - Dépendances avec d'autres mécaniques
   - Impact sur l'expérience utilisateur

### Étape 3 : Documenter la Mécanique

1. **Créer la documentation** :
   - Description complète de la mécanique
   - Règles détaillées
   - Équilibrage et probabilités
   - Exemples et cas d'usage
   - Diagrammes de flux si nécessaire

2. **Organiser la documentation** :
   - Stocker dans `docs/game-design/` (à créer si nécessaire)
   - Nommer avec un format clair : `MECHANIC-{nom}-{description}.md`
   - Référencer dans l'issue ou le plan associé

### Étape 4 : Valider avec Alex

1. **Présenter la mécanique** :
   - Expliquer les règles et l'équilibrage
   - Justifier les choix de design
   - Montrer comment cela répond aux besoins utilisateurs

2. **Ajuster si nécessaire** :
   - Prendre en compte les retours d'Alex
   - Ajuster l'équilibrage ou les règles
   - Réitérer jusqu'à validation

### Étape 5 : Documenter pour l'Implémentation

1. **Créer un guide d'implémentation** :
   - Spécifications techniques claires
   - Exemples de code ou pseudocode si nécessaire
   - Points d'attention pour l'implémentation
   - Tests à prévoir

2. **Référencer dans le plan** :
   - Si un plan existe déjà : Ajouter une référence à la mécanique
   - Si aucun plan n'existe : La mécanique guidera la création du plan

## Format de Documentation

### Structure Standard

```markdown
# MECHANIC-{nom} : {Description}

## Issue Associée

[Lien vers l'issue produit si elle existe]

## Vue d'Ensemble

{Description générale de la mécanique, son objectif, et son rôle dans le jeu}

## Règles de Base

### Activation

{Comment la mécanique est activée ou déclenchée}

### Fonctionnement

{Comment la mécanique fonctionne étape par étape}

### Conditions

{Conditions nécessaires pour que la mécanique fonctionne}

### Interactions

{Comment la mécanique interagit avec les autres systèmes}

## Équilibrage

### Probabilités

{Probabilités et distributions si applicable}

### Coûts et Récompenses

{Coûts nécessaires et récompenses obtenues}

### Progression

{Comment la mécanique évolue avec la progression du joueur}

### Scaling

{Comment la mécanique scale avec le niveau du joueur}

## Exemples et Cas d'Usage

### Exemple 1 : {Titre}

{Description de l'exemple avec contexte}

### Exemple 2 : {Titre}

{Description de l'exemple avec contexte}

## Cas Limites

{Les cas limites et exceptions à gérer}

## Métriques à Surveiller

{Les métriques de gameplay à surveiller pour cette mécanique}

## Implémentation Technique

### Spécifications

{Spécifications techniques pour l'implémentation}

### Points d'Attention

{Points importants à considérer lors de l'implémentation}

### Tests à Prévoir

{Tests à écrire pour valider la mécanique}

## Historique

{Historique des modifications de la mécanique}
```

## Exemple Concret

### MECHANIC-EXPLORATION : Système d'Exploration de Planètes

**Vue d'Ensemble** : Permet aux joueurs de découvrir de nouvelles planètes en scannant des systèmes stellaires.

**Règles** :
- Le joueur peut scanner un système stellaire pour découvrir des planètes
- Chaque scan coûte X ressources ou prend Y temps
- Le scan révèle 1-3 planètes aléatoirement
- Les planètes découvertes sont ajoutées à la collection du joueur

**Équilibrage** :
- Coût du scan : 100 ressources ou 5 minutes de temps réel
- Probabilité de découvrir 1 planète : 60%
- Probabilité de découvrir 2 planètes : 30%
- Probabilité de découvrir 3 planètes : 10%

## Validation

### Critères de Validation

- ✅ Les règles sont claires et complètes
- ✅ L'équilibrage est cohérent avec les autres mécaniques
- ✅ La mécanique répond aux besoins utilisateurs identifiés par Alex
- ✅ La mécanique s'intègre bien avec l'existant
- ✅ La documentation est complète et actionnable
- ✅ Les métriques à surveiller sont identifiées

### Processus de Validation

1. **Auto-validation** : Casey vérifie que tous les critères sont remplis
2. **Validation avec Alex** : Alex valide que la mécanique répond aux besoins produit
3. **Validation technique** : Sam valide que la mécanique peut être implémentée efficacement
4. **Documentation finale** : La mécanique est documentée et référencée

## Tracking

- **Création** : Documenter la création de la mécanique dans l'historique
- **Modifications** : Documenter toutes les modifications dans l'historique
- **Validation** : Documenter les validations avec dates et agents
- **Références** : Mettre à jour les issues et plans qui référencent la mécanique

## Références

- **[GAME-DESIGNER.md](../agents/GAME-DESIGNER.md)** : Documentation complète de l'agent Game Designer
- **[create-issue.md](./create-issue.md)** : Guide pour créer des issues (utilisé avec Alex)
- **[balance-gameplay.md](./balance-gameplay.md)** : Guide pour équilibrer le gameplay après implémentation

---

**Rappel** : En tant qu'agent Game Designer, tu conçois des mécaniques engageantes, équilibrées et amusantes. Tu documentes chaque mécanique de manière complète pour guider l'équipe de développement et assurer une implémentation fidèle au design.

