<?php

use App\Models\Message;
use App\Models\Planet;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('generates test messages successfully with default count', function () {
    $exitCode = Artisan::call('messages:generate', [
        '--email' => $this->user->email,
    ]);

    expect($exitCode)->toBe(0)
        ->and(Artisan::output())->toContain('Successfully created 5 messages!');

    $messages = Message::where('recipient_id', $this->user->id)->get();
    expect($messages)->toHaveCount(5);
});

it('generates test messages with custom count', function () {
    $exitCode = Artisan::call('messages:generate', [
        '--email' => $this->user->email,
        '--count' => 3,
    ]);

    expect($exitCode)->toBe(0)
        ->and(Artisan::output())->toContain('Successfully created 3 messages!');

    $messages = Message::where('recipient_id', $this->user->id)->get();
    expect($messages)->toHaveCount(3);
});

it('generates test messages for first user when email not provided', function () {
    // Clear all users first to ensure we have a clean state
    User::query()->delete();
    Message::query()->delete();
    $user = User::factory()->create();

    $exitCode = Artisan::call('messages:generate', [
        '--count' => 2,
    ]);
    $output = Artisan::output();

    expect($exitCode)->toBe(0)
        ->and($output)->toContain('Using user:')
        ->and($output)->toContain('Successfully created 2 messages!');

    // Verify messages were created for the first user
    $messages = Message::where('recipient_id', $user->id)->get();
    expect($messages)->toHaveCount(2);
});

it('fails when user email not found', function () {
    $exitCode = Artisan::call('messages:generate', [
        '--email' => 'nonexistent@example.com',
    ]);

    expect($exitCode)->toBe(1)
        ->and(Artisan::output())->toContain("User with email 'nonexistent@example.com' not found.");
});

it('fails when no users exist and email not provided', function () {
    User::query()->delete();

    $exitCode = Artisan::call('messages:generate');

    expect($exitCode)->toBe(1)
        ->and(Artisan::output())->toContain('No users found in the database.');
});

it('generates welcome message type', function () {
    Artisan::call('messages:generate', [
        '--email' => $this->user->email,
        '--count' => 1,
    ]);

    $message = Message::where('recipient_id', $this->user->id)->first();
    expect($message->type)->toBe('welcome')
        ->and($message->subject)->toBe('Bienvenue dans l\'univers Stellar');
});

it('generates discovery message type', function () {
    Planet::factory()->create();
    Artisan::call('messages:generate', [
        '--email' => $this->user->email,
        '--count' => 2,
    ]);

    $messages = Message::where('recipient_id', $this->user->id)->get();
    $discoveryMessage = $messages->where('type', 'discovery')->first();
    expect($discoveryMessage)->not->toBeNull()
        ->and($discoveryMessage->type)->toBe('discovery');
});

it('generates discovery message with fallback when no planets exist', function () {
    Planet::query()->delete();
    Artisan::call('messages:generate', [
        '--email' => $this->user->email,
        '--count' => 2,
    ]);

    $messages = Message::where('recipient_id', $this->user->id)->get();
    $discoveryMessage = $messages->where('type', 'discovery')->first();
    expect($discoveryMessage)->not->toBeNull()
        ->and($discoveryMessage->type)->toBe('discovery')
        ->and($discoveryMessage->metadata['type'])->toBe('discovery')
        ->and($discoveryMessage->metadata['discovery_type'])->toBe('anomalie_spatiale');
});

it('generates mission message type', function () {
    Artisan::call('messages:generate', [
        '--email' => $this->user->email,
        '--count' => 3,
    ]);

    $messages = Message::where('recipient_id', $this->user->id)->get();
    $missionMessage = $messages->where('type', 'mission')->first();
    expect($missionMessage)->not->toBeNull()
        ->and($missionMessage->type)->toBe('mission')
        ->and($missionMessage->metadata)->toHaveKey('mission_id');
});

it('generates alert message type', function () {
    Artisan::call('messages:generate', [
        '--email' => $this->user->email,
        '--count' => 4,
    ]);

    $messages = Message::where('recipient_id', $this->user->id)->get();
    $alertMessage = $messages->where('type', 'alert')->first();
    expect($alertMessage)->not->toBeNull()
        ->and($alertMessage->type)->toBe('alert')
        ->and($alertMessage->is_important)->toBeTrue();
});

it('generates system message type', function () {
    Artisan::call('messages:generate', [
        '--email' => $this->user->email,
        '--count' => 5,
    ]);

    $messages = Message::where('recipient_id', $this->user->id)->get();
    $systemMessage = $messages->where('type', 'system')->first();
    expect($systemMessage)->not->toBeNull()
        ->and($systemMessage->type)->toBe('system');
});

it('cycles through message types when count exceeds types', function () {
    Artisan::call('messages:generate', [
        '--email' => $this->user->email,
        '--count' => 10,
    ]);

    $messages = Message::where('recipient_id', $this->user->id)->get();
    expect($messages)->toHaveCount(10);

    // Should have multiple of each type
    $types = $messages->pluck('type')->countBy();
    expect($types->get('welcome', 0))->toBeGreaterThan(0)
        ->and($types->get('discovery', 0))->toBeGreaterThan(0)
        ->and($types->get('mission', 0))->toBeGreaterThan(0)
        ->and($types->get('alert', 0))->toBeGreaterThan(0)
        ->and($types->get('system', 0))->toBeGreaterThan(0);
});

it('displays table with message details', function () {
    $output = Artisan::call('messages:generate', [
        '--email' => $this->user->email,
        '--count' => 2,
    ]);

    $outputText = Artisan::output();
    expect($outputText)->toContain('Type')
        ->and($outputText)->toContain('Subject')
        ->and($outputText)->toContain('Read')
        ->and($outputText)->toContain('Important');
});

it('creates discovery message with planet when available', function () {
    $planet = Planet::factory()->create();
    Artisan::call('messages:generate', [
        '--email' => $this->user->email,
        '--count' => 2,
    ]);

    $discoveryMessage = Message::where('recipient_id', $this->user->id)
        ->where('type', 'discovery')
        ->first();

    expect($discoveryMessage)->not->toBeNull()
        ->and($discoveryMessage->metadata['planet_id'])->toBe($planet->id);
});

it('creates different mission messages based on index', function () {
    Artisan::call('messages:generate', [
        '--email' => $this->user->email,
        '--count' => 8, // Will create multiple mission messages
    ]);

    $missionMessages = Message::where('recipient_id', $this->user->id)
        ->where('type', 'mission')
        ->get();

    expect($missionMessages->count())->toBeGreaterThan(1);
    // Should have different subjects
    $subjects = $missionMessages->pluck('subject')->unique();
    expect($subjects->count())->toBeGreaterThan(1);
});

it('creates different alert messages based on index', function () {
    Artisan::call('messages:generate', [
        '--email' => $this->user->email,
        '--count' => 9, // Will create multiple alert messages
    ]);

    $alertMessages = Message::where('recipient_id', $this->user->id)
        ->where('type', 'alert')
        ->get();

    expect($alertMessages->count())->toBeGreaterThan(1);
    // Should have different subjects
    $subjects = $alertMessages->pluck('subject')->unique();
    expect($subjects->count())->toBeGreaterThan(1);
});

it('creates different system messages based on index', function () {
    Artisan::call('messages:generate', [
        '--email' => $this->user->email,
        '--count' => 10, // Will create multiple system messages
    ]);

    $systemMessages = Message::where('recipient_id', $this->user->id)
        ->where('type', 'system')
        ->get();

    expect($systemMessages->count())->toBeGreaterThan(1);
    // Should have different subjects
    $subjects = $systemMessages->pluck('subject')->unique();
    expect($subjects->count())->toBeGreaterThan(1);
});

it('outputs created message information', function () {
    Artisan::call('messages:generate', [
        '--email' => $this->user->email,
        '--count' => 1,
    ]);

    $outputText = Artisan::output();
    expect($outputText)->toContain('Created');
});

