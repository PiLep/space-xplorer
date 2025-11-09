# COMPONENT-Alert

## Vue d'Ensemble

Le composant Alert affiche des messages d'alerte avec style terminal. Il supporte différents types d'alertes (error, warning, success, info) avec des couleurs et styles adaptés.

**Quand l'utiliser** :
- Messages d'erreur système
- Avertissements utilisateur
- Confirmations de succès
- Informations importantes

## Design

### Variantes

#### Error (Erreur)
- Couleur : Rouge (`error`)
- Usage : Erreurs critiques, échecs d'opérations

#### Warning (Avertissement)
- Couleur : Jaune/Orange (`warning`)
- Usage : Avertissements, actions nécessitant attention

#### Success (Succès)
- Couleur : Vert (`space-primary`)
- Usage : Confirmations, opérations réussies

#### Info (Information)
- Couleur : Bleu (`space-secondary`)
- Usage : Informations générales, notifications

### Structure

Chaque alerte contient :
- Ligne de prompt terminal (optionnelle)
- Message formaté avec préfixe `[TYPE]`

## Spécifications Techniques

### Props

| Prop | Type | Défaut | Description |
|------|------|--------|-------------|
| `type` | string | `'error'` | Type d'alerte : `error`, `warning`, `success`, `info` |
| `message` | string | `''` | Message à afficher |
| `showPrompt` | bool | `true` | Afficher la ligne de prompt terminal |
| `prompt` | string | `'SYSTEM@SPACE-XPLORER:~$'` | Texte du prompt système |

### Configuration des Types

Chaque type a sa propre configuration de couleurs avec un contraste élevé pour une meilleure lisibilité :

```php
'error' => [
    'promptColor' => 'text-error dark:text-error',
    'bgColor' => 'bg-red-100 dark:bg-red-900/30',
    'borderColor' => 'border-red-400 dark:border-error',
    'textColor' => 'text-red-800 dark:text-red-200',
    'prefix' => '[ERROR]',
],
```

**Améliorations de contraste** :
- **Mode light** : Texte très foncé (`-900`) sur fond clair (`-100`) pour un contraste maximal
- **Mode dark** : Texte blanc ou couleurs du design system (`text-white`, `text-space-primary`, `text-space-secondary`) sur fond sombre pour une meilleure lisibilité
- Les fonds restent inchangés, seule la couleur du texte est ajustée pour améliorer le contraste

## Code d'Implémentation

### Composant Blade

**Fichier** : `resources/views/components/alert.blade.php`

Voir le fichier complet pour l'implémentation détaillée.

## Exemples d'Utilisation

### Exemple 1 : Erreur Standard

```blade
<x-alert type="error" message="Failed to load planet data" />
```

**Résultat** :
```
SYSTEM@SPACE-XPLORER:~$ ERROR
[ERROR] Failed to load planet data
```

### Exemple 2 : Succès

```blade
<x-alert type="success" message="Planet data loaded successfully" />
```

**Résultat** :
```
SYSTEM@SPACE-XPLORER:~$ SUCCESS
[SUCCESS] Planet data loaded successfully
```

### Exemple 3 : Avertissement

```blade
<x-alert type="warning" message="Low fuel reserves detected" />
```

**Résultat** :
```
SYSTEM@SPACE-XPLORER:~$ WARNING
[WARNING] Low fuel reserves detected
```

### Exemple 4 : Information

```blade
<x-alert type="info" message="System maintenance scheduled for tonight" />
```

**Résultat** :
```
SYSTEM@SPACE-XPLORER:~$ INFO
[INFO] System maintenance scheduled for tonight
```

### Exemple 5 : Sans Prompt

```blade
<x-alert type="error" :message="$error" :showPrompt="false" />
```

**Résultat** :
```
[ERROR] Failed to load planet data
```

## Responsive

Le composant s'adapte automatiquement :
- **Mobile** : Padding réduit, texte adaptatif
- **Tablet** : Padding standard
- **Desktop** : Padding généreux

## Accessibilité

- Attribut `role="alert"` pour les lecteurs d'écran
- Contraste de couleurs respecté pour chaque type
- Support du mode sombre avec couleurs adaptées
- Messages clairs et concis

## Notes de Design

- **Cohérence** : Utiliser les types appropriés pour chaque situation
- **Visibilité** : Les couleurs distinctes permettent une identification rapide du type d'alerte
- **Lisibilité** : Le préfixe `[TYPE]` facilite la lecture rapide
- **Réutilisabilité** : Composant très flexible avec plusieurs variantes

---

**Référence** : Voir **[DESIGN-SYSTEM.md](../DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

