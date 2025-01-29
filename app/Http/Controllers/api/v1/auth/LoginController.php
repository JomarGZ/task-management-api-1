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
     * @response 200 {"status": 200,
    "data": {
        "access_token": "14|75JIc0wUjVULaI7t7Lnh5JC3SV5xjvkSB6CQ1zUF1913f9e1",
        "user": {
            "id": 16,
            "name": "jomar godinez",
            "email": "jomar23@gmail.com"
        }
    },
    "message": "Authenticated successfully."}
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

        // $expiresAt = $request->remember ? null : now()->addMinutes((int) config('session.lifetime'));
        $token = $user->createToken($device)->plainTextToken;

        return $this->success(
            'Authenticated successfully.',
            [
                'access_token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
    }
}
