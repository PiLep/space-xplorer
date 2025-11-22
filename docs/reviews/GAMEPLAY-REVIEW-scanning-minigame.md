# GAMEPLAY-REVIEW : Mini-Jeu de Scanning

## Contexte

Review du mini-jeu de scanning (Scan Circulaire) du point de vue Game Design. Cette review analyse les mécaniques de jeu, l'équilibrage, l'expérience utilisateur, et propose des améliorations concrètes.

## Statut

⚠️ **Fonctionnel mais nécessite des améliorations pour une meilleure expérience de jeu**

## Vue d'Ensemble

Le mini-jeu de scanning est fonctionnellement implémenté et répond aux besoins de base. Cependant, plusieurs aspects peuvent être améliorés pour créer une expérience de jeu plus engageante, équilibrée et amusante selon les objectifs définis dans la documentation de game design.

## Analyse des Mécaniques de Jeu

### ✅ Mécaniques Bien Implémentées

1. **Système de signaux éphémères** : Les signaux apparaissent et disparaissent correctement
2. **Zone optimale** : Le système de zone optimale (25%-75% de la durée) est bien implémenté
3. **Validation serveur** : La validation côté serveur empêche la triche
4. **Score progressif** : Le score préliminaire est calculé en temps réel

### ⚠️ Mécaniques à Améliorer

#### 1. **Feedback Visuel Insuffisant**

**Problème** :
- Les signaux sont très petits (4x4px) et difficiles à voir
- Pas de feedback visuel clair quand un signal est cliqué (succès/échec)
- Pas d'indication visuelle de la zone optimale avant de cliquer
- Pas d'animation de "lock" satisfaisante quand un signal est verrouillé

**Impact** : L'expérience de jeu manque de satisfaction immédiate et de clarté

**Amélioration proposée** :
- Augmenter la taille des signaux (8-12px)
- Ajouter un effet visuel clair lors du clic (succès = vert + animation, échec = rouge + animation)
- Ajouter un indicateur visuel de la zone optimale (changement de couleur ou taille du signal)
- Animation plus satisfaisante lors du verrouillage (effet de "lock" avec son si possible)

#### 2. **Manque de Progression et Tension**

**Problème** :
- Tous les signaux sont générés au début, pas de progression dynamique
- Pas de système de difficulté progressive
- Pas de feedback sur la performance en temps réel
- Pas de système de "combo" ou de bonus pour des clics consécutifs réussis

**Impact** : Le jeu manque d'engagement et de sentiment de progression

**Amélioration proposée** :
- Générer les signaux progressivement pendant le jeu (pas tous au début)
- Ajouter un système de difficulté progressive (signaux plus rapides, zones optimales plus petites)
- Afficher un compteur de "combo" pour les clics réussis consécutifs
- Bonus de score pour les combos (ex: +5 points par combo de 3+)

#### 3. **Timing et Durée**

**Problème** :
- Durée fixe de 60 secondes peut être trop longue ou trop courte selon le nombre de signaux
- Pas de système de fin anticipée si tous les signaux sont verrouillés
- Les signaux apparaissent tous dans la première moitié du jeu (selon le code)

**Impact** : L'expérience peut être frustrante ou ennuyeuse selon les cas

**Amélioration proposée** :
- Adapter la durée selon le nombre de signaux (ex: 30-45 secondes pour 8 signaux)
- Permettre de terminer le jeu plus tôt si tous les signaux sont verrouillés (avec bonus de temps)
- Distribuer les signaux sur toute la durée du jeu pour maintenir l'engagement

#### 4. **Manque de Variété**

**Problème** :
- Tous les signaux sont identiques visuellement
- Pas de types de signaux différents (rares, communs, spéciaux)
- Pas de système de signaux "impossibles" liés au Parallaxe (mentionné dans la doc)

**Impact** : Le jeu devient répétitif rapidement

**Amélioration proposée** :
- Ajouter des types de signaux différents (visuellement et mécaniquement)
- Signaux rares qui donnent plus de points mais sont plus difficiles à verrouiller
- Signaux spéciaux liés au Parallaxe (impossibles à verrouiller, apparaissent rarement)
- Variété visuelle (couleurs, tailles, animations différentes)

## Analyse de l'Équilibrage

### Score et Récompenses

**Problème actuel** :
- Le score est basé uniquement sur le pourcentage de signaux verrouillés
- Pas de bonus pour la précision ou la vitesse
- Le bonus de précision dans le validator (jusqu'à 20 points) n'est pas visible pour le joueur

**Impact** : Les joueurs ne comprennent pas comment améliorer leur score

**Amélioration proposée** :
- Afficher clairement les différents facteurs de score :
  - Base : Signaux verrouillés (60%)
  - Précision : Clics réussis / total clics (20%)
  - Vitesse : Bonus pour terminer rapidement (20%)
- Afficher le score décomposé à la fin du jeu

### Difficulté

**Problème actuel** :
- Difficulté fixe, pas d'adaptation selon le niveau du joueur
- Pas de système de difficulté dynamique mentionné dans la doc mais pas implémenté

**Impact** : Le jeu peut être trop facile ou trop difficile selon le joueur

**Amélioration proposée** :
- Implémenter le système de difficulté dynamique mentionné dans la doc :
  - +5% difficulté par niveau joueur (max +50%)
  - -10% à -30% selon les modules installés
  - Modifications temporaires selon les événements (+/- 10%)
- Ajuster la durée des signaux, la taille de la zone optimale, et le nombre de signaux selon la difficulté

## Analyse de l'Expérience Utilisateur

### Points Positifs

1. **Interface claire** : Le radar est visuellement reconnaissable
2. **Instructions simples** : Les instructions sont claires et concises
3. **Feedback immédiat** : Le score est mis à jour en temps réel

### Points à Améliorer

#### 1. **Clarté des Instructions**

**Problème** : Les instructions mentionnent "zone optimale" mais ne l'expliquent pas visuellement

**Amélioration** :
- Ajouter une animation ou un exemple visuel de la zone optimale
- Expliquer clairement que la zone optimale est le milieu de la durée du signal

#### 2. **Feedback de Performance**

**Problème** : Pas de feedback sur la performance pendant le jeu

**Amélioration** :
- Afficher des messages de feedback ("Excellent!", "Trop tôt!", "Trop tard!")
- Afficher un indicateur de performance (ex: barre de précision)

#### 3. **Écran de Résultats**

**Problème** : L'écran de résultats est basique et ne célèbre pas la performance

**Amélioration** :
- Ajouter des animations de célébration pour les bons scores
- Afficher des statistiques détaillées (signaux verrouillés, précision, temps)
- Comparer avec les performances précédentes si disponibles

## Problèmes Techniques Identifiés

### 1. **Synchronisation Temps Client/Serveur**

**Problème** : Le code JavaScript utilise `Date.now()` alors que le serveur utilise `microtime(true) * 1000`, ce qui peut causer des désynchronisations

**Impact** : Les signaux peuvent apparaître/disparaître à des moments différents côté client et serveur

**Amélioration** : Utiliser le même système de timestamp ou synchroniser le temps client avec le serveur au début du jeu

### 2. **Performance JavaScript**

**Problème** : La fonction `updateSignals()` tourne toutes les 100ms et recrée des éléments DOM fréquemment

**Impact** : Peut causer des problèmes de performance sur des appareils moins puissants

**Amélioration** : Optimiser la mise à jour (utiliser requestAnimationFrame, éviter les recréations inutiles)

### 3. **Gestion des Erreurs**

**Problème** : Pas de gestion d'erreur si le clic échoue ou si le signal n'existe plus

**Impact** : Expérience frustrante si quelque chose ne fonctionne pas

**Amélioration** : Ajouter une gestion d'erreur robuste avec feedback utilisateur

## Recommandations Prioritaires

### Priorité Haute

1. **Améliorer le feedback visuel** (signaux plus grands, animations de succès/échec)
2. **Générer les signaux progressivement** (pas tous au début)
3. **Afficher clairement les facteurs de score** (base, précision, vitesse)
4. **Corriger la synchronisation temps client/serveur**

### Priorité Moyenne

5. **Ajouter un système de combo** (bonus pour clics consécutifs réussis)
6. **Implémenter la difficulté dynamique** (selon niveau joueur et modules)
7. **Améliorer l'écran de résultats** (statistiques détaillées, célébration)
8. **Ajouter des types de signaux différents** (rares, spéciaux)

### Priorité Basse

9. **Ajouter des signaux Parallaxe** (impossibles à verrouiller, très rares)
10. **Optimiser les performances JavaScript** (requestAnimationFrame, moins de recréations DOM)
11. **Ajouter des sons** (feedback audio pour les actions)

## Plan d'Amélioration Suggéré

### Phase 1 : Améliorations Essentielles (1-2 semaines)

- Améliorer le feedback visuel (taille, animations, couleurs)
- Générer les signaux progressivement
- Afficher les facteurs de score clairement
- Corriger la synchronisation temps

### Phase 2 : Améliorations d'Engagement (2-3 semaines)

- Système de combo
- Difficulté dynamique
- Écran de résultats amélioré
- Types de signaux différents

### Phase 3 : Améliorations Avancées (1-2 semaines)

- Signaux Parallaxe
- Optimisations performance
- Sons et feedback audio

## Métriques à Surveiller

Après les améliorations, surveiller :

1. **Taux de complétion** : Pourcentage de joueurs qui terminent le mini-jeu
2. **Score moyen** : Distribution des scores pour vérifier l'équilibrage
3. **Temps moyen de jeu** : Vérifier que la durée est appropriée
4. **Taux de précision** : Pourcentage de clics réussis vs total
5. **Engagement** : Nombre de tentatives par joueur (si plusieurs essais sont permis)

## Conclusion

Le mini-jeu de scanning est fonctionnellement correct mais nécessite des améliorations significatives pour offrir une expérience de jeu engageante et satisfaisante. Les améliorations prioritaires concernent principalement le feedback visuel, la progression dynamique, et la clarté du système de scoring.

**Prochaines étapes recommandées** :
1. Créer une issue pour les améliorations prioritaires (Phase 1)
2. Collaborer avec Riley (Designer) pour les améliorations visuelles
3. Collaborer avec Jordan (Fullstack) pour l'implémentation technique
4. Tester les améliorations avec des utilisateurs réels

## Références

- [mini-games-system.md](../game-design/mini-games-system.md) - Spécifications du système de mini-jeux
- [GAME-DESIGNER.md](../agents/GAME-DESIGNER.md) - Rôle et responsabilités du Game Designer
- [ISSUE-007-implement-minigame-base-system.md](../issues/ISSUE-007-implement-minigame-base-system.md) - Issue originale

