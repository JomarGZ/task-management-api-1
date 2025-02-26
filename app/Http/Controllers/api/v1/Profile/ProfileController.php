<?php

namespace App\Http\Controllers\api\v1\Profile;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Requests\api\v1\Profile\UpdateProfileRequest;
use App\Http\Resources\api\v1\tenants\TenantMemberResource;
use App\Models\User;

class ProfileController extends ApiController
{
    public function store(UpdateProfileRequest $request)
    {   
       
        $data = $request->only(['name', 'email']);
        $data = array_filter($data);

        if (empty($data)) {
            return $this->error(
                'No data provided for update.',
                null,
                400
            );
        }
        $user = User::select('id', 'name', 'email')->findOrFail($request->user()->id);
        $user->update($data);
       
        return new TenantMemberResource($user);
    }
}
