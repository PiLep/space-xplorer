# COMPONENT-Filter Card

## Vue d'Ensemble

Le composant Filter Card est un conteneur standardisé pour les sections de filtres avec style cohérent du design system.

**Quand l'utiliser** :
- Sections de filtres dans les pages de liste
- Formulaires de recherche avec filtres
- Conteneurs pour groupes de filtres

## Spécifications Techniques

### Props

| Prop | Type | Défaut | Description |
|------|------|--------|-------------|
| `title` | string | `null` | Titre optionnel de la section |

## Exemples d'Utilisation

```blade
<x-filter-card title="Filters">
    <form method="GET" class="flex gap-4 items-end">
        <!-- Filtres -->
    </form>
</x-filter-card>
```

---

**Référence** : Voir **[DESIGN-SYSTEM.md](../DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.





