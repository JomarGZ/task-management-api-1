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
    public function index(User $user, Request $request)
    {

       $tasks = $user->tasks()
            ->select([
                'tasks.id',
                'tasks.title', 
                'tasks.description',
                'tasks.priority_level',
                'tasks.status',
                'tasks.deadline_at',
                'tasks.started_at',
                'tasks.completed_at',
                'tasks.category',
                'tasks.created_at'
            ])
           ->with([
            'users:id,name',
            'users.media',
            'project:id' 
           ])
            ->filterBySearch($request->search)
            ->filterByStatus($request->status)
            ->filterByAssigneeId($request->assigneeId)
            ->latest()
            ->filterByPriorityLevel($request->priority_level)
            ->paginate(5);
        
        return TaskResource::collection($tasks);
    }
}
