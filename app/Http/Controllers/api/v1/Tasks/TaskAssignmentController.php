<?php

namespace App\Http\Controllers\api\v1\Tasks;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Requests\api\v1\Assignments\DestroyAssignmentRequest;
use App\Http\Requests\api\v1\Assignments\StoreAssignmentRequest;
use App\Http\Resources\api\v1\Tasks\TaskResource;
use App\Models\Task;
use App\Services\v1\TaskAssignmentService;
use Illuminate\Http\Request;

class TaskAssignmentController extends ApiController
{
    public function store(Task $task, StoreAssignmentRequest $request)
    {
        if ($request->has('assigneeIds')) {
            $task->load('project.assignedTeamMembers');
            (new TaskAssignmentService($task))->assignToTask($request->assigneeIds)->notifyAssignees();
        }
        return new TaskResource($task->fresh()->load([
            'project:id,name,description,created_at',
            'users:id,name' 
        ]))->additional(['message' => 'Assigned task successfully']);
    }

    public function destroy(Task $task, DestroyAssignmentRequest $request)
    {
        (new TaskAssignmentService($task))->removeAssignedMember($request->assigneeId);

         return new TaskResource($task->fresh()->load([
            'project:id,name,description,created_at',
            'users:id,name' 
        ]))->additional(['message' => 'Remove assignee of task successfully']);
    }
}
