<?php

namespace App\Http\Controllers\api\v1\Tenants;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\Tenant\UpdateUserPositionRequest;
use App\Http\Resources\api\v1\tenants\TenantMemberResource;
use App\Models\User;
use Illuminate\Http\Request;

class TenantMemberPositionController extends Controller
{
    public function update(User $user, UpdateUserPositionRequest $request)
    {
        $user->update($request->validated());

        return TenantMemberResource::make($user->fresh()->load('media'))->additional([
            'message' => 'Tenant member position updated successfully'
        ]);
    }
}
