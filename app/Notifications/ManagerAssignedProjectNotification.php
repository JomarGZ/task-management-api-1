<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
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
        $this->project = $project;
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
        $mail =  (new MailMessage)
            ->subject('Project Assigned Notification')
            ->line('**You are assigned to a project as project manager.**');

        if ($this->project) {
            $mail->line("**Project Name:** " .($this->project->name ?? 'Not specified'));
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
        $result = [
            "message" => "You have been assigned to a project as Project Manager"
        ];
        if (isset($this->project->name)) {
            $result['message'] = "You have been assigned to a project {$this->project->name} as project manager";
        }
        $result['link'] = isset($this->project->id)
        ? [
            'name' => 'projects.show',
            'params' => [
                'projectId' => $this->project->id
            ]
        ]
        : '#';
        $result['is_external'] = false;
        return $result;
    }
}
