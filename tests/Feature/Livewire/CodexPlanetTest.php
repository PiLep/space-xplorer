<?php

use App\Models\CodexEntry;
use App\Models\Planet;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

it('renders codex planet component', function () {
    $entry = CodexEntry::factory()->create(['is_public' => true]);

    Livewire::test(\App\Livewire\CodexPlanet::class, ['id' => $entry->id])
        ->assertStatus(200)
        ->assertSee($entry->display_name);
});

it('displays planet name or fallback name', function () {
    $entry = CodexEntry::factory()->public()->named()->create([
        'name' => 'Alpha Centauri',
        'fallback_name' => 'Planète Tellurique #1234',
    ]);

    Livewire::test(\App\Livewire\CodexPlanet::class, ['id' => $entry->id])
        ->assertSee('Alpha Centauri');
});

it('displays fallback name when planet is not named', function () {
    $entry = CodexEntry::factory()->public()->create([
        'name' => null,
        'fallback_name' => 'Planète Tellurique #1234',
    ]);

    Livewire::test(\App\Livewire\CodexPlanet::class, ['id' => $entry->id])
        ->assertSee('Planète Tellurique #1234');
});

it('displays discoverer information', function () {
    $user = User::factory()->create(['name' => 'John Explorer']);
    $entry = CodexEntry::factory()->public()->create([
        'discovered_by_user_id' => $user->id,
        'created_at' => now()->subDays(5),
    ]);

    Livewire::test(\App\Livewire\CodexPlanet::class, ['id' => $entry->id])
        ->assertSee('John Explorer')
        ->assertSee($entry->created_at->format('d/m/Y'));
});

it('displays planet characteristics', function () {
    $planet = Planet::factory()->create();
    $planet->properties()->updateOrCreate(
        ['planet_id' => $planet->id],
        [
            'type' => 'terrestrial',
            'size' => 'medium',
            'temperature' => 'temperate',
            'atmosphere' => 'breathable',
            'terrain' => 'rocky',
            'resources' => 'abundant',
        ]
    );

    $entry = CodexEntry::factory()->create([
        'planet_id' => $planet->id,
        'is_public' => true,
    ]);

    Livewire::test(\App\Livewire\CodexPlanet::class, ['id' => $entry->id])
        ->assertSee('Caractéristiques')
        ->assertSee('Terrestrial')
        ->assertSee('Medium')
        ->assertSee('Temperate')
        ->assertSee('Breathable')
        ->assertSee('Rocky')
        ->assertSee('Abundant');
});

it('displays planet description', function () {
    $entry = CodexEntry::factory()->create([
        'description' => 'This is a beautiful planet with amazing features.',
        'is_public' => true,
    ]);

    Livewire::test(\App\Livewire\CodexPlanet::class, ['id' => $entry->id])
        ->assertSee('Description')
        ->assertSee('This is a beautiful planet with amazing features.');
});

it('displays planet image when available', function () {
    $planet = Planet::factory()->create([
        'image_url' => 'https://example.com/planet.jpg',
    ]);
    $entry = CodexEntry::factory()->create([
        'planet_id' => $planet->id,
        'is_public' => true,
    ]);

    Livewire::test(\App\Livewire\CodexPlanet::class, ['id' => $entry->id])
        ->assertSee('https://example.com/planet.jpg');
});

it('displays planet video when available', function () {
    $planet = Planet::factory()->create([
        'video_url' => 'https://example.com/planet.mp4',
    ]);
    $entry = CodexEntry::factory()->create([
        'planet_id' => $planet->id,
        'is_public' => true,
    ]);

    Livewire::test(\App\Livewire\CodexPlanet::class, ['id' => $entry->id])
        ->assertSee('https://example.com/planet.mp4');
});

it('shows name planet button for discoverer when planet is not named', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $entry = CodexEntry::factory()->create([
        'discovered_by_user_id' => $user->id,
        'is_named' => false,
        'is_public' => true,
    ]);

    Livewire::test(\App\Livewire\CodexPlanet::class, ['id' => $entry->id])
        ->assertSee('Nommer cette planète');
});

it('does not show name planet button when planet is already named', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $entry = CodexEntry::factory()->named()->create([
        'discovered_by_user_id' => $user->id,
        'is_named' => true,
        'is_public' => true,
    ]);

    Livewire::test(\App\Livewire\CodexPlanet::class, ['id' => $entry->id])
        ->assertDontSee('Nommer cette planète');
});

it('does not show name planet button for non-discoverer', function () {
    $discoverer = User::factory()->create();
    $otherUser = User::factory()->create();
    Auth::login($otherUser);

    $entry = CodexEntry::factory()->create([
        'discovered_by_user_id' => $discoverer->id,
        'is_named' => false,
        'is_public' => true,
    ]);

    Livewire::test(\App\Livewire\CodexPlanet::class, ['id' => $entry->id])
        ->assertDontSee('Nommer cette planète');
});

it('shows contribute button for authenticated users', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $entry = CodexEntry::factory()->create(['is_public' => true]);

    Livewire::test(\App\Livewire\CodexPlanet::class, ['id' => $entry->id])
        ->assertSee('Contribuer');
});

it('does not show contribute button for guests', function () {
    Auth::logout();

    $entry = CodexEntry::factory()->create(['is_public' => true]);

    Livewire::test(\App\Livewire\CodexPlanet::class, ['id' => $entry->id])
        ->assertDontSee('Contribuer');
});

it('opens name modal when name button is clicked', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $entry = CodexEntry::factory()->create([
        'discovered_by_user_id' => $user->id,
        'is_named' => false,
        'is_public' => true,
    ]);

    Livewire::test(\App\Livewire\CodexPlanet::class, ['id' => $entry->id])
        ->call('openNameModal')
        ->assertSet('showNameModal', true);
});

it('opens contribute modal when contribute button is clicked', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $entry = CodexEntry::factory()->create(['is_public' => true]);

    Livewire::test(\App\Livewire\CodexPlanet::class, ['id' => $entry->id])
        ->call('openContributeModal')
        ->assertSet('showContributeModal', true);
});

it('closes name modal', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $entry = CodexEntry::factory()->create([
        'discovered_by_user_id' => $user->id,
        'is_public' => true,
    ]);

    Livewire::test(\App\Livewire\CodexPlanet::class, ['id' => $entry->id])
        ->set('showNameModal', true)
        ->call('closeNameModal')
        ->assertSet('showNameModal', false);
});

it('closes contribute modal', function () {
    $user = User::factory()->create();
    Auth::login($user);

    $entry = CodexEntry::factory()->create(['is_public' => true]);

    Livewire::test(\App\Livewire\CodexPlanet::class, ['id' => $entry->id])
        ->set('showContributeModal', true)
        ->call('closeContributeModal')
        ->assertSet('showContributeModal', false);
});

it('displays back button to codex index', function () {
    $entry = CodexEntry::factory()->create(['is_public' => true]);

    Livewire::test(\App\Livewire\CodexPlanet::class, ['id' => $entry->id])
        ->assertSee('Retour à l')
        ->assertSee('index');
});

it('handles non-existent entry gracefully', function () {
    Livewire::test(\App\Livewire\CodexPlanet::class, ['id' => 'non-existent-id'])
        ->assertSet('error', function ($error) {
            return $error !== null;
        });
});

