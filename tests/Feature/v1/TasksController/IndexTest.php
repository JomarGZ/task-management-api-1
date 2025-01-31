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
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use LazilyRefreshDatabase;
    private User $member;
    private Tenant $tenant;

    private Team $team;
    private Project $project;
    private $tasks;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->member = User::factory()->recycle($this->tenant)->create(['role' => Role::MEMBER->value]);
        $this->team = Team::factory()->create();
        
        $this->team->members()->attach($this->member->id, ['role' => Role::MEMBER->value, 'tenant_id' => $this->tenant->id]);

        $this->project = Project::factory()->recycle($this->tenant)->create();
        $this->tasks =Task::factory(2)
            ->recycle($this->tenant)
            ->recycle($this->project)
            ->recycle($this->member)
            ->create([
                'title' => fake()->randomElement(['Task A', 'Task B']),
                'description' => 'description', 
                'created_at' => fn() => now()->subDays(rand(0, 1))
            ]);
    }

    public function test_task_can_sort_by_valid_columns()
    {
        Sanctum::actingAs($this->member);
        Task::factory()
            ->recycle($this->tenant)
            ->recycle($this->member)
            ->recycle($this->project)
            ->create(['title' => 'A task']);
        Task::factory()
            ->recycle($this->tenant)
            ->recycle($this->member)
            ->recycle($this->project)
            ->create(['title' => 'B task']);
        $response = $this->getJson("api/v1/projects/{$this->project->id}/tasks?column=title&direction=asc");

        $response->assertOk();
        
        $data = $response->json('data');
        $this->assertEquals('A task', $data[0]['title']);
        $this->assertEquals('B task', $data[1]['title']);
    }

    public function test_task_invalid_sort_column_falls_back_to_created_at()
    {
        Sanctum::actingAs($this->member);
        Task::factory()
            ->recycle($this->tenant)
            ->recycle($this->project)
            ->create([
                'created_at' => now()->subDay(),
                'description' => 'description'
            ]);

        Task::factory()
            ->recycle($this->tenant)
            ->recycle($this->project)
            ->create([
                'created_at' => now(),
                'description' => 'description'
        ]);
        $response = $this->getJson("api/v1/projects/{$this->project->id}/tasks?column=invalid_column");

        
        $data = $response->json('data');
        $this->assertEquals(now()->toDateString(), date('Y-m-d', strtotime($data[0]['created_at'])));
    }

    public function test_can_search_tasks()
    {
        Sanctum::actingAs($this->member);

        Task::factory()
            ->recycle($this->tenant)
            ->recycle($this->project)
            ->create([
            'title' => 'Target task',
            'description' => 'Some description'
        ]);
        
        Task::factory()
            ->recycle($this->tenant)
            ->recycle($this->project)
            ->create([
            'title' => 'Another task',
            'description' => 'Different description'
        ]);

        $response = $this->getJson("api/v1/projects/{$this->project->id}/tasks?search=Target task");

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Target task');
    }

    public function test_it_returns_paginated_tasks()
    {
        Sanctum::actingAs($this->member);
       
        $response = $this->getJson("api/v1/projects/{$this->project->id}/tasks");
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonStructure([
            'data' => [
               [ 
                    'id',
                    'title',
                    'description',
                    'priority_level',
                    'status',
                    'deadline_at',
                    'started_at',
                    'completed_at'
                ]
            ],
            'links',
            'meta'
        ]);

    }
}
