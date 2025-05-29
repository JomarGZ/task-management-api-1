<?php

namespace App\Notifications;

use App\Enums\NotificationType;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification implements ShouldQueue  
{
    use Queueable;

    protected Task $task;
    protected User $assigner;
    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task, User $assigner)
    {
        $this->task = $task;
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
        return ['database', 'mail', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
            // Use FRONTEND_URL from the .env file
        $frontendUrl = config('app.frontend_url'); // Ensure FRONTEND_URL is properly loaded from .env

        $mail =  (new MailMessage)
            ->subject('Task Assigned Notification')
            ->line('**You are assigned to a task.**');

        if ($this->task) {
            $mail->line("**Name:** ".  ($this->task->title ?? 'Not specified'));
            $mail->line("**Deadline:** " . ($this->task->deadline_at ?? 'Not specified')); 
            $mail->line("**Priority:** " . ($this->task->priority_level ?? 'Not specified')); 
            $mail->line("**Status:** " . ($this->task->status ?? 'Not specified')); 
        }
        if (isset($this->task->project)) {
            $mail->line("**Project Name:** " . ($this->assigner->name ?? 'Not specified'));
            $mail->line("**Assigner:** " . ($this->assigner->name ?? 'System'));
            if (isset($this->task->project->id) && isset($this->task->id)) {
                $mail->action('View Task', $frontendUrl . '/projects/' . $this->task->project->id . '/tasks/' . $this->task->id);
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
        $taskTitle = $this->task->title ?? 'a task';
        $assignerName = $this->assigner->name ?? 'System';
        $this->task->load('project:id');
        return [
            'link' => [
                'name' => 'tasks.show',
                'params' => ['projectId' => $this->task->project->id, 'taskId' => $this->task->id],
            ],
            'assigner' => [
                'name' => $assignerName,
                'avatar' => $this->assigner?->getFirstMediaUrl('avatar', 'thumb-60')
            ],
            'taskTitle' => $taskTitle,
            'priority' => $this->task->priority_level,
            'dueDate' => $this->task->deadline_at,
            'type' => NotificationType::TASK_ASSIGNMENT->value
        ];
    }
}
