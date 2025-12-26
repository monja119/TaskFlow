<?php

namespace Tests\Feature\API;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected string $token;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'role' => \App\Enums\UserRole::ADMIN,
        ]);
        $this->token = $this->user->createToken('test-token')->plainTextToken;
        $this->project = Project::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_user_can_list_their_tasks(): void
    {
        Task::factory()->count(3)->create([
            'project_id' => $this->project->id,
        ]);

        $response = $this->withToken($this->token)
            ->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_user_can_create_task(): void
    {
        $response = $this->withToken($this->token)
            ->postJson('/api/tasks', [
                'title' => 'Test Task',
                'description' => 'Test Description',
                'status' => 'todo',
                'priority' => 'high',
                'project_id' => $this->project->id,
                'due_date' => '2025-12-31',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'title', 'description', 'status', 'priority']
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'project_id' => $this->project->id,
        ]);
    }

    public function test_user_can_view_task(): void
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
        ]);

        $response = $this->withToken($this->token)
            ->getJson('/api/tasks/' . $task->id);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $task->id,
                    'title' => $task->title,
                ]
            ]);
    }

    public function test_user_can_update_task(): void
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
        ]);

        $response = $this->withToken($this->token)
            ->putJson('/api/tasks/' . $task->id, [
                'title' => 'Updated Task',
                'status' => 'in_progress',
                'priority' => 'high',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => 'Updated Task',
                    'status' => 'in_progress',
                ]
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task',
        ]);
    }

    public function test_user_can_delete_task(): void
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
        ]);

        $response = $this->withToken($this->token)
            ->deleteJson('/api/tasks/' . $task->id);

        $response->assertStatus(204);

        // Task uses soft deletes, so check for deleted_at instead
        $this->assertSoftDeleted('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_user_can_filter_tasks_by_status(): void
    {
        Task::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'todo',
        ]);
        Task::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'completed',
        ]);

        $response = $this->withToken($this->token)
            ->getJson('/api/tasks?status=todo');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    ['status' => 'todo']
                ]
            ]);
    }

    public function test_user_can_filter_tasks_by_priority(): void
    {
        Task::factory()->create([
            'project_id' => $this->project->id,
            'priority' => 'high',
        ]);
        Task::factory()->create([
            'project_id' => $this->project->id,
            'priority' => 'low',
        ]);

        $response = $this->withToken($this->token)
            ->getJson('/api/tasks?priority=high');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJson([
                'data' => [
                    ['priority' => 'high']
                ]
            ]);
    }

    public function test_validation_fails_for_invalid_task_data(): void
    {
        $response = $this->withToken($this->token)
            ->postJson('/api/tasks', [
                'title' => '', // Required
                'status' => 'invalid_status',
                'priority' => 'invalid_priority',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'status', 'priority']);
    }
}
