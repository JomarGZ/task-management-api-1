<?php

namespace Tests\Feature\v1\TenantMemberController;

use App\Enums\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;
    private User $memberToUpdate;
    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::factory()->create();
        // Create a test user with appropriate permissions
        $this->user = User::factory()->recycle($this->tenant)->create(['role' => Role::ADMIN->value]);
        User::factory()->recycle($this->tenant)->count(5)->create();
        $this->memberToUpdate = User::factory()->recycle($this->tenant)->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'role' => Role::MEMBER->value
        ]);
        Sanctum::actingAs($this->user);
        
    }

    public function test_update_member_successfully()
    {
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role' => Role::PROJECT_MANAGER->value
        ];

        $response = $this->putJson("api/v1/tenant/members/{$this->memberToUpdate->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'role'
                ]
            ])
            ->assertJsonPath('data.name', $updateData['name'])
            ->assertJsonPath('data.email', $updateData['email'])
            ->assertJsonPath('data.role', $updateData['role']);

        // Assert the database was updated
        $this->assertDatabaseHas('users', [
            'id' => $this->memberToUpdate->id,
            'name' => $updateData['name'],
            'email' => $updateData['email'],
            'role' => $updateData['role']
        ]);
    }

    public function test_update_validates_unique_email_except_self()
    {
        // Create another user with an email we'll try to use
        $otherUser = User::factory()->create([
            'email' => 'other@example.com'
        ]);

        // Try to update with someone else's email
        $response = $this->putJson("api/v1/tenant/members/{$this->memberToUpdate->id}", [
            'name' => 'Updated Name',
            'email' => $otherUser->email,
            'role' => Role::MEMBER->value
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        // Try to update with own email (should succeed)
        $response = $this->putJson("api/v1/tenant/members/{$this->memberToUpdate->id}", [
            'name' => 'Updated Name',
            'email' => $this->memberToUpdate->email,
            'role' => Role::MEMBER->value
        ]);

        $response->assertStatus(200);
    }

    public function test_update_validates_required_fields()
    {
        $response = $this->putJson("api/v1/tenant/members/{$this->memberToUpdate->id}", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'role']);
    }

    public function test_update_validates_valid_role()
    {
        $response = $this->putJson("api/v1/tenant/members/{$this->memberToUpdate->id}", [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role' => 'invalid_role'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);
    }

    public function test_update_partial_data()
    {
        $updateData = [
            'name' => 'Updated Name',
        ];

        $response = $this->putJson("api/v1/tenant/members/{$this->memberToUpdate->id}", $updateData);

        $response->assertStatus(422); // Assuming all fields are required
    }

    public function test_update_nonexistent_member()
    {
        $response = $this->putJson("api/v1/tenant/members/4324", [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'role' => 'user'
        ]);

        $response->assertStatus(404);
    }
}
