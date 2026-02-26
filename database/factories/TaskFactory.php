<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'       => fake()->sentence(3),
            'priority'   => fake()->numberBetween(1, 100),
            'project_id' => null,
        ];
    }
}
