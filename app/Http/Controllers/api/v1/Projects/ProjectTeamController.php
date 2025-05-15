<?php

namespace App\Http\Controllers\api\v1\Projects;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Requests\api\v1\Assignments\ProjectTeamAssignmentRequest;
use App\Http\Requests\api\v1\Assignments\ProjectUnassignmentRequest;
use App\Http\Resources\api\v1\Projects\ProjectResource;
use App\Http\Resources\api\v1\tenants\TenantMemberResource;
use App\Models\Project;
use App\Services\v1\ProjectAssignmentService;
use Illuminate\Http\Request;

class ProjectTeamController extends ApiController
{
    public function index(Project $project)
    {
        return TenantMemberResource::collection($project->assignedTeamMembers()->with('media')->get())->additional([
            'message' => 'Retrieved team members successfully'
        ]);
    }
    public function store(ProjectTeamAssignmentRequest $request, Project $project)
    {
        (new ProjectAssignmentService($project))
            ->assignedProjectMembers($request->assign_team_members)
            ->notifyAssignedMembers();

        return new ProjectResource($project->load('assignedTeamMembers:id,name,role'))->additional([
            'message' => 'Project assigned successfully'
        ]);
    }

    public function destroy(Project $project, ProjectUnassignmentRequest $request)
    {
        $removeAssignedMemberId = $request->assignedMemberId;
        if (!empty($removeAssignedMemberId)){
            (new ProjectAssignmentService($project))
                ->removeAssignedMember($removeAssignedMemberId);
        }
        return new ProjectResource($project->load('assignedTeamMembers:id,name,role'))->additional([
            'message' => 'Remove assigned member successfully'
        ]);
    }
}
