<?php

namespace App\Http\Controllers\api\v1\Projects;

use App\Enums\Enums\Statuses;
use App\Http\Controllers\api\v1\ApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectStatusController extends ApiController
{
    public function index()
    {
        return $this->success(
            'Statuses retrieved successfully',
            Statuses::cases()
        );
    }
}
