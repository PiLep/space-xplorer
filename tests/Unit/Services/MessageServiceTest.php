<?php

use App\Models\Message;
use App\Models\Planet;
use App\Models\User;
use App\Services\MessageService;

beforeEach(function () {
    $this->service = new MessageService;
    $this->user = User::factory()->create();
});

it('creates system message', function () {
    $message = $this->service->createSystemMessage(
        $this->user,
        'Test Subject',
        'Test Content',
        ['key' => 'value']
    );

    expect($message)->toBeInstanceOf(Message::class)
        ->and($message->sender_id)->toBeNull()
        ->and($message->recipient_id)->toBe($this->user->id)
        ->and($message->type)->toBe('system')
        ->and($message->subject)->toBe('Test Subject')
        ->and($message->content)->toBe('Test Content')
        ->and($message->metadata)->toBe(['key' => 'value']);
});

it('creates welcome message', function () {
    $message = $this->service->createWelcomeMessage($this->user);

    expect($message)->toBeInstanceOf(Message::class)
        ->and($message->sender_id)->toBeNull()
        ->and($message->recipient_id)->toBe($this->user->id)
        ->and($message->type)->toBe('welcome')
        ->and($message->subject)->toBe('Bienvenue dans l\'univers Stellar')
        ->and($message->content)->toContain($this->user->name)
        ->and($message->content)->toContain($this->user->matricule)
        ->and($message->metadata['type'])->toBe('welcome');
});

it('creates discovery message from planet', function () {
    $planet = Planet::factory()->create();
    $message = $this->service->createDiscoveryMessage($this->user, $planet);

    expect($message)->toBeInstanceOf(Message::class)
        ->and($message->sender_id)->toBeNull()
        ->and($message->recipient_id)->toBe($this->user->id)
        ->and($message->type)->toBe('discovery')
        ->and($message->subject)->toBe('Nouvelle planète découverte')
        ->and($message->content)->toContain($planet->name)
        ->and($message->metadata['planet_id'])->toBe($planet->id)
        ->and($message->metadata['planet_name'])->toBe($planet->name);
});

it('creates discovery message from array', function () {
    $discoveryData = [
        'discovery_type' => 'special',
        'name' => 'Ancient Artifact',
    ];
    $message = $this->service->createDiscoveryMessage($this->user, $discoveryData);

    expect($message)->toBeInstanceOf(Message::class)
        ->and($message->sender_id)->toBeNull()
        ->and($message->recipient_id)->toBe($this->user->id)
        ->and($message->type)->toBe('discovery')
        ->and($message->subject)->toBe('Découverte spéciale')
        ->and($message->metadata['type'])->toBe('discovery')
        ->and($message->metadata['name'])->toBe('Ancient Artifact');
});

it('creates discovery message with custom subject and content', function () {
    $planet = Planet::factory()->create();
    $message = $this->service->createDiscoveryMessage(
        $this->user,
        $planet,
        'Custom Subject',
        'Custom Content'
    );

    expect($message->subject)->toBe('Custom Subject')
        ->and($message->content)->toBe('Custom Content');
});

it('creates mission message', function () {
    $message = $this->service->createMissionMessage(
        $this->user,
        'Mission Subject',
        'Mission Content',
        ['mission_id' => '123']
    );

    expect($message)->toBeInstanceOf(Message::class)
        ->and($message->sender_id)->toBeNull()
        ->and($message->recipient_id)->toBe($this->user->id)
        ->and($message->type)->toBe('mission')
        ->and($message->subject)->toBe('Mission Subject')
        ->and($message->content)->toBe('Mission Content')
        ->and($message->metadata['type'])->toBe('mission')
        ->and($message->metadata['mission_id'])->toBe('123');
});

it('creates alert message', function () {
    $message = $this->service->createAlertMessage(
        $this->user,
        'Alert Subject',
        'Alert Content',
        ['priority' => 'high']
    );

    expect($message)->toBeInstanceOf(Message::class)
        ->and($message->sender_id)->toBeNull()
        ->and($message->recipient_id)->toBe($this->user->id)
        ->and($message->type)->toBe('alert')
        ->and($message->subject)->toBe('Alert Subject')
        ->and($message->content)->toBe('Alert Content')
        ->and($message->is_important)->toBeTrue()
        ->and($message->metadata['type'])->toBe('alert')
        ->and($message->metadata['priority'])->toBe('high');
});

it('gets messages for user with pagination', function () {
    Message::factory()->to($this->user)->count(25)->create();

    $messages = $this->service->getMessagesForUser($this->user, 'all', null, 20);

    expect($messages)->toHaveCount(20)
        ->and($messages->total())->toBe(25)
        ->and($messages->perPage())->toBe(20);
});

it('filters messages by unread status', function () {
    Message::factory()->to($this->user)->unread()->count(5)->create();
    Message::factory()->to($this->user)->read()->count(3)->create();

    $messages = $this->service->getMessagesForUser($this->user, 'unread');

    expect($messages->total())->toBe(5)
        ->and($messages->every(fn ($msg) => $msg->is_read === false))->toBeTrue();
});

it('filters messages by read status', function () {
    Message::factory()->to($this->user)->unread()->count(5)->create();
    Message::factory()->to($this->user)->read()->count(3)->create();

    $messages = $this->service->getMessagesForUser($this->user, 'read');

    expect($messages->total())->toBe(3)
        ->and($messages->every(fn ($msg) => $msg->is_read === true))->toBeTrue();
});

it('filters messages by type', function () {
    Message::factory()->to($this->user)->ofType('welcome')->count(3)->create();
    Message::factory()->to($this->user)->ofType('discovery')->count(2)->create();

    $messages = $this->service->getMessagesForUser($this->user, 'all', 'welcome');

    expect($messages->total())->toBe(3)
        ->and($messages->every(fn ($msg) => $msg->type === 'welcome'))->toBeTrue();
});

it('orders messages by created_at descending', function () {
    $oldMessage = Message::factory()->to($this->user)->create(['created_at' => now()->subDay()]);
    $newMessage = Message::factory()->to($this->user)->create(['created_at' => now()]);

    $messages = $this->service->getMessagesForUser($this->user);

    expect($messages->first()->id)->toBe($newMessage->id)
        ->and($messages->last()->id)->toBe($oldMessage->id);
});

it('only returns messages for specified user', function () {
    $otherUser = User::factory()->create();
    Message::factory()->to($this->user)->count(3)->create();
    Message::factory()->to($otherUser)->count(2)->create();

    $messages = $this->service->getMessagesForUser($this->user);

    expect($messages->total())->toBe(3)
        ->and($messages->every(fn ($msg) => $msg->recipient_id === $this->user->id))->toBeTrue();
});

it('filters messages by trash status', function () {
    $message1 = Message::factory()->to($this->user)->create();
    $message2 = Message::factory()->to($this->user)->create();
    $message3 = Message::factory()->to($this->user)->create();

    $message2->delete(); // Soft delete
    $message3->delete(); // Soft delete

    $messages = $this->service->getMessagesForUser($this->user, 'trash');

    expect($messages->total())->toBe(2)
        ->and($messages->every(fn ($msg) => $msg->trashed() === true))->toBeTrue()
        ->and($messages->pluck('id')->contains($message2->id))->toBeTrue()
        ->and($messages->pluck('id')->contains($message3->id))->toBeTrue()
        ->and($messages->pluck('id')->contains($message1->id))->toBeFalse();
});

