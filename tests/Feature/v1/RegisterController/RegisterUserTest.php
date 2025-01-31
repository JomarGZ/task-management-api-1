<?php

namespace Tests\Feature\v1\RegisterController;

use App\Enums\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use LazilyRefreshDatabase;
 
    public function test_it_can_register_a_user(): void
    {
        $response = $this->postJson('api/v1/auth/register', [
            'name' => 'test name',
            'email' => 'test@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'test@gmail.com', 'role' => Role::ADMIN]);
    }

    public function test_it_creates_a_tenant_when_user_registers()
    {
        $response = $this->postJson('api/v1/auth/register', [
            'name' => 'test name',
            'email' => 'test@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(201);
        
        $user = User::where('email', 'test@gmail.com')->first();

        $this->assertDatabaseHas('tenants', [
            'name' => "{$user->name} tenant"
        ]);

        $tenant = Tenant::where('name', "{$user->name} tenant")->first();

        $this->assertEquals($user->tenant_id, $tenant->id);
    }
    
    public function test_it_requires_name_email_and_password()
    {
        $response = $this->postJson('api/v1/auth/register', []);

        $response->assertStatus(422); // Validation error
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_it_requires_name_to_be_a_string_and_not_exceed_max_length()
    {
        $response = $this->postJson('api/v1/auth/register', [
            'name' => str_repeat('a', 256), // Exceeding max length
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_it_requires_email_to_be_unique_and_valid()
    {
        // Create a user with a specific email
        User::factory()->create(['email' => 'test@example.com']);

        // Attempt to use the same email
        $response = $this->postJson('api/v1/auth/register', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_it_requires_password_to_be_confirmed()
    {
        $response = $this->postJson('api/v1/auth/register', [
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_it_allows_valid_data()
    {
        $response = $this->postJson('api/v1/auth/register', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201); // Assuming successful creation returns 201
        $response->assertJsonStructure([
            'status',
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email'
                ],
                'access_token'
            ],
            'message'
        ]);
    }
}
