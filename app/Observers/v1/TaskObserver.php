<?php

namespace App\Observers\v1;

use App\Enums\Enums\PriorityLevel;
use App\Enums\Enums\Statuses;
use App\Models\Task;
use App\Notifications\TaskAssignedNotification;
use League\CommonMark\Util\PrioritizedList;

class TaskObserver
{
    public function updated(Task $task): void
    {
        $task->updatePreviousDeadlineIfChanged();
        $task->updateTimeStampsBaseOnStatus();
        if($task->wasChanged('status') && $task->status === Statuses::COMPLETED->value) {
            $task->completed_at = $task->updated_at ?? now();
        }
    }


    public function creating(Task $task)
    {
        // $task->status = Statuses::TO_DO->value;
        // $task->priority_level = PriorityLevel::LOW->value;
    }
}
