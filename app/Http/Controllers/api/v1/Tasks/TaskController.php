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
                'project:id,name', 
                'assignedUsers:id,name'
            ])
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->priority, function ($query) use ($request) {
                $query->where('priority', $request->priority);
            })
            ->when($request->project_id, function ($query)  use ($request) {
                $query->where('project_id', $request->project_id);
            })
            ->when($request->assignee, function ($query) use($request) {
                $query->whereHas('assignedUsers', function (Builder $query) use ($request){
                    $query->where('users.id', $request->assignee);
                });
            })
            ->when($request->search, function ($query) use($request) {
                $query->whereAny([
                    'title',
                    'description'
                ], 'like', "%$request->search%");
            })
            ->paginate(10);

        return TaskResource::collection($tasks);
    }
}
