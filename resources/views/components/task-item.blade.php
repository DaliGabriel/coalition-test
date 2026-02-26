@props(['task', 'projects'])

<li
    data-id="{{ $task->id }}"
    x-data="{ editing: false }"
    class="flex items-center gap-4 px-5 py-4 group hover:bg-gray-50 transition-colors"
>
    {{-- Drag handle --}}
    <span
        class="drag-handle cursor-grab text-gray-300 hover:text-gray-500 select-none text-lg leading-none"
        title="Drag to reorder"
    >&#8597;</span>

    {{-- Priority badge --}}
    <span class="priority-badge flex-shrink-0 w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center justify-center">
        {{ $task->priority }}
    </span>

    {{-- Display mode --}}
    <div class="flex-1 min-w-0" x-show="!editing">
        <span class="block truncate text-gray-800 text-sm font-medium">{{ $task->name }}</span>
        @if ($task->project)
            <span class="text-xs text-gray-400">{{ $task->project->name }}</span>
        @endif
    </div>

    {{-- Inline edit form --}}
    <form
        method="POST"
        action="{{ route('tasks.update', $task) }}"
        class="flex-1 flex gap-2 min-w-0"
        x-show="editing"
        x-cloak
    >
        @csrf
        @method('PUT')
        <input
            type="text"
            name="name"
            value="{{ $task->name }}"
            required
            class="flex-1 min-w-0 rounded border border-gray-300 px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
        >
        @if ($projects->isNotEmpty())
            <x-project-select
                :projects="$projects"
                :selected="$task->project_id"
                class="rounded px-2 py-1"
            />
        @endif
        <button type="submit" class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded transition-colors">
            Save
        </button>
        <button type="button" @click="editing = false" class="text-xs text-gray-500 hover:text-gray-700 px-2 py-1">
            Cancel
        </button>
    </form>

    {{-- Action buttons --}}
    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity" x-show="!editing">
        <button
            type="button"
            @click="editing = true"
            class="text-xs text-indigo-600 hover:text-indigo-800 font-medium"
        >
            Edit
        </button>
        <form
            method="POST"
            action="{{ route('tasks.destroy', $task) }}"
            @submit.prevent="confirm('Delete this task?') && $el.submit()"
        >
            @csrf
            @method('DELETE')
            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">
                Delete
            </button>
        </form>
    </div>
</li>
