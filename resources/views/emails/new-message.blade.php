<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau message - Stellar</title>
</head>
<body style="font-family: 'Courier New', monospace; background-color: #0a0a0a; color: #ffffff; padding: 20px; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #1a1a1a; border: 1px solid #333333; padding: 30px;">
        <!-- Header -->
        <div style="border-bottom: 1px solid #333333; padding-bottom: 20px; margin-bottom: 20px;">
            <div style="color: #00ff88; font-size: 18px; font-weight: bold; margin-bottom: 5px;">
                SYSTEM@STELLAR:~$
            </div>
            <div style="color: #00aaff; font-size: 14px;">
                [INFO] Nouveau message reçu
            </div>
        </div>

        <!-- Content -->
        <div style="margin-bottom: 20px;">
            <p style="color: #ffffff; margin-bottom: 15px;">
                Explorateur {{ $user->matricule }},
            </p>

            <p style="color: #ffffff; margin-bottom: 15px;">
                Vous avez reçu un nouveau message dans votre inbox Stellar.
            </p>

            <div style="background-color: #0a0a0a; border: 1px solid #333333; padding: 15px; margin: 20px 0;">
                <div style="color: #00ff88; font-weight: bold; margin-bottom: 10px;">
                    {{ $inboxMessage->subject }}
                </div>
                <div style="color: #666666; font-size: 12px;">
                    Type: {{ strtoupper($inboxMessage->type) }} | Date: {{ $inboxMessage->created_at->format('Y-m-d H:i:s') }}
                </div>
            </div>

            <p style="color: #ffffff; margin-bottom: 20px;">
                Connectez-vous à votre terminal pour consulter le message.
            </p>
        </div>

        <!-- CTA Button -->
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $inboxUrl }}" style="display: inline-block; background-color: #00ff88; color: #0a0a0a; padding: 12px 24px; text-decoration: none; font-weight: bold; border-radius: 4px;">
                Accéder à l'inbox
            </a>
        </div>

        <!-- Footer -->
        <div style="border-top: 1px solid #333333; padding-top: 20px; margin-top: 20px; color: #666666; font-size: 12px; text-align: center;">
            <div style="margin-bottom: 5px;">
                STELLAR CORPORATION
            </div>
            <div>
                Division Exploration - Système automatisé de communication
            </div>
        </div>
    </div>
</body>
</html>

