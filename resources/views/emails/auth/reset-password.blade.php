@component('emails.layouts.base', ['title' => 'Réinitialisation de mot de passe - Stellar', 'preheader' => $preheader ?? 'Réinitialisez votre mot de passe en cliquant sur le lien ci-dessous.'])
    @component('emails.components.header', ['prompt' => 'SYSTEM@STELLAR:~$', 'message' => '[INFO] Password Reset Request', 'type' => 'info'])
    @endcomponent

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="color: #00ff00; font-size: 14px; margin-bottom: 20px; font-family: 'Courier New', monospace; line-height: 1.6;">
                Bonjour,
            </td>
        </tr>
        <tr>
            <td style="color: #00ff00; font-size: 14px; margin-bottom: 20px; font-family: 'Courier New', monospace; line-height: 1.6;">
                Vous avez demandé la réinitialisation de votre mot de passe pour votre compte Stellar.
            </td>
        </tr>
        <tr>
            <td style="color: #00ffff; font-size: 14px; margin-bottom: 20px; font-family: 'Courier New', monospace; line-height: 1.6;">
                [ACTION REQUIRED] Cliquez sur le bouton ci-dessous pour réinitialiser votre mot de passe :
            </td>
        </tr>
    </table>

    @component('emails.components.button', ['url' => $resetUrl, 'text' => '> RESET_PASSWORD'])
    @endcomponent

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="color: #00ff00; font-size: 14px; margin-bottom: 20px; font-family: 'Courier New', monospace; line-height: 1.6;">
                Ou copiez ce lien dans votre navigateur :
            </td>
        </tr>
        <tr>
            <td style="word-break: break-all; font-size: 12px; color: #00ffff; font-family: 'Courier New', monospace; margin-bottom: 20px;">
                {{ $resetUrl }}
            </td>
        </tr>
        <tr>
            <td style="color: #ffaa00; font-size: 12px; margin-top: 20px; font-family: 'Courier New', monospace; line-height: 1.6;">
                [SECURITY] Ce lien expirera dans 60 minutes. Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.
            </td>
        </tr>
    </table>

    @component('emails.components.footer')
    @endcomponent
@endcomponent
