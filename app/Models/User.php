<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification as CustomResetPasswordNotification;
use Aws\S3\Exception\S3Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
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
}
