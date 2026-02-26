@props(['tasks', 'projects'])

<div class="bg-white rounded-xl shadow-sm">
    @if ($tasks->isEmpty())
        <p class="text-center text-gray-400 py-12">No tasks yet. Add one above!</p>
    @else
        <ul
            x-data="taskSorter()"
            data-reorder-url="{{ route('tasks.reorder') }}"
            class="divide-y divide-gray-100"
        >
            @foreach ($tasks as $task)
                <x-task-item :task="$task" :projects="$projects" />
            @endforeach
        </ul>

        @if ($tasks->count() > 1)
            <p class="text-center text-xs text-gray-400 py-3">Drag tasks to reorder them.</p>
        @endif
    @endif
</div>
