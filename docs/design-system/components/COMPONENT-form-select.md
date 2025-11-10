# COMPONENT-Form Select

## Vue d'Ensemble

Le composant Form Select est un champ select avec label, validation et support du mode sombre. Il est compatible avec le design system Space Xplorer.

**Quand l'utiliser** :
- Champs de sélection dans les formulaires
- Filtres avec options prédéfinies
- Sélection de type/catégorie

## Spécifications Techniques

### Props

| Prop | Type | Défaut | Description |
|------|------|--------|-------------|
| `name` | string | `''` | Nom du champ (requis) |
| `id` | string | `null` | ID du champ (défaut: name) |
| `label` | string | `null` | Label du champ |
| `required` | bool | `false` | Champ requis |
| `value` | string | `null` | Valeur sélectionnée |
| `options` | array | `[]` | Options : `[['value' => '', 'label' => '']]` ou array simple |
| `placeholder` | string | `null` | Texte placeholder |
| `help` | string | `null` | Texte d'aide |
| `variant` | string | `'classic'` | Variante : `classic`, `terminal` |

## Exemples d'Utilisation

```blade
<x-form-select
    name="type"
    label="Resource Type"
    placeholder="Select a type"
    :options="[
        ['value' => 'avatar_image', 'label' => 'Avatar Image'],
        ['value' => 'planet_image', 'label' => 'Planet Image'],
    ]"
/>
```

---

**Référence** : Voir **[DESIGN-SYSTEM.md](../DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

