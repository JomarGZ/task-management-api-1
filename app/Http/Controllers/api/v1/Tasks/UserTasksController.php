<?php

namespace App\Http\Controllers\api\v1\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Resources\api\v1\Tasks\TaskResource;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use function Laravel\Prompts\select;

class UserTasksController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user_id;
        $assigneeId = null;
        if ($request->has('user_id') && !empty($userId)) {
            $assigneeId = User::select('id')->findOrFail($userId)->id;
        }
        $tasks = Task::query()
            ->select(['id', 'title', 'status', 'priority_level', 'project_id'])
            ->with('project:id,name')
            ->assignedTo($assigneeId)
            ->filterBySearch($request->search)
            ->filterByStatus($request->status)
            ->filterByPriorityLevel($request->priority_level)
            ->paginate(5);

        return TaskResource::collection($tasks);
    }
}
