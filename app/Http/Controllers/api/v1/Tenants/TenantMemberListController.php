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
    public function __invoke(Request $request)
    {
        $users = User::query()
            ->select('id', 'name')
            ->when($request->query('filtered_out_member_ids'),function ($query) use ($request) {
                $query->whereNotIn('id', $request->query('filtered_out_member_ids'));
            })
            ->get();
        return $this->success(
            'Tenant members retrieved successfully',
            $users
        );
    }
}
