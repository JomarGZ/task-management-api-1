<?php

namespace App\Http\Controllers\api\v1\Tasks;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\Assignments\DestroyAssignmentRequest;
use App\Http\Requests\api\v1\Assignments\StoreAssignmentRequest;
use App\Models\Task;
use App\Services\V1\TaskAssignmentService;
use Illuminate\Http\Request;

class TaskAssignmentController extends ApiController
{
    public function store(Task $task, StoreAssignmentRequest $request)
    {
        if ($request->has('assignees')) {
            $assignmentService = new TaskAssignmentService($task);
            $assignees = $assignmentService->prepareAssignees($request->assignees);
            $assignmentService->assignToTask($assignees)->notifyAssignees();
        }
        $task->load('assignedUsers');
        return $this->success(
            'Task assigned successfully',
            $task
        );
    }

    public function update(Task $task, DestroyAssignmentRequest $request)
    {
        if ($request->has('assignees')) {
            $task->assignedUsers()->detach($request->assignees);
        }
        $task->load('assignedUsers');
        return $this->success(
            'Task unassigned successfully',
            $task
        );
    }
}
