<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AvatarChanged
{
    use Dispatchable, SerializesModels;

    /**
     * Indicate that the event should be broadcast.
     *
     * @var bool
     */
    public $shouldBroadcast = false;

    /**
     * The old avatar path.
     */
    public ?string $oldAvatarPath;

    /**
     * The new avatar path.
     */
    public string $newAvatarPath;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public User $user,
        ?string $oldAvatarPath = null,
        string $newAvatarPath = ''
    ) {
        $this->oldAvatarPath = $oldAvatarPath;
        $this->newAvatarPath = $newAvatarPath;
    }
}
