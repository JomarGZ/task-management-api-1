<?php

namespace App\Http\Controllers\api\v1\Tasks;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Requests\api\v1\Tasks\UpdateTaskAssignmentRequest;
use App\Http\Resources\api\v1\Tasks\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProjectTaskDevAssignmentController extends ApiController
{
    /**
     * assign or reassign a developer to a task
     * 
     * @group Task Management
     * @param \App\Http\Requests\api\v1\Tasks\UpdateTaskAssignmentRequest $request
     * @queryParams assigned_dev_id int The ID of the developer to assign the task to. Example: 1
     * @queryParams assigned_qa_id int The ID of the QA to assign the task to. Example: 2 
     */
    public function store(UpdateTaskAssignmentRequest $request, Task $task)
    {
        if ($request->has('assigned_dev_id')) {
            $task->assigned_dev_id = $request->assigned_dev_id;
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

    /**
     * unassign a developer from a task
     * 
     * @group Task Management
     * @param \App\Models\Task $task
     */
    public function destroy(Task $task)
    {
        if (!is_null($task->assigned_dev_id)) {
            $task->assigned_dev_id = null;
            $task->save();
        } else {
            throw ValidationException::withMessages([
                'assigned_dev_id' => ['The task is not assigned to any developer.']
            ]);
        }
        return new TaskResource(
            $task->load([
                'project:id,name,description',
                'assignedDev:id,name,email',
                'assignedQA:id,name,email'
                ])
        );
    }
}
