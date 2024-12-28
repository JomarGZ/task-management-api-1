<?php

namespace Tests\Feature\v1\TeamController;

use App\Enums\Role;
use App\Models\Team;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ViewTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $admin;
    private Tenant $tenant;
    private Team $team;
    private Team $otherTenantTeam;

 
    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->admin = User::factory()->recycle($this->tenant)->create();
        $this->team = Team::factory()->recycle($this->tenant)->create(['name' => 'old team']);
        
        $this->otherTenantTeam = Team::factory()->create(['name' => 'old team']);
        Sanctum::actingAs($this->admin);
    }

    public function test_it_requires_authentication_to_view_a_team()
    {
        $this->refreshApplication();
        $response = $this->getJson("api/v1/teams/{$this->team->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_only_allow_user_with_the_same_tenant_to_view_the_team()
    {
        $response = $this->getJson("api/v1/teams/{$this->otherTenantTeam->id}");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
    public function test_can_view_a_team_with_members()
    {
        $response = $this->getJson("api/v1/teams/{$this->team->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'members'
            ]
        ]);

    }
}
