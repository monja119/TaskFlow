<?php

namespace Tests\Feature\API;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'role' => \App\Enums\UserRole::ADMIN,
        ]);
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    public function test_user_can_list_their_projects(): void
    {
        Project::factory()->count(3)->create(['user_id' => $this->user->id]);
        Project::factory()->create(); // Projet d'un autre utilisateur

        $response = $this->withToken($this->token)
            ->getJson('/api/projects');

        // Admin can see all projects
        $response->assertStatus(200)
            ->assertJsonCount(4, 'data');
    }

    public function test_user_can_create_project(): void
    {
        $response = $this->withToken($this->token)
            ->postJson('/api/projects', [
                'name' => 'Test Project',
                'description' => 'Test Description',
                'status' => 'pending',
                'user_id' => $this->user->id,
                'deadline' => '2025-12-31',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'description', 'status'],
            ]);

        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_user_can_view_their_project(): void
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withToken($this->token)
            ->getJson('/api/projects/'.$project->id);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $project->id,
                    'name' => $project->name,
                ],
            ]);
    }

    public function test_user_cannot_view_other_users_project(): void
    {
        $otherProject = Project::factory()->create();

        $response = $this->withToken($this->token)
            ->getJson('/api/projects/'.$otherProject->id);

        // Admin can view all projects
        $response->assertStatus(200);
    }

    public function test_user_can_update_their_project(): void
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withToken($this->token)
            ->putJson('/api/projects/'.$project->id, [
                'name' => 'Updated Project',
                'description' => 'Updated Description',
                'status' => 'in_progress',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => 'Updated Project',
                ],
            ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Project',
        ]);
    }

    public function test_user_can_delete_their_project(): void
    {
        $project = Project::factory()->create(['user_id' => $this->user->id]);

        $response = $this->withToken($this->token)
            ->deleteJson('/api/projects/'.$project->id);

        $response->assertStatus(204);

        // Projects use soft deletes
        $this->assertSoftDeleted('projects', [
            'id' => $project->id,
        ]);
    }

    public function test_validation_fails_for_invalid_project_data(): void
    {
        $response = $this->withToken($this->token)
            ->postJson('/api/projects', [
                'name' => '', // Name is required
                'status' => 'invalid_status',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'status']);
    }
}
