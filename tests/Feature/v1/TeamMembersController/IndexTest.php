<?php

namespace Tests\Feature\v1\TeamMembersController;

use App\Enums\Role;
use App\Models\Team;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use LazilyRefreshDatabase;

    private Tenant $tenant;
    private User $teamLEad;
    private Team $team;
    private $teamMembers;

    protected function setUp(): void
    {
        Parent::setUp();

        $this->tenant = Tenant::factory()->create();

        $this->teamLEad = User::factory()->recycle($this->tenant)->create(['role' => Role::TEAM_LEAD->value]);
        $this->team = Team::factory()->recycle($this->tenant)->create();
        $this->teamMembers = User::factory(5)->recycle($this->tenant)->create();

        Sanctum::actingAs($this->teamLEad);
        $this->team->members()->attach($this->teamMembers, ['tenant_id' => $this->tenant->id]);

    }


    public function test_it_requires_authentication_to_view_list_of_team_members_each_team()
    {
        $this->refreshApplication();
        $response = $this->getJson("api/v1/teams/{$this->team->id}/members");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

    }

    public function test_only_allow_to_view_team_members_with_the_same_tenant()
    {
        $this->refreshApplication();

        $otherTenant = Tenant::factory()->create();
        $otherTeam = Team::factory()->recycle($otherTenant)->create();
        $otherTeamMembers = User::factory()->recycle($otherTenant)->create();
        $otherTeam->members()->attach($otherTeamMembers, ['tenant_id' => $otherTenant->id]);
        Sanctum::actingAs($this->teamLEad);
        $response = $this->getJson("api/v1/teams/{$otherTeam->id}/members");

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }

    public function test_it_can_view_listing_of_team_members()
    {
        $response = $this->getJson("api/v1/teams/{$this->team->id}/members");

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonCount(5, 'data');
        $response->assertJsonStructure([
            'data' => [
               [
                    'id',
                    'name'
               ]
            ]
        ]);

    }
}
