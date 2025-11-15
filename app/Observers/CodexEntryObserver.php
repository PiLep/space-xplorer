<?php

namespace App\Observers;

use App\Models\CodexEntry;
use Illuminate\Support\Facades\Cache;

class CodexEntryObserver
{
    /**
     * Handle the CodexEntry "created" event.
     */
    public function created(CodexEntry $codexEntry): void
    {
        $this->clearCache();
    }

    /**
     * Handle the CodexEntry "updated" event.
     */
    public function updated(CodexEntry $codexEntry): void
    {
        $this->clearCache();
    }

    /**
     * Handle the CodexEntry "deleted" event.
     */
    public function deleted(CodexEntry $codexEntry): void
    {
        $this->clearCache();
    }

    /**
     * Clear codex-related cache.
     */
    private function clearCache(): void
    {
        Cache::forget('codex.stats');
        Cache::forget('codex.recent_discoveries');
    }
}

