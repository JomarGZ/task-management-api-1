<?php

namespace App\Http\Controllers\api\v1\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\Tasks\StoreTaskRequest;
use App\Http\Resources\api\v1\Projects\ProjectResource;
use App\Http\Resources\api\v1\Tasks\TaskResource;
use App\Models\Project;
use App\Models\Task;
use App\Utilities\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Project $project)
    {

        $tasks = $project->tasks()
            ->select([
                'id',
                'title', 
                'description',
                'priority_level',
                'status',
                'deadline_at',
                'started_at',
                'completed_at'
            ])
            ->search($request->query('search'))
            ->filterByStatus($request->query('status'))
            ->filterByPriorityLevel($request->query('priority_level'))
            ->orderBy(
                $request->query('column', 'created_at'), 
                $request->query('direction', 'desc')
            )
            ->paginate(5);
        
            return ApiResponse::success([
                'project' => (new ProjectResource($project))->resolve(),
                'tasks' => TaskResource::collection($tasks)->response()->getData(true)
            ], 'Tasks retrieved successfully');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request, Project $project)
    {
        $task = $project->tasks()->create($request->validated());

        return ApiResponse::success(
            TaskResource::make($task),
            'Task created successfully',
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        Gate::authorize('show', $task);

        return ApiResponse::success(
            TaskResource::make($task),
            'Task retrieved successfully'  
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreTaskRequest $request, Task $task)
    {
        $task->update($request->validated());

        return ApiResponse::success(
            TaskResource::make($task),
            'Task updated successfully',
            Response::HTTP_ACCEPTED
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        Gate::authorize('delete', $task);
        $task->delete();
        return response()->noContent();
    }
}
