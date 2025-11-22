<?php

use App\Models\ScheduledTask;

it('checks if task is enabled', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => true,
    ]);

    expect($task->isEnabled())->toBeTrue();
});

it('checks if task is disabled', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => false,
    ]);

    expect($task->isEnabled())->toBeFalse();
});

it('enables a disabled task', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => false,
    ]);

    $result = $task->enable();

    expect($result)->toBeTrue();
    $task->refresh();
    expect($task->is_enabled)->toBeTrue();
});

it('enables an already enabled task', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => true,
    ]);

    $result = $task->enable();

    expect($result)->toBeTrue();
    $task->refresh();
    expect($task->is_enabled)->toBeTrue();
});

it('disables an enabled task', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => true,
    ]);

    $result = $task->disable();

    expect($result)->toBeTrue();
    $task->refresh();
    expect($task->is_enabled)->toBeFalse();
});

it('disables an already disabled task', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => false,
    ]);

    $result = $task->disable();

    expect($result)->toBeTrue();
    $task->refresh();
    expect($task->is_enabled)->toBeFalse();
});

it('toggles task from enabled to disabled', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => true,
    ]);

    $result = $task->toggle();

    expect($result)->toBeTrue();
    $task->refresh();
    expect($task->is_enabled)->toBeFalse();
});

it('toggles task from disabled to enabled', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => false,
    ]);

    $result = $task->toggle();

    expect($result)->toBeTrue();
    $task->refresh();
    expect($task->is_enabled)->toBeTrue();
});

it('marks task as run', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => true,
        'last_run_at' => null,
    ]);

    $beforeRun = now()->subSecond();
    $result = $task->markAsRun();
    $afterRun = now()->addSecond();

    expect($result)->toBeTrue();
    $task->refresh();
    expect($task->last_run_at)->not->toBeNull()
        ->and($task->last_run_at->isAfter($beforeRun))->toBeTrue()
        ->and($task->last_run_at->isBefore($afterRun))->toBeTrue();
});

it('updates last_run_at when marking as run', function () {
    $oldRunTime = now()->subDay();
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => true,
        'last_run_at' => $oldRunTime,
    ]);

    $task->markAsRun();
    $task->refresh();

    expect($task->last_run_at->isAfter($oldRunTime))->toBeTrue();
});

it('finds task by name', function () {
    $task = ScheduledTask::create([
        'name' => 'unique_task_name',
        'command' => 'test:command',
        'is_enabled' => true,
    ]);

    $found = ScheduledTask::findByName('unique_task_name');

    expect($found)->not->toBeNull()
        ->and($found->id)->toBe($task->id)
        ->and($found->name)->toBe('unique_task_name');
});

it('returns null when task name not found', function () {
    $found = ScheduledTask::findByName('non_existent_task');

    expect($found)->toBeNull();
});

it('gets all enabled tasks', function () {
    ScheduledTask::create([
        'name' => 'enabled_task_1',
        'command' => 'test:command1',
        'is_enabled' => true,
    ]);

    ScheduledTask::create([
        'name' => 'enabled_task_2',
        'command' => 'test:command2',
        'is_enabled' => true,
    ]);

    ScheduledTask::create([
        'name' => 'disabled_task',
        'command' => 'test:command3',
        'is_enabled' => false,
    ]);

    $enabledTasks = ScheduledTask::enabled();

    expect($enabledTasks)->toHaveCount(2)
        ->and($enabledTasks->pluck('name')->toArray())->toContain('enabled_task_1')
        ->and($enabledTasks->pluck('name')->toArray())->toContain('enabled_task_2')
        ->and($enabledTasks->pluck('name')->toArray())->not->toContain('disabled_task');
});

it('returns empty collection when no enabled tasks exist', function () {
    ScheduledTask::create([
        'name' => 'disabled_task_1',
        'command' => 'test:command1',
        'is_enabled' => false,
    ]);

    ScheduledTask::create([
        'name' => 'disabled_task_2',
        'command' => 'test:command2',
        'is_enabled' => false,
    ]);

    $enabledTasks = ScheduledTask::enabled();

    expect($enabledTasks)->toHaveCount(0);
});

it('casts is_enabled to boolean', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => 1, // Integer instead of boolean
    ]);

    expect($task->is_enabled)->toBeBool()
        ->and($task->is_enabled)->toBeTrue();
});

it('casts last_run_at to datetime', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => true,
        'last_run_at' => '2024-01-01 12:00:00',
    ]);

    expect($task->last_run_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});

it('casts next_run_at to datetime', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => true,
        'next_run_at' => '2024-01-01 12:00:00',
    ]);

    expect($task->next_run_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});

it('casts metadata to array', function () {
    $metadata = ['key' => 'value', 'number' => 123, 'nested' => ['a' => 'b']];

    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => true,
        'metadata' => $metadata,
    ]);

    expect($task->metadata)->toBeArray()
        ->and($task->metadata)->toBe($metadata);
});

it('handles null metadata', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => true,
        'metadata' => null,
    ]);

    expect($task->metadata)->toBeNull();
});

it('preserves other attributes when enabling', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => false,
        'schedule_time' => '02:00',
        'description' => 'Test description',
    ]);

    $task->enable();
    $task->refresh();

    expect($task->schedule_time)->toBe('02:00')
        ->and($task->description)->toBe('Test description')
        ->and($task->is_enabled)->toBeTrue();
});

it('preserves other attributes when disabling', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => true,
        'schedule_time' => '02:00',
        'description' => 'Test description',
    ]);

    $task->disable();
    $task->refresh();

    expect($task->schedule_time)->toBe('02:00')
        ->and($task->description)->toBe('Test description')
        ->and($task->is_enabled)->toBeFalse();
});

it('preserves other attributes when toggling', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => true,
        'schedule_time' => '02:00',
        'description' => 'Test description',
    ]);

    $task->toggle();
    $task->refresh();

    expect($task->schedule_time)->toBe('02:00')
        ->and($task->description)->toBe('Test description')
        ->and($task->is_enabled)->toBeFalse();
});

it('preserves other attributes when marking as run', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => true,
        'schedule_time' => '02:00',
        'description' => 'Test description',
    ]);

    $task->markAsRun();
    $task->refresh();

    expect($task->schedule_time)->toBe('02:00')
        ->and($task->description)->toBe('Test description')
        ->and($task->is_enabled)->toBeTrue()
        ->and($task->last_run_at)->not->toBeNull();
});

