<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledTask extends Model
{
    protected $fillable = [
        'name',
        'command',
        'is_enabled',
        'schedule_time',
        'description',
        'last_run_at',
        'next_run_at',
        'metadata',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Check if the task is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->is_enabled;
    }

    /**
     * Enable the task.
     */
    public function enable(): bool
    {
        return $this->update(['is_enabled' => true]);
    }

    /**
     * Disable the task.
     */
    public function disable(): bool
    {
        return $this->update(['is_enabled' => false]);
    }

    /**
     * Toggle the task enabled state.
     */
    public function toggle(): bool
    {
        return $this->update(['is_enabled' => ! $this->is_enabled]);
    }

    /**
     * Update last run timestamp.
     */
    public function markAsRun(): bool
    {
        return $this->update(['last_run_at' => now()]);
    }

    /**
     * Find a task by name.
     */
    public static function findByName(string $name): ?self
    {
        return static::where('name', $name)->first();
    }

    /**
     * Get all enabled tasks.
     */
    public static function enabled(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('is_enabled', true)->get();
    }
}
