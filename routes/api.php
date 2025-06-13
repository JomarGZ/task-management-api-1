<?php

use App\Http\Controllers\api\v1\auth\LoginController;
use App\Http\Controllers\api\v1\auth\LogoutController;
use App\Http\Controllers\api\v1\auth\PasswordUpdateController;
use App\Http\Controllers\api\v1\auth\RegisterController;
use App\Http\Controllers\api\v1\Chats\ChannelController;
use App\Http\Controllers\api\v1\Chats\ChannelParticipantsController;
use App\Http\Controllers\api\v1\Chats\DirectChannelController;
use App\Http\Controllers\api\v1\Chats\GeneralChannelController;
use App\Http\Controllers\api\v1\Chats\MessageController;
use App\Http\Controllers\api\v1\Chats\MessageLikesController;
use App\Http\Controllers\api\v1\Chats\MessageRepliesController;
use App\Http\Controllers\api\v1\Comments\CommentController;
use App\Http\Controllers\api\v1\Dashboard\TaskCompletionTrendController;
use App\Http\Controllers\api\v1\Dashboard\TaskDistributionController;
use App\Http\Controllers\api\v1\Dashboard\TaskStatisticsController;
use App\Http\Controllers\api\v1\notifications\NotificationBulkController;
use App\Http\Controllers\api\v1\notifications\NotificationController;
use App\Http\Controllers\api\v1\Notifications\NotificationDropDownController;
use App\Http\Controllers\api\v1\Profile\ProfileController;
use App\Http\Controllers\api\v1\Profile\UpdateProfilePhotoController;
use App\Http\Controllers\api\v1\Projects\ProjectController;
use App\Http\Controllers\api\v1\Projects\ProjectPriorityController;
use App\Http\Controllers\api\v1\Projects\ProjectStatusController;
use App\Http\Controllers\api\v1\Projects\ProjectTaskController;
use App\Http\Controllers\api\v1\Projects\ProjectTeamController;
use App\Http\Controllers\api\v1\TaskComments\TaskCommentController;
use App\Http\Controllers\api\v1\tasks\PriorityTimelineController;
use App\Http\Controllers\api\v1\Tasks\ProjectTaskDevAssignmentController;
use App\Http\Controllers\api\v1\Tasks\ProjectTaskQAAssignmentController;
use App\Http\Controllers\api\v1\Tasks\TaskAssignmentController;
use App\Http\Controllers\api\v1\Tasks\TaskController;
use App\Http\Controllers\api\v1\Tasks\TaskLinksController;
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
use App\Http\Controllers\api\v1\Tenants\TenantMemberPositionController;
use App\Http\Resources\api\v1\tenants\TenantMemberResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    $user = $request->user()->load(['media']); 
    return new TenantMemberResource($user);
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function() {
    Route::put('auth/password-update', PasswordUpdateController::class);
    Route::post('auth/logout', LogoutController::class);

    Route::post('user/profile-update', [ProfileController::class, 'store']);
    Route::post('user/profile-avatar', [UpdateProfilePhotoController::class, 'store']);
    Route::apiResource('teams', TeamController::class)->shallow();
    Route::apiResource('teams.members', TeamMembersController::class)
        ->parameters(['members' => 'user'])
        ->except(['update', 'show']);
        
    Route::get('tenant/members/list', TenantMemberListController::class);
    Route::patch('tenant-member/position/update/{user}', [TenantMemberPositionController::class, 'update']);
    Route::apiResource('tenant/members', TenantMembersController::class)
        ->parameters(['members' => 'user']);
    Route::get('project-statuses', [ProjectStatusController::class, 'index']);
    Route::get('project-priority-levels', ProjectPriorityController::class);
    Route::post('projects/{project}/assignment', [ProjectTeamController::class, 'store']);
    Route::get('projects/{project}/team-members', [ProjectTeamController::class, 'index']);
    Route::delete('projects/{project}/assignment', [ProjectTeamController::class, 'destroy']);
    Route::apiResource('projects', ProjectController::class);

    
    Route::get('task-priority-levels', [TaskPriorityController::class, 'index']);
    Route::post('tasks/{task}/assignment', [TaskAssignmentController::class, 'store']);
    Route::delete('tasks/{task}/unassignment', [TaskAssignmentController::class, 'destroy']);
    Route::patch('tasks/{task}/status', [TaskStatusController::class, 'update']);
    Route::patch('tasks/{task}/assign-developer', [ProjectTaskDevAssignmentController::class, 'store']);
    Route::delete('tasks/{task}/unassign-developer', [ProjectTaskDevAssignmentController::class, 'destroy']);
    Route::patch('tasks/{task}/assign-qa', [ProjectTaskQAAssignmentController::class, 'store']);
    Route::delete('tasks/{task}/unassign-qa', [ProjectTaskQAAssignmentController::class, 'destroy']);
    Route::post('tasks/{task}/links', [TaskLinksController::class, 'store']);
    Route::put('tasks/links/{link}', [TaskLinksController::class, 'update']);
    Route::delete('tasks/links/{link}', [TaskLinksController::class, 'destroy']);

    Route::apiResource('projects.tasks', ProjectTaskController::class)->shallow();
    Route::prefix('standalone')->group(function () {
        Route::get('tasks', [TaskController::class, 'index'])->name('tasks.index');
    });
    Route::post('/comments', [CommentController::class, 'store']);
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->middleware('can:update,comment');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->middleware('can:delete,comment');
    Route::get('tasks/{task}/comments',[TaskCommentController::class, 'index']);
    Route::get('project-statuses', [ProjectStatusController::class, 'index']);
    Route::get('teams/{team}/statistic', [TeamStatisticController::class, 'index']);

    Route::prefix('user')->group(function () {
        Route::apiResource('task-status-counts', UserTaskStatusController::class)->only('index');
        Route::apiResource('upcoming-tasks-deadlines', UserUpcomingTaskDeadlineController::class)->only('index');
        Route::get('/{user}/tasks',[ UserTasksController::class, 'index']);
        Route::get('priority-timeline', [PriorityTimelineController::class, 'index']);

        Route::apiResource('notifications', NotificationController::class)->only(['index', 'update', 'destroy']);
        Route::get('notifications/dropdown', [NotificationDropDownController::class, 'index']);
        Route::post('notifications/mark-all-as-read', [NotificationBulkController::class, 'update']);
        Route::post('notifications/delete-all', [NotificationBulkController::class, 'destroy']);
    });

    Route::prefix('dashboard')->group(function () {
        Route::get('tasks-statistic/counts', [TaskStatisticsController::class, 'index']);
        Route::get('task-completion/statistic', [TaskCompletionTrendController::class, 'index']);
        Route::get('stask-distribution', [TaskDistributionController::class, 'index']);
    });

    Route::prefix('chat')->group(function () {
        Route::apiResource('channels', ChannelController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::apiResource('messages', MessageController::class)->only(['store', 'update', 'destroy']);
        Route::get('channels/{channel}/messages', [MessageController::class, 'index']);
        Route::apiResource('channels.participants', ChannelParticipantsController::class)->shallow()->only(['store', 'index']);
        Route::get('channel/messages/{message}/replies', [MessageRepliesController::class, 'index']);
        Route::post('channel/messages/{message}/like', [MessageLikesController::class , 'store']);
        Route::get('channel/general', [GeneralChannelController::class, 'show']);
        Route::post('channel/direct', [DirectChannelController::class, 'store']);
    });
});

Route::post('auth/register', RegisterController::class)->name('register');
Route::post('auth/login', LoginController::class);

Broadcast::routes(['middleware' => ['auth:sanctum']]);