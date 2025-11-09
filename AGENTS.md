# AGENTS.md - Documentation pour l'IA

## Workflow

Pour comprendre l'ordre d'intervention des agents et le processus complet :
- **[WORKFLOW.md](./WORKFLOW.md)** : Schéma et description du workflow de développement

## Memory Bank

Documentation du projet Space Xplorer :

- **[PROJECT_BRIEF.md](./docs/memory_bank/PROJECT_BRIEF.md)** : Vision métier, fonctionnalités, personas, flux utilisateurs
- **[ARCHITECTURE.md](./docs/memory_bank/ARCHITECTURE.md)** : Architecture technique, modèle de données, API endpoints, flux métier
- **[STACK.md](./docs/memory_bank/STACK.md)** : Stack technique (Laravel, Livewire, MySQL)

## Agents

Agents disponibles pour guider l'IA dans différents rôles :

- **[PRODUCT.md](./docs/agents/PRODUCT.md)** : **Alex** - Agent Product Manager - Vision produit, priorisation, expérience utilisateur
- **[LEAD-DEV.md](./docs/agents/LEAD-DEV.md)** : **Sam** - Agent Lead Developer - Architecture technique, transformation d'issues en plans de développement
- **[ARCHITECT.md](./docs/agents/ARCHITECT.md)** : **Morgan** - Agent Architecte - Review architecturale, cohérence technique, qualité du code
- **[FULLSTACK-DEV.md](./docs/agents/FULLSTACK-DEV.md)** : **Jordan** - Agent Fullstack Developer - Implémentation des plans de développement en code fonctionnel

## Prompts

Actions disponibles pour les agents :

- **[create-issue.md](./docs/prompts/create-issue.md)** : Guide pour créer des issues produit dans `docs/issues/`
- **[create-plan.md](./docs/prompts/create-plan.md)** : Guide pour créer des plans de développement dans `docs/tasks/`
- **[review-task.md](./docs/prompts/review-task.md)** : Guide pour reviewer les plans de développement
- **[implement-task.md](./docs/prompts/implement-task.md)** : Guide pour implémenter les plans de développement
- **[review-implementation.md](./docs/prompts/review-implementation.md)** : Guide pour reviewer le code implémenté
- **[review-functional.md](./docs/prompts/review-functional.md)** : Guide pour reviewer fonctionnellement une implémentation
- **[create-pr.md](./docs/prompts/create-pr.md)** : Guide pour créer une Pull Request vers develop
- **[update-tracking.md](./docs/prompts/update-tracking.md)** : Guide pour mettre à jour et suivre les issues et tasks

## Rules

Règles de génération de documentation :

- **[MARKDOWN_RULES.md](./docs/rules/MARKDOWN_RULES.md)** : Principes de rédaction des documents
