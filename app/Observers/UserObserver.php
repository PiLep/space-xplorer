<?php

namespace App\Observers;

use App\Events\UserDeleted;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "deleted" event.
     *
     * Dispatches UserDeleted event after the user is deleted
     * to allow cleanup of related data (sessions, tokens, etc.)
     */
    public function deleted(User $user): void
    {
        event(new UserDeleted($user));
    }
}

