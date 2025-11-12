<?php

namespace App\Listeners;

use App\Events\UserDeleted;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupUserData
{
    /**
     * Handle the event.
     *
     * Cleans up all user-related data after deletion:
     * - Deletes Sanctum tokens
     * - Deletes user sessions
     *
     * Note: Avatar is kept in storage as it may be reused by other users.
     */
    public function handle(UserDeleted $event): void
    {
        $user = $event->user;

        try {
            // 1. Delete Sanctum tokens
            $this->deleteSanctumTokens($user);

            // 2. Delete user sessions
            $this->deleteSessions($user);

            Log::info('User data cleaned up successfully', [
                'user_id' => $user->id,
                'user_email' => $user->email,
            ]);
        } catch (\Exception $e) {
            // Log error but don't block deletion
            Log::error('Error during user data cleanup', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Delete all Sanctum tokens for the user.
     */
    protected function deleteSanctumTokens($user): void
    {
        $deleted = DB::table('personal_access_tokens')
            ->where('tokenable_type', User::class)
            ->where('tokenable_id', $user->id)
            ->delete();

        if ($deleted > 0) {
            Log::info('Sanctum tokens deleted', [
                'user_id' => $user->id,
                'tokens_deleted' => $deleted,
            ]);
        }
    }

    /**
     * Delete all sessions for the user.
     */
    protected function deleteSessions($user): void
    {
        $deleted = DB::table('sessions')
            ->where('user_id', $user->id)
            ->delete();

        if ($deleted > 0) {
            Log::info('User sessions deleted', [
                'user_id' => $user->id,
                'sessions_deleted' => $deleted,
            ]);
        }
    }
}

