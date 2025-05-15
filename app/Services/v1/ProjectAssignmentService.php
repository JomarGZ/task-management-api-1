<?php
namespace App\Services\V1;

use App\Models\Project;
use App\Models\User;
use App\Notifications\AssignedToProjectNotification;
use App\Notifications\ManagerAssignedProjectNotification;
use Illuminate\Support\Facades\Notification;

class ProjectAssignmentService {
    protected $project;
    protected $newAssigneeIds = [];
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function assignedProjectMembers(array $newMemberIds): self
    {
       
        $existingMemberIds = $this->project->assignedTeamMembers()->pluck('users.id');
        $currentCount = collect($existingMemberIds)->count();
        $this->newAssigneeIds = collect($newMemberIds)->diff($existingMemberIds);
        $newCounts = count($this->newAssigneeIds);
        if ($currentCount + $newCounts > $this->getMaxUsers()) {
            throw new \InvalidArgumentException(
                 "Project already has {$currentCount} members. Adding {$newCounts} would exceed the max members of {$this->getMaxUsers()}."
            );
        }
        $this->project->assignedTeamMembers()->syncWithoutDetaching($this->newAssigneeIds);
        return $this;
    }
   
    public function notifyAssignedMembers(): self
    {   
        if (!empty($this->newAssigneeIds)) {
          $recipients = collect(array_diff($this->newAssigneeIds->toArray(),[auth()->id()]));
            $newMembers = User::whereIn('id', $recipients)->get();
            if (!empty($newMembers)) {
                Notification::send($newMembers, new AssignedToProjectNotification($this->project, request()->user()));
            }
        }
        return $this;
    }
    
    public function removeAssignedMember($userId)
    {   
        if (!$userId) {
            return;
        }
        $this->project->assignedTeamMembers()->detach($userId);
    }
    
    public function getMaxUsers()
    {
        return config('limits.per_item.max_user_per_project');
    }
}