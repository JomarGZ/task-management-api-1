<?php

namespace Tests\Feature\v1\TasksController;

use App\Enums\Role;
use App\Models\Project;
use App\Models\Task;
use App\Models\Team;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DestroyTest extends TestCase
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
        $this->team = Team::factory()->create();
        
        $this->team->members()->attach($this->manager->id, ['role' => Role::PROJECT_MANAGER->value, 'tenant_id' => $this->tenant->id]);
        $this->team->members()->attach($this->member->id, ['role' => Role::MEMBER->value, 'tenant_id' => $this->tenant->id]);

        $this->project = Project::factory()->recycle($this->tenant)->recycle($this->team)->create();
        $this->task = Task::factory()->recycle($this->tenant)->recycle($this->project)->recycle($this->member)->create(['title' => 'old task', 'description' => 'description']);
    }

    public function test_it_requires_authentication_to_delete_a_task()
    {
        $response = $this->deleteJson("api/v1/tasks/{$this->task->id}");

        $response->assertUnauthorized();

        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id
        ]);
    }

    public function test_should_not_leak_the_task_from_other_tenant_in_performing_the_deletion()
    {
        $otherTenantTask = Task::factory()->create();
        Sanctum::actingAs($this->manager);
        $response = $this->deleteJson("api/v1/tasks/{$otherTenantTask->id}");

        $response->assertNotFound();
    }

    public function test_only_manager_in_the_team_can_delete_the_task()
    {
        Sanctum::actingAs($this->member);
        $response = $this->deleteJson("api/v1/tasks/{$this->task->id}");

        $response->assertUnauthorized();
        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id
        ]);
    }
    public function test_it_can_delete_a_task_as_a_project_manager()
    {
        Sanctum::actingAs($this->manager);
        $response = $this->deleteJson("api/v1/tasks/{$this->task->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('tasks', [
            'id' => $this->task->id
        ]);
    }
}
