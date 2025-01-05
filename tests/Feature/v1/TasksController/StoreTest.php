<?php

namespace Tests\Feature\v1\TasksController;

use App\Enums\Role;
use App\Models\Project;
use App\Models\Task;
use App\Models\Team;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use LazilyRefreshDatabase;
    
    private User $manager;
    private User $member;
    private Tenant $tenant;

    private Team $team;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        ini_set('memory_limit', '256M');
        $this->tenant = Tenant::factory()->create();
        $this->manager = User::factory()->recycle($this->tenant)->create(['role' => Role::MEMBER->value]);
        $this->member = User::factory()->recycle($this->tenant)->create(['role' => Role::MEMBER->value]);
        $this->team = Team::factory()->recycle($this->tenant)->create();
        
        $this->team->members()->attach($this->manager->id, ['role' => Role::PROJECT_MANAGER->value, 'tenant_id' => $this->tenant->id]);
        $this->team->members()->attach($this->member->id, ['role' => Role::MEMBER->value, 'tenant_id' => $this->tenant->id]);

        $this->project = Project::factory()->recycle($this->tenant)->recycle($this->team)->create();
       
    }

    public function test_rquires_authentication_for_storing_task()
    {
        $response = $this->postJson("api/v1/projects/{$this->project->id}/tasks", [
            'title' => 'title test',
            'description' => 'description test',
        ]);

        $response->assertUnauthorized();
    }
    public function test_only_allow_team_manager_to_create_task()
    {
        Sanctum::actingAs($this->member);
        $response = $this->postJson("api/v1/projects/{$this->project->id}/tasks", [
            'title' => 'title test',
            'description' => 'description test',
        ]);

        $response->assertUnauthorized();
    }

    public function test_it_can_create_task_with_project_manager_role_in_a_team()
    {
        Sanctum::actingAs($this->manager);
        $response = $this->postJson("api/v1/projects/{$this->project->id}/tasks", [
            'title' => 'title test',
            'description' => 'description test',
        ]);
        $response->assertCreated();

        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'description',
                'priority_level',
                'status',
                'deadline_at',
                'started_at',
                'completed_at',
                'project' => [
                    'id',
                    'name',
                    'description'
                ]
            ]
        ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'title test'
        ]);
    }

}
