<?php

use App\Models\User;
use App\Services\EmailVerificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

beforeEach(function () {
    $this->service = app(EmailVerificationService::class);
});

it('renders verify email component', function () {
    $user = User::factory()->unverified()->create();
    Auth::login($user);

    Livewire::test(\App\Livewire\VerifyEmail::class)
        ->assertStatus(200)
        ->assertSee('[INFO] A verification code has been sent to your email');
});

it('redirects to login if not authenticated', function () {
    Livewire::test(\App\Livewire\VerifyEmail::class)
        ->assertRedirect(route('login'));
});

it('redirects to dashboard if email already verified', function () {
    $user = User::factory()->create();
    Auth::login($user);

    Livewire::test(\App\Livewire\VerifyEmail::class)
        ->assertRedirect(route('dashboard'));
});

it('verifies email with correct code', function () {
    $user = User::factory()->unverified()->create();
    Auth::login($user);

    $code = $this->service->generateCode($user);

    // Setting code to 6 digits automatically triggers verification
    Livewire::test(\App\Livewire\VerifyEmail::class)
        ->set('code', $code)
        ->assertRedirect(route('dashboard'));

    $user->refresh();
    expect($user->email_verified_at)->not->toBeNull();
});

it('shows error with incorrect code', function () {
    $user = User::factory()->unverified()->create();
    Auth::login($user);

    $this->service->generateCode($user);

    // Setting code to 6 digits automatically triggers verification
    Livewire::test(\App\Livewire\VerifyEmail::class)
        ->set('code', '000000')
        ->assertHasErrors(['code'])
        ->assertSee('[ERROR] Invalid verification code');

    $user->refresh();
    expect($user->email_verified_at)->toBeNull();
});

it('shows error with expired code', function () {
    $user = User::factory()->unverified()->create();
    Auth::login($user);

    $code = $this->service->generateCode($user);
    $user->update([
        'email_verification_code_expires_at' => now()->subMinute(),
    ]);

    // Setting code to 6 digits automatically triggers verification
    Livewire::test(\App\Livewire\VerifyEmail::class)
        ->set('code', $code)
        ->assertHasErrors(['code'])
        ->assertSee('[ERROR] Verification code has expired');
});

it('shows error when maximum attempts exceeded', function () {
    $user = User::factory()->unverified()->create();
    Auth::login($user);

    $this->service->generateCode($user);
    $user->update([
        'email_verification_attempts' => 5,
    ]);

    // Setting code to 6 digits automatically triggers verification
    Livewire::test(\App\Livewire\VerifyEmail::class)
        ->set('code', '123456')
        ->assertHasErrors(['code'])
        ->assertSee('[ERROR] Maximum verification attempts exceeded');
});

it('resends code when cooldown has passed', function () {
    $user = User::factory()->unverified()->create();
    Auth::login($user);

    $this->service->generateCode($user);
    $user->update([
        'email_verification_code_sent_at' => now()->subMinutes(3),
    ]);

    Livewire::test(\App\Livewire\VerifyEmail::class)
        ->call('resend')
        ->assertSee('[SUCCESS] New verification code sent');
});

it('prevents resending code before cooldown', function () {
    $user = User::factory()->unverified()->create();
    Auth::login($user);

    $this->service->generateCode($user);
    $user->update([
        'email_verification_code_sent_at' => now()->subMinute(),
    ]);

    Livewire::test(\App\Livewire\VerifyEmail::class)
        ->call('resend')
        ->assertSee('[INFO] Resend available in');
});

it('auto-formats code input to 6 digits', function () {
    $user = User::factory()->unverified()->create();
    Auth::login($user);

    Livewire::test(\App\Livewire\VerifyEmail::class)
        ->set('code', '12345a')
        ->assertSet('code', '12345')
        ->set('code', '123456789')
        ->assertSet('code', '123456');
});

it('displays attempts remaining', function () {
    $user = User::factory()->unverified()->create();
    Auth::login($user);

    $this->service->generateCode($user);
    $user->update([
        'email_verification_attempts' => 3,
    ]);

    Livewire::test(\App\Livewire\VerifyEmail::class)
        ->assertSee('2 verification attempts remaining');
});

it('displays masked email', function () {
    $user = User::factory()->unverified()->create([
        'email' => 'john.doe@example.com',
    ]);
    Auth::login($user);

    Livewire::test(\App\Livewire\VerifyEmail::class)
        ->assertSee('j*******@example.com');
});
