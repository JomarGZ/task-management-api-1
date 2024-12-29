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

class ViewTest extends TestCase
{
    use LazilyRefreshDatabase;
    
    private User $manager;
    private User $member;
    private Tenant $tenant;
    private Team $team;
    private Project $project;
    private Task $task;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->manager = User::factory()->recycle($this->tenant)->create(['role' => Role::MEMBER->value]);
        $this->member = User::factory()->recycle($this->tenant)->create(['role' => Role::MEMBER->value]);
        $this->team = Team::factory()->recycle($this->tenant)->create();
        
        $this->team->members()->attach($this->manager->id, ['role' => Role::PROJECT_MANAGER->value, 'tenant_id' => $this->tenant->id]);
        $this->team->members()->attach($this->member->id, ['role' => Role::MEMBER->value, 'tenant_id' => $this->tenant->id]);

        $this->project = Project::factory()->recycle($this->tenant)->recycle($this->team)->create();
        $this->task = Task::factory()->recycle($this->tenant)->recycle($this->project)->recycle($this->member)->create(['title' => 'old task', 'description' => 'description']);
    }

    public function test_rquires_authentication_to_view_a_task()
    {
        $response = $this->getJson("api/v1/tasks/{$this->task->id}");

        $response->assertUnauthorized();
    }

    public function test_task_should_not_leak_to_other_tenant()
    {
        $otherTenantTask = Task::factory()->create();
        Sanctum::actingAs($this->member);
        $response = $this->getJson("api/v1/tasks/{$otherTenantTask->id}");

        $response->assertNotFound();
    }

    public function test_task_can_be_view()
    {
        Sanctum::actingAs($this->member);
        $response = $this->getJson("api/v1/tasks/{$this->task->id}");

        $response->assertOk();
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
                'created_at',
                'project' => [
                    'id',
                    'name',
                    'description',
                    'created_at'
                ]
            ]
        ]);
    }

}
