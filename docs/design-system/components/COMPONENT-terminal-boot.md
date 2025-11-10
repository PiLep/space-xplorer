# COMPONENT-Terminal Boot

## Vue d'Ensemble

Le composant Terminal Boot affiche une séquence de messages de démarrage système avec animations et effets de fade-out. Il est utilisé pour créer une expérience immersive lors de l'initialisation de modules ou de pages.

**Quand l'utiliser** :
- Initialisation de pages avec chargement de données
- Démarrage de modules système
- Séquence de boot avec messages progressifs
- Indication visuelle du chargement avec style terminal

## Design

### Apparence

- Ligne de prompt terminal (optionnelle)
- Liste de messages avec couleurs selon le type :
  - `[OK]` : Vert (space-primary)
  - `[ERROR]` : Rouge (error)
  - Autres : Gris (gray-500)
- Animation de fade-out pour les anciens messages
- Curseur clignotant pendant le chargement

### Structure

```
SYSTEM@SPACE-XPLORER:~$ boot_terminal
[INIT] Initializing terminal interface...
[OK] Terminal initialized
[LOAD] Connecting to database...
...
```

## Spécifications Techniques

### Props

| Prop | Type | Défaut | Description |
|------|------|--------|-------------|
| `bootMessages` | array | `[]` | Tableau des messages de boot à afficher |
| `terminalBooted` | bool | `false` | État de boot terminé (active le fade-out) |
| `showPrompt` | bool | `true` | Afficher la ligne de prompt terminal |
| `pollMethod` | string | `null` | Méthode Livewire à appeler pour le polling (ex: `'nextBootStep'`) |

### Comportement

- **Fade-out automatique** : Les messages anciens (sauf les 4 derniers) disparaissent avec animation
- **Polling Livewire** : Si `pollMethod` est défini, le composant utilise `wire:poll` pour mettre à jour automatiquement
- **Délai d'animation** : Chaque message a un délai progressif (`index * 0.1s`)

## Code d'Implémentation

### Composant Blade

**Fichier** : `resources/views/components/terminal-boot.blade.php`

```blade
@props([
    'bootMessages' => [],
    'terminalBooted' => false,
    'showPrompt' => true,
    'pollMethod' => null,
])

<div class="mb-8 font-mono">
    @if($showPrompt)
        <div class="{{ $terminalBooted && count($bootMessages) > 3 ? 'fade-out-boot-message' : '' }}">
            <x-terminal-prompt command="boot_terminal" />
        </div>
    @endif
    
    <!-- Boot Messages -->
    <div class="space-y-1 mb-4">
        @foreach($bootMessages as $index => $message)
            @php
                $shouldFadeOut = $terminalBooted && $index < max(0, count($bootMessages) - 4);
            @endphp
            <div class="text-sm {{ str_contains($message, '[OK]') ? 'text-space-primary dark:text-space-primary' : (str_contains($message, '[ERROR]') ? 'text-error dark:text-error' : 'text-gray-500 dark:text-gray-500') }} {{ $shouldFadeOut ? 'fade-out-boot-message' : '' }}" style="animation-delay: {{ $index * 0.1 }}s;">
                {{ $message }}
            </div>
        @endforeach
        @if(!$terminalBooted && $pollMethod)
            <div class="text-sm text-space-primary dark:text-space-primary animate-pulse" wire:poll.400ms="{{ $pollMethod }}">
                <span class="inline-block w-2 h-4 bg-space-primary dark:bg-space-primary">_</span>
            </div>
        @endif
    </div>
</div>
```

## Exemples d'Utilisation

### Exemple 1 : Avec Livewire Polling

```blade
<x-terminal-boot 
    :bootMessages="$bootMessages" 
    :terminalBooted="$terminalBooted"
    :pollMethod="'nextBootStep'"
/>
```

**Dans le composant Livewire** :
```php
public $bootMessages = [];
public $terminalBooted = false;
public $bootStep = 0;

public function nextBootStep()
{
    $steps = [
        '[INIT] Initializing terminal interface...',
        '[OK] Terminal initialized',
        '[LOAD] Connecting to database...',
        '[OK] Database connection established',
    ];
    
    if ($this->bootStep < count($steps)) {
        $this->bootMessages[] = $steps[$this->bootStep];
        $this->bootStep++;
        
        if ($this->bootStep >= count($steps)) {
            $this->terminalBooted = true;
        }
    }
}
```

### Exemple 2 : Sans Polling (Messages Statiques)

```blade
<x-terminal-boot 
    :bootMessages="[
        '[INIT] System starting...',
        '[OK] System initialized',
        '[READY] System ready'
    ]" 
    :terminalBooted="true"
    :pollMethod="null"
/>
```

### Exemple 3 : Sans Prompt

```blade
<x-terminal-boot 
    :bootMessages="$bootMessages" 
    :terminalBooted="$terminalBooted"
    :showPrompt="false"
/>
```

## Responsive

Le composant s'adapte automatiquement :
- **Mobile** : Taille de texte standard
- **Tablet** : Taille standard
- **Desktop** : Taille standard

## Accessibilité

- Les messages sont lisibles avec un bon contraste
- Support du mode sombre intégré
- Les animations sont subtiles et ne gênent pas la lecture

## Notes de Design

- **Cohérence** : Utilise le même style terminal que les autres composants
- **Performance** : Le polling Livewire est optimisé (400ms)
- **Animation** : Les délais progressifs créent un effet visuel agréable
- **Fade-out** : Garde les 4 derniers messages visibles pour contexte

---

**Référence** : Voir **[DESIGN-SYSTEM.md](../DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.


