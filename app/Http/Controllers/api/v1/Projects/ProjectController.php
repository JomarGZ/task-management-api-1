<?php

namespace App\Http\Controllers\api\v1\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\Projects\FilteringProjectRequest;
use App\Http\Requests\api\v1\Projects\StoreProjectRequest;
use App\Http\Resources\api\v1\Projects\ProjectResource;
use App\Models\Project;
use App\Utilities\ApiResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(FilteringProjectRequest $request)
    {
        $projects = Project::query()
            ->select(['id', 'team_id', 'name', 'description'])
            ->with('teamAssignee:id,name')
            ->search($request->query('search'))
            ->orderBy(
                $request->query('column', 'created_at'),
                $request->query('direction', 'desc')
            )
            ->paginate(5);
        
        return ApiResponse::success(
            ProjectResource::collection($projects)->response()->getData(true),
            'Projects retrieved successfully'
        );
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $project = Project::create($request->validated());
        return ApiResponse::success(
            ProjectResource::make($project),
            'Project created successfully',
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        Gate::authorize('view', $project);
        return ApiResponse::success(
            ProjectResource::make($project),
            'Project retrieved successfully'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProjectRequest $request, Project $project)
    {
        $project->update($request->validated());

        return ApiResponse::success(
            ProjectResource::make($project->load('teamAssignee')),
            'Project updated successfully',
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        Gate::authorize('delete', $project);
        $project->delete();
        return response()->noContent();
    }
}
