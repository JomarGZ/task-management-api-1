<?php
namespace App\Services\v1;

use App\Models\Task;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Support\Facades\Notification;

class TaskAssignmentService {
    protected $task;
    protected $assigneeIds = [];
    
    protected $maxAssignees = 5;
    
    public function __construct(?Task $task = null, array $assigneeIds)
    {
        $this->task = $task;
        $this->assigneeIds = $assigneeIds;
    }
    private function ensureAssigneesNotEmpty()
    {
        throw_if(empty($this->assigneeIds), \InvalidArgumentException::class, 'Assignees cannot be empty.');
    }

    public function prepareAssignees()
    {
        $this->ensureAssigneesNotEmpty();
        return array_fill_keys($this->assigneeIds, ['tenant_id' => auth()->user()->tenant_id]);
    }

    public function assignToTask()
    {
        $this->ensureAssigneesNotEmpty();
        $assigneesWithTenantId = $this->prepareAssignees();
        $this->task->assignedUsers()->syncWithoutDetaching($assigneesWithTenantId);
        return $this;
    }

    public function notifyAssignees()
    {
        $this->task->load(['assignedUsers' => function ($query) {
            $query->whereIn('users.id', $this->assigneeIds);
        }, 'project']);
        $assignees = $this->task->assignedUsers;
        if (!empty($assignees)) {
            Notification::send($assignees, new TaskAssignedNotification($this->task));
        }
        return $this;
    }

}