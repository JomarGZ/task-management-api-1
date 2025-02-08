<?php

namespace App\Http\Controllers\api\v1\Tasks;

use App\Enums\Enums\PriorityLevel;
use App\Http\Controllers\api\v1\ApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaskPriorityController extends ApiController
{
    public function index()
    {
        return $this->success(
            'Priority levels retrieved successfully',
            PriorityLevel::cases()
        );
    }
}
