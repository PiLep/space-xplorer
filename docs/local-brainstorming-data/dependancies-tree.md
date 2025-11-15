# Stellar – Dependency Roadmap (Visual Tree)

Ce document présente un arbre de dépendances global entre les systèmes : onboarding, mini-jeux, gestion, Stellarpedia, narration CYOA et Parallaxe. Le but est de visualiser l'ordre logique de développement, les prérequis et les blocages.

---

## 1. Vue globale (Mermaid)
```mermaid
graph TD
    A[Core Systems] --> B[Onboarding]
    A --> C[Daily Loop]
    C --> D[Production System]
    C --> E[Mini-Games]
    C --> F[Daily Events]
    C --> G[Expeditions]

    G --> H[Narrative CYOA System]
    H --> I[Branching Story Engine]
    I --> J[Parallaxe Integration]

    G --> K[Discoveries]
    K --> L[Stellarpedia Core]
    L --> M[Stellarpedia Contributions]

    D --> N[Gestion System]
    N --> G
    N --> E
    N --> H

    J --> O[Parallaxe Rare Events]
    O --> H
    O --> L

    B --> C
```
---

## 2. Explication de l'arbre

### **A. Core Systems**
Base technique : utilisateurs, planètes d'origine, UI minimale, backend stable.

### **B. Onboarding**
Prérequis : Core Systems

Débloque : Daily Loop (boucle quotidienne complète).

---

## 3. Daily Loop
Composée de :
- Production du jour
- Mini-jeu du jour
- Événement du jour
- Expédition du jour

### Dépendances internes
- La production alimente la gestion.
- Les mini-jeux influencent les récompenses.
- Les expéditions mènent aux découvertes.

---

## 4. Gestion (Management)
Prérequis : Production

Influence :
- Difficulté mini-jeux
- Chances d'expédition
- Résolution des événements narratifs

---

## 5. Narration à choix (CYOA)
Prérequis : Expéditions + Daily Events

Débloque :
- Micro-arcs narratifs
- Choix impactant
- Conséquences persistantes

---

## 6. Stellarpedia
Prérequis : Discoveries

Étapes :
1. Architecture
2. Contributions
3. Votes & canonisation
4. Entrées rares liées au Parallaxe

---

## 7. Parallaxe (Fil rouge)
Prérequis : CYOA + Expéditions

Implique :
- Branches secrètes
- Glitchs narratifs
- Indices rares
- Entrées cachées dans le Stellarpedia

---

## 8. Roadmap en phases

### **Phase 1 – Fondations**
- Core Systems
- Onboarding
- Daily Loop basique

### **Phase 2 – Systems Richness**
- Mini-games supplémentaires
- Production & Gestion
- Expéditions enrichies

### **Phase 3 – Lore & Monde**
- Stellarpedia complet
- Discoveries étendus
- CYOA avancé

### **Phase 4 – Mystère et profondeur**
- Parallaxe niveau 1
- Micro-arcs persistants
- Branches secrètes

### **Phase 5 – Univers vivant**
- Events globaux
- Parallaxe niveau 2
- Contenu hebdomadaire optionnel


# Stellar – Visual Roadmap


Mise à jour avec point de départ : **15 novembre 2025**.


```mermaid
gantt
dateFormat YYYY-MM-DD
title Stellar Development Roadmap (Updated from Nov 15, 2025)


section Phase 1 – Foundations
Core Systems :done, p1a, 2025-11-15, 14d
Onboarding :active, p1b, 2025-11-29, 14d
Basic Daily Loop : p1c, 2025-12-13, 21d


section Phase 2 – Systems Enrichment
Mini-Games v1 : p2a, 2026-01-03, 14d
Expeditions Core : p2b, 2026-01-17, 21d
Management Lite : p2c, 2026-02-07, 21d


section Phase 3 – World & Lore
Discoveries Expansion : p3a, 2026-02-28, 21d
Stellarpedia Core : p3b, 2026-03-20, 21d
CYOA Engine : p3c, 2026-04-10, 21d


section Phase 4 – Parallaxe & Depth
Parallaxe Tier 1 : p4a, 2026-05-01, 21d
Narrative Micro-Arcs : p4b, 2026-05-22, 21d


section Phase 5 – Universe Live
Global Events System : p5a, 2026-06-12, 21d
Parallaxe Tier 2 : p5b, 2026-07-03, 30d
```