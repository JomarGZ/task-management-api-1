<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ManagerAssignedProjectNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $project;
    /**
     * Create a new notification instance.
     */
    public function __construct(Project $project)
    {
        $this->afterCommit();
        $this->project = $project;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('You are assigned to a project as project manager.')
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
        return [
            "message" => "You have been assigned to a project as project manager: {$this->project->name}",
            'main_entity' => [
                'entity_id' => $this->project->id,
                'entity_type' => 'task'
            ],
            'related_entity' => [],
        ];
    }
}
