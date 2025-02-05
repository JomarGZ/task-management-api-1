<?php

namespace App\Http\Controllers\api\v1\Tasks;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\Assignments\StoreAssignmentRequest;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskAssignmentController extends ApiController
{
    public function store(Task $task, StoreAssignmentRequest $request)
    {
        if ($request->has('assignees')) {
            $assignees = array_fill_keys($request->assignees, ['tenant_id' => auth()->user()->tenant_id]);
            $task->assignedUsers()->syncWithoutDetaching($assignees);
        }
        $task->load('assignedUsers');
        return $this->success(
            'Task assigned successfully',
            $task
        );
    }
}
