<?php

namespace App\Http\Controllers\api\v1\Projects;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Requests\api\v1\Assignments\ProjectTeamAssignmentRequest;
use App\Models\Project;
use App\Services\v1\ProjectAssignmentService;
class ProjectTeamController extends ApiController
{
    public function store(ProjectTeamAssignmentRequest $request, Project $project)
    {
        (new ProjectAssignmentService($project))
            ->assignedProjectMembers($request->assign_team_members)
            ->notifyAssignedMembers();

        return $this->success(
            'Project team members assigned successfully',
            $project->load('assignedTeamMembers:id,name,role')
        );
    }
}
