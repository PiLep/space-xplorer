<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Test event class for testing scenarios without user property.
 * This is used in unit tests to verify fallback to authenticated user.
 */
class TestEvent
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
    public function __construct()
    {
        //
    }
}

