<?php

use App\Http\Controllers\api\v1\auth\LoginController;
use App\Http\Controllers\api\v1\auth\RegisterController;
use App\Http\Controllers\api\v1\Tenants\TenantMembersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('auth/register', RegisterController::class);
Route::post('auth/login', LoginController::class);

Route::middleware('auth:sanctum')->group(function() {
    Route::apiResource('tenant/members', TenantMembersController::class)
        ->parameters(['members' => 'user']);
});