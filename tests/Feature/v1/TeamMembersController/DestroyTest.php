<?php

namespace Tests\Feature\v1\TeamMembersController;

use App\Enums\Role;
use App\Models\Team;
use App\Models\TeamUser;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $admin;
    private User $member;
    private Team $team;
    private User $teamMember;

    protected function setUp(): void
    {
        Parent::setUp();

        $this->tenant = Tenant::factory()->create();

        $this->admin = User::factory()->recycle($this->tenant)->create(['role' => Role::ADMIN->value]);
        $this->member = User::factory()->recycle($this->tenant)->create(['role' => Role::MEMBER->value]);
        $this->team = Team::factory()->create();
        $this->teamMember = User::factory()->recycle($this->tenant)->create();

        $this->team->members()->attach($this->teamMember->id, ['tenant_id' => $this->tenant->id]);
    }

    public function test_it_requires_authentication_to_remove_a_member_to_team()
    {

        $response = $this->deletejson("api/v1/teams/{$this->team->id}/members/{$this->teamMember->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertDatabaseHas('team_user', [
            'member_id' => $this->teamMember->id
        ]);

    }
    public function test_only_admin_can_remove_a_member_to_a_team()
    {
        Sanctum::actingAs($this->member);
        $response = $this->deletejson("api/v1/teams/{$this->team->id}/members/{$this->teamMember->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertDatabaseHas('team_user', [
            'member_id' => $this->teamMember->id
        ]);
    }
    public function test_only_allow_to_remove_a_member_to_a_team_if_the_same_tenant()
    {
        Sanctum::actingAs(User::factory()->create());
        $response = $this->deletejson("api/v1/teams/{$this->team->id}/members/{$this->teamMember->id}");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertDatabaseHas('team_user', [
            'member_id' => $this->teamMember->id
        ]);
    }
    public function test_it_can_remove_a_member_of_a_team()
    {
        Sanctum::actingAs($this->admin);
        $response = $this->deletejson("api/v1/teams/{$this->team->id}/members/{$this->teamMember->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('team_user', [
            'member_id' => $this->teamMember->id
        ]);
    }
}
