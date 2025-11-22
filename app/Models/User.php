<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification as CustomResetPasswordNotification;
use Aws\S3\Exception\S3Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use League\Flysystem\UnableToCheckFileExistence;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasUlids, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'home_planet_id',
        'avatar_url',
        'avatar_generating',
        'is_super_admin',
        'email_verified_at',
        'first_login_at',
        'email_verification_code',
        'email_verification_code_expires_at',
        'email_verification_attempts',
        'email_verification_code_sent_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'first_login_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
            'email_verification_code_expires_at' => 'datetime',
            'email_verification_code_sent_at' => 'datetime',
        ];
    }

    /**
     * Get the user's home planet.
     */
    public function homePlanet(): BelongsTo
    {
        return $this->belongsTo(Planet::class, 'home_planet_id');
    }

    /**
     * Get the user's hire date (uses created_at).
     */
    public function getHiredAtAttribute(): ?\Carbon\Carbon
    {
        return $this->created_at;
    }

    /**
     * Get the user's hire date in universe time.
     */
    public function hiredAtUniverse(): \Carbon\Carbon
    {
        return app(\App\Services\UniverseTimeService::class)
            ->timestampToUniverseTime($this->created_at);
    }

    /**
     * Get formatted hire date in universe time.
     * Format: "Semaine X, Année YYYY"
     */
    public function hiredAtUniverseFormatted(): string
    {
        $universeDate = $this->hiredAtUniverse();

        $service = app(\App\Services\UniverseTimeService::class);
        $year = (int) $universeDate->format('Y');
        $yearStart = \Carbon\Carbon::create($year, 1, 1, 0, 0, 0);
        $daysSinceYearStart = $yearStart->diffInDays($universeDate);
        $week = (int) floor($daysSinceYearStart / 7) + 1;

        if ($week > 52) {
            $year++;
            $week = 1;
        }

        return sprintf('Semaine %d, Année %d', $week, $year);
    }

    /**
     * Calculate seniority (ancienneté) in universe time.
     * Returns array with years, months, weeks, and days.
     */
    public function seniorityUniverse(): array
    {
        $service = app(\App\Services\UniverseTimeService::class);
        $hiredAtUniverse = $this->hiredAtUniverse();
        $currentUniverse = $service->now();

        // Ensure current is after hired
        if ($currentUniverse->lt($hiredAtUniverse)) {
            return [
                'years' => 0,
                'months' => 0,
                'weeks' => 0,
                'days' => 0,
                'total_days' => 0,
            ];
        }

        // Calculate total days first (as integer, rounding down)
        $totalDays = (int) $hiredAtUniverse->diffInDays($currentUniverse, false);

        // Calculate breakdown
        $years = (int) floor($totalDays / 365);
        $remainingDays = $totalDays % 365;

        $months = (int) floor($remainingDays / 30);
        $remainingDays = $remainingDays % 30;

        $weeks = (int) floor($remainingDays / 7);
        $days = $remainingDays % 7;

        return [
            'years' => $years,
            'months' => $months,
            'weeks' => $weeks,
            'days' => $days,
            'total_days' => $totalDays,
        ];
    }

    /**
     * Get formatted seniority (ancienneté) in universe time.
     * Format: "X années, Y mois" or "Y semaines" or "Z jours"
     */
    public function seniorityUniverseFormatted(): string
    {
        $seniority = $this->seniorityUniverse();

        $parts = [];

        if ($seniority['years'] > 0) {
            $parts[] = $seniority['years'].' '.($seniority['years'] > 1 ? 'années' : 'année');
        }

        if ($seniority['months'] > 0) {
            $parts[] = $seniority['months'].' '.($seniority['months'] > 1 ? 'mois' : 'mois');
        }

        if (empty($parts) && $seniority['weeks'] > 0) {
            $parts[] = $seniority['weeks'].' '.($seniority['weeks'] > 1 ? 'semaines' : 'semaine');
        }

        if (empty($parts) && $seniority['days'] > 0) {
            $parts[] = $seniority['days'].' '.($seniority['days'] > 1 ? 'jours' : 'jour');
        }

        if (empty($parts)) {
            // Si moins d'un jour, afficher "moins d'un jour" ou "nouvellement embauché"
            return 'moins d\'un jour';
        }

        return implode(', ', $parts);
    }

    /**
     * Get formatted seniority (ancienneté) in universe time (English).
     * Format: "X years, Y months" or "Y weeks" or "Z days"
     */
    public function seniorityUniverseFormattedEn(): string
    {
        $seniority = $this->seniorityUniverse();

        $parts = [];

        if ($seniority['years'] > 0) {
            $parts[] = $seniority['years'].' '.($seniority['years'] > 1 ? 'years' : 'year');
        }

        if ($seniority['months'] > 0) {
            $parts[] = $seniority['months'].' '.($seniority['months'] > 1 ? 'months' : 'month');
        }

        if (empty($parts) && $seniority['weeks'] > 0) {
            $parts[] = $seniority['weeks'].' '.($seniority['weeks'] > 1 ? 'weeks' : 'week');
        }

        if (empty($parts) && $seniority['days'] > 0) {
            $parts[] = $seniority['days'].' '.($seniority['days'] > 1 ? 'days' : 'day');
        }

        if (empty($parts)) {
            return 'less than a day';
        }

        return implode(', ', $parts);
    }

    /**
     * Get seniority in real time (for comparison).
     * Returns array with years, months, weeks, and days in real time.
     */
    public function seniorityReal(): array
    {
        $hiredAt = $this->created_at;
        $now = now();

        $years = $hiredAt->diffInYears($now);
        $months = $hiredAt->copy()->addYears($years)->diffInMonths($now);
        $weeks = $hiredAt->copy()->addYears($years)->addMonths($months)->diffInWeeks($now);
        $days = $hiredAt->copy()->addYears($years)->addMonths($months)->addWeeks($weeks)->diffInDays($now);

        return [
            'years' => $years,
            'months' => $months,
            'weeks' => $weeks,
            'days' => $days,
            'total_days' => $hiredAt->diffInDays($now),
        ];
    }

    /**
     * Get the messages sent by the user.
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get the messages received by the user.
     */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    /**
     * Get the codex contributions made by the user.
     */
    public function codexContributions(): HasMany
    {
        return $this->hasMany(CodexContribution::class, 'contributor_user_id');
    }

    /**
     * Get the planets discovered by the user.
     */
    public function discoveredPlanets(): HasMany
    {
        return $this->hasMany(CodexEntry::class, 'discovered_by_user_id');
    }

    /**
     * Get the count of unread messages for the user.
     */
    public function unreadMessagesCount(): int
    {
        return $this->receivedMessages()->unread()->count();
    }

    /**
     * Get the avatar URL, reconstructing it from the stored path if needed.
     *
     * This accessor handles both:
     * - Old format: Full URL stored (for backward compatibility)
     * - New format: Path stored (reconstructed dynamically)
     *
     * Returns null if the file doesn't exist in storage or if there's an error accessing storage.
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (! $value) {
                    return null;
                }

                // If it's already a full URL (old format), return as is
                // Note: We can't easily verify existence of external URLs without HTTP requests
                if (filter_var($value, FILTER_VALIDATE_URL)) {
                    return $value;
                }

                // Otherwise, it's a path - verify file exists before returning URL
                $disk = config('image-generation.storage.disk', 's3');

                try {
                    // Check if file exists in storage
                    // Wrap in try-catch to handle S3 errors (403, network issues, etc.)
                    if (! Storage::disk($disk)->exists($value)) {
                        return null;
                    }

                    return Storage::disk($disk)->url($value);
                } catch (UnableToCheckFileExistence $flysystemException) {
                    // Flysystem wraps S3 exceptions, try to extract the underlying S3 exception
                    $previous = $flysystemException->getPrevious();
                    if ($previous instanceof S3Exception) {
                        // Log detailed S3 error information
                        Log::warning('S3 error checking avatar existence in storage', [
                            'path' => $value,
                            'disk' => $disk,
                            's3_error_code' => $previous->getAwsErrorCode(),
                            's3_error_message' => $previous->getAwsErrorMessage(),
                            's3_request_id' => $previous->getAwsRequestId(),
                            'http_status' => $previous->getStatusCode(),
                            'error' => $previous->getMessage(),
                            'flysystem_error' => $flysystemException->getMessage(),
                        ]);
                    } else {
                        // Log Flysystem error without S3 details
                        Log::warning('Flysystem error checking avatar existence in storage', [
                            'path' => $value,
                            'disk' => $disk,
                            'error' => $flysystemException->getMessage(),
                            'previous_exception' => $previous ? get_class($previous) : null,
                        ]);
                    }

                    return null;
                } catch (S3Exception $s3Exception) {
                    // Log detailed S3 error information (direct S3 exception)
                    Log::warning('S3 error checking avatar existence in storage', [
                        'path' => $value,
                        'disk' => $disk,
                        's3_error_code' => $s3Exception->getAwsErrorCode(),
                        's3_error_message' => $s3Exception->getAwsErrorMessage(),
                        's3_request_id' => $s3Exception->getAwsRequestId(),
                        'http_status' => $s3Exception->getStatusCode(),
                        'error' => $s3Exception->getMessage(),
                    ]);

                    return null;
                } catch (\Exception $e) {
                    // Log the error but don't break the application
                    // Return null to indicate the file is not available
                    Log::warning('Error checking avatar existence in storage', [
                        'path' => $value,
                        'disk' => $disk,
                        'error' => $e->getMessage(),
                        'exception_class' => get_class($e),
                    ]);

                    return null;
                }
            }
        );
    }

    /**
     * Get the user's matricule (first 5 characters of ID in uppercase).
     *
     * @return string The matricule code
     */
    protected function matricule(): Attribute
    {
        return Attribute::make(
            get: fn () => strtoupper(substr((string) $this->id, 0, 5))
        );
    }

    /**
     * Check if user avatar is available (not generating and URL exists).
     */
    public function hasAvatar(): bool
    {
        return ! $this->avatar_generating && $this->avatar_url !== null;
    }

    /**
     * Check if user avatar is currently being generated.
     */
    public function isAvatarGenerating(): bool
    {
        return $this->avatar_generating === true;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }

    /**
     * Maximum verification attempts per code.
     */
    public const MAX_VERIFICATION_ATTEMPTS = 5;

    /**
     * Cooldown time in minutes before resending code.
     */
    public const RESEND_COOLDOWN_MINUTES = 2;

    /**
     * Check if the user's email has been verified.
     *
     * @return bool True if email is verified, false otherwise
     */
    public function hasVerifiedEmail(): bool
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Check if the user has a pending verification code that is not expired.
     *
     * @return bool True if a valid verification code exists, false otherwise
     */
    public function hasPendingVerificationCode(): bool
    {
        return $this->email_verification_code !== null
            && $this->email_verification_code_expires_at !== null
            && $this->email_verification_code_expires_at->isFuture();
    }

    /**
     * Check if the user can resend a verification code (cooldown check).
     *
     * @return bool True if cooldown period has passed, false otherwise
     */
    public function canResendVerificationCode(): bool
    {
        if ($this->email_verification_code_sent_at === null) {
            return true;
        }

        $cooldownEnd = $this->email_verification_code_sent_at->copy()->addMinutes(self::RESEND_COOLDOWN_MINUTES);

        return now()->isAfter($cooldownEnd);
    }

    /**
     * Check if the user has exceeded the maximum verification attempts.
     *
     * @return bool True if maximum attempts reached, false otherwise
     */
    public function hasExceededVerificationAttempts(): bool
    {
        return $this->email_verification_attempts >= self::MAX_VERIFICATION_ATTEMPTS;
    }
}
