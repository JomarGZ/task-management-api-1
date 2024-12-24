<?php

namespace App\Http\Controllers\api\v1\auth;

use App\Http\Controllers\api\v1\ApiController;
use Illuminate\Http\Request;

class LogoutController extends ApiController
{
    /**
     * Logout
     * 
     * Signs out the user and destroy the API token
     * 
     * @group Authentication
     * @response 200 {}
     */
    public function __invoke(Request $request)
    {
        $request->user()->currentAccessToken()->delete();;
        
        return $this->ok('');
    }
}
