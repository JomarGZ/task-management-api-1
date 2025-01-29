<?php

namespace Tests\Feature\v1\TeamController;

use App\Enums\Role;
use App\Models\Team;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ViewTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Tenant $tenant;
    private Team $team;
    private Team $otherTenantTeam;

 
    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->admin = User::factory()->recycle($this->tenant)->create();
        $this->team = Team::factory()->create(['name' => 'old team']);
        
        $this->otherTenantTeam = Team::factory()->create(['name' => 'old team']);
    }

    public function test_it_requires_authentication_to_view_a_team()
    {
        $response = $this->getJson("api/v1/teams/{$this->team->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_return_team_data()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson("api/v1/teams/{$this->otherTenantTeam->id}");

        $response->assertOk();
    }
   
}
