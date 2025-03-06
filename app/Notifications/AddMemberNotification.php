<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AddMemberNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $tenantMemberCredential)
    {
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $frontendUrl = config('app.frontend_url');

        $mail =  (new MailMessage)
                ->subject('You are invited to SprintSync')
                ->line('**Welcome to SprintSync**')
                ->line('**Below are your login credentials:**');
        if ($this->tenantMemberCredential) {
            if (isset($this->tenantMemberCredential['name'])) {
                $mail->line("**Name:** {$this->tenantMemberCredential['name']}");
            }
            if (isset($this->tenantMemberCredential['email'])) {
                $mail->line("**Email:** {$this->tenantMemberCredential['email']}");
            }
            if (isset($this->tenantMemberCredential['plain_password'])) {
                $mail->line("**Password:** {$this->tenantMemberCredential['plain_password']}");
            }
        }
        $mail->action('Login to SprintSync', $frontendUrl);
        $mail->line('Thank you for joining us!');

        return $mail;
    }

}
