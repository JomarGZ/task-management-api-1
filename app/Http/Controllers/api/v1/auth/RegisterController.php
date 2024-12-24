<?php

namespace App\Http\Controllers\api\v1\auth;

use App\Http\Requests\api\v1\auth\RegistrationRequest;
use App\Models\Tenant;
use App\Models\User;
use App\Enums\Role;
use App\Http\Controllers\api\v1\ApiController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class RegisterController extends ApiController
{
     /**
     * Register
     * 
     * Register and authenticated the user and returns the user's API token
     * @unauthenticated
     * @group Authentication
     * @response 201 {  "status": 201,
    "data": {
        "access_token": "{YOUR_AUTH_KEY}"
    },
    "message": "Registered Successfully"}
     */
    public function __invoke(RegistrationRequest $request)
    {
       
        $user = User::withoutGlobalScopes()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $tenant = Tenant::create([
            'name' => "$user->name tenant"
        ]);
        $user->tenant_id = $tenant->id;
        $user->role = Role::ADMIN->value;
        $user->save();

        $device = substr($request->userAgent() ?? '', 0, 255);

        return $this->success(
            'Registered Successfully',
            ['access_token' => $user->createToken($device)->plainTextToken],
            Response::HTTP_CREATED
        );
    }
}
