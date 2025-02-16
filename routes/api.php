<?php

use App\Http\Controllers\api\v1\auth\LoginController;
use App\Http\Controllers\api\v1\auth\LogoutController;
use App\Http\Controllers\api\v1\auth\PasswordUpdateController;
use App\Http\Controllers\api\v1\auth\RegisterController;
use App\Http\Controllers\api\v1\notifications\NotificationController;
use App\Http\Controllers\api\v1\Projects\ProjectController;
use App\Http\Controllers\api\v1\Projects\ProjectPriorityController;
use App\Http\Controllers\api\v1\Projects\ProjectStatusController;
use App\Http\Controllers\api\v1\Projects\ProjectTaskController;
use App\Http\Controllers\api\v1\Projects\ProjectTeamController;
use App\Http\Controllers\api\v1\TaskComments\TaskCommentController;
use App\Http\Controllers\api\v1\Tasks\ProjectTaskDevAssignmentController;
use App\Http\Controllers\api\v1\Tasks\ProjectTaskQAAssignmentController;
use App\Http\Controllers\api\v1\Tasks\TaskAssignmentController;
use App\Http\Controllers\api\v1\Tasks\TaskController;
use App\Http\Controllers\api\v1\Tasks\TaskPriorityController;
use App\Http\Controllers\api\v1\Teams\TeamController;
use App\Http\Controllers\api\v1\Teams\TeamMembersController;
use App\Http\Controllers\api\v1\Tenants\TenantMembersController;
use App\Http\Controllers\api\v1\Tasks\TaskStatusController;
use App\Http\Controllers\api\v1\Tasks\UserTasksController;
use App\Http\Controllers\api\v1\Tasks\UserTaskStatusController;
use App\Http\Controllers\api\v1\Tasks\UserUpcomingTaskDeadlineController;
use App\Http\Controllers\api\v1\Teams\TeamStatisticController;
use App\Http\Controllers\api\v1\Tenants\TenantMemberListController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function() {
    Route::put('auth/password-update', PasswordUpdateController::class);
    Route::post('auth/logout', LogoutController::class);

    Route::apiResource('teams', TeamController::class)->shallow();
    Route::apiResource('teams.members', TeamMembersController::class)
        ->parameters(['members' => 'user'])
        ->except(['update', 'show']);
        
    Route::get('tenant/members/list', TenantMemberListController::class);
    Route::apiResource('tenant/members', TenantMembersController::class)
        ->parameters(['members' => 'user']);
    Route::get('project-statuses', [ProjectStatusController::class, 'index']);
    Route::get('project-priority-levels', ProjectPriorityController::class);
    Route::post('projects/{project}/assignment', [ProjectTeamController::class, 'store']);
    Route::apiResource('projects', ProjectController::class);

    Route::get('task-priority-levels', [TaskPriorityController::class, 'index']);
    Route::post('tasks/{task}/assignment', [TaskAssignmentController::class, 'store']);
    Route::put('tasks/{task}/unassignment', [TaskAssignmentController::class, 'update']);
    Route::patch('tasks/{task}/status', [TaskStatusController::class, 'update']);
    Route::get('tasks-statuses', [TaskStatusController::class, 'index']);
    Route::patch('tasks/{task}/assign-developer', [ProjectTaskDevAssignmentController::class, 'store']);
    Route::delete('tasks/{task}/unassign-developer', [ProjectTaskDevAssignmentController::class, 'destroy']);
    Route::patch('tasks/{task}/assign-qa', [ProjectTaskQAAssignmentController::class, 'store']);
    Route::delete('tasks/{task}/unassign-qa', [ProjectTaskQAAssignmentController::class, 'destroy']);
   
    Route::apiResource('projects.tasks', ProjectTaskController::class)->shallow();
    Route::prefix('standalone')->group(function () {
        Route::get('tasks', [TaskController::class, 'index'])->name('tasks.index');
    });
    Route::apiResource('tasks.comments', TaskCommentController::class)->shallow()->except('index');
    Route::get('project-statuses', [ProjectStatusController::class, 'index']);
    Route::get('teams/{team}/statistic', [TeamStatisticController::class, 'index']);

    Route::prefix('user')->group(function () {
        Route::apiResource('task-status-counts', UserTaskStatusController::class)->only('index');
        Route::apiResource('upcoming-tasks-deadlines', UserUpcomingTaskDeadlineController::class)->only('index');
        Route::apiResource('assigned-tasks', UserTasksController::class)->only('index');
        
        Route::get('notifications', [NotificationController::class, 'index']);
    });
});

Route::post('auth/register', RegisterController::class)->name('register');
Route::post('auth/login', LoginController::class);

Broadcast::routes(['middleware' => ['auth:sanctum']]);