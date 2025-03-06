<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Notifications\UpcomingTaskDeadlineNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class NotifyUpcomingTaskDeadlines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-upcoming-task-deadlines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify users about task deadlines tomorrow';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Task::withoutGlobalScopes()->whereDate('deadline_at', now()->addDay()->toDateString())
            ->select('id', 'project_id', 'title', 'deadline_at', 'priority_level', 'status')
            ->with([
                'assignedUsers' => function($query) {
                    $query->select('users.id', 'users.email');
                },
                'project' => function($query) {
                    $query->select('id', 'name');
                }
            ])
            ->chunk(500, function ($tasks) {
                foreach($tasks as $task) {
                    if (!empty($task->assignedUsers)) {
                        Notification::send($task->assignedUsers, new UpcomingTaskDeadlineNotification($task));
                    }
                }
                info('Task deadline notifications is on queue and will be send');
            });
    }
}
