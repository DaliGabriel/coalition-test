<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\IndexTaskRequest;
use App\Http\Requests\Task\ReorderTasksRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function __construct(private readonly TaskService $taskService) {}

    /**
     * Display the task list, optionally filtered by project.
     */
    public function index(IndexTaskRequest $request): View
    {
        return view('tasks.index', [
            'projects'          => $this->taskService->allProjects(),
            'tasks'             => $this->taskService->allTasks($request->projectId()),
            'selectedProjectId' => $request->projectId(),
        ]);
    }

    /**
     * Store a newly created task.
     */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $this->taskService->create($request->validated());

        return redirect()
            ->route('tasks.index', $this->projectFilterParam($request->validated('project_id')))
            ->with('success', 'Task created successfully.');
    }

    /**
     * Update an existing task's name and/or project.
     */
    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $this->taskService->update($task, $request->validated());

        return redirect()
            ->route('tasks.index', $this->projectFilterParam($request->validated('project_id')))
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove a task from the database.
     */
    public function destroy(Task $task): RedirectResponse
    {
        $projectId = $task->project_id;

        $this->taskService->delete($task);

        return redirect()
            ->route('tasks.index', $this->projectFilterParam($projectId))
            ->with('success', 'Task deleted successfully.');
    }

    /**
     * Persist the new task order after a drag-and-drop reorder.
     *
     * Expects a JSON body: { "tasks": [3, 1, 2] } — ordered task IDs.
     * Priority is derived from each ID's position in the array (1-based).
     */
    public function reorder(ReorderTasksRequest $request): JsonResponse
    {
        $this->taskService->reorder($request->validated('tasks'));

        return response()->json(['message' => 'Order saved.']);
    }

    /**
     * Build the redirect query parameters to preserve the active project filter.
     */
    private function projectFilterParam(?int $projectId): array
    {
        return $projectId ? ['project_id' => $projectId] : [];
    }
}
