<?php

namespace App\Observers\v1;

use App\Models\Task;
use App\Notifications\TaskAssignedNotification;

class TaskObserver
{


    public function updated(Task $task): void
    {
        $task->updatePreviousDeadlineIfChanged();
        $task->updateTimeStampsBaseOnStatus();
        if ($task->wasChanged('assigned_id')) {
            $task->assignee->notify(new TaskAssignedNotification($task));
        }
    }
    

}
