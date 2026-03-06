<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_by' => 1,
            'name' => fake()->company() . ' Project',
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['active', 'archived']),
        ];
    }
}
