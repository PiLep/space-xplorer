# COMPONENT-Empty State

## Vue d'Ensemble

Le composant Empty State affiche un état vide avec icône optionnelle, titre, description et action pour guider l'utilisateur.

**Quand l'utiliser** :
- Listes vides
- Aucun résultat de recherche
- États initiaux

## Spécifications Techniques

### Props

| Prop | Type | Défaut | Description |
|------|------|--------|-------------|
| `title` | string | `'No items found'` | Titre de l'état vide |
| `description` | string | `null` | Description optionnelle |

### Slots

- `icon` : Slot pour l'icône SVG optionnelle
- `action` : Slot pour l'action (bouton) optionnelle

## Exemples d'Utilisation

```blade
<x-empty-state
    title="No resources found"
    description="Get started by creating your first resource."
>
    <x-slot:icon>
        <svg class="h-12 w-12 text-gray-400">...</svg>
    </x-slot:icon>
    <x-slot:action>
        <x-button variant="primary" size="sm">Create Resource</x-button>
    </x-slot:action>
</x-empty-state>
```

---

**Référence** : Voir **[DESIGN-SYSTEM.md](../DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.






