<?php

namespace App\Events;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReceived
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
        public Message $message,
        public User $recipient
    ) {
        //
    }
}
