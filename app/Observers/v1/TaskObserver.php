<?php

namespace App\Observers\v1;

use App\Models\Task;
use App\Notifications\TaskAssignedNotification;

class TaskObserver
{

    /**
     * Handle the Task "updated" event.
     */
    public function updating(Task $task): void
    {
        $task->updatePreviousDeadlineIfChanged();
        $task->updateTimeStampsBaseOnStatus();

        if ($task->wasChanged('assignee_id')) {
            $task->assignee->notify(new TaskAssignedNotification($task));
        }
    }

}
