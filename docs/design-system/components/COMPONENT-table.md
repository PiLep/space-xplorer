# Table Component - Design System

## Vue d'Ensemble

Le composant Table est un composant complet et flexible pour afficher des données tabulaires avec support de la pagination, du formatage des données, et de multiples variantes de style. Il est conçu pour être utilisé dans les pages d'administration et les interfaces nécessitant l'affichage de données structurées.

## Usage

Composant réutilisable pour afficher des données tabulaires avec headers, rows, pagination, et variantes de style.

**Fichier** : `resources/views/components/table.blade.php`

## Variantes

### Default (défaut)

Style standard avec padding généreux et hover sur les lignes.

```blade
<x-table
    :headers="[
        ['label' => 'Name', 'key' => 'name'],
        ['label' => 'Email', 'key' => 'email'],
    ]"
    :rows="$users"
/>
```

### Compact

Style compact avec padding réduit pour afficher plus de données.

```blade
<x-table
    variant="compact"
    :headers="[
        ['label' => 'ID', 'key' => 'id'],
        ['label' => 'Name', 'key' => 'name'],
    ]"
    :rows="$items"
/>
```

### Striped

Lignes alternées avec couleurs différentes pour améliorer la lisibilité.

```blade
<x-table
    variant="striped"
    :headers="$headers"
    :rows="$rows"
/>
```

## Props

### headers (array, requis)

Définition des colonnes du tableau. Peut être un array simple de strings ou un array d'objets avec options.

**Format simple** :
```php
['Name', 'Email', 'Status']
```

**Format avancé** :
```php
[
    [
        'label' => 'ID',
        'key' => 'id',
        'align' => 'left', // 'left', 'center', 'right'
        'class' => 'font-mono text-gray-500',
        'cellClass' => 'font-mono text-gray-500',
        'format' => null, // 'date', 'datetime', 'datetime-full'
    ],
    [
        'label' => 'Name',
        'key' => 'name',
        'align' => 'left',
    ],
    [
        'label' => 'Created',
        'key' => 'created_at',
        'format' => 'datetime',
    ],
]
```

**Options de header** :
- `label` (string) : Texte affiché dans l'en-tête
- `key` (string) : Clé pour accéder à la valeur dans les rows (support dot notation)
- `align` (string) : Alignement du texte ('left', 'center', 'right', défaut: 'left')
- `class` (string) : Classes CSS additionnelles pour l'en-tête
- `cellClass` (string) : Classes CSS additionnelles pour les cellules
- `format` (string) : Format de date ('date', 'datetime', 'datetime-full')

### rows (array|Collection, requis)

Données à afficher dans le tableau. Peut être un array ou une Collection Laravel.

```php
$rows = User::all();
// ou
$rows = [
    ['name' => 'John', 'email' => 'john@example.com'],
    ['name' => 'Jane', 'email' => 'jane@example.com'],
];
```

### emptyMessage (string, optionnel)

Message affiché quand il n'y a pas de données. Défaut : `'No data found'`

```blade
<x-table
    :headers="$headers"
    :rows="$rows"
    emptyMessage="Aucun utilisateur trouvé"
/>
```

### emptyColspan (int, optionnel)

Nombre de colonnes pour le message vide. Par défaut, utilise le nombre de headers.

```blade
<x-table
    :headers="$headers"
    :rows="$rows"
    :emptyColspan="5"
/>
```

### pagination (Pagination, optionnel)

Instance de pagination Laravel pour afficher la pagination en bas du tableau.

```blade
<x-table
    :headers="$headers"
    :rows="$users"
    :pagination="$users"
/>
```

### variant (string, optionnel)

Variante de style : `'default'`, `'compact'`, `'striped'`. Défaut : `'default'`

### responsive (bool, optionnel)

Active le scroll horizontal sur mobile. Défaut : `true`

```blade
<x-table
    :headers="$headers"
    :rows="$rows"
    :responsive="false"
/>
```

### hover (bool, optionnel)

Active l'effet hover sur les lignes. Défaut : `true`

```blade
<x-table
    :headers="$headers"
    :rows="$rows"
    :hover="false"
/>
```

## Formatage des Dates

Le composant supporte le formatage automatique des dates via la propriété `format` dans les headers :

- `'date'` : Format `Y-m-d` (2025-01-15)
- `'datetime'` : Format `Y-m-d H:i` (2025-01-15 14:30)
- `'datetime-full'` : Format `Y-m-d H:i:s` (2025-01-15 14:30:45)

```blade
<x-table
    :headers="[
        ['label' => 'Created', 'key' => 'created_at', 'format' => 'datetime'],
        ['label' => 'Updated', 'key' => 'updated_at', 'format' => 'date'],
    ]"
    :rows="$items"
/>
```

## Utilisation avec Slot

Pour un contrôle total sur le rendu des lignes, utilisez le slot :

```blade
<x-table
    :headers="[
        ['label' => 'ID', 'key' => 'id'],
        ['label' => 'Name', 'key' => 'name'],
        ['label' => 'Actions'],
    ]"
>
    @foreach($users as $user)
        <tr>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500 dark:text-gray-400">
                {{ $user->id }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                {{ $user->name }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <x-button href="{{ route('admin.users.show', $user) }}" variant="ghost" size="sm">
                    View
                </x-button>
            </td>
        </tr>
    @endforeach
</x-table>
```

## Exemples d'Utilisation

### Tableau Simple

```blade
<x-table
    :headers="[
        ['label' => 'Name', 'key' => 'name'],
        ['label' => 'Email', 'key' => 'email'],
        ['label' => 'Status', 'key' => 'status'],
    ]"
    :rows="$users"
/>
```

### Tableau avec Pagination

```blade
<x-table
    :headers="[
        ['label' => 'ID', 'key' => 'id', 'align' => 'right'],
        ['label' => 'Name', 'key' => 'name'],
        ['label' => 'Email', 'key' => 'email'],
        ['label' => 'Created', 'key' => 'created_at', 'format' => 'datetime'],
    ]"
    :rows="$users"
    :pagination="$users"
    variant="striped"
/>
```

### Tableau Compact avec Relations

```blade
<x-table
    variant="compact"
    :headers="[
        ['label' => 'User', 'key' => 'name'],
        ['label' => 'Planet', 'key' => 'homePlanet.name'],
        ['label' => 'Resources', 'key' => 'resources_count', 'align' => 'right'],
    ]"
    :rows="$users"
/>
```

### Tableau avec Colonnes Personnalisées

```blade
<x-table
    :headers="[
        ['label' => 'ID', 'key' => 'id', 'cellClass' => 'font-mono text-gray-500'],
        ['label' => 'Name', 'key' => 'name', 'cellClass' => 'font-medium'],
        ['label' => 'Status', 'key' => 'status'],
        ['label' => 'Actions'],
    ]"
    :rows="$items"
>
    @foreach($items as $item)
        <tr>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-500 dark:text-gray-400">
                {{ $item->id }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                {{ $item->name }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                <x-badge variant="{{ $item->status === 'active' ? 'success' : 'default' }}">
                    {{ ucfirst($item->status) }}
                </x-badge>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <x-button href="{{ route('items.show', $item) }}" variant="ghost" size="sm">
                    View
                </x-button>
            </td>
        </tr>
    @endforeach
</x-table>
```

## Spécifications Techniques

### Structure HTML

```html
<div class="bg-surface-dark shadow rounded-lg border border-border-dark overflow-hidden">
    <div class="overflow-x-auto"> <!-- Si responsive -->
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th>...</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-surface-dark divide-y divide-gray-200">
                <tr>...</tr>
            </tbody>
        </table>
    </div>
    <!-- Pagination si fournie -->
</div>
```

### Classes CSS

**Table** :
- `min-w-full` : Largeur minimale pleine largeur
- `divide-y` : Séparateurs verticaux entre lignes
- `divide-gray-200 dark:divide-gray-700` : Couleur des séparateurs

**Header** :
- `bg-gray-50 dark:bg-gray-800` : Fond de l'en-tête
- `px-6 py-3` : Padding (default) ou `px-4 py-2` (compact)
- `text-xs font-medium uppercase tracking-wider` : Style du texte

**Body** :
- `bg-white dark:bg-surface-dark` : Fond des cellules
- `px-6 py-4` : Padding (default) ou `px-4 py-2` (compact)
- Hover : `[&_tr:hover]:bg-gray-50 dark:[&_tr:hover]:bg-surface-medium`
- Striped : `[&_tr:nth-child(even)]:bg-gray-50 dark:[&_tr:nth-child(even)]:bg-surface-medium`

## Accessibilité

- Utilisation de `<th scope="col">` pour les en-têtes
- Support du mode sombre avec classes `dark:`
- Contraste suffisant pour la lisibilité
- Structure sémantique HTML5 (`<table>`, `<thead>`, `<tbody>`)

## Responsive

Le composant est responsive par défaut avec :
- Scroll horizontal automatique sur mobile
- Classes Tailwind responsive pour l'espacement
- Support des breakpoints `sm:`, `md:`, `lg:`

## Bonnes Pratiques

1. **Utiliser des keys descriptives** : Utiliser des noms de colonnes clairs dans les headers
2. **Formatage des dates** : Utiliser le format intégré plutôt que de formater manuellement
3. **Pagination** : Toujours fournir la pagination pour les grandes listes
4. **Slot pour complexité** : Utiliser le slot pour les cas complexes (badges, boutons, etc.)
5. **Relations** : Utiliser la dot notation pour accéder aux relations (`homePlanet.name`)

## Cas d'Usage

- Pages d'administration (users, resources, etc.)
- Listes de données avec pagination
- Tableaux de bord avec statistiques
- Interfaces nécessitant l'affichage de données structurées

---

**Référence** : Voir **[DESIGN-SYSTEM.md](../DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

