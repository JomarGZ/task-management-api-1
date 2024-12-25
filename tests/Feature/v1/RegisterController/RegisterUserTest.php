<?php

namespace Tests\Feature\v1\RegisterController;

use App\Enums\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
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

    public function test_it_return_corrent_json_Structure_response()
    {
        $response = $this->postJson('api/v1/auth/register', [
            'name' => 'test name',
            'email' => 'test@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(201);
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
    
}
