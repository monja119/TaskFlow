<?php

namespace Tests\Feature;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $manager;
    protected User $member;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->manager = User::factory()->create(['role' => UserRole::MANAGER]);
        $this->member = User::factory()->create(['role' => UserRole::MEMBER]);
        $this->project = Project::factory()->create(['user_id' => $this->manager->id]);
    }

    #[Test]
    public function admin_can_list_all_tasks()
    {
        Task::factory()->count(5)->create(['project_id' => $this->project->id]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/tasks');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'status', 'priority', 'project', 'user']
                ]
            ])
            ->assertJsonCount(5, 'data');
    }

    #[Test]
    public function member_can_only_see_their_tasks()
    {
        Task::factory()->count(3)->create([
            'project_id' => $this->project->id,
            'user_id' => $this->member->id
        ]);
        Task::factory()->count(2)->create(['project_id' => $this->project->id]);

        $response = $this->actingAs($this->member)
            ->getJson('/api/tasks');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function admin_can_create_task()
    {
        $data = [
            'title' => 'New Task',
            'description' => 'Task description',
            'status' => TaskStatus::TODO->value,
            'priority' => TaskPriority::HIGH->value,
            'project_id' => $this->project->id,
            'user_id' => $this->member->id,
            'start_date' => now()->toDateString(),
            'due_date' => now()->addWeeks(2)->toDateString(),
            'estimated_hours' => 8,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/tasks', $data);

        $response->assertCreated()
            ->assertJsonFragment(['title' => 'New Task']);

        $this->assertDatabaseHas('tasks', ['title' => 'New Task']);
    }

    #[Test]
    public function manager_can_create_task()
    {
        $data = [
            'title' => 'Manager Task',
            'status' => TaskStatus::TODO->value,
            'priority' => TaskPriority::MEDIUM->value,
            'project_id' => $this->project->id,
        ];

        $response = $this->actingAs($this->manager)
            ->postJson('/api/tasks', $data);

        $response->assertCreated();
        $this->assertDatabaseHas('tasks', ['title' => 'Manager Task']);
    }

    #[Test]
    public function member_cannot_create_task()
    {
        $data = [
            'title' => 'Member Task',
            'status' => TaskStatus::TODO->value,
            'priority' => TaskPriority::LOW->value,
            'project_id' => $this->project->id,
        ];

        $response = $this->actingAs($this->member)
            ->postJson('/api/tasks', $data);

        $response->assertForbidden();
    }

    #[Test]
    public function admin_can_update_any_task()
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'title' => 'Old Title'
        ]);

        $response = $this->actingAs($this->admin)
            ->putJson("/api/tasks/{$task->id}", [
                'title' => 'Updated Title',
                'status' => TaskStatus::IN_PROGRESS->value,
                'priority' => $task->priority->value,
                'project_id' => $task->project_id,
            ]);

        $response->assertOk()
            ->assertJsonFragment(['title' => 'Updated Title']);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Updated Title']);
    }

    #[Test]
    public function member_can_update_their_task()
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->member->id
        ]);

        $response = $this->actingAs($this->member)
            ->putJson("/api/tasks/{$task->id}", [
                'title' => $task->title,
                'status' => TaskStatus::IN_PROGRESS->value,
                'priority' => $task->priority->value,
                'project_id' => $task->project_id,
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => TaskStatus::IN_PROGRESS->value
        ]);
    }

    #[Test]
    public function member_cannot_update_others_task()
    {
        $task = Task::factory()->create(['project_id' => $this->project->id]);

        $response = $this->actingAs($this->member)
            ->putJson("/api/tasks/{$task->id}", [
                'title' => 'Hacked',
                'status' => $task->status->value,
                'priority' => $task->priority->value,
                'project_id' => $task->project_id,
            ]);

        $response->assertForbidden();
    }

    #[Test]
    public function admin_can_delete_any_task()
    {
        $task = Task::factory()->create(['project_id' => $this->project->id]);

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }

    #[Test]
    public function manager_can_delete_task_in_their_project()
    {
        $task = Task::factory()->create(['project_id' => $this->project->id]);

        $response = $this->actingAs($this->manager)
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }

    #[Test]
    public function member_cannot_delete_task()
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->member->id
        ]);

        $response = $this->actingAs($this->member)
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertForbidden();
    }

    #[Test]
    public function can_filter_tasks_by_status()
    {
        Task::factory()->count(2)->create([
            'project_id' => $this->project->id,
            'status' => TaskStatus::TODO
        ]);
        Task::factory()->count(3)->create([
            'project_id' => $this->project->id,
            'status' => TaskStatus::IN_PROGRESS
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/tasks?status=' . TaskStatus::IN_PROGRESS->value);

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function can_filter_tasks_by_priority()
    {
        Task::factory()->count(2)->create([
            'project_id' => $this->project->id,
            'priority' => TaskPriority::HIGH
        ]);
        Task::factory()->count(1)->create([
            'project_id' => $this->project->id,
            'priority' => TaskPriority::LOW
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/tasks?priority=' . TaskPriority::HIGH->value);

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    #[Test]
    public function can_filter_tasks_by_project()
    {
        $anotherProject = Project::factory()->create();
        
        Task::factory()->count(3)->create(['project_id' => $this->project->id]);
        Task::factory()->count(2)->create(['project_id' => $anotherProject->id]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/tasks?project_id=' . $this->project->id);

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function can_filter_tasks_by_assigned_user()
    {
        Task::factory()->count(2)->create([
            'project_id' => $this->project->id,
            'user_id' => $this->member->id
        ]);
        Task::factory()->count(1)->create(['project_id' => $this->project->id]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/tasks?user_id=' . $this->member->id);

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    #[Test]
    public function can_search_tasks_by_title()
    {
        Task::factory()->create([
            'project_id' => $this->project->id,
            'title' => 'Implement authentication'
        ]);
        Task::factory()->create([
            'project_id' => $this->project->id,
            'title' => 'Design homepage'
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/tasks?search=authentication');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['title' => 'Implement authentication']);
    }

    #[Test]
    public function validates_required_fields_on_create()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/tasks', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'status', 'priority', 'project_id']);
    }

    #[Test]
    public function validates_due_date_after_start_date()
    {
        $data = [
            'title' => 'Test Task',
            'status' => TaskStatus::TODO->value,
            'priority' => TaskPriority::MEDIUM->value,
            'project_id' => $this->project->id,
            'start_date' => now()->toDateString(),
            'due_date' => now()->subDays(1)->toDateString(),
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/tasks', $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['due_date']);
    }

    #[Test]
    public function validates_estimated_hours_positive()
    {
        $data = [
            'title' => 'Test Task',
            'status' => TaskStatus::TODO->value,
            'priority' => TaskPriority::MEDIUM->value,
            'project_id' => $this->project->id,
            'estimated_hours' => -5,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/tasks', $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['estimated_hours']);
    }

    #[Test]
    public function unauthenticated_user_cannot_access_tasks()
    {
        $response = $this->getJson('/api/tasks');

        $response->assertUnauthorized();
    }
}
