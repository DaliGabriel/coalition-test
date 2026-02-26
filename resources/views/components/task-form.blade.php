@props(['projects', 'selectedProjectId'])

<div class="mb-8 bg-white rounded-xl shadow-sm p-6">
    <h2 class="text-lg font-semibold text-gray-700 mb-4">Add a new task</h2>
    <form method="POST" action="{{ route('tasks.store') }}" class="flex flex-wrap gap-3">
        @csrf
        <input
            type="text"
            name="name"
            placeholder="Task name..."
            value="{{ old('name') }}"
            required
            class="flex-1 min-w-[200px] rounded-lg border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
        >

        @if ($projects->isNotEmpty())
            <x-project-select
                :projects="$projects"
                :selected="old('project_id', $selectedProjectId)"
                class="rounded-lg px-3 py-2"
            />
        @endif

        <button
            type="submit"
            class="rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 transition-colors"
        >
            Add Task
        </button>
    </form>
</div>
