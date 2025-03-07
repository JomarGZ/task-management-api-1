<?php

namespace App\Notifications;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Bus\Queueable;
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
        $this->onQueue('notifications');
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
        $frontendUrl = config('app.frontend_url');

        $mail =  (new MailMessage)
            ->subject('Upcoming Task Deadline')
            ->line('**You have upcoming task deadline**')
            ->line('**Task Details:**');
        if ($this->task) {
            $mail->line("**Name:** " . ($this->task->title ?? 'Not specified'));
            $mail->line("**Deadline:** " . ($this->task->deadline_at ?? 'Not specified')); 
            $mail->line("**Priority:** " . ucfirst($this->task->priority_level ?? 'Not specified'));
            $mail->line("**Status:** " . ucfirst($this->task->status ?? 'Not specified'));
        }
        if (isset($this->task->project) && isset($this->task->project->id)) {
            $mail->line("**Project Name:** {$this->task->project->name}");
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
        $result = [ 
            "message" => "You have an upcoming task deadline",
        ];
        
        if (isset($this->task)) {
            if (isset($this->task->title)) {
                $result['message'] = "You have upcoming task deadline: {$this->task->title}";
            }
        }
        $result['link'] = isset($his->task->id) && isset($this->task->project->id)
        ? [
            'name' => 'tasks.show',
            'params' => [
                'projectId' => $this->task->project->id,
                'taskId' => $this->task->id
            ]
        ]
        : '#';
        $result['is_external'] = false;
        return $result;
    }
   
}
