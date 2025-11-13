<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FailedLoginAttempt
{
    use Dispatchable, SerializesModels;

    /**
     * Indicate that the event should be broadcast.
     *
     * @var bool
     */
    public $shouldBroadcast = false;

    /**
     * The email address used in the failed login attempt.
     */
    public string $email;

    /**
     * The IP address from which the attempt was made.
     */
    public ?string $ipAddress;

    /**
     * The user agent from which the attempt was made.
     */
    public ?string $userAgent;

    /**
     * Create a new event instance.
     */
    public function __construct(
        string $email,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ) {
        $this->email = $email;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
    }
}

