<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Enums\Role;
use App\Models\Project;
use App\Models\Task;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $tenant = Tenant::factory()
            ->create();
        Project::factory()
            ->recycle($tenant)
            ->has(
                Task::factory()
                ->recycle($tenant)
                ->count(5)
            )
            ->create();

        User::factory()
            ->recycle($tenant)
            ->create([
            'role' => Role::Admin->value,
            'name' => 'jomar',
            'email' => 'jomar@gmail.com'
        ]);
        User::factory(20)->recycle($tenant)->create(['role' => Role::Admin->value]);


    }
}
