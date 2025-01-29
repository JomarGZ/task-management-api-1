<?php

namespace Tests\Feature\v1\TenantMemberController;

use App\Enums\Role;
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
        $this->user = User::factory()->recycle($this->tenant)->create(['role' => Role::ADMIN->value]);
        
        Sanctum::actingAs($this->user);

        User::factory()->recycle($this->tenant)->count(10)->create(['role' => Role::MEMBER->value]);
    }

    public function test_index_returns_paginated_users_with_default_sorting()
    {
        $response = $this->getJson('api/v1/tenant/members');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'role', 'email']
                ],
                'links',
                'meta'
            ])
            ->assertJsonCount(10, 'data');
    }

    public function test_index_sorts_by_valid_column_and_direction()
    {
        $response = $this->getJson('api/v1/tenant/members?column=name&direction=asc');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        // Assert records are sorted by name in ascending order
        $names = array_column($data, 'name');
        $sortedNames = $names;
        sort($sortedNames);
        $this->assertEquals($sortedNames, $names);
    }

    
    public function test_index_uses_default_sorting_for_invalid_column()
    {
        $response = $this->getJson('api/v1/tenant/members?column=invalid_column');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        // Should default to created_at desc
        $dates = array_column($data, 'created_at');
        $sortedDates = $dates;
        rsort($sortedDates);
        $this->assertEquals($sortedDates, $dates);
    }

    public function test_index_uses_default_direction_for_invalid_direction()
    {
        $response = $this->getJson('api/v1/tenant/members?direction=invalid');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        $dates = array_column($data, 'created_at');
        $sortedDates = $dates;
        sort($sortedDates);
        $this->assertEquals($sortedDates, $dates);
    }

    public function test_index_filters_by_search_term()
    {
        $tenant = $this->tenant;
        User::withoutEvents(function() use ($tenant) {
            return User::factory()->recycle($tenant)->create([
                'role' => Role::MEMBER->value,
                'name' => 'Unique Search Name'
            ]);
        });

        $response = $this->getJson('api/v1/tenant/members?search=Unique Search');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Unique Search Name');
    }

    public function test_index_filters_by_role()
    {
        $tenant = $this->tenant;

        User::withoutEvents(function() use ($tenant) {
            User::factory()->recycle($tenant)->create([
                'role' => Role::PROJECT_MANAGER->value,
            ]);
            User::factory()->recycle($tenant)->create([
                'role' => Role::MEMBER->value
            ]);
        });

        $response = $this->getJson('api/v1/tenant/members?role=' . Role::MEMBER->value);

        $response->assertStatus(200)
            ->assertJsonPath('data.0.role', Role::MEMBER->value);
    }

    public function test_index_combines_multiple_filters_and_sorting()
    {
        $tenant = $this->tenant;
        User::withoutEvents(function () use ($tenant) {
            User::factory()->recycle($tenant)->create([
                'name' => 'Test User',
                'role' => 'manager',
                'email' => 'test@example.com'
            ]);
    
        });
        
        $response = $this->getJson('api/v1/tenant/members?role=manager&search=Test&column=email&direction=asc');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.name', 'Test User')
            ->assertJsonPath('data.0.role', 'manager');
    }
}
