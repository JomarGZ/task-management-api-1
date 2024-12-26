<?php

namespace Tests\Feature\v1\LoginController;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginUserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_can_authenticate_the_user(): void
    {
        $user = User::factory()->create();
        $response = $this->postJson('api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'status',
            'data' => [
                'access_token',
                'user' => [
                    'id',
                    'name',
                    'email'
                ]
            ],
            'message'
        ]);
    }
    public function test_it_requires_email_and_password()
    {
        $response = $this->postJson('api/v1/auth/login', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'email',
            'password'
        ]);

    }
    public function test_it_requires_email_to_be_valid(): void
    {
        $response = $this->postJson('api/v1/auth/login', [
            'email' => 'notemail',
            'password' => 'password'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_it_will_not_authenticate_if_incorrect_credentials(): void
    {
        $response = $this->postJson('api/v1/auth/login', [
            'email' => 'test@gmail.com',
            'password' => 'password'
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }
}
