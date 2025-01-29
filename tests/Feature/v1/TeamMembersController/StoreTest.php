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

class StoreTest extends TestCase
{
    use LazilyRefreshDatabase;

    private Tenant $tenant;
    private User $teamLEad;
    private User $admin;
    private User $member;
    private Team $team;
    private $teamMembers;

    protected function setUp(): void
    {
        Parent::setUp();

        $this->tenant = Tenant::factory()->create();

        $this->admin = User::factory()->recycle($this->tenant)->create(['role' => Role::ADMIN->value]);
        $this->teamLEad = User::factory()->recycle($this->tenant)->create(['role' => Role::TEAM_LEAD->value]);
        $this->member = User::factory()->recycle($this->tenant)->create(['role' => Role::MEMBER->value]);
        
        $this->team = Team::factory()->recycle($this->tenant)->create();
        $this->teamMembers = User::factory(5)->recycle($this->tenant)->create();

        Sanctum::actingAs($this->admin);
        $this->team->members()->attach($this->teamMembers, ['tenant_id' => $this->tenant->id]);

    }

    public function test_it_requires_authentication_to_add_a_member_to_a_team()
    {
        $this->refreshApplication();

        $response = $this->postJson("api/v1/teams/{$this->team->id}/members", [
            'member_id' => $this->member->id,
            'role' => Role::MEMBER->value
        ]);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $this->assertDatabaseMissing('team_user', [
            'member_id' => $this->member->id
        ]);
    }

    public function test_it_prevent_duplication_when_adding_the_same_member_to_a_team()
    {
        $response = $this->postJson("api/v1/teams/{$this->team->id}/members", [
            'member_id' => $this->teamMembers->random()->id,
            'role' => Role::MEMBER->value
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertDatabaseHas('team_user', [
            'member_id' => $this->teamMembers->random()->id
        ]); 
    }
    public function test_only_admin_allows_to_add_member_to_a_team()
    {
        Sanctum::actingAs($this->teamLEad);
        $response = $this->postJson("api/v1/teams/{$this->team->id}/members", [
            'member_id' => $this->member->id,
            'role' => Role::MEMBER->value
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
    public function test_only_allows_to_add_member_that_belongs_to_the_same_tenant()
    {
        $this->refreshApplication();
        $otherTenantMember = User::factory()->create();
        Sanctum::actingAs($this->admin);
        $response = $this->postJson("api/v1/teams/{$this->team->id}/members", [
            'member_id' => $otherTenantMember->id,
            'role' => Role::MEMBER->value
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertDatabaseMissing('team_user', [
            'member_id' => $otherTenantMember->id
        ]);
    }

    public function test_it_can_add_member_to_a_team()
    {
        $response = $this->postJson("api/v1/teams/{$this->team->id}/members", [
            'member_id' => $this->member->id,
            'role' => Role::MEMBER->value
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('team_user', [
            'member_id' => $this->member->id
        ]);
    }

}
