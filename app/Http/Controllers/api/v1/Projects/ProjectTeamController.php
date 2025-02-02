<?php

namespace App\Http\Controllers\api\v1\Projects;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectTeamController extends ApiController
{
    public function store(Request $request, Project $project)
    {
        $project->assignedTeamMembers()->sync($request->assign_team_members);

        return $this->success(
            'Project team members assigned successfully',
            $project->load('assignedTeamMembers:id,name,role')
        );
    }
}
