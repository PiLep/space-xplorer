@component('emails.layouts.base', ['title' => 'Vérification d\'email - Stellar', 'preheader' => $preheader ?? 'Vérifiez votre adresse email avec le code à 6 chiffres ci-dessous.'])
    @component('emails.components.header', ['prompt' => 'SYSTEM@STELLAR:~$', 'message' => '[INFO] Email Verification', 'type' => 'info'])
    @endcomponent

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="color: #00ff00; font-size: 14px; margin-bottom: 20px; font-family: 'Courier New', monospace; line-height: 1.6;">
                Bonjour {{ $user->name }},
            </td>
        </tr>
        <tr>
            <td style="color: #00ff00; font-size: 14px; margin-bottom: 20px; font-family: 'Courier New', monospace; line-height: 1.6;">
                Bienvenue sur Stellar ! Pour compléter votre inscription, veuillez vérifier votre adresse email.
            </td>
        </tr>
        <tr>
            <td style="color: #00ffff; font-size: 14px; margin-bottom: 20px; font-family: 'Courier New', monospace; line-height: 1.6;">
                [ACTION REQUIRED] Utilisez le code de vérification ci-dessous :
            </td>
        </tr>
    </table>

    <!-- Code Display -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 30px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="border: 2px solid #00ff00; border-radius: 4px; padding: 20px; background-color: #001100;">
                    <tr>
                        <td align="center" style="font-size: 32px; font-weight: bold; color: #00ff00; font-family: 'Courier New', monospace; letter-spacing: 8px;">
                            {{ $code }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style="color: #00ff00; font-size: 14px; margin-bottom: 20px; font-family: 'Courier New', monospace; line-height: 1.6;">
                Entrez ce code sur la page de vérification pour compléter votre inscription.
            </td>
        </tr>
        <tr>
            <td style="margin-top: 20px;">
                @component('emails.components.button', ['url' => $verificationUrl, 'text' => '> VERIFY_EMAIL'])
                @endcomponent
            </td>
        </tr>
        <tr>
            <td style="color: #00ff00; font-size: 14px; margin-top: 20px; font-family: 'Courier New', monospace; line-height: 1.6;">
                Ou copiez ce lien dans votre navigateur :
            </td>
        </tr>
        <tr>
            <td style="word-break: break-all; font-size: 12px; color: #00ffff; font-family: 'Courier New', monospace; margin-bottom: 20px;">
                {{ $verificationUrl }}
            </td>
        </tr>
        <tr>
            <td style="color: #ffaa00; font-size: 12px; margin-top: 20px; font-family: 'Courier New', monospace; line-height: 1.6;">
                [SECURITY] Ce code expirera dans 15 minutes. Si vous n'avez pas créé de compte, ignorez cet email.
            </td>
        </tr>
    </table>

    @component('emails.components.footer')
    @endcomponent
@endcomponent



