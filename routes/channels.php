<?php

use App\Models\Task;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('task.{taskId}.comments', function ($user, $taskId) {
    return $user->can('view', Task::findOrFail($taskId));
});
Broadcast::routes();