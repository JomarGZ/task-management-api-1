<?php

namespace App\Http\Controllers\api\v1\auth;

use App\Enums\ChatTypeEnum;
use App\Http\Requests\api\v1\auth\RegistrationRequest;
use App\Models\Tenant;
use App\Models\User;
use App\Enums\Role;
use App\Http\Controllers\api\v1\ApiController;
use App\Models\Channel;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RegisterController extends ApiController
{
     /**
     * Register
     * 
     * Register and authenticated the user and returns the user's API token
     * @unauthenticated
     * @group Authentication
     * @response 201 { "status": 201,
    "data": {
        "user": {
            "id": 16,
            "name": "jomar godinez",
            "email": "jomar23@gmail.com"
        },
        "access_token": "13|UDbQwZ8VdvhF635bikuBhejmBYO5LzdBXDYufIK6e8f5b1bf"
    },
    "message": "Registered Successfully"}
     */
    public function __invoke(RegistrationRequest $request)
    {
       try {
            DB::beginTransaction();

            $user = User::withoutGlobalScopes()->create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $tenant = Tenant::create(['name' => "$user->name tenant"]);

            $generalChannel = Channel::create([
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
                'type' => ChatTypeEnum::GENERAL->value,
                'name' => 'General', 
                'description' => "Default general channel for {$tenant->name}",
                'is_default' => true, 
            ]);

            $generalChannel->participants()->attach($user->id, [
                'tenant_id' => $tenant->id,
            ]);

            $user->update([
                'tenant_id' => $tenant->id,
                'role' => Role::ADMIN->value,
            ]);

            DB::commit();

           $device = substr($request->userAgent() ?? '', 0, 255);
            return $this->success(
                'Registered Successfully',
                [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'access_token' => $user->createToken($device)->plainTextToken,
                ],
                Response::HTTP_CREATED
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Registration failed: {$e->getMessage()}", ['exception' => $e]);
            return $this->error('Registration failed', ['error' => $e->getMessage()], 500);
        }
       
    }
}
