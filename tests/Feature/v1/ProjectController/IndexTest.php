<?php

namespace Tests\Feature\v1\ProjectController;

use App\Models\Project;
use App\Models\Team;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;
    private Team $team;
    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->recycle($this->tenant)->create();
        $this->team = Team::factory()->recycle($this->tenant)->create();
        
        // Authenticate user
        Sanctum::actingAs($this->user);
    }

    public function test_can_fetch_paginated_projects()
    {
        
        Project::factory(7)->recycle($this->tenant)->recycle($this->team)->create();

        $response = $this->getJson('/api/v1/projects');
        $response->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'team_assignee'
                    ]
                ],
                'links',
                'meta'
            ]);
    }

    public function test_default_sorting_is_created_at_desc()
    {
        $oldProject = Project::factory()->recycle($this->tenant)
            ->create(attributes: [
                'created_at' => now()->subDays(2)
            ]);
        
        $newProject = Project::factory()->recycle($this->tenant)
            ->create([
                'created_at' => now()
            ]);

        $response = $this->getJson('/api/v1/projects');

        $response->assertOk();
        
        $data = $response->json('data');
        $this->assertEquals($newProject->id, $data[0]['id']); 
        $this->assertEquals($oldProject->id, $data[1]['id']);
    }

    public function test_can_sort_by_valid_columns()
    {
        Project::factory()->recycle($this->tenant)
            ->create(attributes: [
                'name' => 'Project A'
            ]);
        Project::factory()->recycle($this->tenant)
            ->create(attributes: [
                'name' => 'Project B'
            ]);
   
        $response = $this->getJson('/api/v1/projects?column=name&direction=asc');

        $response->assertOk();
        
        $data = $response->json('data');
        $this->assertEquals('Project A', $data[0]['name']);
        $this->assertEquals('Project B', $data[1]['name']);
    }

    public function test_invalid_sort_column_falls_back_to_created_at()
    {
        Project::factory()->recycle($this->tenant)->create([
            'created_at' => now()->subDay()
        ]);
        Project::factory()->recycle($this->tenant)->create([
            'created_at' => now()
        ]);
     
        $response = $this->getJson('/api/v1/projects?column=invalid_column');

        $response->assertOk();
        
        $data = $response->json('data');
        $this->assertEquals(now()->toDateString(), date('Y-m-d', strtotime($data[0]['created_at'])));
    }

    public function test_invalid_direction_falls_back_to_asc()
    {
        Project::factory()->recycle($this->tenant)->create([
            'name' => 'Project B'
        ]);
        
        Project::factory()->recycle($this->tenant)->create([
            'name' => 'Project A'
        ]);

        $response = $this->getJson('/api/v1/projects?column=name&direction=invalid');

        $response->assertOk();
        
        $data = $response->json('data');
        $this->assertEquals('Project A', $data[0]['name']);
    }

    public function test_can_search_projects()
    {
        Project::factory()->recycle($this->tenant)->create([
            'name' => 'Target Project',
            'description' => 'Some description'
        ]);
        
        Project::factory()->recycle($this->tenant)->create([
            'name' => 'Another Project',
            'description' => 'Different description'
        ]);

        $response = $this->getJson('/api/v1/projects?search=Target');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Target Project');
    }

    public function test_includes_team_assignee_relationship()
    {
        $project = Project::factory()->recycle($this->tenant)->create();

        $response = $this->getJson('/api/v1/projects');

        $response->assertOk()
            ->assertJsonPath('data.0.team_assignee.id', $project->team_id)
            ->assertJsonPath('data.0.team_assignee.name', $project->teamAssignee->name);
    }

    public function test_tenant_scope_applies_correctly()
    {
       
        $tenantA = Tenant::factory()->create(['name' => 'tenant A']);

        Project::factory()->count(2)->create();

        $userA = User::withoutEvents(function () use ($tenantA) {
            return User::factory()->create([
                'tenant_id' => $tenantA->id,
                'name' => 'User A',
            ]);
        });
        Sanctum::actingAs($userA);
    
        Project::factory()->count(3)->create();
    
        $response = $this->getJson('/api/v1/projects');
        $response->assertOk();
        $response->assertJsonCount(3, 'data'); 
    }
}