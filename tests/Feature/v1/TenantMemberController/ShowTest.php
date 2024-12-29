<?php

namespace Tests\Feature\v1\TenantMemberController;

use App\Enums\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $memberToShow;
    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->recycle($this->tenant)->create(['role' => Role::ADMIN->value]);
        User::factory()->recycle($this->tenant)->count(5)->create();
        
        $this->memberToShow = User::factory()->recycle($this->tenant)->create([
            'name' => 'Test Member',
            'email' => 'test.member@example.com',
            'role' => Role::MEMBER->value
        ]);
        Sanctum::actingAs($this->user);
        
      
    }

    // ... [Previous test methods remain the same] ...

    public function test_show_returns_correct_member()
    {
        $response = $this->getJson("api/v1/tenant/members/{$this->memberToShow->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'role'
                ]
            ])
            ->assertJsonPath('data.id', $this->memberToShow->id)
            ->assertJsonPath('data.name', $this->memberToShow->name)
            ->assertJsonPath('data.email', $this->memberToShow->email)
            ->assertJsonPath('data.role', $this->memberToShow->role);
    }

    public function test_show_returns_404_for_nonexistent_member()
    {
        $response = $this->getJson('api/v1/tenant/members/2342342');

        $response->assertStatus(404);
    }

    public function test_show_validates_member_id_format()
    {
        $response = $this->getJson('api/v1/tenant/members/invalidid');

        $response->assertStatus(404);
    }
}
