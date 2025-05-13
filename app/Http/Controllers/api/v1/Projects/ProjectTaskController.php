<?php

namespace App\Http\Controllers\api\v1\Projects;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Requests\api\v1\Tasks\StoreTaskRequest;
use App\Http\Resources\api\v1\Tasks\TaskResource;
use App\Models\Project;
use App\Models\Task;
use App\Services\v1\TaskService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class ProjectTaskController extends ApiController
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
            ->filterBySearch($request->search)
            ->filterByStatus($request->status)
            ->latest()
            ->filterByPriorityLevel($request->priority_level)
            ->paginate(5);
        
        return TaskResource::collection($tasks);
    }

    /**
     * Create Task
     * 
     * Store a newly created task in storage.
     * @group Task Management
     * @response 201 {    "data": {
        "id": 43,
        "title": "new taskdasdadasdsadsasdaddadasdasddas",
        "description": "this is descriptionp",
        "priority_level": null,
        "status": null,
        "deadline_at": null,
        "started_at": null,
        "completed_at": null,
        "created_at": "2025-01-12T05:03:50.000000Z",
        "photo_attachments": [
            {
                "id": 24,
                "url": "http://task-management-api-1.test/media/24/image-car.jpg",
                "name": "image-car.jpg",
                "size": 1834859,
                "mime_type": "image/jpeg"
            },
            {
                "id": 25,
                "url": "http://task-management-api-1.test/media/25/image-motorbike.jpg",
                "name": "image-motorbike.jpg",
                "size": 312287,
                "mime_type": "image/jpeg"
            }
        ],
        "project": {
            "id": 1,
            "name": "Odio et impedit error et veniam quam.",
            "description": "I'll be jury,\" Said cunning old Fury: \"I'll try the first to speak. 'What size do you know about it, you know.' 'Not at all,' said Alice: 'besides, that's not a moment like a serpent. She had.",
            "created_at": "2025-01-10T08:29:13.000000Z"
        }
    }}
     */
    public function store(StoreTaskRequest $request, Project $project)
    {
        $task = $project->tasks()->create($request->validated());

        if ($request->hasFile('photo_attachments')) {
            foreach($request->file('photo_attachments') as $attachment) {
                $task->addMedia($attachment)->preservingOriginal()->toMediaCollection('task_attachments');
            }
        }

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

        return new TaskResource($task->load([
            'project:id,name,description,created_at', 
        ]));
        
    }

    /**
     * Update Task
     * 
     * Update the specified task in storage.
     * @group Task Management
     * @response 200 {    "data": {
        "id": 43,
        "title": "new taskdasdadasdsadsasdaddadasdasddas",
        "description": "this is descriptionp",
        "priority_level": null,
        "status": null,
        "deadline_at": null,
        "started_at": null,
        "completed_at": null,
        "created_at": "2025-01-12T05:03:50.000000Z",
        "photo_attachments": [
            {
                "id": 24,
                "url": "http://task-management-api-1.test/media/24/image-car.jpg",
                "name": "image-car.jpg",
                "size": 1834859,
                "mime_type": "image/jpeg"
            },
            {
                "id": 25,
                "url": "http://task-management-api-1.test/media/25/image-motorbike.jpg",
                "name": "image-motorbike.jpg",
                "size": 312287,
                "mime_type": "image/jpeg"
            }
        ],
        "project": {
            "id": 1,
            "name": "Odio et impedit error et veniam quam.",
            "description": "I'll be jury,\" Said cunning old Fury: \"I'll try the first to speak. 'What size do you know about it, you know.' 'Not at all,' said Alice: 'besides, that's not a moment like a serpent. She had.",
            "created_at": "2025-01-10T08:29:13.000000Z"
        }
    }}
     */
    public function update(StoreTaskRequest $request, Task $task)
    {
        $task->update($request->validated());

        return new TaskResource($task->load([
            'project:id,name,description,created_at',
            'comments:id,commentable_id,commentable_type,author_id,content,created_at,updated_at',
            'comments.author:id,name,email,role',
        ]));
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
