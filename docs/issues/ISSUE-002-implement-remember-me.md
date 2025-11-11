# ISSUE-002 : Implémenter la persistence de connexion (Remember Me)

## Type
Feature

## Priorité
High

## Description

Implémenter la fonctionnalité "Remember Me" pour permettre aux utilisateurs de rester connectés même après la fermeture du navigateur. Cette fonctionnalité améliore grandement l'expérience utilisateur en réduisant la friction de reconnexion.

## Contexte Métier

**Problème actuel** :
- Les utilisateurs doivent se reconnecter à chaque fois qu'ils ferment leur navigateur
- Les sessions expirent après 120 minutes d'inactivité
- Cela crée une friction inutile pour les utilisateurs réguliers

**Valeur utilisateur** :
- Réduit la friction de reconnexion
- Améliore l'engagement des utilisateurs
- Permet une expérience plus fluide et agréable
- Les utilisateurs peuvent revenir facilement sur le jeu sans avoir à se reconnecter

**Impact** :
- Augmente la probabilité que les utilisateurs reviennent sur le jeu
- Réduit l'abandon lors de la reconnexion
- Améliore la satisfaction globale de l'expérience utilisateur

## Critères d'Acceptation

- [ ] Ajouter une checkbox "Se souvenir de moi" sur le formulaire de connexion (Livewire et API)
- [ ] Implémenter la logique "Remember Me" dans `AuthService::login()` et `AuthService::loginFromCredentials()`
- [ ] Utiliser `Auth::login($user, $remember)` avec le paramètre `$remember` basé sur la checkbox
- [ ] Le cookie de session doit persister au-delà de la fermeture du navigateur quand "Remember Me" est coché
- [ ] La durée de vie du cookie "Remember Me" doit être configurable (par défaut 30 jours)
- [ ] Fonctionner pour les connexions via Livewire (routes web)
- [ ] Fonctionner pour les connexions via API (tokens Sanctum - le token persiste déjà, mais documenter le comportement)
- [ ] Tester que la déconnexion invalide bien le cookie "Remember Me"
- [ ] Tester que le changement de mot de passe invalide les sessions "Remember Me" (à faire dans une issue future)

## Détails Techniques

### Backend

**Service AuthService** :
- Modifier `login()` et `loginFromCredentials()` pour accepter un paramètre `$remember` (booléen)
- Utiliser `Auth::login($user, $remember)` au lieu de `Auth::login($user)`
- Le champ `remember_token` existe déjà dans la table `users` (migration déjà créée)

**Form Requests** :
- Ajouter le champ `remember` (optionnel, booléen) dans `LoginRequest`
- Validation : `'remember' => 'sometimes|boolean'`

**Livewire Components** :
- Ajouter une checkbox dans `LoginTerminal.php` pour "Se souvenir de moi"
- Passer la valeur à `AuthService::loginFromCredentials()`

**API** :
- Ajouter le champ `remember` (optionnel) dans la requête de login API
- Documenter que pour l'API, les tokens Sanctum ont déjà une durée de vie longue, mais le comportement de session peut être différent

### Frontend

**Formulaire de connexion** :
- Ajouter une checkbox avec le label "Se souvenir de moi" ou "Remember me"
- Positionner la checkbox de manière intuitive (sous le champ mot de passe, avant le bouton de connexion)

**UX** :
- La checkbox doit être claire et facilement accessible
- Le texte doit être explicite sur ce que fait cette option

### Configuration

**Session** :
- Vérifier la configuration dans `config/session.php`
- La durée de vie du cookie "Remember Me" est gérée par Laravel automatiquement
- Par défaut, Laravel utilise `SESSION_LIFETIME` pour les sessions normales et une durée plus longue pour "Remember Me"

**Sécurité** :
- Le cookie "Remember Me" doit être sécurisé (httpOnly, secure en production)
- Vérifier que les paramètres de sécurité sont corrects dans `config/session.php`

## Notes

- Le champ `remember_token` existe déjà dans la migration `create_users_table.php`
- Laravel gère automatiquement la génération et la validation du token "Remember Me"
- Cette fonctionnalité est essentielle pour l'expérience utilisateur et doit être implémentée tôt
- Pour l'API avec Sanctum, les tokens ont déjà une durée de vie longue, mais cette fonctionnalité améliore l'expérience pour les utilisateurs Livewire

## Review Visuelle

### État Actuel de l'Interface

**Screenshot** : Interface de connexion actuelle (avant implémentation de Remember Me)

L'interface de connexion utilise un style terminal rétro-futuriste avec :
- **Fond** : Fond sombre avec effet de scanlines (style CRT)
- **Messages de statut** : Messages avec préfixes (`[OK]`, `[INFO]`, `[LOAD]`, `[READY]`) en vert et blanc
- **Prompts système** : `SYSTEM@SPACE-XPLORER:~$` en vert pour les commandes
- **Style monospace** : Police monospace pour une ambiance terminal authentique
- **Formulaire** : Boîte avec bordure verte lumineuse et effet de glow
- **Champs** : 
  - Email : `enter_email` avec placeholder `user@domain.com`
  - Password : `enter_password` avec placeholder masqué `••••••••`
- **Bouton** : Bouton vert lumineux `> EXECUTE_LOGIN`
- **Lien d'inscription** : `> REGISTER_NEW_USER` en bleu

**Note** : La checkbox "Se souvenir de moi" n'est pas encore présente dans l'interface. Elle devra s'intégrer harmonieusement entre le champ password et le bouton de connexion.

### Recommandations Design

Voir la review visuelle complète : **[VISUAL-REVIEW-002-remember-me.md](../reviews/VISUAL-REVIEW-002-remember-me.md)**

**Recommandations principales** :

1. **Style Terminal Cohérent** : Utiliser le style terminal avec préfixe `[OPTION]` pour la checkbox
   ```blade
   <span class="ml-2 text-gray-300 dark:text-gray-300 text-sm font-mono">
       [OPTION] Se souvenir de moi
   </span>
   ```

2. **Positionnement** : Placer la checkbox entre le champ password et le bouton de soumission avec espacement `mb-6`

3. **Accessibilité** : Ajouter les attributs ARIA appropriés (`id`, `name`, `aria-label`)

4. **Texte** : Utiliser "Se souvenir de moi" (français) pour être cohérent avec le reste de l'interface

### Code d'Implémentation Proposé

```blade
<!-- Remember Me Checkbox -->
<div class="mb-6">
    <label class="flex items-center cursor-pointer group">
        <input
            type="checkbox"
            wire:model="remember"
            id="remember"
            name="remember"
            aria-label="Se souvenir de moi"
            class="w-4 h-4 text-space-primary bg-surface-dark border-border-dark rounded focus:ring-space-primary focus:ring-2 cursor-pointer transition-colors duration-150"
        >
        <span class="ml-2 text-gray-300 dark:text-gray-300 text-sm font-mono group-hover:text-space-primary transition-colors duration-150">
            [OPTION] Se souvenir de moi
        </span>
    </label>
</div>
```

## Références

- [ARCHITECTURE.md](../memory_bank/ARCHITECTURE.md) - Authentification et sessions
- [PROJECT_BRIEF.md](../memory_bank/PROJECT_BRIEF.md) - Parcours utilisateur
- [Laravel Authentication - Remember Me](https://laravel.com/docs/authentication#remembering-users)
- [VISUAL-REVIEW-002-remember-me.md](../reviews/VISUAL-REVIEW-002-remember-me.md) - Review visuelle complète

## Suivi et Historique

### Statut

En cours

### GitHub

- **Issue GitHub** : [#4](https://github.com/PiLep/space-xplorer/issues/4)
- **Branche** : `issue/002-remember-me`

### Historique

#### 2025-01-XX - Alex (Product) - Création de l'issue
**Statut** : À faire
**Détails** : Issue créée pour améliorer l'expérience utilisateur avec la persistence de connexion
**Notes** : Priorité haute car impact direct sur l'engagement utilisateur
**GitHub** : Issue créée sur GitHub (#4) et branche dédiée créée (`issue/002-remember-me`)

#### 2025-01-XX - Sam (Lead Dev) - Création du plan de développement
**Statut** : En cours
**Détails** : Plan de développement créé (TASK-002). Le plan décompose l'issue en 3 phases avec 8 tâches au total. Estimation totale : ~4h de développement.
**Fichiers modifiés** : 
- docs/tasks/TASK-002-implement-remember-me.md (nouveau)
- docs/issues/ISSUE-002-implement-remember-me.md (mis à jour)
**Notes** : Le champ `remember_token` existe déjà dans la migration users. L'implémentation nécessite des modifications dans AuthService, LoginRequest, LoginTerminal et AuthController.

#### 2025-01-XX - Riley (Designer) - Review visuelle de l'issue
**Statut** : En cours
**Détails** : Review visuelle complète effectuée pour valider l'intégration design de la fonctionnalité "Remember Me". L'interface actuelle a été analysée et des recommandations design ont été fournies.
**Fichiers modifiés** : 
- docs/reviews/VISUAL-REVIEW-002-remember-me.md (nouveau)
- docs/issues/ISSUE-002-implement-remember-me.md (mis à jour avec section Review Visuelle)
**Notes** : 
- ✅ L'issue et le plan sont bien conçus du point de vue design
- ⚠️ Recommandations principales : utiliser le style terminal avec préfixe `[OPTION]`, ajouter les attributs ARIA, standardiser sur "Se souvenir de moi" (français)
- 📸 Screenshot de l'interface actuelle documenté dans la review visuelle
- ✅ Code d'implémentation proposé fourni avec style terminal cohérent

