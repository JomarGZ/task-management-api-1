<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\Team;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TeamUser>
 */
class TeamUserFactory extends Factory
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
            'member_id' => User::factory(),
            'team_id' => Team::factory(),
            'role' => fake()->randomElement([Role::MEMBER, Role::TEAM_LEAD])->value
        ];
    }
}
