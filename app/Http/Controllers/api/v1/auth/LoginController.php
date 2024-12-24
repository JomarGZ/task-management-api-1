<?php

namespace App\Http\Controllers\api\v1\auth;

use App\Http\Controllers\api\v1\ApiController;
use App\Http\Requests\api\v1\auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends ApiController
{
    /**
     * Login
     * 
     * Authenticate the user and returns the user's API token
     * @unauthenticated
     * @group Authentication
     * @response 201 {"status": 201,
    "data": {
        "access_token": "{Your_auth_token}"
    },
    "message": "Authenticated"}
     */
    public function __invoke(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials is incorrect']
            ]);
        }

        $device = substr($request->userAgent() ?? '', 0, 255);

        $expiresAt = $request->remember ? null : now()->addMinutes((int) config('session.lifetime'));
        
        return $this->success(
            'Authenticated',
            ['access_token' => $user->createToken($device,expiresAt: $expiresAt)->plainTextToken],
            Response::HTTP_CREATED
        );
    }
}
