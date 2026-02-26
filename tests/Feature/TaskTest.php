<?php

use App\Models\Project;
use App\Models\Task;

// ─── Index ────────────────────────────────────────────────────────────────────

it('shows the task list page with required view data', function () {
    $this->get(route('tasks.index'))
        ->assertOk()
        ->assertViewIs('tasks.index')
        ->assertViewHas(['tasks', 'projects', 'selectedProjectId']);
});

it('lists all tasks ordered by priority', function () {
    Task::factory()->create(['name' => 'Second Task', 'priority' => 2]);
    Task::factory()->create(['name' => 'First Task',  'priority' => 1]);

    $tasks = $this->get(route('tasks.index'))->viewData('tasks');

    expect($tasks->first()->name)->toBe('First Task');
});

it('filters tasks by project', function () {
    $project = Project::factory()->create();
    Task::factory()->for($project)->create(['name' => 'Project Task']);
    Task::factory()->create(['name' => 'Other Task']);

    $tasks = $this->get(route('tasks.index', ['project_id' => $project->id]))->viewData('tasks');

    expect($tasks)->toHaveCount(1)
        ->and($tasks->first()->name)->toBe('Project Task');
});

// ─── Store ────────────────────────────────────────────────────────────────────

it('creates a task and redirects with success message', function () {
    $this->post(route('tasks.store'), ['name' => 'New Task'])
        ->assertRedirect(route('tasks.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('tasks', ['name' => 'New Task']);
});

it('assigns priority 1 to the first task in a project', function () {
    $project = Project::factory()->create();

    $this->post(route('tasks.store'), ['name' => 'First Task', 'project_id' => $project->id]);

    $this->assertDatabaseHas('tasks', ['name' => 'First Task', 'priority' => 1]);
});

it('auto-increments priority for subsequent tasks in the same project', function () {
    $project = Project::factory()->create();
    Task::factory()->for($project)->create(['priority' => 1]);

    $this->post(route('tasks.store'), ['name' => 'Second Task', 'project_id' => $project->id]);

    $this->assertDatabaseHas('tasks', ['name' => 'Second Task', 'priority' => 2]);
});

it('requires a task name', function () {
    $this->post(route('tasks.store'), ['name' => ''])
        ->assertSessionHasErrors('name');
});

it('rejects an invalid project id on store', function () {
    $this->post(route('tasks.store'), ['name' => 'Task', 'project_id' => 9999])
        ->assertSessionHasErrors('project_id');
});

// ─── Update ───────────────────────────────────────────────────────────────────

it('updates a task name and redirects with success message', function () {
    $task = Task::factory()->create(['name' => 'Old Name']);

    $this->put(route('tasks.update', $task), ['name' => 'New Name'])
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('tasks', ['id' => $task->id, 'name' => 'New Name']);
});

it('requires a task name on update', function () {
    $task = Task::factory()->create();

    $this->put(route('tasks.update', $task), ['name' => ''])
        ->assertSessionHasErrors('name');
});

// ─── Destroy ──────────────────────────────────────────────────────────────────

it('deletes a task and redirects with success message', function () {
    $task = Task::factory()->create();

    $this->delete(route('tasks.destroy', $task))
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

it('preserves the project filter on delete redirect', function () {
    $project = Project::factory()->create();
    $task    = Task::factory()->for($project)->create();

    $this->delete(route('tasks.destroy', $task))
        ->assertRedirect(route('tasks.index', ['project_id' => $project->id]));
});

// ─── Reorder ──────────────────────────────────────────────────────────────────

it('reorders tasks and returns a json success response', function () {
    $task1 = Task::factory()->create(['priority' => 1]);
    $task2 = Task::factory()->create(['priority' => 2]);
    $task3 = Task::factory()->create(['priority' => 3]);

    $this->postJson(route('tasks.reorder'), ['tasks' => [$task3->id, $task1->id, $task2->id]])
        ->assertOk()
        ->assertJson(['message' => 'Order saved.']);

    $this->assertDatabaseHas('tasks', ['id' => $task3->id, 'priority' => 1]);
    $this->assertDatabaseHas('tasks', ['id' => $task1->id, 'priority' => 2]);
    $this->assertDatabaseHas('tasks', ['id' => $task2->id, 'priority' => 3]);
});

it('rejects reorder with non-existent task ids', function () {
    $this->postJson(route('tasks.reorder'), ['tasks' => [9999]])
        ->assertUnprocessable();
});

it('rejects reorder when tasks array is missing', function () {
    $this->postJson(route('tasks.reorder'), [])
        ->assertUnprocessable();
});
