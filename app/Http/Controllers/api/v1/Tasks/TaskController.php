<?php

namespace App\Http\Controllers\api\v1\Tasks;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Resources\api\v1\Tasks\TaskResource;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TaskController extends ApiController
{
    public function index(Request $request)
    {
        $tasks = Task::query()
            ->select([
                'id',
                'title',
                'description',
                'status',
                'priority_level',
                'project_id'
            ])
            ->with([
                'media',
                'project:id,name', 
            ])
            ->filterByStatus($request->status)
            ->filterByPriorityLevel($request->priority_level)
            ->filterByProjectId($request->project_id)
            ->filterByAssigneeId($request->assigneeId)
            ->filterBySearch($request->search)
            ->paginate(10);

        return TaskResource::collection($tasks);
    }
}
