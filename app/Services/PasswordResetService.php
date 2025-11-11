<?php

namespace App\Services;

use App\Events\PasswordResetCompleted;
use App\Events\PasswordResetRequested;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;

class PasswordResetService
{
    /**
     * Send password reset link to the user.
     *
     * @return string Status of the password reset link sending
     */
    public function sendResetLink(string $email): string
    {
        // Dispatch event for tracking
        event(new PasswordResetRequested($email));

        // Send reset link using Laravel's Password facade
        $status = Password::sendResetLink(['email' => $email]);

        return $status;
    }

    /**
     * Reset the user's password.
     *
     * @return string Status of the password reset
     */
    public function reset(array $credentials): string
    {
        // Reset password using Laravel's Password facade
        $status = Password::reset(
            $credentials,
            function (User $user, string $password) {
                // Update password
                $user->password = \Illuminate\Support\Facades\Hash::make($password);
                $user->save();

                // Invalidate Remember Me tokens
                $this->invalidateRememberMe($user);

                // Invalidate all web sessions for security
                $this->invalidateSessions($user);

                // Dispatch event for tracking
                event(new PasswordResetCompleted($user));
            }
        );

        return $status;
    }

    /**
     * Invalidate all Remember Me tokens for the user.
     */
    public function invalidateRememberMe(User $user): void
    {
        // Invalidate all remember tokens for this user
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->whereNotNull('user_id')
            ->delete();
    }

    /**
     * Invalidate all web sessions for the user.
     */
    public function invalidateSessions(User $user): void
    {
        // Invalidate all sessions for this user
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->delete();
    }
}
