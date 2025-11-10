<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DiscoveryMade
{
    use Dispatchable, SerializesModels;

    /**
     * Indicate that the event should be broadcast.
     *
     * @var bool
     */
    public $shouldBroadcast = false;

    /**
     * The type of discovery made.
     */
    public string $discoveryType;

    /**
     * Additional data about the discovery.
     *
     * @var array<string, mixed>
     */
    public array $discoveryData;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public User $user,
        string $discoveryType,
        array $discoveryData = []
    ) {
        $this->discoveryType = $discoveryType;
        $this->discoveryData = $discoveryData;
    }
}
