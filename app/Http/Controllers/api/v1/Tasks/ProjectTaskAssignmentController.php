<?php

namespace App\Http\Controllers\api\v1\Tasks;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Requests\api\v1\Tasks\UpdateTaskAssignmentRequest;
use App\Http\Resources\api\v1\Tasks\TaskResource;
use App\Models\Task;

class ProjectTaskAssignmentController extends ApiController
{
    /**
     * Update the assigned developer and QA for the task.
     * 
     * @group Task Management
     * @param \App\Http\Requests\api\v1\Tasks\UpdateTaskAssignmentRequest $request
     * @queryParams assigned_dev_id int The ID of the developer to assign the task to. Example: 1
     * @queryParams assigned_qa_id int The ID of the QA to assign the task to. Example: 2 
     * @response 200 {"data": {
        "id": 18,
        "title": "new taskdasdadasdsadsasdaddadasdasddas",
        "description": "this is description",
        "priority_level": "high",
        "status": "pending",
        "deadline_at": null,
        "started_at": null,
        "completed_at": null,
        "project": {
            "id": 2,
            "name": "update project",
            "description": "description"
        },
        "assigned_dev": {
            "id": 1,
            "name": "John Doe",
            "email": "
        },
        "assigned_qa": {
            "id": 2,
            "name": "Jane Doe",
            "email": "
        },   
    }}
     */
    public function update(UpdateTaskAssignmentRequest $request, Task $task)
    {
        if ($request->has('assigned_dev_id') || $request->assigned_dev_id === null) {
            $task->assigned_dev_id = $request->assigned_dev_id;
        }
        if ($request->has('assigned_qa_id') || $request->assigned_qa_id === null) {
            $task->assigned_qa_id = $request->assigned_qa_id;
        }
        $task->save();

        return new TaskResource(
            $task->load([
                'project:id,name,description',
                'assignedDev:id,name,email',
                'assignedQA:id,name,email'
                ])
        );
    }
}
