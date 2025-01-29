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
        if ($task->wasChanged('assigned_dev_id') && $task->assigned_dev_id !== null) {
            $task->assignedDev->notify(new TaskAssignedNotification($task));
        }
        if ($task->wasChanged('assigned_qa_id') && $task->assigned_qa_id !== null) {
            $task->assignedQA->notify(new TaskAssignedNotification($task));
        }
    }

}
