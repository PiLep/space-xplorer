<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Planet extends Model
{
    use HasFactory, HasUlids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'size',
        'temperature',
        'atmosphere',
        'terrain',
        'resources',
        'description',
        'image_url',
    ];

    /**
     * Get the users that have this planet as their home planet.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'home_planet_id');
    }

    /**
     * Get the planet image URL, reconstructing it from the stored path if needed.
     *
     * This accessor handles both:
     * - Old format: Full URL stored (for backward compatibility)
     * - New format: Path stored (reconstructed dynamically)
     *
     * Returns null if the file doesn't exist in storage.
     */
    protected function imageUrl(): Attribute
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
