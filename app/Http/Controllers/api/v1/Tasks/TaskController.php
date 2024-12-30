<?php

namespace App\Http\Controllers\api\v1\Tasks;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Requests\api\v1\Tasks\StoreTaskRequest;
use App\Http\Resources\api\v1\Tasks\TaskResource;
use App\Models\Project;
use App\Models\Task;
use App\Services\V1\TaskService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class TaskController extends ApiController
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }
    /**
     * List Tasks
     * 
     * Retrieve a paginated list of tasks associated with a specific project.
     * 
     * @group Task Management
     * 
     * @urlParam project int required The ID of the project whose tasks are to be retrieved. Example: 1
     * 
     * @queryParam search string Filter tasks by a search term in their title or description. This performs a partial match. Example: search=homepage
     * @queryParam status string Filter tasks by their status. Example: status=completed
     * @queryParam priority_level string Filter tasks by their priority level. Example: priority_level=high
     * @queryParam column string The column to sort tasks by. Allowed values: `title`, `description`, `priority_level`, `status`, `deadline_at`, `started_at`, `completed_at`, `created_at`. Defaults to `created_at`. Example: column=title
     * @queryParam direction string The direction to sort tasks by. Allowed values: `asc`, `desc`. Defaults to `desc`. Example: direction=asc
     * @queryParam page int The page number of the paginated results. Example: page=2
     * 
     */
    public function index(Request $request, Project $project)
    {
        $column = $this->taskService
            ->getValidSortColumn(
                $request->query('column', 'created_at')
            );
        $direction = $this->taskService
            ->getValidSortDirection(
                $request->query('direction', 'desc')
            );

        $tasks = $project->tasks()
            ->select([
                'id',
                'title', 
                'description',
                'priority_level',
                'status',
                'deadline_at',
                'started_at',
                'completed_at',
                'created_at'
            ])
            ->search($request->query('search'))
            ->filterByStatus($request->query('status'))
            ->filterByPriorityLevel($request->query('priority_level'))
            ->orderBy(
                $column, 
                $direction
            )
            ->paginate(5);
        
        return TaskResource::collection($tasks);
    }

    /**
     * Create Task
     * 
     * Store a newly created task in storage.
     * @group Task Management
     * @response 201 {"data": {
        "id": 18,
        "title": "new taskdasdadasdsadsasdaddadasdasddas",
        "description": "this is description",
        "priority_level": null,
        "status": null,
        "deadline_at": null,
        "started_at": null,
        "completed_at": null,
        "project": {
            "id": 2,
            "name": "update project",
            "description": "description"
        }
    }}
     */
    public function store(StoreTaskRequest $request, Project $project)
    {
        $task = $project->tasks()->create($request->validated());

        return new TaskResource($task->load('project'));
    }

    /**
     * Retrieve Task
     * 
     * Display the specified task.
     * @group Task Management
     * 
     */
    public function show(Task $task)
    {
        Gate::authorize('view', $task);

       return new TaskResource($task->load(['project:id,name,description', 'assignee:id,name']));
    }

    /**
     * Update Task
     * 
     * Update the specified task in storage.
     * @group Task Management
     * @response 200 {"data": {
        "id": 15,
        "title": "update title 1",
        "description": "description",
        "priority_level": "urgent",
        "status": "completed",
        "deadline_at": null,
        "started_at": "2024-12-22",
        "completed_at": "2024-12-22",
        "project": {
            "id": 2,
            "name": "update project",
            "description": "description"
        }
    }}
     */
    public function update(StoreTaskRequest $request, Task $task)
    {
        $task->update($request->validated());

       return new TaskResource($task->load('project'));
    }

    /**
     * Delete Task
     * 
     * Remove the specified task from storage.
     * @group Task Management
     */
    public function destroy(Task $task)
    {
        Gate::authorize('delete', $task);
        $task->delete();
        return $this->ok('');
    }
}
