<?php

use App\Events\ResourceApproved;
use App\Events\ResourceRejected;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'email' => 'admin@example.com',
        'is_super_admin' => true,
    ]);

    // Set empty whitelist for tests
    config(['admin.email_whitelist' => '']);

    Auth::guard('admin')->login($this->admin);

    // Fake storage for file existence checks
    Storage::fake('s3');
});

it('renders resource review component successfully', function () {
    Livewire::test(\App\Livewire\Admin\ResourceReview::class)
        ->assertStatus(200);
});

it('loads next pending resource with valid file on mount', function () {
    $creator = User::factory()->create();
    $resource = Resource::factory()
        ->pending()
        ->ofType('avatar_image')
        ->create([
            'created_by' => $creator->id,
            'file_path' => 'avatars/test.jpg',
        ]);

    Storage::disk('s3')->put('avatars/test.jpg', 'fake content');

    Livewire::test(\App\Livewire\Admin\ResourceReview::class)
        ->assertSet('currentResource.id', $resource->id)
        ->assertSet('pendingCount', 1);
});

it('skips resources without valid files', function () {
    $creator = User::factory()->create();

    // Create resource without file
    Resource::factory()
        ->pending()
        ->ofType('avatar_image')
        ->create([
            'created_by' => $creator->id,
            'file_path' => null,
        ]);

    // Create resource with valid file
    $validResource = Resource::factory()
        ->pending()
        ->ofType('avatar_image')
        ->create([
            'created_by' => $creator->id,
            'file_path' => 'avatars/valid.jpg',
        ]);

    Storage::disk('s3')->put('avatars/valid.jpg', 'fake content');

    Livewire::test(\App\Livewire\Admin\ResourceReview::class)
        ->assertSet('currentResource.id', $validResource->id)
        ->assertSet('pendingCount', 1);
});

it('shows zero pending count when no resources exist', function () {
    Livewire::test(\App\Livewire\Admin\ResourceReview::class)
        ->assertSet('currentResource', null)
        ->assertSet('pendingCount', 0);
});

it('counts only pending resources with valid files', function () {
    $creator = User::factory()->create();

    // Create pending resources with valid files
    $resource1 = Resource::factory()
        ->pending()
        ->ofType('avatar_image')
        ->create([
            'created_by' => $creator->id,
            'file_path' => 'avatars/test1.jpg',
        ]);

    $resource2 = Resource::factory()
        ->pending()
        ->ofType('planet_image')
        ->create([
            'created_by' => $creator->id,
            'file_path' => 'planets/test2.jpg',
        ]);

    // Create pending resource without valid file
    Resource::factory()
        ->pending()
        ->ofType('avatar_image')
        ->create([
            'created_by' => $creator->id,
            'file_path' => null,
        ]);

    // Create approved resource (should not be counted)
    Resource::factory()
        ->approved()
        ->ofType('avatar_image')
        ->create([
            'created_by' => $creator->id,
            'file_path' => 'avatars/approved.jpg',
        ]);

    Storage::disk('s3')->put('avatars/test1.jpg', 'fake content');
    Storage::disk('s3')->put('planets/test2.jpg', 'fake content');

    Livewire::test(\App\Livewire\Admin\ResourceReview::class)
        ->assertSet('currentResource.id', $resource1->id)
        ->assertSet('pendingCount', 2);
});

it('approves resource successfully', function () {
    Event::fake();

    $creator = User::factory()->create();
    $resource = Resource::factory()
        ->pending()
        ->ofType('avatar_image')
        ->create([
            'created_by' => $creator->id,
            'file_path' => 'avatars/test.jpg',
        ]);

    Storage::disk('s3')->put('avatars/test.jpg', 'fake content');

    Livewire::test(\App\Livewire\Admin\ResourceReview::class)
        ->call('approve')
        ->assertSet('currentResource', null);

    $resource->refresh();
    expect($resource->status)->toBe('approved')
        ->and($resource->approved_by)->toBe($this->admin->id)
        ->and($resource->approved_at)->not->toBeNull();

    Event::assertDispatched(ResourceApproved::class, function ($event) use ($resource) {
        return $event->resource->id === $resource->id
            && $event->approver->id === $this->admin->id;
    });
});

it('does not approve when no current resource', function () {
    Event::fake([ResourceApproved::class]);

    Livewire::test(\App\Livewire\Admin\ResourceReview::class)
        ->set('currentResource', null)
        ->call('approve');

    Event::assertNotDispatched(ResourceApproved::class);
});

it('opens reject modal', function () {
    $creator = User::factory()->create();
    $resource = Resource::factory()
        ->pending()
        ->ofType('avatar_image')
        ->create([
            'created_by' => $creator->id,
            'file_path' => 'avatars/test.jpg',
        ]);

    Storage::disk('s3')->put('avatars/test.jpg', 'fake content');

    Livewire::test(\App\Livewire\Admin\ResourceReview::class)
        ->call('openRejectModal')
        ->assertSet('showRejectModal', true);
});

it('closes reject modal', function () {
    Livewire::test(\App\Livewire\Admin\ResourceReview::class)
        ->set('showRejectModal', true)
        ->set('rejectionReason', 'Some reason')
        ->call('closeRejectModal')
        ->assertSet('showRejectModal', false)
        ->assertSet('rejectionReason', '');
});

it('rejects resource with reason', function () {
    Event::fake();

    $creator = User::factory()->create();
    $resource = Resource::factory()
        ->pending()
        ->ofType('avatar_image')
        ->create([
            'created_by' => $creator->id,
            'file_path' => 'avatars/test.jpg',
        ]);

    Storage::disk('s3')->put('avatars/test.jpg', 'fake content');

    Livewire::test(\App\Livewire\Admin\ResourceReview::class)
        ->set('rejectionReason', 'Quality not good enough')
        ->call('reject')
        ->assertSet('currentResource', null);

    $resource->refresh();
    expect($resource->status)->toBe('rejected')
        ->and($resource->approved_by)->toBe($this->admin->id)
        ->and($resource->rejection_reason)->toBe('Quality not good enough')
        ->and($resource->approved_at)->not->toBeNull();

    Event::assertDispatched(ResourceRejected::class, function ($event) use ($resource) {
        return $event->resource->id === $resource->id
            && $event->approver->id === $this->admin->id
            && $event->reason === 'Quality not good enough';
    });
});

it('rejects resource without reason', function () {
    Event::fake();

    $creator = User::factory()->create();
    $resource = Resource::factory()
        ->pending()
        ->ofType('avatar_image')
        ->create([
            'created_by' => $creator->id,
            'file_path' => 'avatars/test.jpg',
        ]);

    Storage::disk('s3')->put('avatars/test.jpg', 'fake content');

    Livewire::test(\App\Livewire\Admin\ResourceReview::class)
        ->set('rejectionReason', '')
        ->call('reject')
        ->assertSet('currentResource', null);

    $resource->refresh();
    expect($resource->status)->toBe('rejected')
        ->and($resource->rejection_reason)->toBeNull();

    Event::assertDispatched(ResourceRejected::class, function ($event) use ($resource) {
        return $event->resource->id === $resource->id
            && $event->approver->id === $this->admin->id
            && $event->reason === null;
    });
});

it('does not reject when no current resource', function () {
    Event::fake([ResourceRejected::class]);

    Livewire::test(\App\Livewire\Admin\ResourceReview::class)
        ->set('currentResource', null)
        ->call('reject');

    Event::assertNotDispatched(ResourceRejected::class);
});

it('loads next resource after approval', function () {
    $creator = User::factory()->create();

    $resource1 = Resource::factory()
        ->pending()
        ->ofType('avatar_image')
        ->create([
            'created_by' => $creator->id,
            'file_path' => 'avatars/test1.jpg',
        ]);

    $resource2 = Resource::factory()
        ->pending()
        ->ofType('avatar_image')
        ->create([
            'created_by' => $creator->id,
            'file_path' => 'avatars/test2.jpg',
        ]);

    Storage::disk('s3')->put('avatars/test1.jpg', 'fake content');
    Storage::disk('s3')->put('avatars/test2.jpg', 'fake content');

    $component = Livewire::test(\App\Livewire\Admin\ResourceReview::class);

    expect($component->get('currentResource')->id)->toBe($resource1->id);

    $component->call('approve');

    expect($component->get('currentResource')->id)->toBe($resource2->id);
});

it('loads next resource after rejection', function () {
    $creator = User::factory()->create();

    $resource1 = Resource::factory()
        ->pending()
        ->ofType('avatar_image')
        ->create([
            'created_by' => $creator->id,
            'file_path' => 'avatars/test1.jpg',
        ]);

    $resource2 = Resource::factory()
        ->pending()
        ->ofType('avatar_image')
        ->create([
            'created_by' => $creator->id,
            'file_path' => 'avatars/test2.jpg',
        ]);

    Storage::disk('s3')->put('avatars/test1.jpg', 'fake content');
    Storage::disk('s3')->put('avatars/test2.jpg', 'fake content');

    $component = Livewire::test(\App\Livewire\Admin\ResourceReview::class);

    expect($component->get('currentResource')->id)->toBe($resource1->id);

    $component->call('reject');

    expect($component->get('currentResource')->id)->toBe($resource2->id);
});

it('loads resources in oldest first order', function () {
    $creator = User::factory()->create();

    $oldResource = Resource::factory()
        ->pending()
        ->ofType('avatar_image')
        ->create([
            'created_by' => $creator->id,
            'file_path' => 'avatars/old.jpg',
            'created_at' => now()->subDays(2),
        ]);

    Resource::factory()
        ->pending()
        ->ofType('avatar_image')
        ->create([
            'created_by' => $creator->id,
            'file_path' => 'avatars/new.jpg',
            'created_at' => now(),
        ]);

    Storage::disk('s3')->put('avatars/old.jpg', 'fake content');
    Storage::disk('s3')->put('avatars/new.jpg', 'fake content');

    Livewire::test(\App\Livewire\Admin\ResourceReview::class)
        ->assertSet('currentResource.id', $oldResource->id);
});

it('handles batch loading of resources', function () {
    $creator = User::factory()->create();

    // Create more than batch size (20) resources
    for ($i = 0; $i < 25; $i++) {
        Resource::factory()
            ->pending()
            ->ofType('avatar_image')
            ->create([
                'created_by' => $creator->id,
                'file_path' => "avatars/test{$i}.jpg",
            ]);
        Storage::disk('s3')->put("avatars/test{$i}.jpg", 'fake content');
    }

    Livewire::test(\App\Livewire\Admin\ResourceReview::class)
        ->assertSet('currentResource', function ($resource) {
            return $resource !== null;
        })
        ->assertSet('pendingCount', 25);
});
