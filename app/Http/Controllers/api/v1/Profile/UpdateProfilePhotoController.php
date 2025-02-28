<?php

namespace App\Http\Controllers\api\v1\Profile;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Requests\api\v1\Profile\StoreAvatarRequest;
use App\Http\Resources\api\v1\tenants\TenantMemberResource;
use App\Models\User;

class UpdateProfilePhotoController extends ApiController
{
    public function store(StoreAvatarRequest $request)
    {
        if ($request->hasFile('avatar')) {
            $user = User::findOrFail($request->user()->id);
            $user->addMediaFromRequest('avatar')
                    ->addCustomHeaders([
                        'ACL' => 'public-read'
                    ])
                    ->toMediaCollection('avatar');

            return new TenantMemberResource($user);
        }
        
        return $this->error(
            'Update avatar failed',
            null,
            404
        );
    }
}
