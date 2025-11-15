# Cursor Rules - Stellar Game

This directory contains Cursor rules in MDC format (Markdown with metadata) for the Stellar Game project.

## Design Philosophy

**These rules are pointers, not duplicates.** They reference the actual documentation in `docs/` to avoid duplication and maintain a single source of truth.

## Structure

- `workflow.mdc` - Workflow and agent system (always applied) → References `AGENTS.md`, `WORKFLOW.md`
- `memory-bank.mdc` - Project knowledge base (always applied) → References `docs/memory_bank/*`
- `code-standards.mdc` - Code standards (always applied, PHP/JS files) → References `docs/rules/*`
- `prompts.mdc` - Available prompts (agent requested) → References `docs/prompts/*`
- `documentation.mdc` - Documentation standards (agent requested) → References `docs/rules/*`

## Rule Types

- **alwaysApply: true** - Always included in the model context
- **agentRequested: true** - Available for the AI to include when relevant
- **globs** - File patterns that trigger auto-attachment

## Format

Each rule file uses the MDC format with:
- YAML frontmatter for metadata
- Minimal markdown content with references to `docs/` documentation
- No duplication - rules point to the actual documentation

## Maintenance

- **Update documentation**: Edit files in `docs/` directory
- **Update rules**: Only modify `.cursor/rules/*.mdc` if structure changes
- **Single source of truth**: All content lives in `docs/`, rules are just pointers

## Compatibility

The project also maintains:
- `AGENTS.md` at root - Simple alternative for agent system

The `.cursor/rules/` directory is the recommended approach for Cursor 2025.

## Alternative: Symbolic Links

If you prefer symbolic links for critical files, you can create them:

```bash
cd .cursor/rules
ln -s ../../docs/memory_bank/PROJECT_BRIEF.md project-brief.md
ln -s ../../docs/memory_bank/ARCHITECTURE.md architecture.md
```

However, the current approach (references) is recommended as it's more maintainable and works across all platforms.

