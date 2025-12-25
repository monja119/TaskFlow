<?php

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $manager;
    protected User $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->manager = User::factory()->create(['role' => UserRole::MANAGER]);
        $this->member = User::factory()->create(['role' => UserRole::MEMBER]);
    }

    #[Test]
    public function admin_can_list_all_projects()
    {
        Project::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/projects');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'status', 'progress', 'user', 'tasks_count']
                ],
                'links',
                'meta'
            ])
            ->assertJsonCount(5, 'data');
    }

    #[Test]
    public function member_can_only_see_their_projects()
    {
        Project::factory()->count(3)->create(['user_id' => $this->member->id]);
        Project::factory()->count(2)->create(); // Other user's projects

        $response = $this->actingAs($this->member)
            ->getJson('/api/projects');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function admin_can_create_project()
    {
        $data = [
            'name' => 'New Project',
            'description' => 'Project description',
            'status' => ProjectStatus::PENDING->value,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addMonths(3)->toDateString(),
            'user_id' => $this->admin->id,
            'progress' => 0,
            'risk_score' => 20,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/projects', $data);

        $response->assertCreated()
            ->assertJsonFragment(['name' => 'New Project']);

        $this->assertDatabaseHas('projects', ['name' => 'New Project']);
    }

    #[Test]
    public function manager_can_create_project()
    {
        $data = [
            'name' => 'Manager Project',
            'description' => 'Test description',
            'status' => ProjectStatus::IN_PROGRESS->value,
            'user_id' => $this->manager->id,
            'progress' => 10,
        ];

        $response = $this->actingAs($this->manager)
            ->postJson('/api/projects', $data);

        $response->assertCreated();
        $this->assertDatabaseHas('projects', ['name' => 'Manager Project']);
    }

    #[Test]
    public function member_cannot_create_project()
    {
        $data = [
            'name' => 'Member Project',
            'status' => ProjectStatus::PENDING->value,
            'user_id' => $this->member->id,
        ];

        $response = $this->actingAs($this->member)
            ->postJson('/api/projects', $data);

        $response->assertForbidden();
    }

    #[Test]
    public function admin_can_update_any_project()
    {
        $project = Project::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($this->admin)
            ->putJson("/api/projects/{$project->id}", [
                'name' => 'Updated Name',
                'status' => $project->status->value,
                'user_id' => $project->user_id,
            ]);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Updated Name']);

        $this->assertDatabaseHas('projects', ['id' => $project->id, 'name' => 'Updated Name']);
    }

    #[Test]
    public function manager_can_update_their_project()
    {
        $project = Project::factory()->create(['user_id' => $this->manager->id]);

        $response = $this->actingAs($this->manager)
            ->putJson("/api/projects/{$project->id}", [
                'name' => 'Manager Updated',
                'status' => ProjectStatus::IN_PROGRESS->value,
                'user_id' => $this->manager->id,
                'progress' => 50,
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'name' => 'Manager Updated']);
    }

    #[Test]
    public function member_cannot_update_project()
    {
        $project = Project::factory()->create(['user_id' => $this->member->id]);

        $response = $this->actingAs($this->member)
            ->putJson("/api/projects/{$project->id}", [
                'name' => 'Member Updated',
                'status' => $project->status->value,
                'user_id' => $this->member->id,
            ]);

        $response->assertForbidden();
    }

    #[Test]
    public function admin_can_delete_any_project()
    {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/projects/{$project->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    #[Test]
    public function manager_can_delete_their_project()
    {
        $project = Project::factory()->create(['user_id' => $this->manager->id]);

        $response = $this->actingAs($this->manager)
            ->deleteJson("/api/projects/{$project->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    #[Test]
    public function member_cannot_delete_project()
    {
        $project = Project::factory()->create(['user_id' => $this->member->id]);

        $response = $this->actingAs($this->member)
            ->deleteJson("/api/projects/{$project->id}");

        $response->assertForbidden();
    }

    #[Test]
    public function can_filter_projects_by_status()
    {
        Project::factory()->count(2)->create(['status' => ProjectStatus::PENDING]);
        Project::factory()->count(3)->create(['status' => ProjectStatus::IN_PROGRESS]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/projects?status=' . ProjectStatus::IN_PROGRESS->value);

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    #[Test]
    public function can_search_projects_by_name()
    {
        Project::factory()->create(['name' => 'Mobile App Development']);
        Project::factory()->create(['name' => 'Website Redesign']);
        Project::factory()->create(['name' => 'API Integration']);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/projects?search=Mobile');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'Mobile App Development']);
    }

    #[Test]
    public function validates_required_fields_on_create()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/projects', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'status', 'user_id']);
    }

    #[Test]
    public function validates_progress_range()
    {
        $data = [
            'name' => 'Test Project',
            'status' => ProjectStatus::PENDING->value,
            'user_id' => $this->admin->id,
            'progress' => 150, // Invalid: > 100
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/projects', $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['progress']);
    }

    #[Test]
    public function validates_risk_score_range()
    {
        $data = [
            'name' => 'Test Project',
            'status' => ProjectStatus::PENDING->value,
            'user_id' => $this->admin->id,
            'risk_score' => -10, // Invalid: < 0
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/projects', $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['risk_score']);
    }

    #[Test]
    public function validates_end_date_after_start_date()
    {
        $data = [
            'name' => 'Test Project',
            'status' => ProjectStatus::PENDING->value,
            'user_id' => $this->admin->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->subDays(1)->toDateString(), // Invalid: before start
        ];

        $response = $this->actingAs($this->admin)
            ->postJson('/api/projects', $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['end_date']);
    }

    #[Test]
    public function validates_per_page_upper_bound()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/projects?per_page=200');

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['per_page']);
    }

    #[Test]
    public function unauthenticated_user_cannot_access_projects()
    {
        $response = $this->getJson('/api/projects');

        $response->assertUnauthorized();
    }
}
