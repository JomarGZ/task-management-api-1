<?php
namespace App\Services\v1;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Support\Facades\Notification;

class TaskAssignmentService {
    protected $task;
    protected $newAssigneeIds = [];
    
    protected $maxAssignees;
    
    public function __construct(?Task $task = null)
    {
        $this->task = $task;
        $this->maxAssignees = config('limits.per_item.max_user_per_task');
    }


    public function assignToTask(array $newAssigneeIds)
    {
        if (empty($newAssigneeIds)) {
             throw new \InvalidArgumentException(
                "Assignees ids is required to assigned to task"
            );
        }
        $existingAssigneeIds = $this->task->users()->pluck('users.id');
        $newAssigneeIds = $this->validateAssignees($newAssigneeIds);
        $currentCount = collect($existingAssigneeIds)->count();
        $this->newAssigneeIds = collect(($newAssigneeIds)->diff($existingAssigneeIds));
        $newCounts = count($this->newAssigneeIds);
        if (($currentCount + $newCounts) > $this->maxAssignees) {
            throw new \InvalidArgumentException(
                "Task already has $currentCount assignees. Adding $newCounts would exceed max number of  assignees $this->maxAssignees"
            );
        }

        $this->task->users()->syncWithoutDetaching($this->newAssigneeIds);
        return $this;
    }

    public function notifyAssignees()
    {
        if (!empty($this->newAssigneeIds)) {
            $recipientIds = collect($this->newAssigneeIds)->diff([request()->user()->id]);
            $recipients = User::select('id', 'name', 'email')->whereIn('id', $recipientIds)->get();
            if (!empty($recipients)) {
                Notification::send($recipients, new TaskAssignedNotification($this->task, request()->user()));
            }
        }
        return $this;
    }

    public function removeAssignedMember($userId) 
    {
        throw_unless($userId, \InvalidArgumentException::class, 'User ID is required to remove an assignee');
        $this->task->users()->detach($userId);

    }

    public function validateAssignees(array $newAssigneeIds)
    {
        throw_if(empty($newAssigneeIds), \InvalidArgumentException::class, 'Assignee IDs required');

        return $this->task->project->assignedTeamMembers()->whereIn('users.id', $newAssigneeIds)->pluck('users.id');
    }

}