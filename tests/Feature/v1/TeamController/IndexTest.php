<?php

namespace Tests\Feature\v1\TeamController;

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
    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->recycle($this->tenant)->create();
        Team::factory()->recycle($this->tenant)->create(['name' => 'unique team']);
        Team::factory()->recycle($this->tenant)->count(6)->create();

        Sanctum::actingAs($this->user);
        
    }

    public function test_index_returns_paginated_teams_with_default_sorting()
    {
        $response = $this->getJson('api/v1/teams');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name']
                ],
                'links',
                'meta'
            ])
            ->assertJsonCount(5, 'data');
    }

    public function test_index_sorts_by_name_ascending()
    {
        $response = $this->getJson('api/v1/teams?column=name&direction=asc');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        // Assert records are sorted by name in ascending order
        $names = array_column($data, 'name');
        $sortedNames = $names;
        sort($sortedNames);
        
        $this->assertEquals($sortedNames, $names);
    }

    public function test_index_defaults_to_created_at_for_invalid_column()
    {
        $response = $this->getJson('api/v1/teams?column=invalid_column');

        $response->assertStatus(200);
        
        // Should use created_at desc by default
        $data = $response->json('data');
        $dates = array_column($data, 'created_at');
        $sortedDates = $dates;
        rsort($sortedDates);
        
        $this->assertEquals($sortedDates, $dates);
    }

    public function test_index_defaults_to_desc_for_invalid_direction()
    {
        $response = $this->getJson('api/v1/teams?direction=invalid');

        $response->assertStatus(200);
        
        // Should default to desc
        $data = $response->json('data');
        $dates = array_column($data, 'created_at');
        $sortedDates = $dates;
        rsort($sortedDates);
        
        $this->assertEquals($sortedDates, $dates);
    }

    public function test_index_search_functionality()
    {
        // Create a team with a specific name to search for
     
        $response = $this->getJson('api/v1/teams?search=Unique');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'unique team');
    }

    public function test_index_empty_search_returns_all_paginated()
    {
        $response = $this->getJson('api/v1/teams?search=');
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data'); // Should return first page with 5 items
    }

    public function test_index_combines_search_and_sorting()
    {
        // Create specific test teams
        Team::factory()->create(['name' => 'AAA Test Team']);
        Team::factory()->create(['name' => 'ZZZ Test Team']);
        
        $response = $this->getJson('api/v1/teams?search=Test&column=name&direction=asc');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        // First team should be 'AAA Test Team' when sorted ascending
        $this->assertEquals('AAA Test Team', $data[0]['name']);
    }
}
