@extends('layouts.app')

@section('title', 'Design System - Components - Emails')

@section('content')
<x-design-system.layout>
    <section>
        <div class="mb-6">
            <a href="{{ route('design-system.components') }}" class="text-space-secondary hover:text-space-secondary-light dark:text-space-secondary dark:hover:text-space-secondary-light font-mono text-sm mb-4 inline-block">
                ← Retour aux composants
            </a>
            <h2 class="text-3xl font-bold text-white mb-2 font-mono">TEMPLATES_EMAIL</h2>
            <p class="text-gray-600 dark:text-gray-400">
                Templates d'email avec style terminal pour maintenir la cohérence du design system
            </p>
        </div>
        
        <div class="space-y-8">
            <!-- Reset Password Notification -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Reset Password Notification</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    Email envoyé lorsqu'un utilisateur demande la réinitialisation de son mot de passe. Inclut un bouton d'action, un lien de secours et un avertissement de sécurité.
                </p>
                <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                    <div class="bg-gray-100 dark:bg-space-black rounded-lg p-4 overflow-x-auto">
                        <div style="max-width: 600px; margin: 0 auto; background-color: #1a1a1a; border: 1px solid #00ff00; padding: 30px; font-family: 'Courier New', monospace;">
                            <div style="border-bottom: 1px solid #00ff00; padding-bottom: 20px; margin-bottom: 30px;">
                                <div style="color: #00ff00; font-size: 14px; margin-bottom: 10px;">SYSTEM@STELLAR:~$</div>
                                <div style="color: #00ff00; font-size: 14px; margin-bottom: 20px;">[INFO] Password Reset Request</div>
                            </div>

                            <div style="color: #00ff00; font-size: 14px; margin-bottom: 20px;">
                                Bonjour,
                            </div>

                            <div style="color: #00ff00; font-size: 14px; margin-bottom: 20px;">
                                Vous avez demandé la réinitialisation de votre mot de passe pour votre compte Stellar.
                            </div>

                            <div style="color: #00ffff; font-size: 14px; margin-bottom: 20px;">
                                [ACTION REQUIRED] Cliquez sur le bouton ci-dessous pour réinitialiser votre mot de passe :
                            </div>

                            <div style="text-align: center; margin: 30px 0;">
                                <a href="#" style="display: inline-block; background-color: #00ff00; color: #000000; padding: 12px 24px; text-decoration: none; font-weight: bold; font-family: 'Courier New', monospace;">
                                    > RESET_PASSWORD
                                </a>
                            </div>

                            <div style="color: #00ff00; font-size: 14px; margin-bottom: 20px;">
                                Ou copiez ce lien dans votre navigateur :
                            </div>

                            <div style="word-break: break-all; font-size: 12px; color: #00ffff; margin-bottom: 20px;">
                                https://stellar-game.app/reset-password?token=example&email=user@example.com
                            </div>

                            <div style="color: #ffaa00; font-size: 12px; margin-top: 20px;">
                                [SECURITY] Ce lien expirera dans 60 minutes. Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.
                            </div>

                            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #00ff00; font-size: 12px; color: #666666;">
                                <div style="color: #00ff00; font-size: 14px; margin-bottom: 10px;">SYSTEM@STELLAR:~$</div>
                                <div style="font-size: 12px; color: #666666;">
                                    Stellar - Exploration Spatiale Interactive
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                        <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                            use App\Mail\ResetPasswordNotification;<br>
                            Mail::to($user->email)->send(<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;new ResetPasswordNotification($token, $user->email)<br>
                            );
                        </code>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-2 mb-2">Fichier :</p>
                        <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                            resources/views/emails/auth/reset-password.blade.php
                        </code>
                    </div>
                </div>
            </div>

            <!-- Password Reset Confirmation -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Password Reset Confirmation</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                    Email de confirmation envoyé après la réinitialisation réussie du mot de passe. Inclut des recommandations de sécurité.
                </p>
                <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                    <div class="bg-gray-100 dark:bg-space-black rounded-lg p-4 overflow-x-auto">
                        <div style="max-width: 600px; margin: 0 auto; background-color: #1a1a1a; border: 1px solid #00ff00; padding: 30px; font-family: 'Courier New', monospace;">
                            <div style="border-bottom: 1px solid #00ff00; padding-bottom: 20px; margin-bottom: 30px;">
                                <div style="color: #00ff00; font-size: 14px; margin-bottom: 10px;">SYSTEM@STELLAR:~$</div>
                                <div style="color: #00ff00; font-size: 14px; margin-bottom: 20px;">[SUCCESS] Password Reset Completed</div>
                            </div>

                            <div style="color: #00ff00; font-size: 14px; margin-bottom: 20px;">
                                Bonjour John Doe,
                            </div>

                            <div style="color: #00ff00; font-size: 14px; margin-bottom: 20px;">
                                [CONFIRMATION] Votre mot de passe a été réinitialisé avec succès.
                            </div>

                            <div style="color: #00ff00; font-size: 14px; margin-bottom: 20px;">
                                Si vous n'avez pas effectué cette action, veuillez nous contacter immédiatement.
                            </div>

                            <div style="background-color: #0a0a0a; border-left: 3px solid #00ffff; padding: 15px; margin: 20px 0;">
                                <h3 style="color: #00ffff; font-size: 14px; margin-top: 0;">[SECURITY] Recommandations de sécurité :</h3>
                                <ul style="margin: 10px 0; padding-left: 20px;">
                                    <li style="margin: 5px 0; font-size: 12px; color: #00ff00;">Utilisez un mot de passe unique et fort</li>
                                    <li style="margin: 5px 0; font-size: 12px; color: #00ff00;">Ne partagez jamais votre mot de passe</li>
                                    <li style="margin: 5px 0; font-size: 12px; color: #00ff00;">Changez régulièrement votre mot de passe</li>
                                    <li style="margin: 5px 0; font-size: 12px; color: #00ff00;">Activez l'authentification à deux facteurs si disponible</li>
                                </ul>
                            </div>

                            <div style="color: #00ffff; font-size: 14px; margin-bottom: 20px;">
                                [INFO] Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.
                            </div>

                            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #00ff00; font-size: 12px; color: #666666;">
                                <div style="color: #00ff00; font-size: 14px; margin-bottom: 10px;">SYSTEM@STELLAR:~$</div>
                                <div style="font-size: 12px; color: #666666;">
                                    Stellar - Exploration Spatiale Interactive
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">Usage :</p>
                        <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                            use App\Mail\PasswordResetConfirmation;<br>
                            Mail::to($user->email)->send(<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;new PasswordResetConfirmation($user)<br>
                            );
                        </code>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-2 mb-2">Fichier :</p>
                        <code class="text-xs text-space-primary bg-space-black px-2 py-1 rounded block font-mono">
                            resources/views/emails/auth/password-reset-confirmation.blade.php
                        </code>
                    </div>
                </div>
            </div>

            <!-- Structure et Principes -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Structure et Principes</h3>
                <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 dark:text-glow-subtle font-mono">Style Terminal</h4>
                            <ul class="text-gray-600 dark:text-gray-400 text-sm space-y-2 list-disc list-inside">
                                <li>Typographie monospace (Courier New)</li>
                                <li>Fond sombre (#0a0a0a) avec conteneur (#1a1a1a)</li>
                                <li>Bordures fluorescentes (#00ff00 - vert primary)</li>
                                <li>Messages avec préfixes système ([INFO], [SUCCESS], [ERROR], etc.)</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 dark:text-glow-subtle font-mono">Préfixes de Messages</h4>
                            <div class="grid md:grid-cols-2 gap-2 text-sm font-mono text-gray-900 dark:text-gray-300">
                                <div><span class="text-space-primary">[INFO]</span> - Informations générales</div>
                                <div><span class="text-space-primary">[SUCCESS]</span> - Succès, confirmations</div>
                                <div><span class="text-error">[ERROR]</span> - Erreurs</div>
                                <div><span class="text-warning">[WARNING]</span> - Avertissements</div>
                                <div><span class="text-warning">[SECURITY]</span> - Messages de sécurité</div>
                                <div><span class="text-space-secondary">[ACTION REQUIRED]</span> - Actions requises</div>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 dark:text-glow-subtle font-mono">Composants Standard</h4>
                            <ul class="text-gray-600 dark:text-gray-400 text-sm space-y-2 list-disc list-inside">
                                <li><strong>Header</strong> : Prompt système + message de statut</li>
                                <li><strong>Contenu</strong> : Messages avec préfixes appropriés</li>
                                <li><strong>Bouton d'action</strong> : Style terminal avec format > ACTION_NAME</li>
                                <li><strong>Footer</strong> : Prompt système + informations de l'application</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bonnes Pratiques -->
            <div>
                <h3 class="text-xl font-semibold text-white mb-4 font-mono">Bonnes Pratiques</h3>
                <div class="bg-white dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded-lg p-6 terminal-border-simple">
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 dark:text-glow-subtle font-mono">Compatibilité Email</h4>
                            <ul class="text-gray-600 dark:text-gray-400 text-sm space-y-2 list-disc list-inside">
                                <li>Utiliser des styles inline pour une meilleure compatibilité</li>
                                <li>Éviter les CSS complexes (flexbox, grid)</li>
                                <li>Tester sur plusieurs clients email (Gmail, Outlook, Apple Mail)</li>
                                <li>Largeur maximale de 600px pour le conteneur</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 dark:text-glow-subtle font-mono">Sécurité</h4>
                            <ul class="text-gray-600 dark:text-gray-400 text-sm space-y-2 list-disc list-inside">
                                <li>Ne jamais inclure de tokens ou secrets dans les emails</li>
                                <li>Utiliser des liens sécurisés (HTTPS)</li>
                                <li>Inclure des avertissements de sécurité pour les actions sensibles</li>
                                <li>Mentionner l'expiration des liens</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 dark:text-glow-subtle font-mono">Accessibilité</h4>
                            <ul class="text-gray-600 dark:text-gray-400 text-sm space-y-2 list-disc list-inside">
                                <li>Contraste suffisant entre texte et fond</li>
                                <li>Tailles de police lisibles (minimum 12px)</li>
                                <li>Liens clairs et descriptifs</li>
                                <li>Alternative texte pour les boutons (lien de secours)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-design-system.layout>
@endsection

