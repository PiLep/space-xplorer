# DRAFT - Features Originales pour le Mini-Jeu de Scanning

## Statut
**Draft** - Propositions créatives pour enrichir le mini-jeu de scanning

## Contexte

Le mini-jeu de scanning actuel est fonctionnel mais peut être enrichi avec des features originales qui ajoutent de la profondeur, de la variété et de l'engagement tout en restant dans les contraintes d'un mini-jeu quotidien court (30-60 secondes).

## Principes Directeurs

- **Simplicité** : Les features doivent rester faciles à comprendre
- **Variété** : Ajouter de la rejouabilité sans complexifier
- **Thème spatial** : S'intégrer naturellement dans l'univers Stellar
- **Progression** : Récompenser la maîtrise et la précision
- **Surprise** : Créer des moments mémorables occasionnels

---

## Features Proposées

### 1. 🌟 Signaux Multiples (Signal Clusters)

**Concept** : Parfois, plusieurs signaux apparaissent simultanément et doivent être verrouillés en séquence rapide.

**Mécanique** :
- 2-3 signaux apparaissent en même temps (rare, 15% des sessions)
- Le joueur doit les verrouiller dans un ordre optimal (le plus proche de la zone optimale en premier)
- Bonus de score si tous sont verrouillés dans la zone optimale
- Crée de la tension et de la décision rapide

**Impact** :
- Ajoute de la variété sans complexifier les règles de base
- Récompense la rapidité de décision
- Crée des moments de tension mémorables

**Implémentation** :
- Probabilité configurable dans la config du mini-jeu
- Score bonus : +5 points par signal supplémentaire verrouillé dans la zone optimale

---

### 2. 🎯 Signaux à Trajectoire (Moving Signals)

**Concept** : Certains signaux se déplacent lentement sur le radar au lieu d'être statiques.

**Mécanique** :
- 20-30% des signaux ont une trajectoire circulaire ou linéaire
- Le joueur doit anticiper où le signal sera dans la zone optimale
- La vitesse de déplacement varie (lent à modéré)
- Ajoute une couche de prédiction et de timing

**Impact** :
- Rend le jeu plus dynamique visuellement
- Récompense l'anticipation et la coordination œil-main
- Crée de la variété dans les patterns de jeu

**Implémentation** :
- Trajectoire calculée côté serveur (pour validation)
- Animation fluide côté client
- Score bonus si verrouillé exactement au centre de la zone optimale pendant le mouvement

---

### 3. 🔍 Signaux Fantômes (Ghost Signals)

**Concept** : Des signaux "fantômes" apparaissent brièvement mais ne peuvent pas être verrouillés - ils sont des leurres.

**Mécanique** :
- 10-15% des signaux sont des fantômes (visuellement différents : plus transparents, couleur différente)
- Cliquer sur un fantôme ne fait rien (pas de pénalité, juste une perte de temps)
- Les vrais signaux restent identifiables mais nécessitent plus d'attention
- Crée une mécanique de discrimination visuelle

**Impact** :
- Ajoute une couche de stratégie (identifier les vrais signaux)
- Récompense l'observation attentive
- Évite la répétition mécanique

**Implémentation** :
- Opacité réduite (60%) pour les fantômes
- Couleur légèrement différente (bleu au lieu de vert)
- Pas de zone optimale pour les fantômes (ils disparaissent simplement)

---

### 4. ⚡ Mode Rush (Rush Mode)

**Concept** : Si le joueur verrouille 3 signaux consécutifs dans la zone optimale, le jeu entre en "mode rush" temporaire.

**Mécanique** :
- Après 3 succès consécutifs dans la zone optimale, activation du mode rush (10-15 secondes)
- Pendant le mode rush :
  - Les signaux apparaissent plus rapidement
  - Bonus de score multiplié par 1.5x
  - Effet visuel spécial (radar qui pulse, couleurs plus vives)
- Le mode rush se termine après la durée ou si un signal est manqué

**Impact** :
- Récompense les performances exceptionnelles
- Crée des moments de flow et d'excitation
- Encourage la précision constante

**Implémentation** :
- Compteur de combo côté client et serveur
- Multiplicateur de score appliqué côté serveur lors de la validation
- Animation visuelle distinctive

---

### 5. 🎨 Signaux de Type Différent (Signal Types)

**Concept** : Différents types de signaux avec des propriétés et récompenses différentes.

**Mécaniques** :

#### Signaux Communs (70%)
- Signaux verts standard
- Zone optimale normale (25%-75%)
- Récompense de base

#### Signaux Rares (25%)
- Signaux bleus/cyan
- Zone optimale plus petite (35%-65%)
- Bonus de score : +10 points si verrouillé
- Récompense : +20% de données scientifiques

#### Signaux Exceptionnels (5%)
- Signaux dorés/orange
- Zone optimale très petite (45%-55%)
- Bonus de score : +20 points si verrouillé
- Récompense : +50% de données scientifiques + fragment garanti

**Impact** :
- Crée de la variété visuelle
- Récompense la précision avec des signaux plus difficiles
- Ajoute de la valeur aux signaux rares

**Implémentation** :
- Type déterminé côté serveur lors de la génération
- Couleur et taille de zone optimale différentes
- Score et récompenses ajustés selon le type

---

### 6. 🌊 Vagues d'Interférence (Interference Waves)

**Concept** : Parfois, des vagues d'interférence traversent le radar, rendant les signaux temporairement invisibles ou difficiles à voir.

**Mécanique** :
- 1-2 vagues d'interférence par session (rare, 20% des sessions)
- Les vagues traversent le radar de manière circulaire
- Pendant le passage d'une vague :
  - Les signaux deviennent très transparents (20% opacité)
  - Ou disparaissent complètement pendant 1-2 secondes
- Le joueur doit mémoriser où étaient les signaux ou attendre que la vague passe
- Crée un défi de mémoire et de timing

**Impact** :
- Ajoute de la variété et du défi occasionnel
- Teste la mémoire spatiale du joueur
- Crée des moments de tension uniques

**Implémentation** :
- Animation de vague circulaire (effet visuel)
- Réduction d'opacité des signaux pendant le passage
- Les signaux restent cliquables mais difficiles à voir

---

### 7. 🎯 Système de Précision Graduée (Precision Grading)

**Concept** : Le score de précision est calculé de manière plus granulaire selon où dans la zone optimale le signal est verrouillé.

**Mécanique** :
- Zone optimale divisée en 3 sous-zones :
  - **Zone Parfaite** (40%-60%) : Score de précision 100%
  - **Zone Bonne** (30%-40% ou 60%-70%) : Score de précision 75%
  - **Zone Acceptable** (25%-30% ou 70%-75%) : Score de précision 50%
- Affichage visuel des sous-zones (couleurs différentes, plus subtiles)
- Score final ajusté selon la précision moyenne

**Impact** :
- Récompense la précision maximale
- Encourage à viser le centre de la zone optimale
- Rend le système de scoring plus transparent et équitable

**Implémentation** :
- Calcul côté serveur lors de la validation
- Indicateur visuel subtil des sous-zones (optionnel, pour ne pas surcharger)

---

### 8. 🔄 Signaux Récurrents (Recurring Signals)

**Concept** : Certains signaux réapparaissent plusieurs fois pendant la session s'ils ne sont pas verrouillés.

**Mécanique** :
- 2-3 signaux par session sont "récurrents" (marqués visuellement)
- Si un signal récurrent n'est pas verrouillé, il réapparaît 1-2 fois plus tard
- Chaque réapparition réduit légèrement le score possible (pénalité de -5 points)
- Crée une deuxième chance mais avec un coût

**Impact** :
- Réduit la frustration des signaux manqués
- Ajoute une dimension stratégique (prioriser les signaux récurrents ou non ?)
- Crée de la variété dans les patterns

**Implémentation** :
- Flag `recurring` dans la génération des signaux
- Système de réapparition avec délai calculé
- Score ajusté selon le nombre de tentatives

---

### 9. 🎪 Mode Défi (Challenge Mode)

**Concept** : Après un score excellent (90+), le joueur peut choisir de tenter un "défi bonus" pour des récompenses supplémentaires.

**Mécanique** :
- Après un score de 90+, proposition d'un défi bonus (optionnel)
- Défi : Verrouiller 3 signaux consécutifs dans la zone parfaite (40%-60%)
- Durée : 15 secondes
- Récompense si réussi :
  - Bonus de données scientifiques (+50%)
  - Fragment supplémentaire garanti
  - Petite chance d'artefact rare (5%)
- Si échoué : Pas de pénalité, juste pas de bonus

**Impact** :
- Récompense les performances exceptionnelles
- Ajoute un objectif optionnel pour les joueurs experts
- Crée un moment de célébration et de défi

**Implémentation** :
- Proposition après calcul du score final
- Mini-session séparée avec règles simplifiées
- Récompenses ajoutées aux récompenses de base

---

### 10. 🌌 Signaux Parallaxe (Parallaxe Signals)

**Concept** : Des signaux impossibles à verrouiller liés au Parallaxe apparaissent rarement.

**Mécanique** :
- Très rare (0.5-1% des sessions)
- Signal visuellement distinctif (couleur violette, forme différente, pulsation étrange)
- Apparaît normalement mais ne peut pas être verrouillé (clics n'ont aucun effet)
- Après 3 tentatives infructueuses, le signal disparaît avec un effet visuel spécial
- Récompense : Fragment de lore cryptique sur le Parallaxe (même si non verrouillé)
- Message narratif : "Signal inconnu détecté... Classification impossible."

**Impact** :
- Intègre le Parallaxe de manière mystérieuse
- Crée des moments mémorables et intrigants
- Ajoute de la valeur narrative

**Implémentation** :
- Flag spécial `parallaxe` dans la génération
- Validation côté serveur qui détecte les tentatives
- Récompense narrative spéciale (fragment de lore)

---

### 11. 📊 Tableau de Bord en Temps Réel (Real-time Dashboard)

**Concept** : Affichage en temps réel de statistiques et métriques pendant le jeu.

**Mécanique** :
- Affichage discret de :
  - Précision actuelle (%)
  - Combo actuel (nombre de succès consécutifs)
  - Temps restant
  - Score préliminaire
- Mise à jour en temps réel
- Design minimaliste pour ne pas distraire

**Impact** :
- Donne un feedback immédiat sur la performance
- Encourage à améliorer la précision
- Rend le système de scoring plus transparent

**Implémentation** :
- Calculs côté client pour l'affichage
- Validation finale côté serveur

---

### 12. 🎵 Rythme et Patterns (Rhythm Patterns)

**Concept** : Les signaux apparaissent selon des patterns rythmiques reconnaissables.

**Mécanique** :
- Certaines sessions ont des patterns rythmiques (30% des sessions)
- Les signaux apparaissent selon un rythme régulier (ex: toutes les 2 secondes)
- Si le joueur suit le rythme (verrouille dans le tempo), bonus de score
- Patterns variés : régulier, accélérant, ralentissant, syncopé
- Crée une dimension musicale/rythmique

**Impact** :
- Ajoute une dimension sensorielle différente
- Récompense le sens du rythme
- Crée de la variété dans l'expérience

**Implémentation** :
- Pattern déterminé côté serveur
- Timing calculé selon le pattern
- Bonus appliqué si le joueur suit le rythme

---

## Priorisation des Features

### Phase 1 : Améliorations Essentielles (Impact Immédiat)
1. **Signaux de Type Différent** - Variété visuelle et récompenses différenciées
2. **Système de Précision Graduée** - Transparence et équité du scoring
3. **Tableau de Bord en Temps Réel** - Feedback immédiat

### Phase 2 : Features d'Engagement (Rejouabilité)
4. **Mode Rush** - Récompense les performances exceptionnelles
5. **Signaux à Trajectoire** - Dynamisme visuel
6. **Signaux Multiples** - Moments de tension

### Phase 3 : Features Narratives (Profondeur)
7. **Signaux Parallaxe** - Intégration narrative
8. **Signaux Fantômes** - Stratégie et discrimination
9. **Vagues d'Interférence** - Défi de mémoire

### Phase 4 : Features Avancées (Complexité)
10. **Signaux Récurrents** - Deuxième chance stratégique
11. **Mode Défi** - Défi optionnel pour experts
12. **Rythme et Patterns** - Dimension sensorielle

---

## Combinaisons de Features

Certaines features peuvent être combinées pour créer des expériences uniques :

- **Signaux Rares + Mode Rush** : Bonus multipliés pour des moments exceptionnels
- **Signaux à Trajectoire + Précision Graduée** : Défi de précision sur cibles mobiles
- **Signaux Multiples + Mode Rush** : Moments de tension maximale
- **Vagues d'Interférence + Signaux Fantômes** : Test de discrimination visuelle et mémoire

---

## Métriques à Surveiller

Après l'implémentation de chaque feature :

1. **Engagement** :
   - Temps moyen passé sur le mini-jeu
   - Taux de complétion
   - Nombre de tentatives (si plusieurs essais permis)

2. **Performance** :
   - Score moyen avec/sans la feature
   - Distribution des scores
   - Taux de réussite des features spéciales (mode rush, défi)

3. **Satisfaction** :
   - Feedback des joueurs sur les features
   - Features les plus appréciées
   - Features à ajuster ou retirer

---

## Notes d'Implémentation

### Configuration JSON Étendue

```json
{
  "scanning": {
    "signal_count": 8,
    "signal_duration_min": 4000,
    "signal_duration_max": 8000,
    "total_duration": 60000,
    
    "features": {
      "signal_types": {
        "enabled": true,
        "common_percentage": 70,
        "rare_percentage": 25,
        "exceptional_percentage": 5
      },
      "moving_signals": {
        "enabled": true,
        "percentage": 25,
        "speed_min": 0.5,
        "speed_max": 2.0
      },
      "ghost_signals": {
        "enabled": true,
        "percentage": 12
      },
      "rush_mode": {
        "enabled": true,
        "combo_required": 3,
        "duration_seconds": 12,
        "score_multiplier": 1.5
      },
      "precision_grading": {
        "enabled": true,
        "zones": {
          "perfect": {"min": 40, "max": 60, "score": 100},
          "good": {"min": 30, "max": 40, "score": 75},
          "acceptable": {"min": 25, "max": 30, "score": 50}
        }
      },
      "parallaxe_signals": {
        "enabled": true,
        "probability": 0.008
      }
    },
    
    "score_thresholds": {
      "failure": {"min": 0, "max": 25, "reward": {"type": "scientific_data", "amount": 0}},
      "minimal": {"min": 25, "max": 60, "reward": {"type": "scientific_data", "amount": 50}},
      "good": {"min": 60, "max": 85, "reward": {"type": "scientific_data", "amount": 100}},
      "excellent": {"min": 85, "max": 100, "reward": {"type": "scientific_data", "amount": 150}}
    }
  }
}
```

### Validation Serveur

Toutes les features doivent être validées côté serveur :
- Types de signaux vérifiés lors de la validation
- Trajectoires calculées et vérifiées
- Combos et modes spéciaux validés
- Score ajusté selon les features activées

---

## Conclusion

Ces features originales enrichissent le mini-jeu de scanning tout en respectant les contraintes d'un mini-jeu quotidien court et simple. Elles ajoutent de la variété, de la profondeur et de l'engagement sans complexifier excessivement l'expérience.

**Prochaines étapes recommandées** :
1. Valider les features avec Alex (Product Manager) et Casey (Game Designer)
2. Prioriser les features selon les objectifs produit
3. Créer des issues/tasks pour l'implémentation
4. Tester les features individuellement avant de les combiner

---

## Références

- [GAMEPLAY-REVIEW-scanning-minigame.md](../reviews/GAMEPLAY-REVIEW-scanning-minigame.md) - Review actuelle du mini-jeu
- [mini-games-system.md](../mini-games-system.md) - Système de mini-jeux

