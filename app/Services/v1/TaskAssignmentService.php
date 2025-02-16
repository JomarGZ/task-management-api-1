<?php
namespace App\Services\v1;

use App\Models\Task;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Support\Facades\Notification;

class TaskAssignmentService {
    protected $task;
    
    public function __construct(?Task $task = null)
    {
        $this->task = $task;
    }
    private function ensureAssigneesNotEmpty(array $assignees)
    {
        throw_if(empty($assignees), \InvalidArgumentException::class, 'Assignees cannot be empty.');
    }

    public function prepareAssignees(array $assignees)
    {
        $this->ensureAssigneesNotEmpty($assignees);
        return array_fill_keys($assignees, ['tenant_id' => auth()->user()->tenant_id]);
    }

    public function assignToTask(array $assignees)
    {
        $this->ensureAssigneesNotEmpty($assignees);
        $this->task->assignedUsers()->syncWithoutDetaching($assignees);
        return $this;
    }

    public function notifyAssignees()
    {
        $this->task->load(['assignedUsers', 'project']);
        $assignees = $this->task->assignedUsers;
        $project = $this->task->project;
        Notification::send($assignees, new TaskAssignedNotification($this->task, $project));
        return $this;
    }

}