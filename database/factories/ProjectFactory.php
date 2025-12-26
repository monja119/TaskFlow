<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->optional()->dateTimeBetween('-1 month', '+1 month');
        $end = fake()->optional()->dateTimeBetween($start ?? 'now', '+2 months');

        return [
            'name' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'status' => fake()->randomElement(ProjectStatus::cases())->value,
            'start_date' => $start,
            'end_date' => $end,
            'user_id' => User::factory(),
            'progress' => fake()->numberBetween(0, 100),
            'risk_score' => fake()->optional()->randomFloat(2, 0, 100),
        ];
    }
}
