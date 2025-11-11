<?php

namespace App\Models;

use Aws\S3\Exception\S3Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToCheckFileExistence;

class Resource extends Model
{
    use HasFactory, HasUlids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'status',
        'file_path',
        'prompt',
        'tags',
        'description',
        'metadata',
        'created_by',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'metadata' => 'array',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Get the user who created this resource.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved this resource.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the file URL, reconstructing it from the stored path if needed.
     *
     * This accessor handles both:
     * - Old format: Full URL stored (for backward compatibility)
     * - New format: Path stored (reconstructed dynamically)
     *
     * Returns null if the file doesn't exist in storage or if there's an error accessing storage.
     */
    protected function fileUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $filePath = $this->file_path;

                if (! $filePath) {
                    return null;
                }

                // If it's already a full URL (old format), return as is
                if (filter_var($filePath, FILTER_VALIDATE_URL)) {
                    return $filePath;
                }

                // Determine storage disk based on resource type
                $disk = match ($this->type) {
                    'avatar_image', 'planet_image' => config('image-generation.storage.disk', 's3'),
                    'planet_video' => config('video-generation.storage.disk', 's3'),
                    default => 's3',
                };

                try {
                    // Check if file exists in storage
                    if (! Storage::disk($disk)->exists($filePath)) {
                        return null;
                    }

                    return Storage::disk($disk)->url($filePath);
                } catch (UnableToCheckFileExistence $flysystemException) {
                    $previous = $flysystemException->getPrevious();
                    if ($previous instanceof S3Exception) {
                        Log::warning('S3 error checking resource file existence in storage', [
                            'resource_id' => $this->id,
                            'path' => $filePath,
                            'disk' => $disk,
                            's3_error_code' => $previous->getAwsErrorCode(),
                            's3_error_message' => $previous->getAwsErrorMessage(),
                            's3_request_id' => $previous->getAwsRequestId(),
                            'http_status' => $previous->getStatusCode(),
                            'error' => $previous->getMessage(),
                        ]);
                    } else {
                        Log::warning('Flysystem error checking resource file existence in storage', [
                            'resource_id' => $this->id,
                            'path' => $filePath,
                            'disk' => $disk,
                            'error' => $flysystemException->getMessage(),
                        ]);
                    }

                    return null;
                } catch (S3Exception $s3Exception) {
                    Log::warning('S3 error checking resource file existence in storage', [
                        'resource_id' => $this->id,
                        'path' => $filePath,
                        'disk' => $disk,
                        's3_error_code' => $s3Exception->getAwsErrorCode(),
                        's3_error_message' => $s3Exception->getAwsErrorMessage(),
                        's3_request_id' => $s3Exception->getAwsRequestId(),
                        'http_status' => $s3Exception->getStatusCode(),
                        'error' => $s3Exception->getMessage(),
                    ]);

                    return null;
                } catch (\Exception $e) {
                    Log::warning('Error checking resource file existence in storage', [
                        'resource_id' => $this->id,
                        'path' => $filePath,
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
     * Scope a query to only include approved resources.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include pending resources.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include rejected resources.
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope a query to only include generating resources.
     */
    public function scopeGenerating(Builder $query): Builder
    {
        return $query->where('status', 'generating');
    }

    /**
     * Scope a query to filter by resource type.
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include resources with valid files.
     *
     * Note: This requires loading resources to check file_url, so use sparingly.
     * For better performance, filter after loading a small batch.
     */
    public function scopeWithValidFile(Builder $query): Builder
    {
        // This is a placeholder scope - actual filtering happens in memory
        // because file_url is a computed accessor that checks storage
        return $query;
    }

    /**
     * Check if this resource has a valid file.
     */
    public function hasValidFile(): bool
    {
        return $this->file_url !== null;
    }

    /**
     * Scope a query to filter resources by matching tags.
     *
     * Finds resources where at least one tag matches any of the provided tags.
     * Tags should be normalized to lowercase for consistent matching.
     *
     * @param  Builder  $query  The query builder
     * @param  array<string>  $tags  Array of tags to match (should be lowercase)
     */
    public function scopeWithMatchingTags(Builder $query, array $tags): Builder
    {
        if (empty($tags)) {
            return $query;
        }

        // Normalize and clean tags
        $normalizedTags = array_map('strtolower', array_map('trim', $tags));
        $normalizedTags = array_filter(array_unique($normalizedTags));

        if (empty($normalizedTags)) {
            return $query;
        }

        // Find resources where at least one tag matches
        return $query->where(function ($q) use ($normalizedTags) {
            foreach ($normalizedTags as $tag) {
                $q->orWhereJsonContains('tags', $tag);
            }
        });
    }

    /**
     * Find a random approved resource of a specific type, optionally matching tags.
     *
     * @param  string  $type  The resource type ('avatar_image', 'planet_image', 'planet_video')
     * @param  array<string>|null  $tags  Optional array of tags to match. If provided, will try to find resources matching these tags.
     * @return resource|null A random approved resource or null if none found
     */
    public static function findRandomApproved(string $type, ?array $tags = null): ?self
    {
        $query = self::approved()->ofType($type);

        if ($tags !== null && ! empty($tags)) {
            $query->withMatchingTags($tags);
        }

        return $query->inRandomOrder()->first();
    }

    /**
     * Approve this resource.
     *
     * @param  User  $user  The user approving the resource
     */
    public function approve(User $user): bool
    {
        return $this->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);
    }

    /**
     * Reject this resource.
     *
     * @param  User  $user  The user rejecting the resource
     * @param  string|null  $reason  The reason for rejection
     */
    public function reject(User $user, ?string $reason = null): bool
    {
        return $this->update([
            'status' => 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Check if the resource is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the resource is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the resource is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if the resource is generating.
     */
    public function isGenerating(): bool
    {
        return $this->status === 'generating';
    }
}
