<?php

namespace Tests\Feature\v1\UpdatePasswordController;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;
    private string $currentPassword = 'current-password';
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'password' => Hash::make($this->currentPassword)
        ]);
    }

    public function test_user_can_update_password_with_valid_credentials()
    {
        Sanctum::actingAs($this->user);
        
        $response = $this->putJson('api/v1/auth/password-update', [
            'current_password' => $this->currentPassword,
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123'
        ]);

        $response->assertOk()
            ->assertJson(['message' => 'Your password has been updated.']);

        $this->user->refresh();
        $this->assertTrue(Hash::check('new-password123', $this->user->password));
    }

    public function test_requires_valid_current_password()
    {
        Sanctum::actingAs($this->user);
        
        $response = $this->putJson('api/v1/auth/password-update', [
            'current_password' => 'wrong-password',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
            
        $this->user->refresh();
        $this->assertTrue(Hash::check($this->currentPassword, $this->user->password));
    }

    public function test_password_must_be_confirmed()
    {
        Sanctum::actingAs($this->user);
        
        $response = $this->putJson('api/v1/auth/password-update', [
            'current_password' => $this->currentPassword,
            'password' => 'new-password123',
            'password_confirmation' => 'different-password'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

 
    public function test_revokes_other_tokens_after_password_update()
    {
        $this->user->createToken('token1');
        $this->user->createToken('token2');
        $initialTokenCount = $this->user->tokens()->count();
        
        Sanctum::actingAs($this->user);
        
        $response = $this->putJson('api/v1/auth/password-update', [
            'current_password' => $this->currentPassword,
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123'
        ]);

        $response->assertOk();
        
        // Assert that tokens were revoked
        $this->user->refresh();
        $this->assertLessThan($initialTokenCount, $this->user->tokens()->count());
    }

    public function test_unauthenticated_user_cannot_update_password()
    {
        $response = $this->putJson('api/v1/auth/password-update', [
            'current_password' => $this->currentPassword,
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123'
        ]);

        $response->assertStatus(401);
    }
}