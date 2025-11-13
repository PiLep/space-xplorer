<?php

namespace App\Mail;

use App\Models\Message;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class NewMessageNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Message $message,
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
            subject: '[STELLAR] Nouveau message : '.$this->message->subject,
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
        return new Content(
            view: 'emails.new-message',
            with: [
                'inboxMessage' => $this->message,
                'user' => $this->user,
                'inboxUrl' => route('inbox'),
            ],
        );
    }
}

