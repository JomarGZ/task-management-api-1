<?php

namespace App\Http\Controllers\api\v1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\api\v1\Tasks\TaskResource;
use App\Models\Task;
use App\Services\v1\TaskStatisticService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaskCompletionTrendController extends Controller
{
    public function index(Request $request)
    {
        $timeframe = $request->input('timeframe', 'daily');
        $tasksCompletionCount = [];
        $query = Task::query();
        $taskStatsService = new TaskStatisticService(); 
        for($i = 0; $i <= 7; $i++) {
        
            $timeRange = $taskStatsService->setTimeFrame($timeframe, $i);

            $taskCompletedCount = $query->clone()->whereBetween('completed_at', [$timeRange['start_date'], $timeRange['end_date']])->count();
            $tasksTargetCount = $query->clone()->whereBetween('deadline_at', [$timeRange['start_date'], $timeRange['end_date']])->count();
            $tasksCompletionCount[] = [
                'target' => $tasksTargetCount,
                'completed' => $taskCompletedCount,
                'date' => $timeRange
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $tasksCompletionCount,
            'message' => 'Tasks Completion trends retrieved successfully'
        ]);
    }

 
}
