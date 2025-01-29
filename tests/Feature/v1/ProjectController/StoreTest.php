<?php

namespace Tests\Feature\v1\ProjectController;

use App\Enums\Role;
use App\Models\Project;
use App\Models\Team;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Team $team;
    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->recycle($this->tenant)->create();
        $this->team = Team::factory()->create();
        
        // Authenticate user
    }

    public function test_it_requires_authentication_to_create_a_project()
    {
        
        $response = $this->postJson('api/v1/projects', [
            'name' => 'add project',
            'description' => 'description'
        ]);

        $response->assertUnauthorized();

    }
    public function test_can_create_project(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('api/v1/projects', [
            'name' => 'add project',
            'description' => 'description'
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => ['id', 'name', 'description']]);
    }

    public function test_project_should_belongs_to_the_authenticated_user_who_created_the_project()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('api/v1/projects', [
            'name' => 'add project',
            'description' => 'description'
        ]);
        $newLyCreatedProject = Project::select('id','tenant_id')
            ->where('id',(int) $response->json('data.id'))
            ->first();
        $this->assertEquals($this->user->tenant_id, $newLyCreatedProject->tenant_id);
    }

    public function test_only_allows_admin_tenant_to_create_a_project()
    {
        $notAdmin = User::factory()->create(['role' => Role::MEMBER]);

        Sanctum::actingAs($notAdmin);

        $response = $this->postJson('api/v1/projects', [
            'name' => 'add project',
            'description' => 'description'
        ]);

        $response->assertUnauthorized();
    }

    #[Test]
    #[DataProvider('validationDataProvider')]
    public function test_project_validation_rules(array $data, array $expectedErrors): void
    {
        if (isset($data['team_id']) && $data['team_id'] === 1) {
            $data['team_id'] = $this->team->id;
        }
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/v1/projects', $data);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors($expectedErrors);
    }

    public static function validationDataProvider(): array
    {
        return [
            'missing_name' => [
                [
                    'description' => 'A valid description', 
                    'team_id' => 1
                ],
                ['name']
            ],
            'missing_description' => [
                [
                    'name' => 'Project Name', 
                    'team_id' => 1
                ],
                ['description']
            ],
            'name_too_long' => [
                [
                    'name' => str_repeat('a', 256), 
                    'description' => 'A valid description', 
                    'team_id' => 1
                ],
                ['name']
            ],
            'description_too_long' => [
                [
                    'name' => 'Project Name', 
                    'description' => str_repeat('a', 501), 
                    'team_id' => 1
                ],
                ['description']
            ],
            'invalid_team_id' => [
                [
                    'name' => 'Project Name', 
                    'description' => 'A valid description', 
                    'team_id' => 9999
                ],
                ['team_id']
            ],
        ];
    }

}
