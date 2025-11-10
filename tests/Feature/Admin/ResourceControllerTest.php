<?php

use App\Events\ResourceApproved;
use App\Events\ResourceRejected;
use App\Jobs\GenerateResourceJob;
use App\Models\Resource;
use App\Models\User;
use App\Services\ResourceGenerationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $this->password = 'password123';
    $this->admin = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make($this->password),
        'is_super_admin' => true,
    ]);

    config(['admin.email_whitelist' => '']);
    Auth::guard('admin')->login($this->admin);

    Queue::fake();
    Event::fake();
});

it('displays a listing of resources', function () {
    Resource::factory()->count(10)->create();

    $response = $this->get('/admin/resources');

    $response->assertStatus(200)
        ->assertViewIs('admin.resources.index')
        ->assertViewHas('resources');
});

it('paginates resources with 20 per page', function () {
    Resource::factory()->count(25)->create();

    $response = $this->get('/admin/resources');

    $resources = $response->viewData('resources');
    expect($resources)->toHaveCount(20);
});

it('orders resources by latest first', function () {
    $oldResource = Resource::factory()->create(['created_at' => now()->subDays(5)]);
    $newResource = Resource::factory()->create(['created_at' => now()]);

    $response = $this->get('/admin/resources');

    $resources = $response->viewData('resources');
    expect($resources->first()->id)->toBe($newResource->id);
});

it('eager loads creator and approver relationships', function () {
    $creator = User::factory()->create();
    $approver = User::factory()->create();
    $resource = Resource::factory()->create([
        'created_by' => $creator->id,
        'approved_by' => $approver->id,
    ]);

    $response = $this->get('/admin/resources');

    $resources = $response->viewData('resources');
    $foundResource = $resources->firstWhere('id', $resource->id);
    expect($foundResource->relationLoaded('creator'))->toBeTrue()
        ->and($foundResource->relationLoaded('approver'))->toBeTrue();
});

it('filters resources by type', function () {
    Resource::factory()->create(['type' => 'avatar_image']);
    Resource::factory()->create(['type' => 'planet_image']);
    Resource::factory()->create(['type' => 'planet_video']);

    $response = $this->get('/admin/resources?type=avatar_image');

    $resources = $response->viewData('resources');
    expect($resources)->toHaveCount(1)
        ->and($resources->first()->type)->toBe('avatar_image');
});

it('filters resources by status', function () {
    Resource::factory()->create(['status' => 'pending']);
    Resource::factory()->create(['status' => 'approved']);
    Resource::factory()->create(['status' => 'rejected']);

    $response = $this->get('/admin/resources?status=approved');

    $resources = $response->viewData('resources');
    expect($resources)->toHaveCount(1)
        ->and($resources->first()->status)->toBe('approved');
});

it('filters resources by both type and status', function () {
    Resource::factory()->create([
        'type' => 'avatar_image',
        'status' => 'pending',
    ]);
    Resource::factory()->create([
        'type' => 'avatar_image',
        'status' => 'approved',
    ]);
    Resource::factory()->create([
        'type' => 'planet_image',
        'status' => 'pending',
    ]);

    $response = $this->get('/admin/resources?type=avatar_image&status=approved');

    $resources = $response->viewData('resources');
    expect($resources)->toHaveCount(1)
        ->and($resources->first()->type)->toBe('avatar_image')
        ->and($resources->first()->status)->toBe('approved');
});

it('preserves query string when paginating', function () {
    Resource::factory()->count(25)->create(['type' => 'avatar_image']);

    $response = $this->get('/admin/resources?type=avatar_image&page=2');

    $resources = $response->viewData('resources');
    // Check that query string is preserved in pagination links
    $appends = $resources->appends(['type' => 'avatar_image']);
    expect($appends->url(1))->toContain('type=avatar_image');
});

it('shows the form for creating a new resource', function () {
    $response = $this->get('/admin/resources/create');

    $response->assertStatus(200)
        ->assertViewIs('admin.resources.create');
});

it('stores a new resource successfully', function () {
    $mockService = \Mockery::mock(ResourceGenerationService::class);
    $mockService->shouldReceive('extractPlanetTagsFromPrompt')
        ->andReturn(['tag1', 'tag2']);
    $this->app->instance(ResourceGenerationService::class, $mockService);

    $response = $this->post('/admin/resources', [
        'type' => 'planet_image',
        'prompt' => 'A beautiful alien planet with purple skies',
        'tags' => ['alien', 'planet'],
        'description' => 'Test description',
    ]);

    $response->assertRedirect(route('admin.resources.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('resources', [
        'type' => 'planet_image',
        'status' => 'generating',
        'prompt' => 'A beautiful alien planet with purple skies',
        'description' => 'Test description',
        'created_by' => $this->admin->id,
    ]);

    Queue::assertPushed(GenerateResourceJob::class);
});

it('merges manual tags with extracted tags', function () {
    $mockService = \Mockery::mock(ResourceGenerationService::class);
    $mockService->shouldReceive('extractPlanetTagsFromPrompt')
        ->andReturn(['extracted1', 'extracted2']);
    $this->app->instance(ResourceGenerationService::class, $mockService);

    $this->post('/admin/resources', [
        'type' => 'planet_image',
        'prompt' => 'A beautiful alien planet',
        'tags' => ['manual1', 'extracted1'], // extracted1 should be deduplicated
        'description' => 'Test description',
    ]);

    $resource = Resource::latest()->first();
    expect($resource->tags)->toContain('manual1', 'extracted1', 'extracted2')
        ->and($resource->tags)->toHaveCount(3); // No duplicates
});

it('extracts avatar tags when type is avatar_image', function () {
    $mockService = \Mockery::mock(ResourceGenerationService::class);
    $mockService->shouldReceive('extractAvatarTagsFromPrompt')
        ->andReturn(['avatar-tag1', 'avatar-tag2']);
    $this->app->instance(ResourceGenerationService::class, $mockService);

    $this->post('/admin/resources', [
        'type' => 'avatar_image',
        'prompt' => 'A space explorer avatar',
        'description' => 'Test description',
    ]);

    $resource = Resource::latest()->first();
    expect($resource->tags)->toContain('avatar-tag1', 'avatar-tag2');
});

it('validates required fields when storing resource', function () {
    $response = $this->post('/admin/resources', []);

    $response->assertSessionHasErrors(['type', 'prompt']);
});

it('validates resource type is valid', function () {
    $response = $this->post('/admin/resources', [
        'type' => 'invalid_type',
        'prompt' => 'A test prompt',
    ]);

    $response->assertSessionHasErrors(['type']);
});

it('validates prompt minimum length', function () {
    $response = $this->post('/admin/resources', [
        'type' => 'planet_image',
        'prompt' => 'short',
    ]);

    $response->assertSessionHasErrors(['prompt']);
});

it('validates prompt maximum length', function () {
    $response = $this->post('/admin/resources', [
        'type' => 'planet_image',
        'prompt' => str_repeat('a', 2001),
    ]);

    $response->assertSessionHasErrors(['prompt']);
});

it('converts tags string to array', function () {
    $mockService = \Mockery::mock(ResourceGenerationService::class);
    $mockService->shouldReceive('extractPlanetTagsFromPrompt')
        ->andReturn([]);
    $this->app->instance(ResourceGenerationService::class, $mockService);

    $this->post('/admin/resources', [
        'type' => 'planet_image',
        'prompt' => 'A beautiful alien planet',
        'tags' => 'tag1, tag2, tag3',
        'description' => 'Test description',
    ]);

    $resource = Resource::latest()->first();
    expect($resource->tags)->toBeArray()
        ->and($resource->tags)->toContain('tag1', 'tag2', 'tag3');
});

it('displays a specific resource', function () {
    $resource = Resource::factory()->create();

    $response = $this->get("/admin/resources/{$resource->id}");

    $response->assertStatus(200)
        ->assertViewIs('admin.resources.show')
        ->assertViewHas('resource', $resource);
});

it('eager loads creator and approver when showing resource', function () {
    $creator = User::factory()->create();
    $approver = User::factory()->create();
    $resource = Resource::factory()->create([
        'created_by' => $creator->id,
        'approved_by' => $approver->id,
    ]);

    $response = $this->get("/admin/resources/{$resource->id}");

    $viewResource = $response->viewData('resource');
    expect($viewResource->relationLoaded('creator'))->toBeTrue()
        ->and($viewResource->relationLoaded('approver'))->toBeTrue();
});

it('approves a resource successfully', function () {
    $resource = Resource::factory()->create(['status' => 'pending']);

    $response = $this->post("/admin/resources/{$resource->id}/approve", [
        'action' => 'approve',
    ]);

    $response->assertRedirect(route('admin.resources.show', $resource))
        ->assertSessionHas('success');

    $resource->refresh();
    expect($resource->status)->toBe('approved')
        ->and($resource->approved_by)->toBe($this->admin->id)
        ->and($resource->approved_at)->not->toBeNull();

    Event::assertDispatched(ResourceApproved::class, function ($event) use ($resource) {
        return $event->resource->id === $resource->id
            && $event->approver->id === $this->admin->id;
    });
});

it('rejects a resource successfully', function () {
    $resource = Resource::factory()->create(['status' => 'pending']);

    $response = $this->post("/admin/resources/{$resource->id}/approve", [
        'action' => 'reject',
        'rejection_reason' => 'Inappropriate content',
    ]);

    $response->assertRedirect(route('admin.resources.show', $resource))
        ->assertSessionHas('success');

    $resource->refresh();
    expect($resource->status)->toBe('rejected')
        ->and($resource->approved_by)->toBe($this->admin->id)
        ->and($resource->rejection_reason)->toBe('Inappropriate content')
        ->and($resource->approved_at)->not->toBeNull();

    Event::assertDispatched(ResourceRejected::class, function ($event) use ($resource) {
        return $event->resource->id === $resource->id
            && $event->approver->id === $this->admin->id
            && $event->reason === 'Inappropriate content';
    });
});

it('validates action is required when approving', function () {
    $resource = Resource::factory()->create();

    $response = $this->post("/admin/resources/{$resource->id}/approve", []);

    $response->assertSessionHasErrors(['action']);
});

it('validates action is either approve or reject', function () {
    $resource = Resource::factory()->create();

    $response = $this->post("/admin/resources/{$resource->id}/approve", [
        'action' => 'invalid',
    ]);

    $response->assertSessionHasErrors(['action']);
});

it('validates rejection reason is required when rejecting', function () {
    $resource = Resource::factory()->create();

    $response = $this->post("/admin/resources/{$resource->id}/approve", [
        'action' => 'reject',
    ]);

    $response->assertSessionHasErrors(['rejection_reason']);
});

it('validates rejection reason maximum length', function () {
    $resource = Resource::factory()->create();

    $response = $this->post("/admin/resources/{$resource->id}/approve", [
        'action' => 'reject',
        'rejection_reason' => str_repeat('a', 501),
    ]);

    $response->assertSessionHasErrors(['rejection_reason']);
});

it('requires authentication', function () {
    Auth::guard('admin')->logout();

    $response = $this->get('/admin/resources');

    // Middleware may redirect to default login route
    $response->assertRedirect();
});

it('requires super admin privileges', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make($this->password),
        'is_super_admin' => false,
    ]);

    Auth::guard('admin')->login($user);

    $response = $this->get('/admin/resources');

    $response->assertRedirect(route('admin.login'));
});

it('returns 404 for non-existent resource', function () {
    $response = $this->get('/admin/resources/non-existent-id');

    $response->assertStatus(404);
});

it('handles errors when storing resource', function () {
    // Mock service to throw exception
    $mockService = \Mockery::mock(ResourceGenerationService::class);
    $mockService->shouldReceive('extractPlanetTagsFromPrompt')
        ->andThrow(new \Exception('Service error'));
    $this->app->instance(ResourceGenerationService::class, $mockService);

    $response = $this->post('/admin/resources', [
        'type' => 'planet_image',
        'prompt' => 'A beautiful alien planet',
        'description' => 'Test description',
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['error']);
});
