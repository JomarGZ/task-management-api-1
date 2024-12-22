<?php

namespace App\Observers;

use App\Enums\Enums\Statuses;
use App\Models\Task;

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
        if ($task->isDirty('deadline_at')) {
            $task->previous_deadline_at = $task->deadline_at;
        }
        if ($task->isInProgress() && is_null($task->started_at)) {
            $task->started_at = now();
        }
        if ($task->isCompleted() && is_null($task->completed_at)) {
            $task->completed_at = now();
        }

    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        //
    }
}
