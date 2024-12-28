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

class StoreTest extends TestCase
{
   use LazilyRefreshDatabase;

   private User $admin;
   private User $member;
   private Tenant $tenant;

   protected function setUp(): void
   {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->admin = User::factory()->recycle($this->tenant)->create();
        $this->member = User::factory()->recycle($this->tenant)->create(['role' => Role::MEMBER->value]);

        Sanctum::actingAs($this->admin);
   }

   // it requires authentication
   public function test_it_requires_authentication_to_create_a_team()
   {
        $this->refreshApplication();
        $response = $this->postJson('api/v1/teams', [
            'name' => 'test name',
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
   }
   // it can create a team

   public function test_it_can_create_a_team()
   {
        $response = $this->postJson('api/v1/teams', [
            'name' => 'test name',
        ]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => ['id', 'name']
        ]);
   }
   // only allow admin to create a team

   public function test_only_allow_admin_to_create_a_team()
   {
        Sanctum::actingAs($this->member);
        $response = $this->postJson('api/v1/teams', [
            'name' => 'test name',
        ]);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
   }

   public function test_team_should_be_belongs_to_the_tenant_of_the_admin()
   {
        $this->postJson('api/v1/teams', [
            'name' => 'test name',
        ]);

        $newlyCreatedTeam = Team::where('name', 'test name')->first();
        $this->assertNotNull($newlyCreatedTeam);
        $this->assertEquals($this->admin->tenant_id, $newlyCreatedTeam->tenant_id);
   }
   
   #[Test]
   #[DataProvider('validationDataProvider')]
   public function test_team_validation_rules(array $data, array $expectedErrors)
   {
        $response = $this->postJson('api/v1/teams', $data);

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
