<?php

namespace App\Http\Controllers\api\v1\auth;

use App\Http\Controllers\api\v1\ApiController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordUpdateController extends ApiController
{
    /**
     * Update password
     * 
     *  Change user password
     * 
     * @group Authentication
     * @response 201 { "status": 200,
    "message": "Your password has been updated."}
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::default()]
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password)
        ]);

        return $this->ok('Your password has been updated.');
    }
}
