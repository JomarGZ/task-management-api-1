<?php

namespace App\Console\Commands;

use App\Enums\Enums\Statuses;
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
        Task::withoutGlobalScopes()
            ->select('id', 'project_id', 'title', 'deadline_at', 'priority_level', 'status')
            ->with(['users:id,name,email', 'project:id,name'])
            ->where('status', '!=', Statuses::COMPLETED)
            ->whereNotNull('deadline_at')
            ->whereDate('deadline_at', now()->addDay()->toDateString())
           
            ->chunk(500, function ($tasks) {
                $tasks->each(function ($task) {
                    if ($task->users->isNotEmpty()) {
                        info('task', ['users' => $task->users, 'task' => $task]);
                        Notification::send($task->users,   (new UpcomingTaskDeadlineNotification($task))
                        ->delay(now()->addSeconds(5)));
                    }
                });
            });
    }
}
