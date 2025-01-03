<?php

use App\Http\Controllers\api\v1\auth\LoginController;
use App\Http\Controllers\api\v1\auth\LogoutController;
use App\Http\Controllers\api\v1\auth\PasswordUpdateController;
use App\Http\Controllers\api\v1\auth\RegisterController;
use App\Http\Controllers\api\v1\Projects\ProjectController;
use App\Http\Controllers\api\v1\TaskComments\TaskCommentController;
use App\Http\Controllers\api\v1\Tasks\TaskController;
use App\Http\Controllers\api\v1\Tasks\TaskPriorityLevelsAndStatusesController;
use App\Http\Controllers\api\v1\Teams\TeamController;
use App\Http\Controllers\api\v1\Teams\TeamMembersController;
use App\Http\Controllers\api\v1\Tenants\TenantMembersController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function() {
    Route::put('auth/password-update', PasswordUpdateController::class);
    Route::post('auth/logout', LogoutController::class);

    Route::apiResource('teams', TeamController::class)->shallow();
    Route::apiResource('teams.members', TeamMembersController::class)
        ->parameters(['members' => 'user'])
        ->except(['update', 'show']);
        
    Route::apiResource('tenant/members', TenantMembersController::class)
        ->parameters(['members' => 'user']);
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('projects.tasks', TaskController::class)->shallow();
    Route::apiResource('tasks.comments', TaskCommentController::class)->shallow()->except('index');
    Route::get('statuses-and-priority-levels', TaskPriorityLevelsAndStatusesController::class);
});

Route::post('auth/register', RegisterController::class)->name('register');
Route::post('auth/login', LoginController::class);