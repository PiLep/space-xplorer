# COMPONENT-Description List

## Vue d'Ensemble

Le composant Description List affiche des paires terme/valeur avec grille responsive pour les pages de détails.

**Quand l'utiliser** :
- Pages de détails (show)
- Affichage de métadonnées
- Informations structurées

## Spécifications Techniques

### Props

| Prop | Type | Défaut | Description |
|------|------|--------|-------------|
| `columns` | int | `1` | Nombre de colonnes : `1`, `2`, `3` |

### Composant associé : Description Item

Le composant `<x-description-item>` est utilisé à l'intérieur de `<x-description-list>`.

**Props** :
- `term` : Terme (label)
- `value` : Valeur (optionnel, peut utiliser slot)
- `mono` : Police monospace pour les IDs/codes

## Exemples d'Utilisation

```blade
<x-description-list :columns="2">
    <x-description-item term="ID" value="01ARZ3NDEKTSV4RRFFQ69G5FAV" :mono="true" />
    <x-description-item term="Type" value="Planet Image" />
    <x-description-item term="Status">
        <x-badge variant="success">Approved</x-badge>
    </x-description-item>
</x-description-list>
```

---

**Référence** : Voir **[DESIGN-SYSTEM.md](../DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.




