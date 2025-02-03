<?php

namespace App\Http\Controllers\api\v1\Projects;

use App\Enums\Enums\PriorityLevel;
use App\Http\Controllers\api\v1\ApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectPriorityController extends ApiController
{
    public function __invoke()
    {
        return $this->success(
            'Priority level retrieved successfully',
            PriorityLevel::cases()
        );
    }
}
