<?php

namespace App\Http\Controllers\api\v1\tasks;

use App\Http\Controllers\Controller;
use App\Http\Resources\api\v1\Tasks\TaskResource;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PriorityTimelineController extends Controller
{
    public function index()
    { 
        $tasks = Task::query()
            ->select([
                'tasks.id',
                'tasks.title',
                'tasks.deadline_at',
                'tasks.project_id'
            ])
            ->with('project:id,name')
            ->whereNotNull('deadline_at')
            ->assignedTo()
            ->whereDate('deadline_at', '>=', now()->startOfDay())
            ->whereDate('deadline_at', '<=', now()->addDay()->endOfDay())
            ->get()
            ->groupBy(function($task) {
                return $task->deadline_at && Carbon::parse($task->deadline_at)->isToday() 
                    ? 'today' 
                    : 'tomorrow';
            });
        return [
            'today' => TaskResource::collection($tasks->get('today', [])),
            'tomorrow' => TaskResource::collection($tasks->get('tomorrow', []))
        ];
    }
}
