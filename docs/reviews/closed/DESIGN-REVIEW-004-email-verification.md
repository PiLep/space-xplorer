# DESIGN-REVIEW-004 : Vérification d'email par code

## Issue Associée

[ISSUE-004-implement-email-verification.md](../issues/ISSUE-004-implement-email-verification.md)

## Plan Associé

[TASK-004-implement-email-verification.md](../tasks/TASK-004-implement-email-verification.md)

## Type de Review

**Review Design Anticipée** - Analyse de l'issue et du plan avant implémentation pour valider les aspects design/UX

## Statut

✅ **Approuvé avec recommandations UX**

## Vue d'Ensemble

L'issue et le plan sont globalement bien structurés et cohérents avec l'identité visuelle du projet. La fonctionnalité de vérification d'email par code à 6 chiffres s'intègre bien dans le flux utilisateur existant. Cependant, j'ai identifié plusieurs points d'amélioration UX et quelques recommandations pour garantir une expérience utilisateur optimale et cohérente avec le style terminal.

## Points Positifs

### ✅ Cohérence avec le Style Terminal

- Le plan mentionne explicitement le "style terminal" cohérent avec le reste de l'application
- L'approche par code à 6 chiffres est plus adaptée au style terminal qu'un lien de vérification
- La structure proposée suit le même pattern que les pages `LoginTerminal` et `ForgotPassword`

### ✅ Sécurité et Limitations

- Les limitations de tentatives (5 max) et de renvois (2 min) sont bien pensées
- L'expiration après 15 minutes est raisonnable
- Le hashage des codes est correctement prévu

### ✅ Flux Utilisateur

- La redirection après inscription vers la vérification est logique
- La redirection lors de la connexion si email non vérifié est cohérente
- Le flux est clair et progressif

## Recommandations UX et Design

### 1. Formatage Automatique du Code ⚠️

**Problème** : Le plan mentionne "formatage automatique si possible" mais ne précise pas l'implémentation.

**Recommandation** :
- Implémenter un champ de saisie avec formatage automatique en 6 cases séparées (style OTP)
- Alternative : Un seul champ avec auto-séparation visuelle (espaces automatiques)
- Utiliser `inputmode="numeric"` et `pattern="[0-9]*"` pour les claviers mobiles
- Auto-focus sur le champ au chargement de la page
- Auto-soumission après saisie de 6 chiffres (optionnel mais améliorerait l'UX)

**Impact** : Améliore significativement l'expérience utilisateur, surtout sur mobile

### 2. Messages d'Information et Instructions ⚠️

**Problème** : Le plan mentionne "message d'information expliquant qu'un code a été envoyé" mais ne précise pas le contenu exact.

**Recommandation** :
- Message clair avec le style terminal : `[INFO] Verification code sent to {email}. Check your inbox.`
- Instructions explicites : `[INFO] Enter the 6-digit code from your email below.`
- Mentionner le délai d'expiration : `[INFO] Code expires in 15 minutes.`
- Si l'email est masqué partiellement pour la sécurité : `[INFO] Code sent to j***@example.com`

**Impact** : Réduit la confusion et guide mieux l'utilisateur

### 3. Feedback Visuel des Tentatives ⚠️

**Problème** : Le plan mentionne "affichage des tentatives restantes" mais ne précise pas le format.

**Recommandation** :
- Afficher de manière proéminente : `[WARNING] {X} verification attempts remaining`
- Changer la couleur selon le nombre restant :
  - 5-3 tentatives : couleur normale (info)
  - 2 tentatives : couleur warning (jaune/orange)
  - 1 tentative : couleur error (rouge)
- Afficher après chaque tentative incorrecte : `[ERROR] Invalid code. {X} attempts remaining.`

**Impact** : Permet à l'utilisateur de comprendre l'urgence et l'état de ses tentatives

### 4. Compteur de Cooldown pour le Renvoi ⚠️

**Problème** : Le plan mentionne "indication du temps restant avant expiration du code (optionnel)" mais ne mentionne pas le cooldown de renvoi.

**Recommandation** :
- Afficher un compteur en temps réel : `[INFO] Resend available in {X} seconds`
- Désactiver le bouton "Renvoyer" pendant le cooldown avec un style visuel différent
- Utiliser Alpine.js ou Livewire polling pour mettre à jour le compteur en temps réel
- Message après renvoi : `[SUCCESS] New verification code sent. Check your email.`

**Impact** : Évite la frustration et guide l'utilisateur sur quand il peut renvoyer

### 5. Messages d'Erreur Spécifiques ⚠️

**Problème** : Le plan mentionne "messages d'erreur clairs" mais ne liste pas tous les cas.

**Recommandation** :
- Code incorrect : `[ERROR] Invalid verification code. Please check and try again.`
- Code expiré : `[ERROR] Verification code has expired. Please request a new code.`
- Tentatives dépassées : `[ERROR] Maximum verification attempts exceeded. Please request a new code.`
- Email déjà vérifié : `[INFO] Email already verified. Redirecting to dashboard...`
- Erreur d'envoi : `[ERROR] Failed to send verification code. Please try again later.`

**Impact** : Aide l'utilisateur à comprendre exactement ce qui s'est passé et comment résoudre le problème

### 6. Design du Champ de Code ⚠️

**Problème** : Le plan ne précise pas le design exact du champ de saisie.

**Recommandation** :
- Utiliser le composant `x-form-input` avec `variant="terminal"` comme les autres pages
- Placeholder : `000000` ou `------` pour indiquer le format attendu
- Style cohérent avec les autres champs (bordure terminal, scanlines, etc.)
- Taille de police légèrement plus grande pour le code (plus facile à lire)
- Centrer le texte dans le champ pour un meilleur impact visuel

**Impact** : Cohérence visuelle et meilleure lisibilité

### 7. Message de Succès et Redirection ⚠️

**Problème** : Le plan mentionne "message de succès après vérification avec redirection automatique" mais ne précise pas le timing.

**Recommandation** :
- Message de succès : `[SUCCESS] Email verified successfully. Welcome aboard, {name}!`
- Redirection après 2-3 secondes (donner le temps de lire le message)
- Optionnel : Animation de transition avant redirection
- Utiliser le même style de message que les autres pages (terminal-message)

**Impact** : Confirmation claire de la réussite avant redirection

### 8. État Visuel du Bouton "Renvoyer" ⚠️

**Problème** : Le plan ne précise pas l'état visuel du bouton pendant le cooldown.

**Recommandation** :
- État normal : Bouton actif avec style terminal standard
- État cooldown : Bouton désactivé avec style `disabled` et texte du compteur
- Utiliser le composant `x-button` avec `variant="secondary"` et `disabled` pendant le cooldown
- Texte du bouton : `> RESEND_CODE` (normal) ou `> RESEND_CODE (available in {X}s)` (cooldown)

**Impact** : Feedback visuel clair sur l'état du bouton

### 9. Gestion de l'Email Masqué ⚠️

**Problème** : Le plan ne mentionne pas si l'email doit être affiché ou masqué sur la page de vérification.

**Recommandation** :
- Afficher l'email partiellement masqué : `j***@example.com` pour sécurité/privacy
- Permettre à l'utilisateur de voir l'email complet si nécessaire (bouton "Show email")
- Message : `[INFO] Verification code sent to {masked_email}`

**Impact** : Équilibre entre sécurité/privacy et clarté

### 10. Accessibilité ⚠️

**Problème** : Le plan ne mentionne pas explicitement l'accessibilité.

**Recommandation** :
- Labels ARIA appropriés pour le champ de code
- Messages d'erreur avec `role="alert"` pour les lecteurs d'écran
- Contraste suffisant pour tous les messages (vérifier avec les couleurs du design system)
- Navigation au clavier fonctionnelle (Tab, Enter)
- Focus visible sur tous les éléments interactifs

**Impact** : Accessibilité pour tous les utilisateurs

## Structure de la Page Recommandée

Basée sur les pages existantes (`LoginTerminal`, `ForgotPassword`), voici la structure recommandée :

```blade
<x-container variant="compact" class="mt-8 font-mono">
    <!-- Terminal Header -->
    <div class="mb-6">
        <x-terminal-prompt command="init_email_verification" />
        <x-terminal-message
            message="[INFO] A verification code has been sent to your email"
            :marginBottom="''"
        />
        <x-terminal-message
            message="[INFO] Enter the 6-digit code below to verify your email address"
            :marginBottom="''"
        />
    </div>

    <!-- Terminal Interface -->
    <div class="dark:bg-surface-dark terminal-border-simple scan-effect overflow-hidden rounded-lg bg-white">
        <div class="p-8">
            <!-- Status Message -->
            @if ($status)
                <x-terminal-message
                    :message="$status"
                    marginBottom="mb-6"
                />
            @endif

            <!-- Email Display (masked) -->
            <div class="mb-6">
                <x-terminal-message
                    message="[INFO] Code sent to: {masked_email}"
                    :marginBottom="''"
                />
            </div>

            <!-- Code Input Form -->
            <form wire:submit="verify">
                <!-- Code Input -->
                <x-form-input
                    type="text"
                    name="code"
                    label="enter_verification_code"
                    wireModel="code"
                    placeholder="000000"
                    variant="terminal"
                    autofocus
                    inputmode="numeric"
                    pattern="[0-9]*"
                    maxlength="6"
                    marginBottom="mb-4"
                />

                <!-- Attempts Remaining -->
                @if ($attemptsRemaining < 5)
                    <x-terminal-message
                        :message="'[WARNING] ' . $attemptsRemaining . ' verification attempts remaining'"
                        marginBottom="mb-4"
                    />
                @endif

                <!-- Submit Command -->
                <div class="mt-8">
                    <x-terminal-prompt command="verify_email" />
                    <x-button
                        type="submit"
                        variant="primary"
                        size="lg"
                        wireLoading="verify"
                        wireLoadingText="[PROCESSING] Verifying code..."
                        terminal
                    >
                        > VERIFY_CODE
                    </x-button>
                </div>
            </form>

            <!-- Resend Code Section -->
            <div class="mt-6">
                <x-terminal-message message="[INFO] Didn't receive the code?" />
                @if ($canResend)
                    <form wire:submit="resend" class="mt-2">
                        <x-button
                            type="submit"
                            variant="secondary"
                            size="md"
                            wireLoading="resend"
                            wireLoadingText="[PROCESSING] Sending new code..."
                            terminal
                        >
                            > RESEND_CODE
                        </x-button>
                    </form>
                @else
                    <x-terminal-message
                        :message="'[INFO] Resend available in ' . $resendCooldown . ' seconds'"
                        marginTop="mt-2"
                    />
                @endif
            </div>
        </div>
    </div>
</x-container>
```

## Design de l'Email

### Recommandations pour le Template Email

**Structure recommandée** :
- Header cohérent avec les autres emails (utiliser `EmailService` si disponible)
- Message d'accueil clair et professionnel
- Code affiché de manière très proéminente (grande taille, police monospace, centré)
- Instructions claires sur où saisir le code
- Lien vers la page de vérification (optionnel mais utile)
- Footer avec informations de sécurité

**Exemple de code dans l'email** :
```
Your verification code is:

    ┌─────────────┐
    │   123456    │
    └─────────────┘

Enter this code on the verification page to complete your registration.
```

**Style** :
- Code en grande taille (24-32px)
- Police monospace pour le code
- Bordure ou background pour le mettre en évidence
- Couleur cohérente avec le design system (accent color)

## Points d'Attention pour l'Implémentation

### 1. Cohérence avec les Autres Pages

- Utiliser les mêmes composants (`x-terminal-prompt`, `x-terminal-message`, `x-form-input`, `x-button`)
- Respecter la même structure de layout (`x-container`, `variant="compact"`)
- Utiliser les mêmes classes CSS et variantes

### 2. Responsive Design

- S'assurer que la page fonctionne bien sur mobile
- Le champ de code doit être facilement accessible sur mobile
- Les messages doivent être lisibles sur petits écrans

### 3. Performance

- Le polling pour le cooldown ne doit pas être trop fréquent (1 seconde max)
- Éviter les animations lourdes qui pourraient ralentir la page

### 4. Tests Visuels

- Tester avec différents codes (valides, invalides, expirés)
- Tester le cooldown visuellement
- Tester les messages d'erreur dans tous les cas
- Tester sur différents appareils et tailles d'écran

## Conclusion

L'issue et le plan sont solides et bien structurés. Les recommandations ci-dessus visent à améliorer l'expérience utilisateur et garantir une cohérence visuelle parfaite avec le reste de l'application. La plupart des points sont des détails d'implémentation qui peuvent être ajoutés lors du développement, mais il est important de les avoir en tête dès le départ.

**Priorité des recommandations** :
1. **Haute priorité** : Formatage automatique du code, messages d'erreur spécifiques, feedback visuel des tentatives
2. **Moyenne priorité** : Compteur de cooldown, design du champ de code, message de succès
3. **Basse priorité** : Email masqué, accessibilité (mais toujours importante)

## Prochaines Étapes

1. ✅ Cette review design est approuvée avec les recommandations ci-dessus
2. ⏳ Attendre l'implémentation par le Fullstack Developer (Jordan)
3. 🔄 Faire une review visuelle complète après l'implémentation avec Chrome DevTools MCP
4. 📸 Prendre des screenshots de la page de vérification pour documentation

---

**Review effectuée par** : Riley (Agent Designer)  
**Statut** : ✅ Approuvé avec recommandations UX

