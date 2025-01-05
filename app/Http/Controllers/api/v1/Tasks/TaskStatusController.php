<?php

namespace App\Http\Controllers\api\v1\Tasks;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Requests\api\v1\Tasks\UpdateTaskStatusRequest;
use App\Http\Resources\api\v1\Tasks\TaskResource;
use App\Models\Task;
use Illuminate\Support\Facades\Gate;

class TaskStatusController extends ApiController
{
    /**
     * Update Task Status
     * 
     * Update the status of the specified task.
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
    public function update(UpdateTaskStatusRequest $request, Task $task)
    {
        Gate::authorize('update', $task);
        $task->update(['status' => $request->status]);

        return new TaskResource($task->load('project'));
    }
}