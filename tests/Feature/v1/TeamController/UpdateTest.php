<?php

namespace Tests\Feature\v1\TeamController;

use App\Enums\Role;
use App\Models\Team;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $admin;
    private User $member;
    private Tenant $tenant;
    private Team $team;
    private Team $otherTenantTeam;

 
    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->admin = User::factory()->recycle($this->tenant)->create();
        $this->member = User::factory()->recycle($this->tenant)->create(['role' => Role::MEMBER->value]);
        $this->team = Team::factory()->recycle($this->tenant)->create(['name' => 'old team']);
        $this->otherTenantTeam = Team::factory()->create(['name' => 'old team']);
        Sanctum::actingAs($this->admin);
    }
 
    // it requires authentication
    public function test_it_requires_authentication_to_update_a_team()
    {
         $this->refreshApplication();
         $response = $this->putJson("api/v1/teams/{$this->team->id}", [
             'name' => 'update team',
         ]);
 
         $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
    // it can create a team
 
    public function test_it_can_update_a_team()
    {
        $response = $this->putJson("api/v1/teams/{$this->team->id}", [
            'name' => 'update team',
        ]);

         $response->assertStatus(Response::HTTP_OK);
         $response->assertJsonStructure([
             'data' => ['id', 'name']
         ]);

         $this->assertDatabaseHas('teams', [
            'id' => $this->team->id,
            'name' => 'update team'
         ]);
    }
    // only allow admin to create a team
 
    public function test_only_allow_admin_to_update_a_team()
    {
        Sanctum::actingAs($this->member);
        $response = $this->putJson("api/v1/teams/{$this->team->id}", [
            'name' => 'update team',
        ]);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
 
    public function test_allow_update_team_that_belongs_to_the_same_tenant()
    {
        $response = $this->putJson("api/v1/teams/{$this->otherTenantTeam->id}", [
            'name' => 'update team',
        ]);
        
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
    
    #[Test]
    #[DataProvider('validationDataProvider')]
    public function test_team_validation_rules(array $data, array $expectedErrors)
    {
        $response = $this->putJson("api/v1/teams/{$this->team->id}", $data);
 
         $response->assertUnprocessable()
             ->assertJsonValidationErrors($expectedErrors);
    }
 
    public static function validationDataProvider(): array
    {
        return [
            'missing_name' => [
                [
                    'description' => 'A valid description', 
                ],
                ['name']
            ],
            'name_too_long' => [
                [
                    'name' => str_repeat('a', 256), 
                    'description' => 'A valid description', 
                ],
                ['name']
            ],
            'name_must_be_string' => [
                [
                    'name' => 234345, 
                    'description' => 'A valid string name', 
                ],
                ['name']
            ],
        ];
    }
}
