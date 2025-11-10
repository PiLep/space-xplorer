<?php

namespace App\Events;

use App\Models\Resource;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResourceApproved
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
        public Resource $resource,
        public User $approver
    ) {
        //
    }
}
