<?php

namespace App\Http\Controllers\api\v1\Tasks;

use App\Enums\Enums\PriorityLevel;
use App\Enums\Enums\Statuses;
use App\Http\Controllers\api\v1\ApiController;
use App\Utilities\ApiResponse;

class TaskPriorityLevelsAndStatusesController extends ApiController
{
    /**
     * List of statuses and priority levels
     * @group Task Management
     * 
     */
    public function __invoke()
    {
        return $this->success(
            'Statuses and priority levels retrieved successfully',
            [
                'statuses' => Statuses::cases(),
                'priorityLevels' => PriorityLevel::cases(),
            ]
            );
    }
}
