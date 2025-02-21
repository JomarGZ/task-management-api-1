<?php

namespace App\Listeners;

use App\Events\CommentCreated;
use App\Models\Project;
use App\Models\Task;
use App\Notifications\TaskCommentNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use InvalidArgumentException;

class CommentNotificationListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CommentCreated $event): void
    {
        $commentableType = $event->comment->commentable_type;
        $comment = $event->comment;
        $commentable = $event->comment->commentable;
        $author = $event->comment->author;
        $assignedUsers = $commentable->assignedUsers()->whereNot('users.id', auth()->user()->id)->get();
        $message = "{$author->name} commented on the task assigned to you.";
        $notificationType = $this->mapCommentableNotification($commentableType, $comment, $message);
        if ($assignedUsers && $notificationType) {
            Notification::send($assignedUsers, $notificationType);
        }

    }

    protected function mapCommentableNotification($commentableType, $comment, $message)
    {
        throw_if(
            !in_array($commentableType, [Task::class, Project::class]),
            InvalidArgumentException::class,
            "Invalid commentable type {$commentableType}"
        );
       return match ($commentableType) {
            Task::class => new TaskCommentNotification($comment, $message),
            default => null,
        };
    }
}
