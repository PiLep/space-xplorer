<?php

use App\Models\CodexContribution;
use App\Models\CodexEntry;
use App\Models\Planet;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

beforeEach(function () {
    // Clear cache before each test
    Cache::flush();
});

it('renders codex index component', function () {
    Livewire::test(\App\Livewire\CodexIndex::class)
        ->assertStatus(200)
        ->assertSee('Codex Stellaris')
        ->assertSee('encyclopédie collaborative');
});

it('displays statistics cards', function () {
    CodexEntry::factory()->public()->discovered()->count(5)->create();
    CodexEntry::factory()->public()->discovered()->named()->count(2)->create();
    CodexContribution::factory()->count(3)->create();

    Livewire::test(\App\Livewire\CodexIndex::class)
        ->assertSee('Articles')
        ->assertSee('Planètes nommées')
        ->assertSee('Contributeurs')
        ->assertSee('Contributions');
});

it('displays recent discoveries section when entries exist', function () {
    CodexEntry::factory()->public()->discovered()->count(5)->create();

    Livewire::test(\App\Livewire\CodexIndex::class)
        ->assertSee('Découvertes récentes');
});

it('does not display recent discoveries section when no entries exist', function () {
    Livewire::test(\App\Livewire\CodexIndex::class)
        ->assertDontSee('Découvertes récentes');
});

it('displays paginated codex entries', function () {
    CodexEntry::factory()->public()->discovered()->count(25)->create();

    Livewire::test(\App\Livewire\CodexIndex::class)
        ->assertSee('Toutes les planètes')
        ->assertViewHas('entries', function ($entries) {
            return $entries->count() === 20; // Default per page
        });
});

it('filters entries by search query', function () {
    CodexEntry::factory()->public()->discovered()->create([
        'name' => 'Alpha Centauri',
        'fallback_name' => 'Planète Tellurique #1234',
        'created_at' => now()->subDays(10),
    ]);
    CodexEntry::factory()->public()->discovered()->create([
        'name' => 'Beta Orionis',
        'fallback_name' => 'Planète Gazeuse #5678',
        'created_at' => now()->subDays(10),
    ]);

    Livewire::test(\App\Livewire\CodexIndex::class)
        ->set('search', 'Alpha')
        ->assertSee('Alpha Centauri')
        ->assertSee('Toutes les planètes')
        ->assertDontSee('Beta Orionis');
});

it('displays search results count when searching', function () {
    CodexEntry::factory()->public()->discovered()->create([
        'name' => 'Alpha Centauri',
    ]);

    Livewire::test(\App\Livewire\CodexIndex::class)
        ->set('search', 'Alpha')
        ->assertSee('1 résultat');
});

it('displays named badge for named planets', function () {
    CodexEntry::factory()->public()->discovered()->named()->create([
        'name' => 'Alpha Centauri',
    ]);

    Livewire::test(\App\Livewire\CodexIndex::class)
        ->assertSee('Nommée')
        ->assertSee('Alpha Centauri');
});

it('displays planet type and size badges', function () {
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

    CodexEntry::factory()->public()->discovered()->create([
        'planet_id' => $planet->id,
    ]);

    Livewire::test(\App\Livewire\CodexIndex::class)
        ->assertSee('Terrestrial')
        ->assertSee('Medium');
});

it('displays discoverer name and date', function () {
    $user = User::factory()->create(['name' => 'John Explorer']);
    $entry = CodexEntry::factory()->public()->discovered()->create([
        'discovered_by_user_id' => $user->id,
        'created_at' => now()->subDays(5),
    ]);

    Livewire::test(\App\Livewire\CodexIndex::class)
        ->assertSee('John Explorer')
        ->assertSee($entry->created_at->format('d/m/Y'));
});

it('performs search with autocompletion', function () {
    CodexEntry::factory()->public()->discovered()->create([
        'name' => 'Alpha Centauri',
        'fallback_name' => 'Planète Tellurique #1234',
    ]);

    Livewire::test(\App\Livewire\CodexIndex::class)
        ->set('search', 'Alp')
        ->call('performSearch')
        ->assertSet('showSearchResults', true)
        ->assertSet('searchResults', function ($results) {
            return count($results) > 0;
        });
});

it('clears search results when search is cleared', function () {
    Livewire::test(\App\Livewire\CodexIndex::class)
        ->set('search', 'Alpha')
        ->call('performSearch')
        ->assertSet('showSearchResults', true)
        ->call('clearSearch')
        ->assertSet('search', '')
        ->assertSet('showSearchResults', false)
        ->assertSet('searchResults', []);
});

it('caches statistics', function () {
    CodexEntry::factory()->public()->discovered()->count(5)->create();

    // Clear cache first
    Cache::forget('codex.stats');

    // First call should cache
    Livewire::test(\App\Livewire\CodexIndex::class);

    // Verify cache exists
    expect(Cache::has('codex.stats'))->toBeTrue();
});

it('caches recent discoveries', function () {
    CodexEntry::factory()->public()->discovered()->count(5)->create();

    // Clear cache first
    Cache::forget('codex.recent_discoveries');

    // First call should cache
    Livewire::test(\App\Livewire\CodexIndex::class);

    // Verify cache exists
    expect(Cache::has('codex.recent_discoveries'))->toBeTrue();
});

it('redirects to planet page when selecting search result', function () {
    $entry = CodexEntry::factory()->public()->discovered()->create([
        'name' => 'Alpha Centauri',
    ]);

    Livewire::test(\App\Livewire\CodexIndex::class)
        ->call('selectResult', $entry->id)
        ->assertRedirect(route('codex.planet', $entry->id));
});

