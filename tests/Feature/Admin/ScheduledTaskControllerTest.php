<?php

use App\Models\ScheduledTask;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->password = 'password123';
    $this->admin = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make($this->password),
        'is_super_admin' => true,
    ]);

    config(['admin.email_whitelist' => '']);
    Auth::guard('admin')->login($this->admin);
});

it('displays a listing of scheduled tasks', function () {
    ScheduledTask::create([
        'name' => 'test_task_1',
        'command' => 'test:command-1',
        'is_enabled' => true,
        'schedule_time' => '02:00',
        'description' => 'Test task 1',
    ]);

    ScheduledTask::create([
        'name' => 'test_task_2',
        'command' => 'test:command-2',
        'is_enabled' => false,
        'schedule_time' => '03:00',
        'description' => 'Test task 2',
    ]);

    $response = $this->get('/admin/scheduled-tasks');

    $response->assertStatus(200)
        ->assertViewIs('admin.scheduled-tasks.index')
        ->assertViewHas('tasks');

    $tasks = $response->viewData('tasks');
    expect($tasks)->toHaveCount(2);
});

it('orders scheduled tasks by name', function () {
    ScheduledTask::create([
        'name' => 'z_task',
        'command' => 'test:z',
        'is_enabled' => true,
        'description' => 'Z task',
    ]);

    ScheduledTask::create([
        'name' => 'a_task',
        'command' => 'test:a',
        'is_enabled' => true,
        'description' => 'A task',
    ]);

    $response = $this->get('/admin/scheduled-tasks');

    $tasks = $response->viewData('tasks');
    expect($tasks->first()->name)->toBe('a_task')
        ->and($tasks->last()->name)->toBe('z_task');
});

it('displays empty state when no scheduled tasks exist', function () {
    ScheduledTask::query()->delete();

    $response = $this->get('/admin/scheduled-tasks');

    $response->assertStatus(200)
        ->assertViewIs('admin.scheduled-tasks.index');

    $tasks = $response->viewData('tasks');
    expect($tasks)->toHaveCount(0);
});

it('toggles a scheduled task from enabled to disabled', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => true,
        'schedule_time' => '02:00',
        'description' => 'Test task',
    ]);

    $response = $this->post("/admin/scheduled-tasks/{$task->id}/toggle");

    $response->assertRedirect(route('admin.scheduled-tasks.index'))
        ->assertSessionHas('success', "Scheduled task 'test_task' has been disabled.");

    $task->refresh();
    expect($task->is_enabled)->toBeFalse();
});

it('toggles a scheduled task from disabled to enabled', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => false,
        'schedule_time' => '02:00',
        'description' => 'Test task',
    ]);

    $response = $this->post("/admin/scheduled-tasks/{$task->id}/toggle");

    $response->assertRedirect(route('admin.scheduled-tasks.index'))
        ->assertSessionHas('success', "Scheduled task 'test_task' has been enabled.");

    $task->refresh();
    expect($task->is_enabled)->toBeTrue();
});

it('enables a scheduled task', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => false,
        'schedule_time' => '02:00',
        'description' => 'Test task',
    ]);

    $response = $this->post("/admin/scheduled-tasks/{$task->id}/enable");

    $response->assertRedirect(route('admin.scheduled-tasks.index'))
        ->assertSessionHas('success', "Scheduled task 'test_task' has been enabled.");

    $task->refresh();
    expect($task->is_enabled)->toBeTrue();
});

it('enables an already enabled scheduled task', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => true,
        'schedule_time' => '02:00',
        'description' => 'Test task',
    ]);

    $response = $this->post("/admin/scheduled-tasks/{$task->id}/enable");

    $response->assertRedirect(route('admin.scheduled-tasks.index'))
        ->assertSessionHas('success', "Scheduled task 'test_task' has been enabled.");

    $task->refresh();
    expect($task->is_enabled)->toBeTrue();
});

it('disables a scheduled task', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => true,
        'schedule_time' => '02:00',
        'description' => 'Test task',
    ]);

    $response = $this->post("/admin/scheduled-tasks/{$task->id}/disable");

    $response->assertRedirect(route('admin.scheduled-tasks.index'))
        ->assertSessionHas('success', "Scheduled task 'test_task' has been disabled.");

    $task->refresh();
    expect($task->is_enabled)->toBeFalse();
});

it('disables an already disabled scheduled task', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => false,
        'schedule_time' => '02:00',
        'description' => 'Test task',
    ]);

    $response = $this->post("/admin/scheduled-tasks/{$task->id}/disable");

    $response->assertRedirect(route('admin.scheduled-tasks.index'))
        ->assertSessionHas('success', "Scheduled task 'test_task' has been disabled.");

    $task->refresh();
    expect($task->is_enabled)->toBeFalse();
});

it('requires authentication to view scheduled tasks', function () {
    Auth::guard('admin')->logout();

    $response = $this->get('/admin/scheduled-tasks');

    // Middleware may redirect to default login route
    $response->assertRedirect();
});

it('requires authentication to toggle scheduled task', function () {
    Auth::guard('admin')->logout();

    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => true,
        'description' => 'Test task',
    ]);

    $response = $this->post("/admin/scheduled-tasks/{$task->id}/toggle");

    $response->assertRedirect();
});

it('requires authentication to enable scheduled task', function () {
    Auth::guard('admin')->logout();

    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => false,
        'description' => 'Test task',
    ]);

    $response = $this->post("/admin/scheduled-tasks/{$task->id}/enable");

    $response->assertRedirect();
});

it('requires authentication to disable scheduled task', function () {
    Auth::guard('admin')->logout();

    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => true,
        'description' => 'Test task',
    ]);

    $response = $this->post("/admin/scheduled-tasks/{$task->id}/disable");

    $response->assertRedirect();
});

it('requires super admin privileges to view scheduled tasks', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make($this->password),
        'is_super_admin' => false,
    ]);

    Auth::guard('admin')->login($user);

    $response = $this->get('/admin/scheduled-tasks');

    $response->assertRedirect(route('admin.login'));
});

it('requires super admin privileges to toggle scheduled task', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make($this->password),
        'is_super_admin' => false,
    ]);

    Auth::guard('admin')->login($user);

    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => true,
        'description' => 'Test task',
    ]);

    $response = $this->post("/admin/scheduled-tasks/{$task->id}/toggle");

    $response->assertRedirect(route('admin.login'));
});

it('requires super admin privileges to enable scheduled task', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make($this->password),
        'is_super_admin' => false,
    ]);

    Auth::guard('admin')->login($user);

    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => false,
        'description' => 'Test task',
    ]);

    $response = $this->post("/admin/scheduled-tasks/{$task->id}/enable");

    $response->assertRedirect(route('admin.login'));
});

it('requires super admin privileges to disable scheduled task', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make($this->password),
        'is_super_admin' => false,
    ]);

    Auth::guard('admin')->login($user);

    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => true,
        'description' => 'Test task',
    ]);

    $response = $this->post("/admin/scheduled-tasks/{$task->id}/disable");

    $response->assertRedirect(route('admin.login'));
});

it('returns 404 for non-existent scheduled task when toggling', function () {
    $response = $this->post('/admin/scheduled-tasks/99999/toggle');

    $response->assertStatus(404);
});

it('returns 404 for non-existent scheduled task when enabling', function () {
    $response = $this->post('/admin/scheduled-tasks/99999/enable');

    $response->assertStatus(404);
});

it('returns 404 for non-existent scheduled task when disabling', function () {
    $response = $this->post('/admin/scheduled-tasks/99999/disable');

    $response->assertStatus(404);
});

it('preserves task data when toggling', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => true,
        'schedule_time' => '02:00',
        'description' => 'Test task description',
        'last_run_at' => now()->subHour(),
    ]);

    $originalScheduleTime = $task->schedule_time;
    $originalDescription = $task->description;
    $originalLastRunAt = $task->last_run_at;

    $this->post("/admin/scheduled-tasks/{$task->id}/toggle");

    $task->refresh();
    expect($task->schedule_time)->toBe($originalScheduleTime)
        ->and($task->description)->toBe($originalDescription)
        ->and($task->last_run_at->format('Y-m-d H:i:s'))->toBe($originalLastRunAt->format('Y-m-d H:i:s'));
});

it('preserves task data when enabling', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => false,
        'schedule_time' => '02:00',
        'description' => 'Test task description',
        'last_run_at' => now()->subHour(),
    ]);

    $originalScheduleTime = $task->schedule_time;
    $originalDescription = $task->description;
    $originalLastRunAt = $task->last_run_at;

    $this->post("/admin/scheduled-tasks/{$task->id}/enable");

    $task->refresh();
    expect($task->schedule_time)->toBe($originalScheduleTime)
        ->and($task->description)->toBe($originalDescription)
        ->and($task->last_run_at->format('Y-m-d H:i:s'))->toBe($originalLastRunAt->format('Y-m-d H:i:s'))
        ->and($task->is_enabled)->toBeTrue();
});

it('preserves task data when disabling', function () {
    $task = ScheduledTask::create([
        'name' => 'test_task',
        'command' => 'test:command',
        'is_enabled' => true,
        'schedule_time' => '02:00',
        'description' => 'Test task description',
        'last_run_at' => now()->subHour(),
    ]);

    $originalScheduleTime = $task->schedule_time;
    $originalDescription = $task->description;
    $originalLastRunAt = $task->last_run_at;

    $this->post("/admin/scheduled-tasks/{$task->id}/disable");

    $task->refresh();
    expect($task->schedule_time)->toBe($originalScheduleTime)
        ->and($task->description)->toBe($originalDescription)
        ->and($task->last_run_at->format('Y-m-d H:i:s'))->toBe($originalLastRunAt->format('Y-m-d H:i:s'))
        ->and($task->is_enabled)->toBeFalse();
});

