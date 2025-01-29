<?php

namespace App\Http\Controllers\api\v1\Projects;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Requests\api\v1\Projects\StoreProjectRequest;
use App\Http\Resources\api\v1\Projects\ProjectResource;
use App\Models\Project;
use App\Services\v1\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectController extends ApiController
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }
    /**
     * List Projects
     *
     * Display a listing of the projects.
     *
     * @group Project Management
     * 
     * @queryParam column string The column to sort by. Allowed values: `name`, `description`, `created_at`. Example: column=name
     * @queryParam direction string The direction to sort by. Allowed values: `asc`, `desc`. Example: direction=asc
     * @queryParam search string Filter projects by name or description. This performs a partial match. Example: search=alpha
     */
    public function index(Request $request)
    {
        $column = $this->projectService
            ->getValidSortColumn(
                $request->query('column', 'created_at')
            );  
        $direction = $this->projectService
            ->getValidSortDirection(
                $request->query('direction', 'desc')
            ); 
    
        $projects = Project::query()
            ->select(['id', 'team_id', 'name', 'description', 'created_at'])
            ->with('teamAssignee:id,name')
            ->search($request->query('search'))
            ->orderBy($column, $direction)
            ->paginate(5);

        return ProjectResource::collection($projects);
    }

    /**
     * Create Project
     * 
     * Store a newly created project in storage.
     * @group Project Management
     * @response 201 {   "data": {
        "id": 4,
        "name": "new project",
        "description": "description"
    }}
     */
    public function store(StoreProjectRequest $request)
    {
        $project = Project::create($request->validated());

        return new ProjectResource($project); 
    }

    /**
     * Retrieve Project
     * 
     * Display the specified project.
     * @group Project Management
     * @response 200 {"data": {
        "id": 2,
        "name": "update project 4324",
        "description": "description",
        "team_assignee": {
            "id": 1,
            "name": "Enim a earum voluptate facilis cumque ex ut."
        }
    }}
     */
    public function show(Project $project)
    {
        Gate::authorize('view', $project);
     
        return new ProjectResource($project->load('teamAssignee:id,name'));
    }

    /**
     * Update Project
     * 
     * Update the specified project in storage.
     * @group Project Management
     * @response 200 { "data": {
        "id": 2,
        "name": "update project",
        "description": "description",
        "team_assignee": {
            "id": 1,
            "name": "Enim a earum voluptate facilis cumque ex ut."
        }
    }}
     */
    public function update(StoreProjectRequest $request, Project $project)
    {
        $project->update($request->validated());

        return new ProjectResource($project->load('teamAssignee:id,name'));
    }

    /**
     * Delete Project
     * 
     * Remove the specified project from storage.
     * @group Project Management
     * @response 200 {}
     */
    public function destroy(Project $project)
    {
        Gate::authorize('delete', $project);
        $project->delete();

        return $this->ok('');
    }
}
