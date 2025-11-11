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

class EmailVerificationNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $code,
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
            subject: 'Vérifiez votre adresse email - '.$appName,
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
        $verificationUrl = route('email.verify');
        $emailService = app(EmailService::class);
        $verificationUrl = $emailService->addUtmParameters($verificationUrl, 'email', 'email', 'email-verification');

        $preheader = 'Vérifiez votre adresse email avec le code à 6 chiffres ci-dessous.';

        return new Content(
            view: 'emails.auth.verify-email',
            text: 'emails.auth.verify-email-text',
            with: [
                'code' => $this->code,
                'user' => $this->user,
                'verificationUrl' => $verificationUrl,
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
