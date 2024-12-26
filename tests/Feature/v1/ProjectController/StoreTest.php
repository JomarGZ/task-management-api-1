<?php

namespace Tests\Feature\v1\ProjectController;

use App\Models\Team;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoreTest extends TestCase
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

    public function test_can_create_project(): void
    {
        $response = $this->postJson('api/v1/projects', [
            'name' => 'add project',
            'description' => 'description'
        ]);

        $response->assertStatus(201);
    }
}
