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
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use LazilyRefreshDatabase;
    private User $user;
    private Team $team;
    private Tenant $tenant;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->recycle($this->tenant)->create(['role' => Role::ADMIN->value]);
        $this->team = Team::factory()->recycle($this->tenant)->create();
        $this->project = Project::factory()->recycle($this->tenant)->create();

        Sanctum::actingAs($this->user);
    }
    public function test_it_require_authentication()
    {
        $this->refreshApplication();        
        $project = Project::factory()->create();
        $response = $this->putJson("api/v1/projects/$project", [
            'name' => 'updated project',
            'description' => 'description'
        ]);
        $response->assertUnauthorized();
    }
    public function test_only_admin_of_tenant_can_update_an_existing_project()
    {
        $tenant = $this->tenant;
        $member = User::withoutEvents(function() use ($tenant) {
            return User::factory()->recycle($tenant)->create(['role' => Role::MEMBER->value]);
        });

        Sanctum::actingAs($member);

        $response = $this->putJson("api/v1/projects/" . $this->project->id, [
            'name' => 'update project',
            'description' => 'description'
        ]);

        $response->assertUnauthorized();
    }
    // it allow the update if belongs to same tenant.
    public function test_allow_the_update_if_belongs_to_same_tenant()
    {
        
        $otherTenantProject = Project::factory()->create();

        $response = $this->putJson("api/v1/projects/" . $otherTenantProject, [
            'name' => 'update project',
            'description' => 'description'
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_can_allow_valid_update()
    {
        $response = $this->putJson("api/v1/projects/" . $this->project->id, [
            'name' => 'update project',
            'description' => 'description'
        ]);
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'team_assignee'
            ]
        ]);
    }
    // validation test

    #[Test]
    #[DataProvider('validationDataProvider')]
    public function test_project_validation_rules(array $data, array $expectedErrors): void
    {
        if (isset($data['team_id']) && $data['team_id'] === 1) {
            $data['team_id'] = $this->team->id;
        }

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
