<?php

namespace App\Mail;

use App\Models\User;
use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class PasswordResetConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user
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
            subject: 'Votre mot de passe a été réinitialisé - '.$appName,
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
        $preheader = 'Votre mot de passe a été réinitialisé avec succès.';

        return new Content(
            view: 'emails.auth.password-reset-confirmation',
            text: 'emails.auth.password-reset-confirmation-text',
            with: [
                'user' => $this->user,
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
