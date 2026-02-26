<?php

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository
{
    /**
     * Return all tasks ordered by priority, with their project eager-loaded.
     * Optionally scoped to a single project.
     */
    public function allOrdered(?int $projectId = null): Collection
    {
        return Task::with('project')
            ->when($projectId, fn ($query) => $query->where('project_id', $projectId))
            ->orderBy('priority')
            ->get();
    }

    /**
     * Return the next available priority within the given project scope.
     * Returns 1 when no tasks exist yet.
     */
    public function nextPriority(?int $projectId): int
    {
        return Task::when($projectId, fn ($query) => $query->where('project_id', $projectId))
            ->max('priority') + 1;
    }

    /**
     * Persist a new task record and return it.
     */
    public function create(array $data): Task
    {
        return Task::create($data);
    }

    /**
     * Update the priority of a single task by its ID.
     */
    public function updatePriority(int $taskId, int $priority): void
    {
        Task::where('id', $taskId)->update(['priority' => $priority]);
    }
}
