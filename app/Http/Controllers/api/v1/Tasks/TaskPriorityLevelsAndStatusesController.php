<?php

namespace App\Http\Controllers\api\v1\Tasks;

use App\Enums\Enums\PriorityLevel;
use App\Enums\Enums\Statuses;
use App\Http\Controllers\Controller;
use App\Utilities\ApiResponse;
use Illuminate\Http\Request;

class TaskPriorityLevelsAndStatusesController extends Controller
{
    public function __invoke()
    {
        return ApiResponse::success(
            [
                'statuses' => Statuses::cases(),
                'priority_levels' => PriorityLevel::cases(),
            ],
            'The statuses and priority levels retrieved successfully',
            );
    }
}
