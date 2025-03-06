<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected Comment $comment, protected $message)
    {
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $task = $this->comment->commentable;
        $project = $this->comment->commentable->project;
        $result = [
            'message' => 'There is noew comment to the task assigned to you'
        ];
        if (isset($this->message)) {
            $result['message'] = $this->message;
        }
        if (isset($task->id)) {
            $result['main_entity'] =  [
                'entity_id' => $task->id,
                'entity_type' => 'task'
            ];
        }
        if (isset($project->id)) {
            $result['related_entity'] = [
                'entity_id' => $project->id,
                'entity_type' => 'project'
            ];
        }
        return $result;
    }
}
