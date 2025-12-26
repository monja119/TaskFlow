<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'priority' => fake()->randomElement(TaskPriority::cases())->value,
            'status' => fake()->randomElement(TaskStatus::cases())->value,
            'project_id' => Project::factory(),
            'user_id' => User::factory(),
            'start_date' => fake()->optional()->dateTimeBetween('-1 week', '+1 week'),
            'due_date' => fake()->optional()->dateTimeBetween('now', '+3 weeks'),
            'completed_at' => null,
            'estimate_minutes' => fake()->optional()->numberBetween(60, 480),
            'actual_minutes' => null,
        ];
    }
}
