# COMPONENT-Terminal Prompt

## Vue d'Ensemble

Le composant Terminal Prompt affiche une ligne de commande terminal avec le prompt système et une commande optionnelle. Il est utilisé pour créer l'ambiance terminal/spatial dans l'interface Space Xplorer.

**Quand l'utiliser** :
- Affichage de commandes système dans les pages
- Messages de statut avec style terminal
- Logs et notifications système
- Indication d'actions en cours

## Design

### Apparence

Ligne de commande avec :
- Prompt système en gris : `SYSTEM@SPACE-XPLORER:~$`
- Commande en couleur primaire (vert) : `command_name`

### Structure

```blade
SYSTEM@SPACE-XPLORER:~$ command_name
```

## Spécifications Techniques

### Props

| Prop | Type | Défaut | Description |
|------|------|--------|-------------|
| `command` | string | `''` | Nom de la commande à afficher |
| `prompt` | string | `'SYSTEM@SPACE-XPLORER:~$'` | Texte du prompt système |

### Classes Tailwind

```blade
<div class="text-sm text-space-primary dark:text-space-primary mb-2">
    <span class="text-gray-500 dark:text-gray-500">{{ $prompt }}</span>
    @if($command)
        <span class="text-space-primary dark:text-space-primary">{{ $command }}</span>
    @endif
</div>
```

## Code d'Implémentation

### Composant Blade

**Fichier** : `resources/views/components/terminal-prompt.blade.php`

```blade
@props([
    'command' => '',
    'prompt' => 'SYSTEM@SPACE-XPLORER:~$',
])

<div class="text-sm text-space-primary dark:text-space-primary mb-2">
    <span class="text-gray-500 dark:text-gray-500">{{ $prompt }}</span>
    @if($command)
        <span class="text-space-primary dark:text-space-primary">{{ $command }}</span>
    @endif
</div>
```

## Exemples d'Utilisation

### Exemple 1 : Commande Simple

```blade
<x-terminal-prompt command="load_user_session" />
```

**Résultat** :
```
SYSTEM@SPACE-XPLORER:~$ load_user_session
```

### Exemple 2 : Prompt Personnalisé

```blade
<x-terminal-prompt 
    command="authenticate" 
    prompt="SECURITY@SPACE-XPLORER:~$" 
/>
```

**Résultat** :
```
SECURITY@SPACE-XPLORER:~$ authenticate
```

### Exemple 3 : Prompt Seul (sans commande)

```blade
<x-terminal-prompt />
```

**Résultat** :
```
SYSTEM@SPACE-XPLORER:~$
```

## Responsive

Le composant s'adapte automatiquement :
- **Mobile** : Taille de texte réduite si nécessaire
- **Tablet** : Taille standard
- **Desktop** : Taille standard

## Accessibilité

- Utiliser des balises sémantiques appropriées
- Le texte est lisible avec un bon contraste
- Support du mode sombre intégré

## Notes de Design

- **Cohérence** : Utiliser le même prompt partout pour maintenir la cohérence
- **Lisibilité** : Le prompt en gris et la commande en couleur primaire créent une hiérarchie visuelle claire
- **Réutilisabilité** : Composant très simple et réutilisable dans toute l'application

---

**Référence** : Voir **[DESIGN-SYSTEM.md](../DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.


