<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de mot de passe - Stellar</title>
    <style>
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
        .warning {
            color: #ffaa00;
            font-size: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="prompt">SYSTEM@STELLAR:~$</div>
            <div class="message success">[INFO] Password Reset Request</div>
        </div>

        <div class="message">
            Bonjour,
        </div>

        <div class="message">
            Vous avez demandé la réinitialisation de votre mot de passe pour votre compte Stellar.
        </div>

        <div class="message info">
            [ACTION REQUIRED] Cliquez sur le bouton ci-dessous pour réinitialiser votre mot de passe :
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetUrl }}" class="button">
                > RESET_PASSWORD
            </a>
        </div>

        <div class="message">
            Ou copiez ce lien dans votre navigateur :
        </div>

        <div class="message" style="word-break: break-all; font-size: 12px; color: #00ffff;">
            {{ $resetUrl }}
        </div>

        <div class="warning">
            [SECURITY] Ce lien expirera dans 60 minutes. Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.
        </div>

        <div class="footer">
            <div class="prompt">SYSTEM@STELLAR:~$</div>
            <div class="message" style="font-size: 12px; color: #666666;">
                Stellar - Exploration Spatiale Interactive
            </div>
        </div>
    </div>
</body>
</html>

