<?php

namespace App\Http\Controllers\api\v1\Tasks;

use App\Enums\Enums\Statuses;
use App\Http\Controllers\api\v1\ApiController;
use App\Http\Requests\api\v1\Tasks\UpdateTaskStatusRequest;
use App\Http\Resources\api\v1\Tasks\TaskResource;
use App\Models\Task;
use Illuminate\Support\Facades\Gate;

class TaskStatusController extends ApiController
{

    public function update(UpdateTaskStatusRequest $request, Task $task)
    {
        $task->update(['status' => $request->status]);

          return new TaskResource($task->fresh()->load([
            'project:id,name,description,created_at',
            'users:id,name' 
        ]));
    }
}
