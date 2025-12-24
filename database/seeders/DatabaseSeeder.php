<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Enums\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Admin Demo',
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        Project::factory()
            ->count(3)
            ->for($user)
            ->has(Task::factory()->count(5))
            ->create();
    }
}
