<?php

namespace App\Http\Controllers\api\v1\Dashboard;

use App\Enums\Enums\Statuses;
use App\Http\Controllers\Controller;
use App\Http\Resources\api\v1\Tasks\TaskStatisticResource;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaskStatisticsController extends Controller
{
    public function index()
    {
        $weeklyData = [];
        $now = Carbon::now();
        for ($i = 0; $i < 5; $i++) {
            $weekStart = $now->copy()->subWeeks($i)->startOfWeek();
            $weekEnd = $now->copy()->subWeeks($i)->endOfWeek();

            $query = Task::select('id')->whereBetween('updated_at', [$weekStart, $weekEnd]);

            $weeklyData[] = [
                'week_start' => $weekStart->toDateString(),
                'week_end' => $weekEnd->toDateString(),
                'week_label' => 'Week' . ($i + 1) . '(' . $weekStart->format('M d') . ' - ' . $weekEnd->format('M d') . ')',
                'total' => $query->count(),
                'completed' => $query->clone()->where('status', Statuses::COMPLETED)->count(),
                'in_progress' => $query->clone()->where('status', Statuses::IN_PROGRESS)->count(),
            ];
        }
        $tasks = Task::query();
        $dataCounts = [
            'current_totals' => [
                'total' => $tasks->count(),
                'completed' => $tasks->clone()->where('status', Statuses::COMPLETED)->count(),
                'in_progress' => $tasks->clone()->where('status', Statuses::IN_PROGRESS)->count(),
            ],
            'weekly_data' => array_reverse($weeklyData),
        ];

        return TaskStatisticResource::make($dataCounts);
    }
}
