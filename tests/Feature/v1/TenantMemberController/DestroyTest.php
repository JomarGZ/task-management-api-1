<?php

namespace Tests\Feature\v1\TenantMemberController;

use App\Enums\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $memberToDelete;
    private Tenant $tenant;
    private User $member;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->recycle($this->tenant)->create(['role' => 'admin']);
        $this->memberToDelete = User::factory()->recycle($this->tenant)->create([
            'name' => 'Member To Delete',
            'email' => 'delete.me@example.com',
            'role' => Role::MEMBER->value
        ]);
        $this->member = User::factory()->recycle($this->tenant)->create(['role' => Role::MEMBER->value]);
        Sanctum::actingAs($this->user);
        
    }

    // ... [Previous test methods remain the same] ...

    public function test_destroy_deletes_member_when_authorized()
    {
        $response = $this->deleteJson("api/v1/tenant/members/{$this->memberToDelete->id}");

        $response->assertStatus(204);
        
        // Verify the user was actually deleted
        $this->assertDatabaseMissing('users', [
            'id' => $this->memberToDelete->id
        ]);
    }

    public function test_destroy_fails_when_unauthorized()
    {
        Sanctum::actingAs($this->member);
        $response = $this->deleteJson("api/v1/tenant/members/{$this->memberToDelete->id}");

        $response->assertStatus(401);
        
        // Verify the user still exists
        $this->assertDatabaseHas('users', [
            'id' => $this->memberToDelete->id
        ]);
    }

    public function test_destroy_returns_404_for_nonexistent_member()
    {
        $response = $this->deleteJson('api/v1/tenant/members/99999');

        $response->assertStatus(404);
    }

    public function test_destroy_prevents_self_deletion()
    {
        // Try to delete the authenticated user
        $response = $this->deleteJson("api/v1/tenant/members/{$this->user->id}");

        // Assuming your Gate policy prevents self-deletion
        $response->assertStatus(401);
        
        // Verify the user still exists
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id
        ]);
    }
  
}
