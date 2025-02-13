<?php

namespace App\Http\Controllers\api\v1\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Resources\api\v1\Tasks\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class UserUpcomingTaskDeadlineController extends Controller
{
    public function index() 
    {
        $upcomingTasksDeadline = Task::query()
            ->select([
                'tasks.deadline_at',
                'tasks.title',
                'tasks.id',
            ])
            ->upcomingDeadlines()
            ->assignedTo()
            ->orderBy('deadline_at')
            ->get();
        return TaskResource::collection($upcomingTasksDeadline);
    }
}
