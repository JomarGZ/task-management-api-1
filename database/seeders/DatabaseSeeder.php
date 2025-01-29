<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Enums\Role;
use App\Models\Project;
use App\Models\Task;
use App\Models\Team;
use App\Models\TeamUser;
use Database\Seeders\v1\TeamSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $tenant = Tenant::factory()
            ->create();

        User::factory()
            ->recycle($tenant)
            ->create([
            'role' => Role::ADMIN->value,
            'name' => 'jomar',
            'email' => 'jomar@gmail.com'
        ]);
        $members = User::factory(9)->recycle($tenant)->create(['role' => Role::MEMBER->value]);

        $team = Team::factory()->create();
        $members->each(function($member) use ($team, $tenant) {
            TeamUser::factory()->recycle($team)->recycle($member)->recycle($tenant)->create([
                'role' => Role::MEMBER->value
            ]);
        });
        $teamLead = User::factory()->recycle($tenant)->create(['role' => Role::TEAM_LEAD->value]);

        TeamUser::factory()->recycle($team)->recycle($tenant)->recycle($teamLead)->create();

        Project::factory()
            ->recycle($tenant)
            ->recycle($team)
            ->has(
                Task::factory()
                ->recycle($tenant)
                ->recycle($members)

                ->count(10)
            )
            ->create();

    }
}
