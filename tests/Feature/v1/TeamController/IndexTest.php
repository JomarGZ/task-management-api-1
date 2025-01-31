<?php

namespace Tests\Feature\v1\TeamController;

use App\Models\Team;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::factory()->create();
        Team::factory()->create();
        
    }

  

    public function test_return_team_data()
    {
        $this->user = User::factory()->recycle($this->tenant)->create();
        Sanctum::actingAs($this->user);

        $response = $this->getJson('api/v1/teams');

        $response->assertStatus(200);
        
    }
 
}
