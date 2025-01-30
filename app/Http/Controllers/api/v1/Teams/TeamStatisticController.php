<?php

namespace App\Http\Controllers\api\v1\Teams;

use App\Enums\Team as EnumsTeam;
use App\Http\Controllers\api\v1\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamStatisticController extends ApiController
{
    public function index(Team $team)
    {
        $roles = $team->roles->pluck('name')->toArray();

        $query = $team->members()->toBase();
        $query->selectRaw("count(case when users.tenant_id = ? then 1 end) as all_members_count" , [auth()->user()->tenant_id]); 

        foreach ($roles as $role) {
            $role = strtolower(str_replace(' ', '_', $role));
            $query->selectRaw("count(case when team_user.role = ? and users.tenant_id = ? then 1 end) as {$role}_count", 
            [$role, auth()->user()->tenant_id]);
        }
        $teamMemberCountsBaseOnRole = $query->first();

        return $this->success(
            'Team members roles count retrieved successfully',
            $teamMemberCountsBaseOnRole
        );
    }
}
