<?php

namespace App\Http\Controllers\api\v1\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordUpdateController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::default()]
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Your password has been updated.'
        ],Response::HTTP_ACCEPTED);
    }
}
