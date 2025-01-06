<?php

namespace App\Http\Controllers\api\v1\Tasks;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Requests\api\v1\Tasks\UpdateTaskAssignmentRequest;
use App\Http\Resources\api\v1\Tasks\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class ProjectTaskAssignmentController extends ApiController
{
    public function __invoke(UpdateTaskAssignmentRequest $request, Task $task)
    {
        $task->update(['assigned_dev_id' => $request->assigned_dev_id]);

        return new TaskResource($task->load('project'));
    }
}
