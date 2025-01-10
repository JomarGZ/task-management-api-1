<?php

namespace App\Http\Controllers\api\v1\Tasks;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Requests\api\v1\Tasks\UpdateTaskAssignmentRequest;
use App\Http\Resources\api\v1\Tasks\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class ProjectTaskAssignmentController extends ApiController
{
    public function update(UpdateTaskAssignmentRequest $request, Task $task)
    {
        if ($request->has('assigned_dev_id') || $request->assigned_dev_id === null) {
            $task->assigned_dev_id = $request->assigned_dev_id;
        }
        if ($request->has('assigned_qa_id') || $request->assigned_qa_id === null) {
            $task->assigned_qa_id = $request->assigned_qa_id;
        }
        $task->save();

        return new TaskResource($task->load('project'));
    }
}
