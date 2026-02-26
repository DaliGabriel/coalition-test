<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\ProjectRepository;
use App\Repositories\TaskRepository;
use Illuminate\Database\Eloquent\Collection;

class TaskService
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly ProjectRepository $projectRepository,
    ) {}

    /**
     * Return all tasks ordered by priority, optionally filtered by project.
     */
    public function allTasks(?int $projectId): Collection
    {
        return $this->taskRepository->allOrdered($projectId);
    }

    /**
     * Return all projects sorted alphabetically.
     */
    public function allProjects(): Collection
    {
        return $this->projectRepository->allOrdered();
    }

    /**
     * Create a new task, auto-assigning its priority within its project scope.
     */
    public function create(array $data): Task
    {
        return $this->taskRepository->create([
            'name'       => $data['name'],
            'project_id' => $data['project_id'] ?? null,
            'priority'   => $this->taskRepository->nextPriority($data['project_id'] ?? null),
        ]);
    }

    /**
     * Update a task's name and/or project assignment.
     */
    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task;
    }

    /**
     * Delete a task from the database.
     */
    public function delete(Task $task): void
    {
        $task->delete();
    }

    /**
     * Reassign priorities to match the given ordered array of task IDs.
     * Each task receives a priority equal to its 1-based position in the array.
     */
    public function reorder(array $orderedIds): void
    {
        foreach ($orderedIds as $position => $taskId) {
            $this->taskRepository->updatePriority($taskId, $position + 1);
        }
    }
}
