# COMPONENT-Navigation

## Vue d'Ensemble

Le composant Navigation est utilisé pour la navigation principale de l'application. Il respecte le design system avec un style rétro-futuriste inspiré des interfaces de vaisseaux spatiaux, offrant une navigation claire et intuitive.

**Quand l'utiliser** :
- Navigation principale de l'application
- Menu de navigation dans les pages
- Navigation contextuelle
- Breadcrumbs pour la hiérarchie

## Design

### Apparence

La navigation utilise un fond sombre (`bg-surface-dark`), des bordures subtiles avec effet terminal (`terminal-border-simple`), et une typographie monospace pour créer une ambiance rétro-futuriste.

### Variantes

#### Sidebar Navigation

**Usage** : Navigation latérale fixe ou sticky, utilisée dans les pages du design system

**Spécifications** :
- Background : `bg-surface-dark` (`#1a1a1a`)
- Border : `border border-border-dark` (`#333333`)
- Padding : `p-4` (16px)
- Border radius : `rounded-lg`
- Sticky : `sticky top-8` (pour rester visible au scroll)
- Font : `font-mono` (monospace)

**États** :
- **Active** : `bg-space-primary text-space-black font-bold`
- **Inactive** : `text-gray-500 dark:text-gray-400`
- **Hover** : `hover:text-space-primary dark:hover:text-space-primary`

**Exemple** :
```html
<aside class="lg:w-64 flex-shrink-0">
    <nav class="bg-surface-dark dark:bg-surface-dark rounded-lg p-4 border border-border-dark dark:border-border-dark terminal-border-simple sticky top-8">
        <div class="space-y-1 font-mono">
            <a href="/overview" 
               class="block px-4 py-2 rounded text-sm transition-colors bg-space-primary text-space-black font-bold">
                > OVERVIEW
            </a>
            <a href="/colors" 
               class="block px-4 py-2 rounded text-sm transition-colors text-gray-500 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary">
                > COLORS
            </a>
        </div>
    </nav>
</aside>
```

#### Top Navigation Menu

**Usage** : Menu de navigation horizontal en haut de la page

**Spécifications** :
- Background : `bg-surface-dark` (`#1a1a1a`)
- Border : `border border-border-dark` (`#333333`)
- Padding : `p-4` (16px)
- Border radius : `rounded-lg`
- Layout : `flex flex-wrap items-center gap-2 sm:gap-4`
- Font : `font-mono text-sm`

**Séparateurs** :
- Utiliser `|` avec `text-gray-500 dark:text-gray-500`
- Masquer sur mobile : `hidden sm:inline`

**Exemple** :
```html
<nav class="mb-8 bg-surface-dark dark:bg-surface-dark rounded-lg p-4 border border-border-dark dark:border-border-dark terminal-border-simple">
    <div class="flex flex-wrap items-center gap-2 sm:gap-4 font-mono text-sm">
        <a href="/overview" 
           class="px-3 py-2 rounded transition-colors bg-space-primary text-space-black font-bold">
            > OVERVIEW
        </a>
        <span class="text-gray-500 dark:text-gray-500 hidden sm:inline">|</span>
        <a href="/colors" 
           class="px-3 py-2 rounded transition-colors text-gray-400 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary">
            > COLORS
        </a>
    </div>
</nav>
```

#### Terminal Command Bar

**Usage** : Barre de navigation fixe en bas de l'écran avec style terminal

**Spécifications** :
- Position : `fixed bottom-0 left-0 right-0`
- Background : `bg-surface-dark` (`#1a1a1a`)
- Border : `border-t border-border-dark` (`#333333`)
- Padding : `py-3 px-4 sm:px-6 lg:px-8`
- Font : `font-mono`
- Z-index : `z-50`

**Exemple** :
```html
<div class="fixed bottom-0 left-0 right-0 bg-surface-dark dark:bg-surface-dark border-t border-border-dark dark:border-border-dark font-mono z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <div class="flex items-center gap-2">
            <span class="text-gray-500 dark:text-gray-500 text-sm">SYSTEM@SPACE-XPLORER:~$</span>
            <div class="flex-1 flex items-center gap-4 text-sm">
                <a href="/dashboard" class="text-space-primary dark:text-space-primary hover:text-space-primary-light dark:hover:text-space-primary-light transition-colors">
                    > DASHBOARD
                </a>
            </div>
        </div>
    </div>
</div>
```

### États

#### Default
- Couleur texte : `text-gray-500 dark:text-gray-400`
- Pas de background

#### Active
- Background : `bg-space-primary` (`#00ff88`)
- Texte : `text-space-black` (`#0a0a0a`)
- Font : `font-bold`

#### Hover
- Couleur texte : `hover:text-space-primary dark:hover:text-space-primary`
- Transition : `transition-colors`

#### Disabled
- Opacity : `opacity-50`
- Cursor : `cursor-not-allowed`
- Pointer events : `pointer-events-none`

## Spécifications Techniques

### Classes Tailwind

**Container Navigation** :
```html
<nav class="bg-surface-dark dark:bg-surface-dark rounded-lg p-4 border border-border-dark dark:border-border-dark terminal-border-simple">
```

**Lien Navigation** :
```html
<a href="/route" 
   class="block px-4 py-2 rounded text-sm transition-colors {{ $isActive ? 'bg-space-primary text-space-black font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary' }}">
    > LABEL
</a>
```

### Structure HTML

**Sidebar Navigation** :
```html
<aside class="lg:w-64 flex-shrink-0">
    <nav class="bg-surface-dark rounded-lg p-4 border border-border-dark terminal-border-simple sticky top-8">
        <div class="space-y-1 font-mono">
            <!-- Liens de navigation -->
        </div>
    </nav>
</aside>
```

**Top Navigation** :
```html
<nav class="mb-8 bg-surface-dark rounded-lg p-4 border border-border-dark terminal-border-simple">
    <div class="flex flex-wrap items-center gap-2 sm:gap-4 font-mono text-sm">
        <!-- Liens avec séparateurs -->
    </div>
</nav>
```

## Code d'Implémentation

### Composant Blade (Sidebar)

```blade
@props(['currentPage' => 'overview'])

<aside class="lg:w-64 flex-shrink-0">
    <nav class="bg-surface-dark dark:bg-surface-dark rounded-lg p-4 border border-border-dark dark:border-border-dark terminal-border-simple sticky top-8">
        <div class="space-y-1 font-mono">
            <a href="{{ route('design-system.overview') }}" 
               class="block px-4 py-2 rounded text-sm transition-colors {{ $currentPage === 'overview' ? 'bg-space-primary text-space-black font-bold' : 'text-gray-500 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary' }}">
                > OVERVIEW
            </a>
            <!-- Autres liens -->
        </div>
    </nav>
</aside>
```

### Composant Blade (Top Menu)

```blade
@props(['currentPage' => 'overview'])

<nav class="mb-8 bg-surface-dark dark:bg-surface-dark rounded-lg p-4 border border-border-dark dark:border-border-dark terminal-border-simple">
    <div class="flex flex-wrap items-center gap-2 sm:gap-4 font-mono text-sm">
        <a href="{{ route('design-system.overview') }}" 
           class="px-3 py-2 rounded transition-colors {{ $currentPage === 'overview' ? 'bg-space-primary text-space-black font-bold' : 'text-gray-400 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary' }}">
            > OVERVIEW
        </a>
        <span class="text-gray-500 dark:text-gray-500 hidden sm:inline">|</span>
        <!-- Autres liens -->
    </div>
</nav>
```

## Exemples d'Utilisation

### Exemple 1 : Sidebar Navigation Simple

```blade
<aside class="lg:w-64 flex-shrink-0">
    <nav class="bg-surface-dark rounded-lg p-4 border border-border-dark terminal-border-simple sticky top-8">
        <div class="space-y-1 font-mono">
            <a href="/dashboard" class="block px-4 py-2 rounded text-sm transition-colors bg-space-primary text-space-black font-bold">
                > DASHBOARD
            </a>
            <a href="/profile" class="block px-4 py-2 rounded text-sm transition-colors text-gray-500 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary">
                > PROFILE
            </a>
        </div>
    </nav>
</aside>
```

### Exemple 2 : Top Navigation avec Séparateurs

```blade
<nav class="mb-8 bg-surface-dark rounded-lg p-4 border border-border-dark terminal-border-simple">
    <div class="flex flex-wrap items-center gap-2 sm:gap-4 font-mono text-sm">
        <a href="/home" class="px-3 py-2 rounded transition-colors bg-space-primary text-space-black font-bold">
            > HOME
        </a>
        <span class="text-gray-500 dark:text-gray-500 hidden sm:inline">|</span>
        <a href="/about" class="px-3 py-2 rounded transition-colors text-gray-400 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary">
            > ABOUT
        </a>
        <span class="text-gray-500 dark:text-gray-500 hidden sm:inline">|</span>
        <a href="/contact" class="px-3 py-2 rounded transition-colors text-gray-400 dark:text-gray-400 hover:text-space-primary dark:hover:text-space-primary">
            > CONTACT
        </a>
    </div>
</nav>
```

## Responsive

### Mobile (< 640px)
- Sidebar : Pleine largeur, empilée au-dessus du contenu
- Top Menu : Liens empilés verticalement ou avec wrap
- Séparateurs : Masqués (`hidden sm:inline`)

### Tablet (640px - 1024px)
- Sidebar : Largeur fixe (`lg:w-64`)
- Top Menu : Liens horizontaux avec wrap si nécessaire

### Desktop (> 1024px)
- Sidebar : Largeur fixe (`lg:w-64`), sticky
- Top Menu : Liens horizontaux avec séparateurs visibles

## Accessibilité

- **Navigation au clavier** : Tous les liens sont accessibles via Tab
- **Focus visible** : Utiliser `focus:ring-2 focus:ring-space-primary`
- **ARIA labels** : Ajouter `aria-label` pour les navigations complexes
- **Current page** : Utiliser `aria-current="page"` pour la page active

**Exemple avec ARIA** :
```html
<nav aria-label="Navigation principale">
    <a href="/current" aria-current="page" class="...">
        > CURRENT PAGE
    </a>
</nav>
```

## Notes de Design

- **Style Terminal** : Utiliser `font-mono` et le préfixe `>` pour créer l'ambiance terminal
- **Cohérence** : Tous les liens utilisent le même style de préfixe `> LABEL`
- **Visibilité** : La page active doit être clairement identifiée avec le fond vert primary
- **Espacement** : Utiliser `space-y-1` pour la sidebar, `gap-2 sm:gap-4` pour le top menu

---

**Référence** : Voir **[DESIGN-SYSTEM.md](../DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.




