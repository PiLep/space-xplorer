# COMPONENT-Stat Card

## Vue d'Ensemble

Le composant Stat Card affiche une carte de statistique avec icône optionnelle pour afficher des métriques dans le style du design system.

**Quand l'utiliser** :
- Dashboard avec statistiques
- Métriques et KPIs
- Indicateurs numériques

## Spécifications Techniques

### Props

| Prop | Type | Défaut | Description |
|------|------|--------|-------------|
| `label` | string | `''` | Label de la statistique |
| `value` | string | `''` | Valeur à afficher |

### Slots

- `icon` : Slot pour l'icône SVG optionnelle

## Exemples d'Utilisation

```blade
<x-stat-card label="Total Users" value="1,234">
    <x-slot:icon>
        <svg class="h-6 w-6 text-space-primary">...</svg>
    </x-slot:icon>
</x-stat-card>
```

---

**Référence** : Voir **[DESIGN-SYSTEM.md](../DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

