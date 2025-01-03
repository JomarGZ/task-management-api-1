<?php

namespace App\Observers\v1;

use App\Models\Task;
use App\Notifications\TaskAssignedNotification;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updating(Task $task): void
    {
        $task->updatePreviousDeadlineIfChanged();
        $task->updateTimeStampsBaseOnStatus();

        if ($task->isDirty('assignee_id')) {
            $assignedUser = $task->assignee;
            $assignedUser->notify(new TaskAssignedNotification($task));

        }
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        //
    }

}
