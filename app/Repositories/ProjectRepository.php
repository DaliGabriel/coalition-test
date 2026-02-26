<?php

namespace App\Repositories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;

class ProjectRepository
{
    /**
     * Return all projects sorted alphabetically.
     */
    public function allOrdered(): Collection
    {
        return Project::orderBy('name')->get();
    }
}
