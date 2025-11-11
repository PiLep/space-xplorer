<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe réinitialisé - Stellar</title>
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
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #00ff00;
            font-size: 12px;
            color: #666666;
        }
        .recommendations {
            background-color: #0a0a0a;
            border-left: 3px solid #00ffff;
            padding: 15px;
            margin: 20px 0;
        }
        .recommendations h3 {
            color: #00ffff;
            font-size: 14px;
            margin-top: 0;
        }
        .recommendations ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .recommendations li {
            margin: 5px 0;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="prompt">SYSTEM@STELLAR:~$</div>
            <div class="message success">[SUCCESS] Password Reset Completed</div>
        </div>

        <div class="message">
            Bonjour {{ $user->name }},
        </div>

        <div class="message success">
            [CONFIRMATION] Votre mot de passe a été réinitialisé avec succès.
        </div>

        <div class="message">
            Si vous n'avez pas effectué cette action, veuillez nous contacter immédiatement.
        </div>

        <div class="recommendations">
            <h3>[SECURITY] Recommandations de sécurité :</h3>
            <ul>
                <li>Utilisez un mot de passe unique et fort</li>
                <li>Ne partagez jamais votre mot de passe</li>
                <li>Changez régulièrement votre mot de passe</li>
                <li>Activez l'authentification à deux facteurs si disponible</li>
            </ul>
        </div>

        <div class="message info">
            [INFO] Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.
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

