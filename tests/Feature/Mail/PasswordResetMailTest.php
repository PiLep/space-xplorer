<?php

use App\Mail\PasswordResetConfirmation;
use App\Mail\ResetPasswordNotification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

it('sends reset password notification with correct content', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);

    $token = Password::createToken($user);

    // Send notification (this will trigger the notification system which sends the mailable)
    $user->sendPasswordResetNotification($token);

    // Verify notification was sent with correct token
    Notification::assertSentTo($user, \App\Notifications\ResetPasswordNotification::class, function ($notification) use ($token) {
        return $notification->token === $token;
    });

    // Verify the notification would send the correct mailable
    $notification = new \App\Notifications\ResetPasswordNotification($token);
    $mailable = $notification->toMail($user);

    expect($mailable)->toBeInstanceOf(ResetPasswordNotification::class)
        ->and($mailable->token)->toBe($token)
        ->and($mailable->email)->toBe($user->email);
});

it('reset password notification contains reset URL', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $token = Password::createToken($user);

    $mailable = new ResetPasswordNotification($token, $user->email);

    expect($mailable->envelope()->subject)->toBe('Réinitialisation de votre mot de passe - Space Xplorer');

    $content = $mailable->content();
    expect($content->view)->toBe('emails.auth.reset-password')
        ->and($content->with['token'])->toBe($token)
        ->and($content->with['email'])->toBe($user->email)
        ->and($content->with['resetUrl'])->toContain('/reset-password/')
        ->and($content->with['resetUrl'])->toContain($token)
        ->and($content->with['resetUrl'])->toContain('email='.urlencode($user->email));
});

it('sends password reset confirmation after successful reset', function () {
    Mail::fake();

    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => \Illuminate\Support\Facades\Hash::make('oldpassword'),
    ]);

    $token = Password::createToken($user);

    $this->post('/reset-password', [
        'token' => $token,
        'email' => 'test@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    Mail::assertSent(PasswordResetConfirmation::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email)
            && $mail->user->id === $user->id;
    });
});

it('password reset confirmation contains user information', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);

    $mailable = new PasswordResetConfirmation($user);

    expect($mailable->envelope()->subject)->toBe('Votre mot de passe a été réinitialisé - Space Xplorer');

    $content = $mailable->content();
    expect($content->view)->toBe('emails.auth.password-reset-confirmation')
        ->and($content->with['user']->id)->toBe($user->id)
        ->and($content->with['user']->email)->toBe($user->email);
});

it('reset password notification is sent to correct email', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $token = Password::createToken($user);

    $user->sendPasswordResetNotification($token);

    // Verify notification was sent to correct user
    Notification::assertSentTo($user, \App\Notifications\ResetPasswordNotification::class);

    // Verify the notification would send the mailable to the correct email
    $notification = new \App\Notifications\ResetPasswordNotification($token);
    $mailable = $notification->toMail($user);

    expect($mailable)->toBeInstanceOf(ResetPasswordNotification::class)
        ->and($mailable->hasTo($user->email))->toBeTrue();
});
