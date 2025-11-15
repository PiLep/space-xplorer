<?php

use App\Models\Planet;
use App\Models\User;
use App\Models\WikiEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->planet = Planet::factory()->create();
    $this->planet->properties()->create([
        'type' => 'tellurique',
        'size' => 'moyenne',
        'temperature' => 'temperee',
        'atmosphere' => 'breathable',
        'terrain' => 'rocky',
        'resources' => 'moderate',
    ]);
    $this->user = User::factory()->create();
    $this->entry = WikiEntry::factory()->create([
        'planet_id' => $this->planet->id,
        'discovered_by_user_id' => $this->user->id,
        'fallback_name' => 'Planète Tellurique #1234',
    ]);
});

it('returns paginated list of wiki entries', function () {
    WikiEntry::factory()->count(5)->create();

    $response = $this->getJson('/api/wiki/planets');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'data' => [
                    '*' => ['id', 'name', 'fallback_name', 'display_name'],
                ],
                'links',
                'meta',
            ],
            'status',
        ]);
});

it('returns wiki entry details', function () {
    $response = $this->getJson("/api/codex/planets/{$this->entry->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'fallback_name',
                'display_name',
                'description',
                'is_named',
                'planet',
                'characteristics',
            ],
            'status',
        ])
        ->assertJson([
            'data' => [
                'id' => $this->entry->id,
                'fallback_name' => $this->entry->fallback_name,
            ],
        ]);
});

it('searches wiki entries', function () {
    WikiEntry::factory()->create([
        'name' => 'Alpha Centauri',
        'planet_id' => Planet::factory()->create()->id,
    ]);

    $response = $this->getJson('/api/codex/search?q=Alpha');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'fallback_name', 'display_name'],
            ],
            'status',
        ]);
});

it('allows authenticated user to name planet', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/codex/planets/{$this->entry->id}/name", [
            'name' => 'Alpha Centauri',
        ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => ['id', 'name', 'fallback_name', 'display_name', 'is_named'],
            'message',
            'status',
        ]);

    $this->entry->refresh();
    expect($this->entry->name)->toBe('Alpha Centauri')
        ->and($this->entry->is_named)->toBeTrue();
});

it('requires authentication to name planet', function () {
    $response = $this->postJson("/api/codex/planets/{$this->entry->id}/name", [
        'name' => 'Alpha Centauri',
    ]);

    $response->assertStatus(401);
});

it('validates name when naming planet', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/codex/planets/{$this->entry->id}/name", [
            'name' => 'AB', // Too short
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('allows authenticated user to contribute', function () {
    $response = $this->actingAs($this->user, 'sanctum')
        ->postJson("/api/codex/planets/{$this->entry->id}/contribute", [
            'content' => 'This is a test contribution with enough characters.',
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => ['id', 'content_type', 'status'],
            'message',
            'status',
        ]);
});

it('requires authentication to contribute', function () {
    $response = $this->postJson("/api/codex/planets/{$this->entry->id}/contribute", [
        'content' => 'Test contribution',
    ]);

    $response->assertStatus(401);
});

