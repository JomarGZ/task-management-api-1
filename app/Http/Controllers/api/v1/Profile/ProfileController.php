<?php

namespace App\Http\Controllers\api\v1\Profile;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Requests\api\v1\Profile\UpdateProfileRequest;
use App\Http\Resources\api\v1\tenants\TenantMemberResource;

class ProfileController extends ApiController
{
    public function store(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $validatedData = $request->validated();

        if (empty($validatedData)) {
            return response()->json(['message' => 'No data provided.'], 400);
        }

        try {
            $user->update($validatedData);
            return new TenantMemberResource($user->refresh());
        } catch (\Exception $e) {
            return response()->json(['message' => 'Update failed.'], 500);
        }
    }

}
