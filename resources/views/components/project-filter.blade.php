@props(['projects', 'selectedProjectId'])

@if ($projects->isNotEmpty())
    <div class="mb-6 bg-white rounded-xl shadow-sm p-4">
        <form method="GET" action="{{ route('tasks.index') }}" class="flex flex-wrap items-center gap-3">
            <label for="project_id" class="text-sm font-medium text-gray-700">Filter by project:</label>
            <select
                id="project_id"
                name="project_id"
                onchange="this.form.submit()"
                class="rounded-lg border-gray-300 border text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400"
            >
                <option value="">All projects</option>
                @foreach ($projects as $project)
                    <option
                        value="{{ $project->id }}"
                        {{ (string) $selectedProjectId === (string) $project->id ? 'selected' : '' }}
                    >
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
            @if ($selectedProjectId)
                <a href="{{ route('tasks.index') }}" class="text-sm text-indigo-600 hover:underline">Clear filter</a>
            @endif
        </form>
    </div>
@endif
