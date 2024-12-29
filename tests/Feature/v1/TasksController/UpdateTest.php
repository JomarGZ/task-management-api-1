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

class UpdateTest extends TestCase
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

}
