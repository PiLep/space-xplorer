# Action: Balance Gameplay

## Description

Cette action permet à l'agent Game Designer (Casey) d'analyser les métriques de gameplay, d'identifier les déséquilibres, et de proposer des ajustements d'équilibrage pour améliorer l'expérience de jeu.

## Quand Utiliser Cette Action

L'agent Game Designer doit utiliser cette action quand :
- Des métriques de gameplay sont disponibles après le déploiement
- Des déséquilibres sont identifiés par les joueurs ou les métriques
- Une mécanique ne fonctionne pas comme prévu
- Les métriques montrent des problèmes d'engagement ou de progression
- Un ajustement d'équilibrage est nécessaire

## Processus d'Équilibrage

### Étape 1 : Collecter et Analyser les Métriques

1. **Collecter les métriques** :
   - Collaborer avec Taylor (Workflow Manager) pour obtenir les métriques
   - Utiliser les outils d'analytics disponibles (Laravel Telescope, logs, etc.)
   - Collecter le feedback des joueurs
   - Analyser les données de jeu disponibles

2. **Analyser les métriques** :
   - Comparer les métriques avec les objectifs de design
   - Identifier les tendances et anomalies
   - Analyser la distribution des joueurs
   - Examiner les métriques d'engagement et de progression

### Étape 2 : Identifier les Problèmes

1. **Identifier les déséquilibres** :
   - Progression trop rapide ou trop lente
   - Récompenses déséquilibrées
   - Probabilités qui ne fonctionnent pas comme prévu
   - Mécaniques sous-utilisées ou sur-utilisées

2. **Identifier les problèmes d'engagement** :
   - Taux de rétention faible
   - Temps de session trop court
   - Abandon précoce
   - Manque d'engagement avec certaines mécaniques

3. **Documenter les problèmes** :
   - Créer un rapport d'analyse avec les métriques
   - Identifier les causes probables
   - Prioriser les problèmes à résoudre

### Étape 3 : Proposer des Ajustements

1. **Concevoir les ajustements** :
   - Ajuster les probabilités si nécessaire
   - Modifier les coûts et récompenses
   - Ajuster la vitesse de progression
   - Rééquilibrer les mécaniques problématiques

2. **Justifier les ajustements** :
   - Expliquer pourquoi chaque ajustement est nécessaire
   - Montrer comment les ajustements résolvent les problèmes identifiés
   - Prévoir l'impact des ajustements sur les métriques

3. **Valider avec Alex** :
   - Présenter les ajustements proposés
   - Justifier les changements
   - Obtenir la validation avant implémentation

### Étape 4 : Documenter les Ajustements

1. **Créer la documentation** :
   - Documenter les problèmes identifiés
   - Documenter les ajustements proposés
   - Documenter les métriques avant et après ajustements
   - Créer un plan de suivi

2. **Organiser la documentation** :
   - Stocker dans `docs/game-design/balance/` (à créer si nécessaire)
   - Nommer avec un format clair : `BALANCE-{date}-{mecanique}.md`
   - Référencer dans les issues ou mécaniques concernées

### Étape 5 : Suivre les Résultats

1. **Surveiller les métriques** :
   - Après implémentation, surveiller les nouvelles métriques
   - Comparer avec les métriques précédentes
   - Vérifier que les ajustements ont l'effet escompté

2. **Ajuster si nécessaire** :
   - Si les ajustements ne résolvent pas les problèmes, proposer de nouveaux ajustements
   - Itérer jusqu'à ce que l'équilibrage soit satisfaisant

## Format de Documentation

### Structure Standard

```markdown
# BALANCE-{date} : {Mécanique ou Système}

## Contexte

{Contexte de l'analyse d'équilibrage, pourquoi cette analyse est nécessaire}

## Métriques Analysées

### Métriques d'Engagement

{Métriques d'engagement collectées et analysées}

### Métriques de Progression

{Métriques de progression collectées et analysées}

### Métriques de Découverte

{Métriques de découverte collectées et analysées}

### Métriques d'Équilibrage

{Métriques d'équilibrage collectées et analysées}

## Problèmes Identifiés

### Problème 1 : {Titre}

{Description du problème, métriques concernées, impact}

### Problème 2 : {Titre}

{Description du problème, métriques concernées, impact}

## Ajustements Proposés

### Ajustement 1 : {Titre}

{Description de l'ajustement, justification, impact attendu}

### Ajustement 2 : {Titre}

{Description de l'ajustement, justification, impact attendu}

## Validation

{Validation avec Alex et autres agents concernés}

## Implémentation

{Plan d'implémentation des ajustements}

## Suivi

### Métriques Avant Ajustements

{Métriques avant les ajustements}

### Métriques Après Ajustements

{Métriques après les ajustements (à remplir après implémentation)}

### Analyse des Résultats

{Analyse des résultats et conclusion}

## Historique

{Historique des modifications et ajustements}
```

## Exemple Concret

### BALANCE-2025-01-27 : Système de Génération de Planètes

**Contexte** : Analyse des métriques après 1 mois de déploiement pour vérifier l'équilibrage des types de planètes.

**Métriques Analysées** :
- Distribution des types de planètes générées
- Taux de satisfaction des joueurs par type de planète
- Engagement avec les planètes rares vs communes

**Problèmes Identifiés** :
- Les planètes océaniques (10%) sont trop rares, créant de la frustration
- Les planètes gazeuses (25%) sont sous-appréciées par les joueurs

**Ajustements Proposés** :
- Augmenter la probabilité des planètes océaniques de 10% à 15%
- Réduire la probabilité des planètes gazeuses de 25% à 20%
- Ajuster les autres probabilités en conséquence

## Métriques Clés à Surveiller

### Métriques d'Engagement

- Temps de session moyen
- Fréquence de connexion
- Taux de rétention (1j, 7j, 30j)
- Taux d'abandon

### Métriques de Progression

- Vitesse de progression
- Distribution des niveaux
- Taux de complétion
- Temps pour première découverte

### Métriques de Découverte

- Nombre de planètes découvertes par joueur
- Distribution des types de planètes
- Taux de découverte de planètes rares
- Engagement avec les découvertes

### Métriques d'Équilibrage

- Distribution des ressources
- Temps pour collecter des ressources
- Satisfaction des récompenses
- Déséquilibres identifiés

## Outils pour l'Analyse

### Analytics

- **Laravel Telescope** : Pour analyser les requêtes et performances
- **Logs** : Pour analyser les événements et comportements
- **Base de données** : Pour analyser les données de jeu directement

### Feedback Utilisateur

- **Surveys** : Questionnaires pour collecter le feedback
- **Support** : Analyser les tickets de support pour identifier les problèmes
- **Communauté** : Analyser les discussions de la communauté

### Tests

- **Tests A/B** : Si possible, tester différents équilibrages
- **Simulations** : Simuler les mécaniques pour prédire les métriques

## Validation

### Critères de Validation

- ✅ Les métriques sont collectées et analysées
- ✅ Les problèmes sont clairement identifiés
- ✅ Les ajustements sont justifiés et mesurables
- ✅ Les ajustements sont validés par Alex
- ✅ La documentation est complète
- ✅ Un plan de suivi est en place

### Processus de Validation

1. **Auto-validation** : Casey vérifie que tous les critères sont remplis
2. **Validation avec Alex** : Alex valide que les ajustements répondent aux besoins produit
3. **Validation technique** : Sam valide que les ajustements peuvent être implémentés
4. **Documentation finale** : Les ajustements sont documentés et référencés

## Tracking

- **Analyse** : Documenter l'analyse dans l'historique
- **Ajustements** : Documenter les ajustements proposés avec dates et agents
- **Implémentation** : Documenter l'implémentation des ajustements
- **Résultats** : Documenter les résultats après implémentation
- **Références** : Mettre à jour les mécaniques concernées

## Références

- **[GAME-DESIGNER.md](../agents/GAME-DESIGNER.md)** : Documentation complète de l'agent Game Designer
- **[design-game-mechanic.md](./design-game-mechanic.md)** : Guide pour concevoir de nouvelles mécaniques
- **[monitor-workflow.md](./monitor-workflow.md)** : Guide pour surveiller le workflow (collaboration avec Taylor)

---

**Rappel** : En tant qu'agent Game Designer, tu analyses les métriques de gameplay pour identifier les déséquilibres et proposer des ajustements. Tu travailles en collaboration avec Taylor pour collecter les métriques et avec Alex pour valider les ajustements. L'équilibrage est un processus continu qui nécessite une surveillance régulière et des ajustements itératifs.

