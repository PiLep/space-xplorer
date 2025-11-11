<?php

namespace App\Mail;

use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class ResetPasswordNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $token,
        public string $email
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $appName = config('app.name', 'Stellar');
        $fromAddress = config('mail.from.address');
        $replyTo = config('mail.reply_to.address', $fromAddress);

        return new Envelope(
            subject: 'Réinitialisation de votre mot de passe - ' . $appName,
            replyTo: $replyTo,
        );
    }

    /**
     * Get the message headers.
     */
    public function headers(): Headers
    {
        $emailService = app(EmailService::class);
        $defaultHeaders = $emailService->getDefaultHeaders();

        return new Headers(
            text: $defaultHeaders,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $resetUrl = route('password.reset', [
            'token' => $this->token,
            'email' => $this->email,
        ]);

        $emailService = app(EmailService::class);
        $resetUrl = $emailService->addUtmParameters($resetUrl, 'email', 'email', 'password-reset');

        $preheader = 'Réinitialisez votre mot de passe en cliquant sur le lien ci-dessous.';

        return new Content(
            view: 'emails.auth.reset-password',
            text: 'emails.auth.reset-password-text',
            with: [
                'token' => $this->token,
                'email' => $this->email,
                'resetUrl' => $resetUrl,
                'preheader' => $preheader,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
