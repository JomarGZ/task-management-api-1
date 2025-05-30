<?php

namespace Tests\Feature\v1\TasksController;

use App\Models\Project;
use App\Models\Task;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
class DeveloperAssignmentTest extends TestCase
{
    use RefreshDatabase;
    private Task $task;
    
    private Tenant $tenant;
    private Project $project;
    private  User $member;

    public function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->member = User::factory()->for($this->tenant)->create();
        $this->project = Project::factory()->for($this->tenant)->create();
        $this->task = Task::factory()->for($this->project)->for($this->tenant)->create();
    }

    public function test_it_requires_authentication_for_task_assignment()
    {
        $response = $this->patchJson("api/v1/tasks/{$this->task->id}/assign-developer", [
            'assigned_dev_id' => $this->member->id,
        ]);

        $response->assertUnauthorized();
    }

    public function test_it_can_assign_or_reassign_dev_to_task()
    {
        Sanctum::actingAs($this->member);
        $response = $this->patchJson("api/v1/tasks/{$this->task->id}/assign-developer", [
            'assigned_dev_id' => $this->member->id,
        ]);

        $response->assertOk();
        $this->assertEquals($this->member->id, $this->task->refresh()->assigned_dev_id);
    }
    public function test_it_can_unassign_dev_to_task()
    {
        Sanctum::actingAs($this->member);
        $response = $this->deleteJson("api/v1/tasks/{$this->task->id}/unassign-developer", [
            'assigned_dev_id' => null,
        ]);

        $response->assertOk();
        $this->assertEquals(null, $this->task->refresh()->assigned_dev_id);
    }

    public function test_validates_assigned_id()
    {
        Sanctum::actingAs($this->member);
        $response = $this->patchJson("api/v1/tasks/{$this->task->id}/assign-developer", [
            'assigned_dev_id' => 'invalid_id',
            'assigned_qa_id' => 'invalid_id'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['assigned_dev_id', 'assigned_qa_id']);
    }


   public function test_ensure_not_leaking_task_assignment_from_other_tenant()
    {
        $tenant = Tenant::factory()->create();
        $member = User::factory()->for($tenant)->create();
        $project = Project::factory()->for($tenant)->create();
        $task = Task::factory()->for($project)->for($tenant)->create();

        Sanctum::actingAs($this->member);
        $response = $this->patchJson("api/v1/tasks/{$task->id}/assign-developer", [
            'assigned_dev_id' => $member->id,
        ]);

        $response->assertNotFound();
    }

    public function test_send_email_notification_when_task_assigned_to_dev()
    {
        Sanctum::actingAs($this->member);
        Notification::fake();

        $response = $this->patchJson("api/v1/tasks/{$this->task->id}/assign-developer", [
            'assigned_dev_id' => $this->member->id,
        ]);

        $response->assertOk();
        Notification::assertSentTo(
            [$this->member],
            TaskAssignedNotification::class
        );
    }

    public function test_send_database_notification_when_task_assigned()
    {
        Sanctum::actingAs($this->member);
        Notification::fake();

        $response = $this->patchJson("api/v1/tasks/{$this->task->id}/assign-developer", [
            'assigned_dev_id' => $this->member->id,
        ]);

        $response->assertOk();
        Notification::assertSentTo(
            [$this->member],
            TaskAssignedNotification::class,
            function ($notification, $channels) {
                return in_array('database', $channels);
            }
        );
    }
}
