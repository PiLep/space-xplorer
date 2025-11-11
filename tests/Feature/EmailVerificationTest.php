<?php

use App\Mail\EmailVerificationNotification;
use App\Models\User;
use App\Services\EmailVerificationService;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
    $this->service = app(EmailVerificationService::class);
});

it('generates and sends verification code after user creation', function () {
    $user = User::factory()->unverified()->create();

    $code = $this->service->generateCode($user);

    expect($code)->toBeString()
        ->and(strlen($code))->toBe(6)
        ->and($user->email_verification_code)->not->toBeNull()
        ->and($user->email_verification_code_expires_at)->not->toBeNull();

    Mail::assertSent(EmailVerificationNotification::class, function ($mail) use ($user, $code) {
        return $mail->hasTo($user->email)
            && $mail->code === $code;
    });
});

it('verifies email with correct code', function () {
    $user = User::factory()->unverified()->create();
    $code = $this->service->generateCode($user);

    $result = $this->service->verifyCode($user, $code);

    expect($result)->toBeTrue();
    $user->refresh();
    expect($user->email_verified_at)->not->toBeNull()
        ->and($user->email_verification_code)->toBeNull();
});

it('rejects incorrect code', function () {
    $user = User::factory()->unverified()->create();
    $this->service->generateCode($user);

    $result = $this->service->verifyCode($user, '000000');

    expect($result)->toBeFalse();
    $user->refresh();
    expect($user->email_verified_at)->toBeNull()
        ->and($user->email_verification_attempts)->toBe(1);
});

it('rejects expired code', function () {
    $user = User::factory()->unverified()->create();
    $code = $this->service->generateCode($user);
    $user->update([
        'email_verification_code_expires_at' => now()->subMinute(),
    ]);

    $result = $this->service->verifyCode($user, $code);

    expect($result)->toBeFalse();
    $user->refresh();
    expect($user->email_verified_at)->toBeNull();
});

it('blocks verification after maximum attempts', function () {
    $user = User::factory()->unverified()->create();
    $code = $this->service->generateCode($user);
    $user->update([
        'email_verification_attempts' => 5,
    ]);

    $result = $this->service->verifyCode($user, $code);

    expect($result)->toBeFalse();
    $user->refresh();
    expect($user->email_verified_at)->toBeNull();
});

it('allows resending code when cooldown has passed', function () {
    $user = User::factory()->unverified()->create();
    $this->service->generateCode($user);
    $user->update([
        'email_verification_code_sent_at' => now()->subMinutes(3),
    ]);

    Mail::fake();

    $this->service->resendCode($user);

    Mail::assertSent(EmailVerificationNotification::class);
});

it('prevents resending code before cooldown', function () {
    $user = User::factory()->unverified()->create();
    $this->service->generateCode($user);
    $user->update([
        'email_verification_code_sent_at' => now()->subMinute(),
    ]);

    expect(fn () => $this->service->resendCode($user))
        ->toThrow(\Exception::class, 'Please wait before requesting a new code.');
});
