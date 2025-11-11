<?php

use App\Models\Resource;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

it('has creator relationship', function () {
    $creator = User::factory()->create();
    $resource = Resource::factory()->create(['created_by' => $creator->id]);

    expect($resource->creator)->not->toBeNull()
        ->and($resource->creator->id)->toBe($creator->id);
});

it('has approver relationship', function () {
    $approver = User::factory()->create();
    $resource = Resource::factory()
        ->approved()
        ->create(['approved_by' => $approver->id]);

    expect($resource->approver)->not->toBeNull()
        ->and($resource->approver->id)->toBe($approver->id);
});

it('casts tags to array', function () {
    $tags = ['man', 'casual', 'test'];
    $resource = Resource::factory()->create(['tags' => $tags]);

    expect($resource->tags)->toBeArray()
        ->and($resource->tags)->toBe($tags);
});

it('casts metadata to array', function () {
    $metadata = ['key' => 'value', 'number' => 123];
    $resource = Resource::factory()->create(['metadata' => $metadata]);

    expect($resource->metadata)->toBeArray()
        ->and($resource->metadata)->toBe($metadata);
});

it('returns null for file_url when file_path is empty', function () {
    $resource = Resource::factory()->create(['file_path' => null]);

    expect($resource->file_url)->toBeNull();
});

it('returns full URL when file_path is already a URL', function () {
    $fullUrl = 'https://example.com/file.jpg';
    $resource = Resource::factory()->create(['file_path' => $fullUrl]);

    expect($resource->file_url)->toBe($fullUrl);
});

it('returns storage URL for avatar_image when file exists', function () {
    Storage::fake('s3');
    $path = 'avatars/avatar.jpg';
    Storage::disk('s3')->put($path, 'fake content');

    $resource = Resource::factory()
        ->ofType('avatar_image')
        ->create(['file_path' => $path]);

    expect($resource->file_url)->toBe(Storage::disk('s3')->url($path));
});

it('returns storage URL for planet_image when file exists', function () {
    Storage::fake('s3');
    $path = 'planets/planet.jpg';
    Storage::disk('s3')->put($path, 'fake content');

    $resource = Resource::factory()
        ->ofType('planet_image')
        ->create(['file_path' => $path]);

    expect($resource->file_url)->toBe(Storage::disk('s3')->url($path));
});

it('returns storage URL for planet_video when file exists', function () {
    Storage::fake('s3');
    $path = 'planets/video.mp4';
    Storage::disk('s3')->put($path, 'fake content');

    $resource = Resource::factory()
        ->ofType('planet_video')
        ->create(['file_path' => $path]);

    expect($resource->file_url)->toBe(Storage::disk('s3')->url($path));
});

it('returns null when file_path is a path but file does not exist', function () {
    Storage::fake('s3');
    $path = 'avatars/nonexistent.jpg';

    $resource = Resource::factory()->create(['file_path' => $path]);

    expect($resource->file_url)->toBeNull();
});

it('scopeApproved filters approved resources', function () {
    Resource::factory()->approved()->create();
    Resource::factory()->pending()->create();
    Resource::factory()->rejected()->create();

    $approved = Resource::approved()->get();

    expect($approved)->toHaveCount(1)
        ->and($approved->first()->status)->toBe('approved');
});

it('scopePending filters pending resources', function () {
    Resource::factory()->pending()->create();
    Resource::factory()->approved()->create();
    Resource::factory()->rejected()->create();

    $pending = Resource::pending()->get();

    expect($pending)->toHaveCount(1)
        ->and($pending->first()->status)->toBe('pending');
});

it('scopeRejected filters rejected resources', function () {
    Resource::factory()->rejected()->create();
    Resource::factory()->approved()->create();
    Resource::factory()->pending()->create();

    $rejected = Resource::rejected()->get();

    expect($rejected)->toHaveCount(1)
        ->and($rejected->first()->status)->toBe('rejected');
});

it('scopeGenerating filters generating resources', function () {
    Resource::factory()->generating()->create();
    Resource::factory()->approved()->create();
    Resource::factory()->pending()->create();

    $generating = Resource::generating()->get();

    expect($generating)->toHaveCount(1)
        ->and($generating->first()->status)->toBe('generating');
});

it('scopeOfType filters by resource type', function () {
    Resource::factory()->ofType('avatar_image')->create();
    Resource::factory()->ofType('planet_image')->create();
    Resource::factory()->ofType('planet_video')->create();

    $avatars = Resource::ofType('avatar_image')->get();

    expect($avatars)->toHaveCount(1)
        ->and($avatars->first()->type)->toBe('avatar_image');
});

it('scopeWithMatchingTags filters resources by tags', function () {
    Resource::factory()->create(['tags' => ['man', 'casual']]);
    Resource::factory()->create(['tags' => ['woman', 'formal']]);
    Resource::factory()->create(['tags' => ['man', 'formal']]);

    $matching = Resource::withMatchingTags(['man'])->get();

    expect($matching)->toHaveCount(2);
});

it('scopeWithMatchingTags returns all when tags array is empty', function () {
    Resource::factory()->create();
    Resource::factory()->create();

    $all = Resource::withMatchingTags([])->get();

    expect($all)->toHaveCount(2);
});

it('scopeWithMatchingTags normalizes search tags to lowercase', function () {
    // Create resources with lowercase tags (as they should be stored)
    Resource::factory()->create(['tags' => ['man', 'casual']]);
    Resource::factory()->create(['tags' => ['woman']]);

    // Search with mixed case tags - scope should normalize them to lowercase
    $matching = Resource::withMatchingTags(['MAN', 'Casual'])->get();

    // Should find the resource with 'man' and 'casual' tags
    expect($matching)->toHaveCount(1)
        ->and($matching->first()->tags)->toContain('man', 'casual');
});

it('hasValidFile returns true when file_url is not null', function () {
    Storage::fake('s3');
    $path = 'avatars/avatar.jpg';
    Storage::disk('s3')->put($path, 'fake content');

    $resource = Resource::factory()->create(['file_path' => $path]);

    expect($resource->hasValidFile())->toBeTrue();
});

it('hasValidFile returns false when file_url is null', function () {
    Storage::fake('s3');
    $path = 'avatars/nonexistent.jpg';

    $resource = Resource::factory()->create(['file_path' => $path]);

    expect($resource->hasValidFile())->toBeFalse();
});

it('findRandomApproved returns random approved resource of type', function () {
    Resource::factory()->approved()->ofType('avatar_image')->create();
    Resource::factory()->approved()->ofType('avatar_image')->create();
    Resource::factory()->pending()->ofType('avatar_image')->create();

    $resource = Resource::findRandomApproved('avatar_image');

    expect($resource)->not->toBeNull()
        ->and($resource->type)->toBe('avatar_image')
        ->and($resource->status)->toBe('approved');
});

it('findRandomApproved returns null when no approved resources exist', function () {
    Resource::factory()->pending()->ofType('avatar_image')->create();

    $resource = Resource::findRandomApproved('avatar_image');

    expect($resource)->toBeNull();
});

it('findRandomApproved filters by tags when provided', function () {
    Resource::factory()
        ->approved()
        ->ofType('avatar_image')
        ->create(['tags' => ['man', 'casual']]);
    Resource::factory()
        ->approved()
        ->ofType('avatar_image')
        ->create(['tags' => ['woman', 'formal']]);

    $resource = Resource::findRandomApproved('avatar_image', ['man']);

    expect($resource)->not->toBeNull()
        ->and($resource->tags)->toContain('man');
});

it('approve updates resource status to approved', function () {
    $user = User::factory()->create();
    $resource = Resource::factory()->pending()->create();

    $result = $resource->approve($user);

    expect($result)->toBeTrue();
    $resource->refresh();
    expect($resource->status)->toBe('approved')
        ->and($resource->approved_by)->toBe($user->id)
        ->and($resource->approved_at)->not->toBeNull()
        ->and($resource->rejection_reason)->toBeNull();
});

it('reject updates resource status to rejected', function () {
    $user = User::factory()->create();
    $resource = Resource::factory()->pending()->create();
    $reason = 'Inappropriate content';

    $result = $resource->reject($user, $reason);

    expect($result)->toBeTrue();
    $resource->refresh();
    expect($resource->status)->toBe('rejected')
        ->and($resource->approved_by)->toBe($user->id)
        ->and($resource->approved_at)->not->toBeNull()
        ->and($resource->rejection_reason)->toBe($reason);
});

it('reject works without reason', function () {
    $user = User::factory()->create();
    $resource = Resource::factory()->pending()->create();

    $result = $resource->reject($user);

    expect($result)->toBeTrue();
    $resource->refresh();
    expect($resource->status)->toBe('rejected')
        ->and($resource->rejection_reason)->toBeNull();
});

it('isApproved returns true for approved resources', function () {
    $resource = Resource::factory()->approved()->create();

    expect($resource->isApproved())->toBeTrue();
});

it('isApproved returns false for non-approved resources', function () {
    $resource = Resource::factory()->pending()->create();

    expect($resource->isApproved())->toBeFalse();
});

it('isPending returns true for pending resources', function () {
    $resource = Resource::factory()->pending()->create();

    expect($resource->isPending())->toBeTrue();
});

it('isPending returns false for non-pending resources', function () {
    $resource = Resource::factory()->approved()->create();

    expect($resource->isPending())->toBeFalse();
});

it('isRejected returns true for rejected resources', function () {
    $resource = Resource::factory()->rejected()->create();

    expect($resource->isRejected())->toBeTrue();
});

it('isRejected returns false for non-rejected resources', function () {
    $resource = Resource::factory()->approved()->create();

    expect($resource->isRejected())->toBeFalse();
});

it('isGenerating returns true for generating resources', function () {
    $resource = Resource::factory()->generating()->create();

    expect($resource->isGenerating())->toBeTrue();
});

it('isGenerating returns false for non-generating resources', function () {
    $resource = Resource::factory()->approved()->create();

    expect($resource->isGenerating())->toBeFalse();
});
