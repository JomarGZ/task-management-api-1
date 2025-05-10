<?php

namespace App\Http\Controllers\api\v1\Tenants;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\api\v1\tenants\TenantMemberResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
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
            ->with('media')
            ->when($request->query('position_id'), function (Builder $query, $positionId) {
                $query->whereHas('position', function (Builder $query) use ($positionId) {
                    $query->where('id', $positionId);
                });
            })
            ->when($request->query('filtered_out_member_ids'),function ($query) use ($request) {
                $query->whereNotIn('id', $request->query('filtered_out_member_ids'));
            })
            ->get();
        return TenantMemberResource::collection($users)->additional([
            'message' => 'Retrieved member list successfully'
        ]);
    }
}
