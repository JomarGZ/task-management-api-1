<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Enums\Team as EnumsTeam;
use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleTeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teamsRoles = [
            EnumsTeam::WEB_DEVELOPMENT->value => [
                Role::JUNIOR_BACKEND_WEB->value,
                Role::SENIOR_BACKEND_WEB->value,
                Role::JUNIOR_FRONTEND_WEB->value,
                Role::SENIOR_FRONTEND_WEB->value,
            ],
            EnumsTeam::MOBILE_DEVELOPMENT->value => [
                Role::JUNIOR_ANDROID->value,
                Role::SENIOR_ANDROID->value,
                Role::JUNIOR_IOS->value,
                Role::SENIOR_IOS->value,
            ],
            EnumsTeam::QUALITY_ASSURANCE->value => [Role::QA_TESTER->value],
            EnumsTeam::DESIGN->value  => [Role::UI_UX_DESIGNER->value] 
        ];
      
        foreach ($teamsRoles as $team => $roles) {
            $team = Team::updateOrCreate(['name' => $team]);
            $team->roles()->upsert(
                array_map(fn($role) => ['name' => $role], $roles),
                ['name']
            );
        }
    }
}
