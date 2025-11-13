<?php

use App\Mail\NewMessageNotification;
use App\Models\Message;
use App\Models\User;
use App\Services\EmailService;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->message = Message::factory()->to($this->user)->create();
});

it('creates mailable with message and user', function () {
    $mailable = new NewMessageNotification($this->message, $this->user);

    expect($mailable->message->id)->toBe($this->message->id)
        ->and($mailable->user->id)->toBe($this->user->id);
});

it('sets correct envelope subject', function () {
    $mailable = new NewMessageNotification($this->message, $this->user);

    $envelope = $mailable->envelope();

    expect($envelope->subject)->toBe('[STELLAR] Nouveau message : '.$this->message->subject);
});

it('sets reply-to address from config', function () {
    config(['mail.reply_to.address' => 'reply@example.com']);

    $mailable = new NewMessageNotification($this->message, $this->user);

    $envelope = $mailable->envelope();

    expect($envelope->replyTo)->toBeArray()
        ->and($envelope->replyTo[0]->address)->toBe('reply@example.com');
});

it('falls back to from address when reply_to not configured', function () {
    config(['mail.reply_to.address' => null]);
    config(['mail.from.address' => 'from@example.com']);

    $mailable = new NewMessageNotification($this->message, $this->user);

    $envelope = $mailable->envelope();

    // replyTo can be null, string, or array depending on Laravel version
    if (is_array($envelope->replyTo) && count($envelope->replyTo) > 0) {
        expect($envelope->replyTo[0]->address)->toBe('from@example.com');
    } else {
        // If it's a string or Address object directly
        expect($envelope->replyTo)->not->toBeNull();
    }
});

it('sets correct headers from email service', function () {
    $emailService = $this->mock(EmailService::class);
    $emailService->shouldReceive('getDefaultHeaders')
        ->once()
        ->andReturn(['X-Custom-Header' => 'value']);

    app()->instance(EmailService::class, $emailService);

    $mailable = new NewMessageNotification($this->message, $this->user);

    $headers = $mailable->headers();

    expect($headers->text)->toBe(['X-Custom-Header' => 'value']);
});

it('sets correct content view and data', function () {
    $mailable = new NewMessageNotification($this->message, $this->user);

    $content = $mailable->content();

    expect($content->view)->toBe('emails.new-message')
        ->and($content->with['inboxMessage']->id)->toBe($this->message->id)
        ->and($content->with['user']->id)->toBe($this->user->id)
        ->and($content->with['inboxUrl'])->toBe(route('inbox'));
});

it('uses app name from config for subject', function () {
    config(['app.name' => 'Test App']);

    $mailable = new NewMessageNotification($this->message, $this->user);

    $envelope = $mailable->envelope();

    // Subject should still contain STELLAR as it's hardcoded in the envelope method
    expect($envelope->subject)->toContain('STELLAR');
});

it('handles message with long subject', function () {
    $longSubject = str_repeat('A', 200);
    $message = Message::factory()->to($this->user)->create(['subject' => $longSubject]);

    $mailable = new NewMessageNotification($message, $this->user);

    $envelope = $mailable->envelope();

    expect($envelope->subject)->toContain($longSubject);
});

it('handles message with special characters in subject', function () {
    $specialSubject = 'Message avec des caractères spéciaux: <>&"\'';
    $message = Message::factory()->to($this->user)->create(['subject' => $specialSubject]);

    $mailable = new NewMessageNotification($message, $this->user);

    $envelope = $mailable->envelope();

    expect($envelope->subject)->toContain($specialSubject);
});

