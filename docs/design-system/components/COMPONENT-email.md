# Email Templates - Design System

## Vue d'Ensemble

Les templates d'email de Space Xplorer sont conçus pour maintenir la cohérence avec l'identité visuelle rétro-futuriste du design system. Ils utilisent le style terminal avec des couleurs fluorescentes et une typographie monospace pour créer une expérience immersive même dans les emails.

## Principes de Design

### Style Terminal
- Typographie monospace (`Courier New`)
- Fond sombre (`#0a0a0a`) avec conteneur (`#1a1a1a`)
- Bordures fluorescentes (`#00ff00` - vert primary)
- Messages avec préfixes système (`[INFO]`, `[SUCCESS]`, `[ERROR]`, etc.)

### Couleurs
- **Primary** : `#00ff00` (Vert fluorescent) - Texte principal, bordures
- **Secondary** : `#00ffff` (Bleu fluorescent) - Messages info, accents
- **Warning** : `#ffaa00` (Orange/Ambre) - Avertissements
- **Background** : `#0a0a0a` (Noir profond)
- **Container** : `#1a1a1a` (Gris foncé)
- **Text Muted** : `#666666` (Gris pour footer)

### Structure Standard

Tous les emails suivent cette structure :

```
┌─────────────────────────────────────┐
│ SYSTEM@SPACE-XPLORER:~$             │
│ [STATUS] Message d'en-tête          │
├─────────────────────────────────────┤
│                                     │
│ Contenu principal                   │
│                                     │
│ [ACTION] Bouton d'action            │
│                                     │
├─────────────────────────────────────┤
│ SYSTEM@SPACE-XPLORER:~$             │
│ Footer                              │
└─────────────────────────────────────┘
```

## Composants d'Email

### Header

En-tête standardisé avec prompt système et message de statut.

```html
<div class="header">
    <div class="prompt">SYSTEM@SPACE-XPLORER:~$</div>
    <div class="message success">[STATUS] Message</div>
</div>
```

### Messages avec Préfixes

Les messages utilisent des préfixes pour indiquer leur type :

- `[INFO]` - Informations générales (vert)
- `[SUCCESS]` - Succès, confirmations (vert)
- `[ERROR]` - Erreurs (rouge)
- `[WARNING]` - Avertissements (orange)
- `[SECURITY]` - Messages de sécurité (orange)
- `[ACTION REQUIRED]` - Actions requises (bleu)

### Boutons d'Action

Boutons avec style terminal pour les actions principales.

```html
<a href="{{ $url }}" class="button">
    > ACTION_NAME
</a>
```

**Styles CSS** :
- Fond : `#00ff00` (vert primary)
- Texte : `#000000` (noir)
- Hover : `#00cc00` (vert foncé)
- Padding : `12px 24px`
- Font : `Courier New`, monospace

### Footer

Footer standardisé avec prompt système et informations de l'application.

```html
<div class="footer">
    <div class="prompt">SYSTEM@SPACE-XPLORER:~$</div>
    <div class="message" style="font-size: 12px; color: #666666;">
        Space Xplorer - Exploration Spatiale Interactive
    </div>
</div>
```

## Templates Disponibles

### Reset Password Notification

**Fichier** : `resources/views/emails/auth/reset-password.blade.php`  
**Mailable** : `App\Mail\ResetPasswordNotification`

**Usage** : Email envoyé lorsqu'un utilisateur demande la réinitialisation de son mot de passe.

**Caractéristiques** :
- Message `[INFO] Password Reset Request`
- Bouton d'action `> RESET_PASSWORD`
- Lien de secours (texte brut)
- Avertissement de sécurité avec expiration (60 minutes)

**Exemple** :
```php
use App\Mail\ResetPasswordNotification;

Mail::to($user->email)->send(
    new ResetPasswordNotification($token, $user->email)
);
```

### Password Reset Confirmation

**Fichier** : `resources/views/emails/auth/password-reset-confirmation.blade.php`  
**Mailable** : `App\Mail\PasswordResetConfirmation`

**Usage** : Email de confirmation envoyé après la réinitialisation réussie du mot de passe.

**Caractéristiques** :
- Message `[SUCCESS] Password Reset Completed`
- Section de recommandations de sécurité
- Message informatif pour la connexion

**Exemple** :
```php
use App\Mail\PasswordResetConfirmation;

Mail::to($user->email)->send(
    new PasswordResetConfirmation($user)
);
```

## Structure CSS Standard

Tous les emails utilisent cette structure CSS de base :

```css
body {
    font-family: 'Courier New', monospace;
    background-color: #0a0a0a;
    color: #00ff00;
    margin: 0;
    padding: 20px;
    line-height: 1.6;
}

.container {
    max-width: 600px;
    margin: 0 auto;
    background-color: #1a1a1a;
    border: 1px solid #00ff00;
    padding: 30px;
}

.header {
    border-bottom: 1px solid #00ff00;
    padding-bottom: 20px;
    margin-bottom: 30px;
}

.prompt {
    color: #00ff00;
    font-size: 14px;
    margin-bottom: 10px;
}

.message {
    color: #00ff00;
    font-size: 14px;
    margin-bottom: 20px;
}

.success {
    color: #00ff00;
}

.info {
    color: #00ffff;
}

.warning {
    color: #ffaa00;
    font-size: 12px;
    margin-top: 20px;
}

.button {
    display: inline-block;
    background-color: #00ff00;
    color: #000000;
    padding: 12px 24px;
    text-decoration: none;
    font-weight: bold;
    margin: 20px 0;
    border: none;
    font-family: 'Courier New', monospace;
}

.button:hover {
    background-color: #00cc00;
}

.footer {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #00ff00;
    font-size: 12px;
    color: #666666;
}
```

## Bonnes Pratiques

### Compatibilité Email

- Utiliser des styles inline pour une meilleure compatibilité
- Éviter les CSS complexes (flexbox, grid)
- Tester sur plusieurs clients email (Gmail, Outlook, Apple Mail)
- Utiliser des tableaux pour la mise en page si nécessaire

### Accessibilité

- Contraste suffisant entre texte et fond
- Tailles de police lisibles (minimum 12px)
- Liens clairs et descriptifs
- Alternative texte pour les boutons (lien de secours)

### Sécurité

- Ne jamais inclure de tokens ou secrets dans les emails
- Utiliser des liens sécurisés (HTTPS)
- Inclure des avertissements de sécurité pour les actions sensibles
- Mentionner l'expiration des liens

### Responsive

- Largeur maximale de 600px pour le conteneur
- Padding adaptatif pour mobile (20px)
- Boutons avec taille minimale pour le touch (44x44px recommandé)

## Exemple de Template Complet

```html
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Titre de l'Email - Space Xplorer</title>
    <style>
        /* Styles CSS standard */
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="prompt">SYSTEM@SPACE-XPLORER:~$</div>
            <div class="message success">[STATUS] Message d'en-tête</div>
        </div>

        <div class="message">
            Contenu principal de l'email...
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $actionUrl }}" class="button">
                > ACTION_NAME
            </a>
        </div>

        <div class="footer">
            <div class="prompt">SYSTEM@SPACE-XPLORER:~$</div>
            <div class="message" style="font-size: 12px; color: #666666;">
                Space Xplorer - Exploration Spatiale Interactive
            </div>
        </div>
    </div>
</body>
</html>
```

## Checklist de Création d'Email

- [ ] Le template utilise le style terminal cohérent
- [ ] Les couleurs respectent la palette du design system
- [ ] Le header contient le prompt système et le statut
- [ ] Les messages utilisent les préfixes appropriés
- [ ] Le bouton d'action suit le style standard
- [ ] Le footer est présent avec le prompt système
- [ ] Les styles sont inline pour la compatibilité
- [ ] Le template est responsive (max-width 600px)
- [ ] Les liens de secours sont fournis pour les boutons
- [ ] Les avertissements de sécurité sont présents si nécessaire
- [ ] Le template a été testé sur plusieurs clients email

## Notes Techniques

### Compatibilité Email

Les clients email ont des limitations importantes :
- Pas de support pour les CSS externes
- Support limité pour les CSS avancés (flexbox, grid)
- Styles inline recommandés pour une meilleure compatibilité
- Tableaux HTML parfois nécessaires pour la mise en page

### Testing

- Utiliser des outils comme Litmus ou Email on Acid
- Tester sur Gmail (web et mobile)
- Tester sur Outlook (versions récentes et anciennes)
- Tester sur Apple Mail
- Vérifier le rendu sur mobile

---

**Référence** : Voir **[DESIGN-SYSTEM.md](../DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

