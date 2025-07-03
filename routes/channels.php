<?php

use App\Models\Task;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('task.{taskId}.comments', function ($user, $taskId) {
    return $user->can('view', Task::findOrFail($taskId));
});

Broadcast::channel('direct.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('user.{userId}.channel.{channelId}', function ($user, $userId, $channelId) {
    return (int) $user->id === (int) $userId && 
           $user->channels()->where('channels.id', $channelId)->exists();
});

Broadcast::routes();