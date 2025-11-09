# COMPONENT-Terminal Message

## Vue d'Ensemble

Le composant Terminal Message affiche des messages système avec style terminal. Il détecte automatiquement le type de message basé sur le préfixe (`[OK]`, `[ERROR]`, `[INFO]`, etc.) et applique la couleur appropriée selon le design system.

**Quand l'utiliser** :
- Messages système dans les interfaces terminal
- Notifications de statut avec style terminal
- Messages d'information, succès, erreur dans un contexte terminal
- Logs et messages de feedback système

## Design

### Types de Messages Supportés

Le composant détecte automatiquement le type de message et applique la couleur correspondante :

| Préfixe | Type | Couleur | Usage |
|---------|------|---------|-------|
| `[OK]` | Success | `text-space-primary` (vert) | Opérations réussies, confirmations |
| `[SUCCESS]` | Success | `text-space-primary` (vert) | Succès d'opérations |
| `[READY]` | Success | `text-space-primary` (vert) | Système prêt, état disponible |
| `[ERROR]` | Error | `text-error` (rouge) | Erreurs critiques, échecs |
| `[INFO]` | Info | `text-space-secondary` (bleu) | Informations générales |
| `[WAIT]` | Wait | `text-gray-500` (gris) | En attente, initialisation |
| `[LOADING]` | Wait | `text-gray-500` (gris) | Chargement en cours |
| Autres | Default | `text-gray-500` (gris) | Messages par défaut |

### Apparence

- Taille de texte : `text-sm`
- Police : Monospace (héritée du contexte parent avec `font-mono`)
- Couleur : Détectée automatiquement selon le préfixe
- Espacement : Margin bottom configurable (défaut : `mb-2`)

## Spécifications Techniques

### Props

| Prop | Type | Défaut | Description |
|------|------|--------|-------------|
| `message` | string | **requis** | Message à afficher avec préfixe (ex: `"[OK] System ready"`) |
| `marginBottom` | string | `'mb-2'` | Classe Tailwind pour la marge inférieure (`mb-2`, `mb-4`, `''` pour aucune marge, etc.) |

### Structure HTML

```blade
<x-terminal-message message="[OK] System initialized" />
```

**Rendu** :
```html
<div class="text-sm text-space-primary dark:text-space-primary mb-2">
    [OK] System initialized
</div>
```

## Code d'Implémentation

### Blade Component

**Fichier** : `resources/views/components/terminal-message.blade.php`

```blade
@props([
    'message', // Message à afficher (ex: "[OK] System ready")
    'marginBottom' => 'mb-2', // Margin bottom personnalisable
])

@php
    // Détection automatique du type de message basée sur le préfixe
    $type = 'default';
    $colorClass = 'text-gray-500 dark:text-gray-500';
    
    if (str_contains($message, '[OK]') || str_contains($message, '[SUCCESS]') || str_contains($message, '[READY]')) {
        $type = 'success';
        $colorClass = 'text-space-primary dark:text-space-primary';
    } elseif (str_contains($message, '[ERROR]')) {
        $type = 'error';
        $colorClass = 'text-error dark:text-error';
    } elseif (str_contains($message, '[INFO]')) {
        $type = 'info';
        $colorClass = 'text-space-secondary dark:text-space-secondary';
    } elseif (str_contains($message, '[WAIT]') || str_contains($message, '[LOADING]')) {
        $type = 'wait';
        $colorClass = 'text-gray-500 dark:text-gray-500';
    }
@endphp

<div class="text-sm {{ $colorClass }} {{ $marginBottom ?: '' }}">
    {{ $message }}
</div>
```

## Exemples d'Utilisation

### Message de Succès

```blade
<x-terminal-message message="[OK] Authentication terminal initialized" />
```

**Résultat** : Message en vert (space-primary)

### Message d'Information

```blade
<x-terminal-message message="[INFO] Please provide your credentials to access the system" />
```

**Résultat** : Message en bleu (space-secondary)

### Message d'Erreur

```blade
<x-terminal-message message="[ERROR] Failed to connect to database" />
```

**Résultat** : Message en rouge (error)

### Message avec Espacement Personnalisé

```blade
<x-terminal-message 
    message="[READY] System ready for commands" 
    marginBottom="mb-4"
/>
```

### Message sans Marge

```blade
<x-terminal-message 
    message="[INFO] Please provide your credentials" 
    :marginBottom="''"
/>
```

### Dans un Contexte Terminal

```blade
<div class="font-mono">
    <x-terminal-prompt command="init_system" />
    <x-terminal-message message="[OK] System initialized" />
    <x-terminal-message message="[INFO] Ready to accept commands" />
</div>
```

## Classes Tailwind Utilisées

### Structure
- `text-sm` : Taille de texte petite
- `mb-2` (par défaut) : Marge inférieure standard

### Couleurs (détectées automatiquement)
- `text-space-primary dark:text-space-primary` : Pour `[OK]`, `[SUCCESS]`, `[READY]`
- `text-error dark:text-error` : Pour `[ERROR]`
- `text-space-secondary dark:text-space-secondary` : Pour `[INFO]`
- `text-gray-500 dark:text-gray-500` : Pour `[WAIT]`, `[LOADING]`, et par défaut

## Détection Automatique

Le composant utilise `str_contains()` pour détecter les préfixes dans le message. L'ordre de détection est :

1. `[OK]`, `[SUCCESS]`, `[READY]` → Success (vert)
2. `[ERROR]` → Error (rouge)
3. `[INFO]` → Info (bleu)
4. `[WAIT]`, `[LOADING]` → Wait (gris)
5. Autres → Default (gris)

**Note** : La détection est sensible à la casse et recherche les préfixes exacts entre crochets.

## Accessibilité

- Les couleurs respectent les standards de contraste WCAG 2.1
- Le texte est lisible sur fond clair et sombre
- Les préfixes entre crochets facilitent la compréhension du type de message

## Notes de Design

- **Cohérence** : Utilise les couleurs du design system pour maintenir la cohérence visuelle
- **Détection automatique** : Simplifie l'utilisation en détectant automatiquement le type de message
- **Flexibilité** : Espacement configurable pour s'adapter à différents contextes
- **Style terminal** : S'intègre parfaitement dans les interfaces terminal avec `font-mono`

## Relation avec Autres Composants

- **Terminal Prompt** : Utilisé conjointement pour créer des séquences de commandes terminal
- **Terminal Boot** : Utilise un pattern similaire pour les messages de boot
- **Alert** : Pour les messages d'alerte plus complexes avec fond et bordures

---

**Référence** : Voir **[DESIGN-SYSTEM.md](./DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

