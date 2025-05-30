<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\Project;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignedToProjectNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $project;
    protected User $assigner;
    /**
     * Create a new notification instance.
     */
    public function __construct(Project $project,User $assigner)
    {
        $this->project = $project;
        $this->assigner = $assigner;
        $this->afterCommit();
        $this->onQueue('notifications');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $frontendUrl = config('app.frontend_url');
        $mail = (new MailMessage)
            ->subject('Project Assigned Notification')
            ->line('**You are assigned to a project**');
        if ($this->project) {
            $mail->line("**Project Name:** " . ($this->project->name ?? 'Not specified'));
            $mail->line("**Assigner:** " . ($this->assigner->name ?? 'System'));
            if (isset($this->project->id)) {
                $mail->action('View Project', $frontendUrl . '/projects/' . $this->project->id);
            }
        }
        $mail->line('Thank you for using our application!');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $projectName = $this->project->name ?? 'a project';
        $assignerName = $this->assigner->name ?? 'System';
       
        return [
            'link' => [
                'name' => 'projects.show',
                'params' => ['projectId' => $this->project->id],
                'query' => []
            ],
            'assigner' => [
                'name' => $assignerName,
                'avatar' => $this->assigner?->getFirstMediaUrl('avatar', 'thumb-60')
            ],
            'projectName' => $projectName,
            'type' => NotificationType::PROJECT_ASSIGNMENT->value
        ];
    }
}
