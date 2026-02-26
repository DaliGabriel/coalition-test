<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Seed the projects table with sample data.
     */
    public function run(): void
    {
        $projects = ['Personal', 'Work', 'Home'];

        foreach ($projects as $name) {
            Project::firstOrCreate(['name' => $name]);
        }
    }
}
