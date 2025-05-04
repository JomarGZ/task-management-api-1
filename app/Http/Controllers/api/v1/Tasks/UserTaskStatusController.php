<?php

namespace App\Http\Controllers\api\v1\Tasks;

use App\Enums\Enums\Statuses;
use App\Http\Controllers\api\v1\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserTaskStatusController extends ApiController
{
    public function index()
    {
       $user = auth()->user();
       $lastWeek = now()->subWeek();

       $assignedTasks = Task::assignedTo($user->id);

       $lastWeekTasks = fn(Builder $query) => $query->where('updated_at', '>=', $lastWeek);

       return $this->success('Retrieved assigned tasks counts successfully',[
            'total' => $assignedTasks->count(),
            'completed' => $assignedTasks->clone()->where('status', Statuses::COMPLETED)->count(),
            'in_progress' => $assignedTasks->clone()->where('status', Statuses::IN_PROGRESS)->count(),
            'last_week' => [
                'total' => $assignedTasks->clone()->where($lastWeekTasks)->count(),
                'completed' => $assignedTasks->clone()->where($lastWeekTasks)->where('status', Statuses::COMPLETED)->count(),
                'in_progress' => $assignedTasks->clone()->where($lastWeekTasks)->where('status', Statuses::IN_PROGRESS)->count()
            ]
       ]);
    }
}
