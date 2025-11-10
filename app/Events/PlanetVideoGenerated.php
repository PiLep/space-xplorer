<?php

namespace App\Events;

use App\Models\Planet;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlanetVideoGenerated
{
    use Dispatchable, SerializesModels;

    /**
     * Indicate that the event should be broadcast.
     *
     * @var bool
     */
    public $shouldBroadcast = false;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Planet $planet,
        public string $videoPath,
        public string $videoUrl
    ) {
        //
    }
}
