<?php

namespace App\Http\Controllers\api\v1\Dashboard;

use App\Enums\Enums\Statuses;
use App\Http\Controllers\Controller;
use App\Http\Resources\api\v1\dashboard\visualization\tasks\TaskDistributionResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskDistributionController extends Controller
{
    public function index()
    {
        $query = Task::query();
        $completedCount = (clone $query)->whereIn('status', Statuses::getCompletedStatuses())->count();
        $inProgressCount = (clone $query)->whereIn('status', Statuses::getInProgressStatuses())->count();
        $toDoCount = (clone $query)->whereIn('status', Statuses::getToDoStatuses())->count();
        $exceptionCount = (clone $query)->whereIn('status', Statuses::getExceptionalStatuses())->count();

        $statusCounts = [
            'completed' => $completedCount,
            'in_progress' => $inProgressCount,
            'to_do' => $toDoCount,
            'exception' => $exceptionCount,
        ];
        return TaskDistributionResource::make($statusCounts)
            ->additional(['message' => 'Task distribution retrieved successfully']);
    }
}
