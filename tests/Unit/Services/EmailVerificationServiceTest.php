<?php

use App\Mail\EmailVerificationNotification;
use App\Models\User;
use App\Services\EmailVerificationService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->service = new EmailVerificationService;
    Mail::fake();
});

it('generates a valid 6-digit code', function () {
    $user = User::factory()->unverified()->create();

    $code = $this->service->generateCode($user);

    expect($code)->toBeString()
        ->and(strlen($code))->toBe(6)
        ->and(ctype_digit($code))->toBeTrue()
        ->and((int) $code)->toBeGreaterThanOrEqual(100000)
        ->and((int) $code)->toBeLessThanOrEqual(999999);
});

it('hashes the code before storing', function () {
    $user = User::factory()->unverified()->create();

    $code = $this->service->generateCode($user);
    $user->refresh();

    expect($user->email_verification_code)->not->toBe($code)
        ->and(Hash::check($code, $user->email_verification_code))->toBeTrue();
});

it('sets code expiration to 15 minutes', function () {
    $user = User::factory()->unverified()->create();

    $this->service->generateCode($user);
    $user->refresh();

    $expectedExpiration = now()->addMinutes(15);
    $actualExpiration = $user->email_verification_code_expires_at;

    expect($actualExpiration)->not->toBeNull()
        ->and($actualExpiration->diffInMinutes($expectedExpiration))->toBeLessThan(1);
});

it('resets verification attempts when generating new code', function () {
    $user = User::factory()->unverified()->create([
        'email_verification_attempts' => 3,
    ]);

    $this->service->generateCode($user);
    $user->refresh();

    expect($user->email_verification_attempts)->toBe(0);
});

it('sends email notification when generating code', function () {
    $user = User::factory()->unverified()->create();

    $code = $this->service->generateCode($user);

    Mail::assertSent(EmailVerificationNotification::class, function ($mail) use ($user, $code) {
        return $mail->hasTo($user->email)
            && $mail->code === $code
            && $mail->user->id === $user->id;
    });
});

it('verifies correct code and marks email as verified', function () {
    $user = User::factory()->unverified()->create();
    $code = $this->service->generateCode($user);

    $result = $this->service->verifyCode($user, $code);
    $user->refresh();

    expect($result)->toBeTrue()
        ->and($user->email_verified_at)->not->toBeNull()
        ->and($user->email_verification_code)->toBeNull();
});

it('rejects incorrect code', function () {
    $user = User::factory()->unverified()->create();
    $this->service->generateCode($user);

    $result = $this->service->verifyCode($user, '000000');
    $user->refresh();

    expect($result)->toBeFalse()
        ->and($user->email_verified_at)->toBeNull()
        ->and($user->email_verification_attempts)->toBe(1);
});

it('increments attempts on failed verification', function () {
    $user = User::factory()->unverified()->create();
    $this->service->generateCode($user);

    $this->service->verifyCode($user, '000000');
    $user->refresh();

    expect($user->email_verification_attempts)->toBe(1);

    $this->service->verifyCode($user, '000000');
    $user->refresh();

    expect($user->email_verification_attempts)->toBe(2);
});

it('rejects expired code', function () {
    $user = User::factory()->unverified()->create();
    $code = $this->service->generateCode($user);

    // Set expiration to past
    $user->update([
        'email_verification_code_expires_at' => now()->subMinute(),
    ]);

    $result = $this->service->verifyCode($user, $code);
    $user->refresh();

    expect($result)->toBeFalse()
        ->and($user->email_verified_at)->toBeNull();
});

it('blocks verification after maximum attempts', function () {
    $user = User::factory()->unverified()->create();
    $code = $this->service->generateCode($user);

    // Set attempts to max
    $user->update([
        'email_verification_attempts' => 5,
    ]);

    $result = $this->service->verifyCode($user, $code);
    $user->refresh();

    expect($result)->toBeFalse()
        ->and($user->email_verified_at)->toBeNull();
});

it('clears verification code after successful verification', function () {
    $user = User::factory()->unverified()->create();
    $code = $this->service->generateCode($user);

    $this->service->verifyCode($user, $code);
    $user->refresh();

    expect($user->email_verification_code)->toBeNull()
        ->and($user->email_verification_code_expires_at)->toBeNull()
        ->and($user->email_verification_attempts)->toBe(0)
        ->and($user->email_verification_code_sent_at)->toBeNull();
});

it('resends code when cooldown has passed', function () {
    $user = User::factory()->unverified()->create();
    $this->service->generateCode($user);

    // Set sent_at to past (more than 2 minutes ago)
    $user->update([
        'email_verification_code_sent_at' => now()->subMinutes(3),
    ]);

    Mail::fake();

    $this->service->resendCode($user);

    Mail::assertSent(EmailVerificationNotification::class);
});

it('throws exception when resending before cooldown', function () {
    $user = User::factory()->unverified()->create();
    $this->service->generateCode($user);

    // Set sent_at to recent (less than 2 minutes ago)
    $user->update([
        'email_verification_code_sent_at' => now()->subMinute(),
    ]);

    expect(fn () => $this->service->resendCode($user))
        ->toThrow(\Exception::class, 'Please wait before requesting a new code.');
});

it('allows resend when sent_at is null', function () {
    $user = User::factory()->unverified()->create([
        'email_verification_code_sent_at' => null,
    ]);

    Mail::fake();

    $this->service->resendCode($user);

    Mail::assertSent(EmailVerificationNotification::class);
});

it('checks if code is valid without incrementing attempts', function () {
    $user = User::factory()->unverified()->create();
    $code = $this->service->generateCode($user);

    $result = $this->service->isCodeValid($user, $code);
    $user->refresh();

    expect($result)->toBeTrue()
        ->and($user->email_verification_attempts)->toBe(0);
});

it('returns false for invalid code in isCodeValid', function () {
    $user = User::factory()->unverified()->create();
    $this->service->generateCode($user);

    $result = $this->service->isCodeValid($user, '000000');
    $user->refresh();

    expect($result)->toBeFalse()
        ->and($user->email_verification_attempts)->toBe(0);
});

it('returns false for expired code in isCodeValid', function () {
    $user = User::factory()->unverified()->create();
    $code = $this->service->generateCode($user);

    $user->update([
        'email_verification_code_expires_at' => now()->subMinute(),
    ]);

    $result = $this->service->isCodeValid($user, $code);

    expect($result)->toBeFalse();
});

it('clears verification code manually', function () {
    $user = User::factory()->unverified()->create();
    $this->service->generateCode($user);

    $this->service->clearVerificationCode($user);
    $user->refresh();

    expect($user->email_verification_code)->toBeNull()
        ->and($user->email_verification_code_expires_at)->toBeNull()
        ->and($user->email_verification_attempts)->toBe(0)
        ->and($user->email_verification_code_sent_at)->toBeNull();
});
