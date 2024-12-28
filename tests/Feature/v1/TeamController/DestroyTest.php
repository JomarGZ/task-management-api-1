<?php

namespace Tests\Feature\v1\TeamController;

use App\Enums\Role;
use App\Models\Team;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $admin;
    private User $member;
    private Tenant $tenant;
    private Team $team;
    private Team $otherTenantTeam;

 
    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->admin = User::factory()->recycle($this->tenant)->create();
        $this->member = User::factory()->recycle($this->tenant)->create(['role' => Role::MEMBER->value]);
        $this->team = Team::factory()->recycle($this->tenant)->create(['name' => 'old team']);
        $this->otherTenantTeam = Team::factory()->create(['name' => 'old team']);
        Sanctum::actingAs($this->admin);
    }

    public function test_it_requires_authentication_to_delete_a_team()
    {
        $this->refreshApplication();
        $response = $this->deleteJson("api/v1/teams/{$this->team->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
   

    public function test_only_allows_admin_to_delete_a_team()
    {
        Sanctum::actingAs($this->member);
        $response = $this->deleteJson("api/v1/teams/{$this->team->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

    }
    // only allows for deletion of team that belongs to the same tenant

    public function test_only_allows_for_deletion_of_team_that_belongs_to_the_same_tenant()
    {
        $response = $this->deleteJson("api/v1/teams/{$this->otherTenantTeam->id}");

        $response->assertStatus(Response::HTTP_NOT_FOUND);

    }
    // it can delete a team

    public function test_it_can_delete_a_team()
    {
        $response = $this->deleteJson("api/v1/teams/{$this->team->id}");

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseMissing('teams', [
            'id' => $this->team->id
        ]);
    }
}
