# AGENTS.md - Documentation pour l'IA

> **Note**: Ce projet utilise également le système de règles Cursor moderne dans `.cursor/rules/` (format MDC). Ce fichier `AGENTS.md` reste disponible comme alternative simple et pour compatibilité.

## 🚀 Démarrage Rapide

- **[PROMPTS_GUIDE.md](./PROMPTS_GUIDE.md)** - Guide complet avec tous les prompts pour chaque étape du workflow

## Workflow

Pour comprendre l'ordre d'intervention des agents et le processus complet :
- **[WORKFLOW.md](./WORKFLOW.md)** : Schéma et description du workflow de développement

## Memory Bank

Documentation du projet Stellar :

- **[PROJECT_BRIEF.md](./docs/memory_bank/PROJECT_BRIEF.md)** : Vision métier, fonctionnalités, personas, flux utilisateurs
- **[ARCHITECTURE.md](./docs/memory_bank/ARCHITECTURE.md)** : Architecture technique, modèle de données, API endpoints, flux métier
- **[STACK.md](./docs/memory_bank/STACK.md)** : Stack technique (Laravel, Livewire, MySQL)

## Agents

Agents disponibles pour guider l'IA dans différents rôles :

- **[PRODUCT.md](./docs/agents/PRODUCT.md)** : **Alex** - Agent Product Manager - Vision produit, priorisation, expérience utilisateur
- **[LEAD-DEV.md](./docs/agents/LEAD-DEV.md)** : **Sam** - Agent Lead Developer - Architecture technique, transformation d'issues en plans de développement
- **[ARCHITECT.md](./docs/agents/ARCHITECT.md)** : **Morgan** - Agent Architecte - Review architecturale, cohérence technique, qualité du code
- **[FULLSTACK-DEV.md](./docs/agents/FULLSTACK-DEV.md)** : **Jordan** - Agent Fullstack Developer - Implémentation des plans de développement en code fonctionnel
- **[DESIGNER.md](./docs/agents/DESIGNER.md)** : **Riley** - Agent Designer - Identité visuelle, expérience utilisateur, cohérence du design
- **[GAME-DESIGNER.md](./docs/agents/GAME-DESIGNER.md)** : **Casey** - Agent Game Designer - Conception des mécaniques de jeu, équilibrage, progression du joueur
- **[MANAGER.md](./docs/agents/MANAGER.md)** : **Taylor** - Agent Workflow Manager - Surveillance du workflow, analyse des processus, rapports d'amélioration

## Prompts

Actions disponibles pour les agents :

- **[elaborate-issue.md](./docs/prompts/elaborate-issue.md)** : Guide pour élaborer progressivement une issue à partir d'une idée (processus interactif Product - Métier)
- **[create-issue.md](./docs/prompts/create-issue.md)** : Guide pour créer des issues produit dans `docs/issues/`
- **[create-plan.md](./docs/prompts/create-plan.md)** : Guide pour créer des plans de développement dans `docs/tasks/`
- **[review-task.md](./docs/prompts/review-task.md)** : Guide pour reviewer les plans de développement
- **[implement-task.md](./docs/prompts/implement-task.md)** : Guide pour implémenter les plans de développement
- **[review-implementation.md](./docs/prompts/review-implementation.md)** : Guide pour reviewer le code implémenté
- **[review-functional.md](./docs/prompts/review-functional.md)** : Guide pour reviewer fonctionnellement une implémentation
- **[review-visual.md](./docs/prompts/review-visual.md)** : Guide pour reviewer visuellement une implémentation (Riley)
- **[manage-design-system.md](./docs/prompts/manage-design-system.md)** : Guide pour créer, reviewer le design system et gérer les composants (Riley)
- **[design-game-mechanic.md](./docs/prompts/design-game-mechanic.md)** : Guide pour concevoir de nouvelles mécaniques de jeu (Casey)
- **[balance-gameplay.md](./docs/prompts/balance-gameplay.md)** : Guide pour équilibrer le gameplay et analyser les métriques (Casey)
- **[create-pr.md](./docs/prompts/create-pr.md)** : Guide pour créer une Pull Request vers develop
- **[update-tracking.md](./docs/prompts/update-tracking.md)** : Guide pour mettre à jour et suivre les issues et tasks
- **[monitor-workflow.md](./docs/prompts/monitor-workflow.md)** : Guide pour surveiller le workflow et créer des rapports d'amélioration
- **[propose-technical-rule.md](./docs/prompts/propose-technical-rule.md)** : Guide pour proposer de nouvelles règles techniques (Morgan, Sam)
- **[update-memory-bank.md](./docs/prompts/update-memory-bank.md)** : Guide pour proposer des modifications de la Memory Bank (Morgan, Sam)

## Rules

Règles et validations :

- **[MARKDOWN_RULES.md](./docs/rules/MARKDOWN_RULES.md)** : Principes de rédaction des documents
- **[HUMAN_VALIDATION.md](./docs/rules/HUMAN_VALIDATION.md)** : Points de validation humaine requise
- **[proposals/](./docs/rules/proposals/)** : Propositions de nouvelles règles techniques
- **[memory_bank/proposals/](./docs/memory_bank/proposals/)** : Propositions de modifications de la Memory Bank
