# Maintenance Guide - Cursor Rules

## Philosophy: Single Source of Truth

**All documentation lives in `docs/`**. The rules in `.cursor/rules/` are **pointers**, not duplicates.

## When to Update What

### Update Documentation (`docs/`)
- ✅ Adding new content
- ✅ Modifying existing content
- ✅ Updating workflows, agents, prompts
- ✅ Changing technical rules

### Update Rules (`.cursor/rules/`)
- ✅ Only when structure changes (new rule files, new globs)
- ✅ When references need updating
- ❌ **Never** duplicate content from `docs/`

## File Organization

```
project-root/
├── .cursor/
│   └── rules/          # Pointers to docs (minimal content)
│       ├── workflow.mdc → References AGENTS.md, WORKFLOW.md
│       ├── memory-bank.mdc → References docs/memory_bank/*
│       └── ...
├── docs/               # Single source of truth
│   ├── agents/         # Agent documentation
│   ├── prompts/        # Prompt guides
│   ├── memory_bank/    # Project knowledge
│   └── rules/          # Technical rules
├── AGENTS.md           # Simple alternative
└── .cursorrules        # Legacy (deprecated)
```

## Benefits

1. **No duplication** - Content exists in one place
2. **Easy maintenance** - Update `docs/`, rules automatically reflect changes
3. **Clear structure** - Rules are lightweight pointers
4. **Version control** - All changes tracked in `docs/`

## Example: Adding a New Agent

1. Create `docs/agents/NEW_AGENT.md` with full documentation
2. Update `docs/agents/PRODUCT.md` if needed
3. Update `.cursor/rules/workflow.mdc` to add a reference (one line)
4. **Do NOT** duplicate agent content in the rule file

## Example: Updating Workflow

1. Edit `WORKFLOW.md` with new steps
2. Rules automatically reference the updated file
3. **Do NOT** update `.cursor/rules/workflow.mdc` with workflow details

