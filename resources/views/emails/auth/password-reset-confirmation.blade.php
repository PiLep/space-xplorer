@component('emails.layouts.base', ['title' => 'Mot de passe réinitialisé - Stellar', 'preheader' => $preheader ?? 'Votre mot de passe a été réinitialisé avec succès.'])
    @component('emails.components.header', ['prompt' => 'SYSTEM@STELLAR:~$', 'message' => '[SUCCESS] Password Reset Completed', 'type' => 'success'])
    @endcomponent

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="color: #00ff00; font-size: 14px; margin-bottom: 20px; font-family: 'Courier New', monospace; line-height: 1.6;">
                Bonjour {{ $user->name }},
            </td>
        </tr>
        <tr>
            <td style="color: #00ff00; font-size: 14px; margin-bottom: 20px; font-family: 'Courier New', monospace; line-height: 1.6;">
                [CONFIRMATION] Votre mot de passe a été réinitialisé avec succès.
            </td>
        </tr>
        <tr>
            <td style="color: #00ff00; font-size: 14px; margin-bottom: 20px; font-family: 'Courier New', monospace; line-height: 1.6;">
                Si vous n'avez pas effectué cette action, veuillez nous contacter immédiatement.
            </td>
        </tr>
    </table>

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #0a0a0a; border-left: 3px solid #00ffff; margin: 20px 0;">
        <tr>
            <td style="padding: 15px;">
                <div style="color: #00ffff; font-size: 14px; margin-top: 0; margin-bottom: 10px; font-family: 'Courier New', monospace; font-weight: bold;">
                    [SECURITY] Recommandations de sécurité :
                </div>
                <ul style="margin: 10px 0; padding-left: 20px; color: #00ff00; font-size: 12px; font-family: 'Courier New', monospace; line-height: 1.6;">
                    <li style="margin: 5px 0;">Utilisez un mot de passe unique et fort</li>
                    <li style="margin: 5px 0;">Ne partagez jamais votre mot de passe</li>
                    <li style="margin: 5px 0;">Changez régulièrement votre mot de passe</li>
                    <li style="margin: 5px 0;">Activez l'authentification à deux facteurs si disponible</li>
                </ul>
            </td>
        </tr>
    </table>

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="color: #00ffff; font-size: 14px; font-family: 'Courier New', monospace; line-height: 1.6;">
                [INFO] Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.
            </td>
        </tr>
    </table>

    @component('emails.components.footer')
    @endcomponent
@endcomponent
