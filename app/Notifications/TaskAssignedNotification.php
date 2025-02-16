<?php

namespace App\Notifications;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification implements ShouldQueue  
{
    use Queueable;

    protected Task $task;
    protected $project;
    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task,?Project $project = null)
    {
        $this->project = $project;
        $this->task = $task;
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
                    ->line('You have been assigned a new task.')
                    ->action('View Task', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            "message" => "You have been assigned to a task: {$this->task->title}",
            'main_entity' => [
                'entity_id' => $this->task->id,
                'entity_type' => 'task'
            ],
            'related_entity' => [
                'entity_id' => $this->project->id,
                'entity_type' => 'project',
            ],
        ];
    }
}
