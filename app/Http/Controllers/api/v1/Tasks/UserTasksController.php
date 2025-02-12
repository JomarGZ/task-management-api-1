<?php

namespace App\Http\Controllers\api\v1\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Resources\api\v1\Tasks\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use function Laravel\Prompts\select;

class UserTasksController extends Controller
{
    public function index(Request $request)
    {
        $tasks = Task::query()
            ->select(['id', 'title', 'status', 'priority_level'])
            ->assignedTo()
            ->filterBySearch($request->search)
            ->filterByStatus($request->status)
            ->filterByPriorityLevel($request->priority_level)
            ->paginate(5);

        return TaskResource::collection($tasks);
    }
}
