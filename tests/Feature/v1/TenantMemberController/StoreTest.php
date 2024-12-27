<?php

namespace Tests\Feature\v1\TenantMemberController;

use App\Enums\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;
    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::factory()->create();
        // Create a test user with appropriate permissions
        $this->user = User::factory()->recycle($this->tenant)->create(['role' => 'admin']);
        
        User::factory()->recycle($this->tenant)->count(5)->create();
        $this->actingAs($this->user);
        
        // Create test data
    }

    // ... [Previous test methods remain the same] ...

    public function test_store_creates_new_member_successfully()
    {
        $memberData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => Role::MEMBER->value
        ];

        $response = $this->postJson('api/v1/tenant/members', $memberData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'role'
                ]
            ])
            ->assertJsonPath('data.name', $memberData['name'])
            ->assertJsonPath('data.email', $memberData['email'])
            ->assertJsonPath('data.role', $memberData['role']);

        // Assert the user was created in the database
        $this->assertDatabaseHas('users', [
            'name' => $memberData['name'],
            'email' => $memberData['email'],
            'role' => $memberData['role']
        ]);

        // Verify password was hashed
        $newUser = User::where('email', $memberData['email'])->first();
        $this->assertTrue(Hash::check('password', $newUser->password));
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->postJson('api/v1/tenant/members', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'role']);
    }

    public function test_store_validates_unique_email()
    {
        $existingEmail = User::pluck('email')->first();
        $memberData = [
            'name' => 'John Doe',
            'email' => $existingEmail, // Using existing email
            'role' => 'user'
        ];

        $response = $this->postJson('api/v1/tenant/members', $memberData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_store_validates_valid_role()
    {
        $memberData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'invalid_role'
        ];

        $response = $this->postJson('api/v1/tenant/members', $memberData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);
    }

    public function test_store_validates_email_format()
    {
        $memberData = [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'role' => 'user'
        ];

        $response = $this->postJson('api/v1/tenant/members', $memberData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
