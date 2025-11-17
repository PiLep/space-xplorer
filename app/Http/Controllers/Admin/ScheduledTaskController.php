<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScheduledTask;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ScheduledTaskController extends Controller
{
    /**
     * Display a listing of scheduled tasks.
     */
    public function index(): View
    {
        $tasks = ScheduledTask::orderBy('name')->get();

        return view('admin.scheduled-tasks.index', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * Toggle a scheduled task enabled state.
     */
    public function toggle(ScheduledTask $scheduledTask): RedirectResponse
    {
        $scheduledTask->toggle();

        $status = $scheduledTask->is_enabled ? 'enabled' : 'disabled';

        return redirect()->route('admin.scheduled-tasks.index')
            ->with('success', "Scheduled task '{$scheduledTask->name}' has been {$status}.");
    }

    /**
     * Enable a scheduled task.
     */
    public function enable(ScheduledTask $scheduledTask): RedirectResponse
    {
        $scheduledTask->enable();

        return redirect()->route('admin.scheduled-tasks.index')
            ->with('success', "Scheduled task '{$scheduledTask->name}' has been enabled.");
    }

    /**
     * Disable a scheduled task.
     */
    public function disable(ScheduledTask $scheduledTask): RedirectResponse
    {
        $scheduledTask->disable();

        return redirect()->route('admin.scheduled-tasks.index')
            ->with('success', "Scheduled task '{$scheduledTask->name}' has been disabled.");
    }
}
