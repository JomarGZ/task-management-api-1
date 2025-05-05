<?php

namespace App\Http\Controllers\api\v1\Tasks;

use App\Enums\Enums\Statuses;
use App\Http\Controllers\api\v1\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserTaskStatusController extends ApiController
{
    public function index()
    {
        $user = auth()->user();
        $weeklyData = [];
        $now = Carbon::now();
        for ($i = 0; $i < 5; $i++) {
            $weekStart = $now->copy()->subWeeks($i)->startOfWeek();
            $weekEnd = $now->copy()->subWeeks($i)->endOfWeek();

            $query = Task::assignedTo($user->id)->whereBetween('updated_at', [$weekStart, $weekEnd]);

            $weeklyData[] = [
                'week_start' => $weekStart->toDateString(),
                'week_end' => $weekEnd->toDateString(),
                'week_label' => 'Week' . ($i + 1) . '(' . $weekStart->format('M d') . ' - ' . $weekEnd->format('M d') . ')',
                'total' => $query->count(),
                'completed' => $query->clone()->where('status', Statuses::COMPLETED)->count(),
                'in_progress' => $query->clone()->where('status', Statuses::IN_PROGRESS)->count(),
            ];
        }
        $assignedTasks = Task::assignedTo($user->id);

        return $this->success('Retrieved assigned tasks counts successfully',[
                'current_totals' => [
                    'total' => $assignedTasks->count(),
                    'completed' => $assignedTasks->clone()->where('status', Statuses::COMPLETED)->count(),
                    'in_progress' => $assignedTasks->clone()->where('status', Statuses::IN_PROGRESS)->count(),
                ],
                'weekly_data' => array_reverse($weeklyData),
        ]);
    }
}
