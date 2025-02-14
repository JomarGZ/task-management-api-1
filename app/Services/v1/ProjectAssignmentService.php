<?php
namespace App\Services\V1;

use App\Models\Project;
use App\Notifications\AssignedToProjectNotification;
use App\Notifications\ManagerAssignedProjectNotification;
use Illuminate\Support\Facades\Notification;

class ProjectAssignmentService {
    protected $project;

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

    public function assignedProjectMembers(array $assignees): self
    {
        $this->project->assignedTeamMembers()->sync($assignees);
        return $this;
    }
   
    public function notifyAssignedMembers(): self
    {   
        $this->project->load('assignedTeamMembers:id,name');
        $projectTeamMembers = $this->project->assignedTeamMembers;
        Notification::send($projectTeamMembers, new AssignedToProjectNotification($this->project));
        return $this;
    }   
   
}