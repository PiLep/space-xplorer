<?php

namespace App\Services;

use App\Exceptions\EmailVerificationException;
use App\Mail\EmailVerificationNotification;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailVerificationService
{
    /**
     * Code expiration time in minutes.
     */
    private const CODE_EXPIRATION_MINUTES = 15;

    /**
     * Maximum verification attempts per code.
     */
    private const MAX_VERIFICATION_ATTEMPTS = 5;

    /**
     * Cooldown time in minutes before resending code.
     */
    private const RESEND_COOLDOWN_MINUTES = 2;

    /**
     * Generate a 6-digit verification code, hash it, store it, and send it via email.
     *
     * @return string The plain code (for testing purposes)
     */
    public function generateCode(User $user): string
    {
        // Generate cryptographically secure 6-digit code
        $code = (string) random_int(100000, 999999);

        // Hash the code before storing
        $hashedCode = Hash::make($code);

        // Calculate expiration time
        $expiresAt = now()->addMinutes(self::CODE_EXPIRATION_MINUTES);

        // Update user with verification code
        $user->update([
            'email_verification_code' => $hashedCode,
            'email_verification_code_expires_at' => $expiresAt,
            'email_verification_attempts' => 0,
            'email_verification_code_sent_at' => now(),
        ]);

        // Send email with code
        try {
            Mail::to($user->email)->send(new EmailVerificationNotification($code, $user));
        } catch (\Exception $e) {
            // Log error but don't block the process
            Log::error('Failed to send email verification code', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }

        return $code;
    }

    /**
     * Verify the code and mark email as verified if correct.
     *
     * @return bool True if code is valid and email is verified, false otherwise
     */
    public function verifyCode(User $user, string $code): bool
    {
        // Check if user has exceeded attempts
        if ($user->hasExceededVerificationAttempts()) {
            return false;
        }

        // Check if code exists and is not expired
        if (! $user->hasPendingVerificationCode()) {
            return false;
        }

        // Increment attempts before verification
        $user->increment('email_verification_attempts');

        // Verify the code
        if (! Hash::check($code, $user->email_verification_code)) {
            return false;
        }

        // Check if code is expired
        if ($user->email_verification_code_expires_at->isPast()) {
            return false;
        }

        // Code is valid - mark email as verified and clear verification code
        $user->update([
            'email_verified_at' => now(),
            'email_verification_code' => null,
            'email_verification_code_expires_at' => null,
            'email_verification_attempts' => 0,
            'email_verification_code_sent_at' => null,
        ]);

        return true;
    }

    /**
     * Resend verification code with cooldown check.
     *
     * @throws EmailVerificationException If cooldown period has not passed
     */
    public function resendCode(User $user): void
    {
        // Check if user can resend (cooldown check)
        if (! $user->canResendVerificationCode()) {
            throw new EmailVerificationException('Please wait before requesting a new code.');
        }

        // Generate and send new code
        $this->generateCode($user);
    }

    /**
     * Check if code is valid without incrementing attempts (for validation purposes).
     *
     * @return bool True if code is valid and not expired, false otherwise
     */
    public function isCodeValid(User $user, string $code): bool
    {
        // Check if code exists and is not expired
        if (! $user->hasPendingVerificationCode()) {
            return false;
        }

        // Check if user has exceeded attempts
        if ($user->hasExceededVerificationAttempts()) {
            return false;
        }

        // Verify the code
        if (! Hash::check($code, $user->email_verification_code)) {
            return false;
        }

        // Check if code is expired
        if ($user->email_verification_code_expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Clear verification code after successful verification or expiration.
     */
    public function clearVerificationCode(User $user): void
    {
        $user->update([
            'email_verification_code' => null,
            'email_verification_code_expires_at' => null,
            'email_verification_attempts' => 0,
            'email_verification_code_sent_at' => null,
        ]);
    }
}
