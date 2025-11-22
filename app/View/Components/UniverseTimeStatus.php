<?php

namespace App\View\Components;

use App\Services\UniverseTimeService;
use Illuminate\View\Component;

class UniverseTimeStatus extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        private UniverseTimeService $universeTimeService
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        try {
            $universeTime = $this->universeTimeService->formatForStatusBar();
        } catch (\Exception $e) {
            // Log l'erreur pour debug
            \Log::error('UniverseTimeStatus error: '.$e->getMessage());
            $universeTime = null;
        }

        return view('components.universe-time-status', [
            'universeTime' => $universeTime,
        ]);
    }
}
