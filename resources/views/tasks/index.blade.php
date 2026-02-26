<x-layouts.app>

    <x-page-header title="Task Manager" subtitle="Create, organise, and prioritise your tasks." />

    <x-flash-messages />

    <x-project-filter :projects="$projects" :selected-project-id="$selectedProjectId" />

    <x-task-form :projects="$projects" :selected-project-id="$selectedProjectId" />

    <x-task-list :tasks="$tasks" :projects="$projects" />

</x-layouts.app>