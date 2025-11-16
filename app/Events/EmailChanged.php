<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailChanged
{
    use Dispatchable, SerializesModels;

    /**
     * Indicate that the event should be broadcast.
     *
     * @var bool
     */
    public $shouldBroadcast = false;

    /**
     * The old email address.
     */
    public string $oldEmail;

    /**
     * The new email address.
     */
    public string $newEmail;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public User $user,
        string $oldEmail,
        string $newEmail
    ) {
        $this->oldEmail = $oldEmail;
        $this->newEmail = $newEmail;
    }
}
