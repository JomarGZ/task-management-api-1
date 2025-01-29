<?php

namespace Tests\Feature\v1\TasksController;

use App\Enums\Enums\Statuses;
use App\Models\Project;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskUpdateStartedAtOnStatusChangeTest extends TestCase
{
    use RefreshDatabase;
    private Task $task;

    private Tenant $tenant;
    private Project $project;
    private User $member;

    protected function setUp(): void
    {
        parent::setUp();

        // Increase memory limit

        $this->tenant = Tenant::factory()->create();
        $this->member = User::factory()->for($this->tenant)->create();
        $this->project = Project::factory()->for($this->tenant)->create();
        $this->task = Task::factory()->for($this->project)->for($this->tenant)->create([
            'started_at' => null,
            'completed_at' => null
        ]);
    }
    public function test_it_requires_authentication()
    {
        $response = $this->putJson("api/v1/tasks/{$this->task->id}", [
            'status' => Statuses::IN_PROGRESS->value,
        ]);

        $response->assertUnauthorized();
    }
    public function test_task_started_at_is_updated_when_status_is_changed_to_started()
    {
       
        Sanctum::actingAs($this->member);
        $this->assertNull($this->task->started_at);
        $response = $this->patchJson("api/v1/tasks/{$this->task->id}/status", [
            'status' => Statuses::IN_PROGRESS->value
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id,
            'started_at' => $this->task->refresh()->started_at
        ]);
    }

    public function test_task_started_at_is_not_updated_when_status_is_not_changed_to_in_progress()
    {
        Sanctum::actingAs($this->member);
        $this->assertNull($this->task->started_at);
        $response = $this->patchJson("api/v1/tasks/{$this->task->id}/status", [
            'status' => Statuses::COMPLETED->value
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id,
            'started_at' => $this->task->refresh()->started_at
        ]);
    }

    public function test_task_completed_at_is_updated_when_status_is_changed_to_completed()
    {
        Sanctum::actingAs($this->member);
        $this->assertNull($this->task->completed_at);
        $response = $this->patchJson("api/v1/tasks/{$this->task->id}/status", [
            'status' => Statuses::COMPLETED->value
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id,
            'completed_at' => $this->task->refresh()->completed_at
        ]);
    }

    public function test_task_completed_at_is_not_updated_when_status_is_not_changed_to_completed()
    {
        Sanctum::actingAs($this->member);
        $this->assertNull($this->task->completed_at);
        $response = $this->patchJson("api/v1/tasks/{$this->task->id}/status", [
            'status' => Statuses::IN_PROGRESS->value
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tasks', [
            'id' => $this->task->id,
            'completed_at' => null
        ]);
    }
}
