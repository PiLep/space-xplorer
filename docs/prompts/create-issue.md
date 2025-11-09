# Action: Create Issue

## Description

Cette action permet à l'agent Product de créer des issues (tickets) dans le dossier `docs/issues` pour donner des indications claires à l'équipe de développement.

## Quand Utiliser Cette Action

L'agent Product doit créer une issue quand :
- Une nouvelle fonctionnalité doit être développée
- Un bug ou problème est identifié
- Une amélioration est nécessaire
- Une tâche technique nécessite des précisions produit
- Une décision produit doit être documentée pour le développement

## Format de l'Issue

Chaque issue doit être créée dans `docs/issues/` avec le format suivant :

**Nom du fichier** : `ISSUE-{numero}-{titre-kebab-case}.md`

Exemple : `ISSUE-001-implement-user-registration.md`

## Structure de l'Issue

```markdown
# ISSUE-{numero} : {Titre de l'issue}

## Type
[Feature | Bug | Improvement | Technical | Documentation]

## Priorité
[High | Medium | Low]

## Description

{Description détaillée de l'issue, du problème ou de la fonctionnalité}

## Contexte Métier

{Pourquoi cette issue est importante du point de vue produit}

## Critères d'Acceptation

- [ ] Critère 1
- [ ] Critère 2
- [ ] Critère 3

## Détails Techniques

{Indications techniques si nécessaire, liens vers l'architecture, etc.}

## Notes

{Notes additionnelles, dépendances, etc.}

## Références

- [Lien vers documentation pertinente]
- [Lien vers architecture]

## Suivi et Historique

### Statut

[À faire | En cours | En review | Approuvé | Terminé]

### Historique

#### [Date] - [Agent] - [Action]
**Statut** : [Nouveau statut]
**Détails** : [Description de ce qui a été fait]
**Fichiers modifiés** : [Si applicable]
**Notes** : [Notes additionnelles]
```

## Exemple d'Issue

```markdown
# ISSUE-001 : Implémenter l'inscription utilisateur avec génération de planète

## Type
Feature

## Priorité
High

## Description

Implémenter le système d'inscription utilisateur qui génère automatiquement une planète d'origine pour chaque nouveau joueur.

## Contexte Métier

Cette fonctionnalité est au cœur du MVP. C'est la première interaction du joueur avec le jeu et elle doit créer une expérience mémorable. La génération de la planète doit être instantanée et la découverte doit être un moment magique.

## Critères d'Acceptation

- [ ] Formulaire d'inscription avec validation (nom, email, mot de passe)
- [ ] Création du compte utilisateur en base de données
- [ ] Génération automatique d'une planète d'origine via l'événement `UserRegistered`
- [ ] Attribution de la planète au joueur (`home_planet_id`)
- [ ] Retour d'un token Sanctum pour l'authentification
- [ ] Redirection vers le tableau de bord après inscription

## Détails Techniques

- Utiliser Laravel Sanctum pour l'authentification
- Créer l'événement `UserRegistered` dans `app/Events/`
- Créer le listener `GenerateHomePlanet` dans `app/Listeners/`
- Utiliser le service `PlanetGeneratorService` pour la génération
- Voir ARCHITECTURE.md pour les détails de l'API endpoint

## Notes

- L'inscription doit être simple et rapide (moins de 30 secondes)
- La génération de planète doit être transparente pour l'utilisateur
- Gérer les erreurs de manière élégante

## Références

- [ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md) - Flux d'inscription
- [PROJECT_BRIEF.md](../memory_bank/PROJECT_BRIEF.md) - Parcours utilisateur
```

## Instructions pour l'Agent Product

Quand tu crées une issue :

1. **Numérotation** : Utilise un numéro séquentiel (001, 002, 003...)
2. **Titre clair** : Le titre doit être descriptif et actionnable
3. **Description complète** : Donne suffisamment de contexte pour que l'équipe comprenne
4. **Critères d'acceptation** : Sois précis sur ce qui doit être fait
5. **Priorisation** : Indique clairement la priorité selon la valeur utilisateur
6. **Références** : Lie vers la documentation pertinente (ARCHITECTURE.md, PROJECT_BRIEF.md, etc.)
7. **Ajouter le suivi** : Créer la section "Suivi et Historique" avec statut "À faire"

### Mise à Jour des Documents

Lors de la création de l'issue :
- **Dans l'issue (ISSUE-XXX)** : Ajouter une section "Suivi et Historique" avec statut "À faire" et une première entrée

L'issue sera mise à jour tout au long du workflow par les différents agents. Voir [update-tracking.md](./update-tracking.md) pour le format exact.

## Organisation

Les issues sont organisées dans `docs/issues/` et peuvent être :
- Traitées par l'équipe de développement
- Référencées dans les PRs et commits
- Utilisées pour suivre la progression du projet

