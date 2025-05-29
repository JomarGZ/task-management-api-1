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
        $author = $event->comment->user;
        if ($commentable instanceof Task) {
            $commentable->load([
                'comments.user:id,name,email',
                'users:id,name,email'
            ]);
            $recipients = $commentable->comments
                ->pluck('user')
                ->merge($commentable->users)
                ->unique('id')
                ->reject(fn ($user) => $user->id === auth()->id());

            $message = "{$author->name} commented on the task assigned to you.";
            $notificationType = $this->mapCommentableNotification($commentableType, $comment, $message);
            if (!empty($recipients) && $notificationType) {
                Notification::send($recipients, $notificationType);
            }
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
