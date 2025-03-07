<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'team_id' => Team::factory(),
            'project_manager' => User::factory(),
            'name' => fake()->sentence(),
            'description' => fake()->realText()
        ];
    }
}
