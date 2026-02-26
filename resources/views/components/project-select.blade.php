@props(['projects', 'selected' => null, 'name' => 'project_id'])

<select
    name="{{ $name }}"
    {{ $attributes->merge(['class' => 'border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400']) }}
>
    <option value="">No project</option>
    @foreach ($projects as $project)
        <option
            value="{{ $project->id }}"
            {{ (string) $selected === (string) $project->id ? 'selected' : '' }}
        >
            {{ $project->name }}
        </option>
    @endforeach
</select>
