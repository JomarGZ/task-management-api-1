<?php

namespace App\Http\Controllers\api\v1\Tenants;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\api\v1\tenants\TenantMemberResource;
use App\Models\User;
use Illuminate\Http\Request;

class TenantMemberListController extends ApiController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        return $this->success(
            'Tenant members retrieved successfully',
            User::select('id', 'name')->get()
        );
    }
}
