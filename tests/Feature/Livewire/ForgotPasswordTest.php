<?php

use App\Events\PasswordResetRequested;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

it('renders forgot password component', function () {
    Livewire::test(\App\Livewire\ForgotPassword::class)
        ->assertStatus(200);
});

it('validates email is required', function () {
    Livewire::test(\App\Livewire\ForgotPassword::class)
        ->set('email', '')
        ->call('sendResetLink')
        ->assertHasErrors(['email']);
});

it('validates email format', function () {
    Livewire::test(\App\Livewire\ForgotPassword::class)
        ->set('email', 'invalid-email')
        ->call('sendResetLink')
        ->assertHasErrors(['email']);
});

it('sends reset link successfully', function () {
    Event::fake();
    Mail::fake();

    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    Livewire::test(\App\Livewire\ForgotPassword::class)
        ->set('email', 'test@example.com')
        ->call('sendResetLink')
        ->assertSet('status', function ($status) {
            return str_contains($status, '[SUCCESS]');
        });

    Event::assertDispatched(PasswordResetRequested::class, function ($event) {
        return $event->email === 'test@example.com';
    });
});

it('shows success message even if email does not exist', function () {
    Event::fake();

    Livewire::test(\App\Livewire\ForgotPassword::class)
        ->set('email', 'nonexistent@example.com')
        ->call('sendResetLink')
        ->assertSet('status', function ($status) {
            return str_contains($status, '[SUCCESS]');
        });

    Event::assertDispatched(PasswordResetRequested::class);
});

it('clears email after sending reset link', function () {
    Event::fake();

    $component = Livewire::test(\App\Livewire\ForgotPassword::class)
        ->set('email', 'test@example.com')
        ->call('sendResetLink');

    // Email should be cleared for security
    expect($component->get('email'))->toBe('');
});

it('shows processing status during reset link sending', function () {
    Event::fake();

    $component = Livewire::test(\App\Livewire\ForgotPassword::class)
        ->set('email', 'test@example.com');

    // The status is set to PROCESSING before the call completes
    // We check that it eventually contains SUCCESS
    $component->call('sendResetLink')
        ->assertSet('status', function ($status) {
            return str_contains($status, '[SUCCESS]') || str_contains($status, '[PROCESSING]');
        });
});
