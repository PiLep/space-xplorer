# Onboarding Joueur – Stellar
Méthodologie d’accueil du nouveau joueur dans l’univers de Stellar.
Durée cible : 3 à 5 minutes.

---

## 1. Objectifs de l’onboarding
- Immerger immédiatement le joueur dans l’univers.
- Introduire les mécaniques sans surcharge.
- Donner une première action interactive.
- Générer de l’attachement via une découverte unique.
- Introduire subtilement le fil rouge (Le Parallaxe).
- Laisser une raison claire de revenir le lendemain.

---

## 2. Structure générale de l’onboarding
L’onboarding se déroule en 6 étapes courtes et scénarisées :

1. Réveil du joueur  
2. Scan initial et mini-jeu d’introduction  
3. Premier micro-choix narratif  
4. Nomination de la planète d’origine  
5. Introduction au Codex Stellaire  
6. Projection vers la prochaine session

---

## 3. Étape par étape

### 3.1 Réveil du joueur (30–45 secondes)
**Objectif :** Introduire le ton du jeu et créer une immersion immédiate.

**Éléments proposés :**
- Écran noir avec « Booting… ».
- Interface instable ou glitchée.
- Texte court de contextualisation.

**Exemple de texte :**
> Vous émergez d’hibernation.  
> Le système de survie vous a réveillé 34 minutes plus tôt que prévu.  
> Une anomalie a été détectée à proximité.

Bouton : `Continuer`

---

### 3.2 Scan initial et mini-jeu (10–20 secondes)
**Objectif :** Première action interactive simple.

- Génération instantanée de la planète d’origine du joueur.
- Affichage partiel des informations.
- Mini-jeu introductif très court (scan rapide).

**Résultat :**
- Informations supplémentaires sur la planète.
- Premiers éléments narratifs atmosphériques.

---

### 3.3 Premier choix narratif (20–30 secondes)
**Objectif :** Introduire la mécanique de choix et leur impact.

**Exemple :**
> Un signal faible est détecté.  
> Sa source est inconnue.

Choix :
- Analyser  
- Ignorer  
- Amplifier  

**Conséquences :**
- Ressource mineure  
- Ligne de lore  
- Variation du ton de certains messages futurs

---

### 3.4 Nomination de la planète d’origine (30–60 secondes)
**Objectif :** Renforcer l’attachement émotionnel.

- Le joueur peut donner un nom à sa planète.
- Suggestion automatique de noms si nécessaire.
- Validation simple et rapide.

**Contrainte :**
- Vérification de sécurité (filtrage et longueur).

---

### 3.5 Introduction au Codex Stellaire (10–20 secondes)
**Objectif :** Présenter le système de lore et de contributions futures.

Le joueur voit :
- L’entrée de sa planète dans le Codex.
- Un court texte généré automatiquement.
- Une mention indiquant qu’il pourra contribuer plus tard.

Bouton : `Accéder au Codex` (facultatif)

---

### 3.6 Projection vers demain (10–15 secondes)
**Objectif :** Donner une raison claire de revenir.

**Exemple :**
> Un scan longue portée sera disponible dans 23 heures.  
> Revenez pour votre premier briefing d’exploration.

Bouton final : `Terminer l’activation`

---

## 4. Intégration du fil rouge : Le Parallaxe
**Objectif :** Introduire discrètement le mystère dès l’onboarding.

**Recommandations :**
- Insérer un léger glitch visuel ou audio.
- Ajouter une phrase courte et ambiguë lors du mini-jeu ou du choix :
  - « Anomalie non identifiée. »  
  - « Valeur attendue manquante. »  
  - « Interférence… source indéterminée. »

Ne jamais expliquer. Ne jamais répéter immédiatement.

---

## 5. Points clés de design
- Ne jamais bloquer le joueur.  
- Aucun texte long.  
- Aucune mécanique complexe introduite trop tôt.  
- Maximum une seule action à chaque étape.  
- Onboarding terminé en moins de 5 minutes.  
- Préparation de la première “session quotidienne” dès la fin.

---

## 6. Flags et états techniques

### `players` (exemples de colonnes)
- `onboarding_completed` (bool)
- `first_daily_event_due_at` (datetime)
- `first_expedition_available_at` (datetime)

### Logs ou métriques utiles
- Temps passé dans l’onboarding
- Abandon potentiel (pour ajuster le flow)
- Choix narratif effectué

---

## 7. Résultat attendu
À la fin de l’onboarding, le joueur doit :
- comprendre l’univers de manière intuitive  
- se sentir propriétaire de sa planète  
- avoir interagi avec un mini-jeu  
- avoir pris un choix narratif simple  
- connaître l’existence du Codex  
- savoir qu’un événement l’attend demain  
- avoir été exposé à un premier indice du Parallaxe

L’expérience doit être fluide, brève et mémorable.

