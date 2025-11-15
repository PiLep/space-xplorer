<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CodexEntry extends Model
{
    use HasFactory, HasUlids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'codex_entries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'planet_id',
        'name',
        'fallback_name',
        'description',
        'discovered_by_user_id',
        'is_named',
        'is_public',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_named' => 'boolean',
        'is_public' => 'boolean',
    ];

    /**
     * Get the planet this codex entry belongs to.
     */
    public function planet(): BelongsTo
    {
        return $this->belongsTo(Planet::class);
    }

    /**
     * Get the user who discovered this planet.
     */
    public function discoveredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'discovered_by_user_id');
    }

    /**
     * Get the contributions for this codex entry.
     */
    public function contributions(): HasMany
    {
        return $this->hasMany(CodexContribution::class, 'codex_entry_id');
    }

    /**
     * Get the display name (user-provided name or fallback).
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?? $this->fallback_name;
    }

    /**
     * Check if the entry has a user-provided name.
     */
    public function hasName(): bool
    {
        return $this->is_named && $this->name !== null;
    }

    /**
     * Scope to get only public entries.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope to get only named entries.
     */
    public function scopeNamed($query)
    {
        return $query->where('is_named', true);
    }
}

