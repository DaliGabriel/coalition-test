<?php

use App\Models\Task;
use App\Repositories\ProjectRepository;
use App\Repositories\TaskRepository;
use App\Services\TaskService;

beforeEach(function () {
    $this->taskRepository    = Mockery::mock(TaskRepository::class);
    $this->projectRepository = Mockery::mock(ProjectRepository::class);
    $this->service           = new TaskService($this->taskRepository, $this->projectRepository);
});

// ─── Create ───────────────────────────────────────────────────────────────────

it('assigns the next priority when creating a task within a project', function () {
    $this->taskRepository->shouldReceive('nextPriority')->with(1)->andReturn(3);
    $this->taskRepository->shouldReceive('create')
        ->with(['name' => 'Test Task', 'project_id' => 1, 'priority' => 3])
        ->andReturn(new Task(['name' => 'Test Task', 'project_id' => 1, 'priority' => 3]));

    $task = $this->service->create(['name' => 'Test Task', 'project_id' => 1]);

    expect($task->priority)->toBe(3);
});

it('assigns priority 1 to the first task with no project', function () {
    $this->taskRepository->shouldReceive('nextPriority')->with(null)->andReturn(1);
    $this->taskRepository->shouldReceive('create')
        ->andReturn(new Task(['name' => 'No Project Task', 'priority' => 1]));

    $task = $this->service->create(['name' => 'No Project Task']);

    expect($task->priority)->toBe(1);
});

// ─── Reorder ──────────────────────────────────────────────────────────────────

it('reassigns priorities from the ordered ids array', function () {
    $this->taskRepository->shouldReceive('updatePriority')->with(3, 1)->once();
    $this->taskRepository->shouldReceive('updatePriority')->with(1, 2)->once();
    $this->taskRepository->shouldReceive('updatePriority')->with(2, 3)->once();

    $this->service->reorder([3, 1, 2]);
});

it('assigns priority 1 to a single task reorder', function () {
    $this->taskRepository->shouldReceive('updatePriority')->with(5, 1)->once();

    $this->service->reorder([5]);
});
