<?php

namespace App\Notifications;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpcomingTaskDeadlineNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 5;
    protected $task;
    protected $project;
    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task, ?Project $project = null)
    {
        $this->task = $task;
        $this->project = $project;
    }
    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Upcoming task deadline.')
                    ->line('Your task is due tomorrow!')
                    ->line("Your task title: {$this->task->title}")
                    ->action('Notification Action', env('FRONTEND_URL'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            "message" => "You have upcoming task deadline: {$this->task->title}",
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
