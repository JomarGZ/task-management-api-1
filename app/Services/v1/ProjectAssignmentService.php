<?php
namespace App\Services\V1;

use App\Models\Project;
use App\Models\User;
use App\Notifications\AssignedToProjectNotification;
use App\Notifications\ManagerAssignedProjectNotification;
use Illuminate\Support\Facades\Notification;

class ProjectAssignmentService {
    protected $project;
    protected $maxMembers = 20;
    protected $newAssigneeIds = [];
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function notifyAssignedManager(): self
    {
        $this->project->load('projectManager');
        $manager = $this->project->projectManager;
        if ($manager) {
            $manager->notify(new ManagerAssignedProjectNotification($this->project));
        }
        return $this;
    }

    public function assignedProjectMembers(array $newMemberIds): self
    {
       
        $existingMemberIds = $this->project->assignedTeamMembers()->pluck('users.id');
        $currentCount = collect($existingMemberIds)->count();
        $this->newAssigneeIds = collect($newMemberIds)->diff($existingMemberIds);
        $newCounts = count($this->newAssigneeIds);
        if ($currentCount + $newCounts > $this->maxMembers) {
            throw new \InvalidArgumentException(
                 "Project already has {$currentCount} members. Adding {$newCounts} would exceed the maxMembers of {$this->maxMembers}."
            );
        }
        $this->project->assignedTeamMembers()->syncWithoutDetaching($newMemberIds);
        return $this;
    }
   
    public function notifyAssignedMembers(): self
    {   

        if (!empty($this->newAssigneeIds)) {
            $newMembers = User::whereIn('id', $this->newAssigneeIds)->get();
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
   
}