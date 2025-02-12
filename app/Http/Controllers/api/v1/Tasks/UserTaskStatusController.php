<?php

namespace App\Http\Controllers\api\v1\Tasks;

use App\Enums\Enums\Statuses;
use App\Http\Controllers\api\v1\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class UserTaskStatusController extends ApiController
{
    public function index()
    {
        $statuses = [Statuses::IN_PROGRESS->value, Statuses::COMPLETED->value, Statuses::OVER_DUE->value];
        $selects = [];
        foreach ($statuses as $status) {
            $key = str_replace(' ', '_', $status);
            $selects[] = "COUNT(case when status = '{$status}' then 1 end) as {$key}";
        }
        $selects[] = "COUNT(*) as total_tasks";

        $tasksCounts = Task::query()->selectRaw(implode(', ', $selects))
            ->assignedTo()
            ->first();

        return $this->success(
            'Task Counts retrieved successfully',
            $tasksCounts
        );
    }
}
