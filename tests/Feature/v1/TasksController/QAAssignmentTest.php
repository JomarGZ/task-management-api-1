<?php

namespace Tests\Feature\v1\TasksController;

use App\Models\Project;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class QAAssignmentTest extends TestCase
{
    use RefreshDatabase;
    private Tenant $tenant;
    private Task $task;
    private User $member;
    private User $qa;

    private Project $project;

    public function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->member = User::factory()->for($this->tenant)->create();
        $this->qa = User::factory()->for($this->tenant)->create();
        $this->project = Project::factory()->for($this->tenant)->create();
        $this->task = Task::factory()->for($this->project)->for($this->tenant)->create();
    }

    public function test_it_requires_authentication_for_qa_assignment()
    {
        $response = $this->patchJson("api/v1/tasks/{$this->task->id}/assign-qa", [
            'assigned_qa_id' => $this->qa->id,
        ]);

        $response->assertUnauthorized();
    }

    public function test_it_can_assign_or_reassign_qa_to_task()
    {
        Sanctum::actingAs($this->member);
        $response = $this->patchJson("api/v1/tasks/{$this->task->id}/assign-qa", [
            'assigned_qa_id' => $this->qa->id,
        ]);

        $response->assertOk();
        $this->assertEquals($this->qa->id, $this->task->refresh()->assigned_qa_id);
    }

    public function test_it_can_unassign_qa_to_task()
    {
        Sanctum::actingAs($this->member);
        $response = $this->deleteJson("api/v1/tasks/{$this->task->id}/unassign-qa", [
            'assigned_qa_id' => null,
        ]);

        $response->assertOk();
        $this->assertNull($this->task->refresh()->assigned_qa_id);
    }
}
