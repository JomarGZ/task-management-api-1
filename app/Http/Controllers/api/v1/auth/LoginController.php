<?php

namespace App\Http\Controllers\api\v1\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\api\v1\auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
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
        return response()->json([
            'access_token' => $user->createToken($device,expiresAt: $expiresAt)->plainTextToken,

        ], Response::HTTP_CREATED);

    }
}
