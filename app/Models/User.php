<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

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
     * Returns null if the file doesn't exist in storage.
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

                // Check if file exists in storage
                if (! Storage::disk($disk)->exists($value)) {
                    return null;
                }

                return Storage::disk($disk)->url($value);
            }
        );
    }
}
