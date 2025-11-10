<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserProfileUpdated
{
    use Dispatchable, SerializesModels;

    /**
     * Indicate that the event should be broadcast.
     *
     * @var bool
     */
    public $shouldBroadcast = false;

    /**
     * The attributes that were updated.
     *
     * @var array<string, mixed>
     */
    public array $changedAttributes;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public User $user,
        array $changedAttributes = []
    ) {
        $this->changedAttributes = $changedAttributes;
    }
}
