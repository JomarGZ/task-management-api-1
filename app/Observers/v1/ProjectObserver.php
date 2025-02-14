<?php

namespace App\Observers\v1;

use App\Enums\Enums\PriorityLevel;
use App\Enums\Enums\Statuses;
use App\Models\Project;
use App\Notifications\ManagerAssignedProjectNotification;

class ProjectObserver
{
    public function creating(Project $project): void
    {
       $project->status = Statuses::NOT_STARTED->value;
       $project->priority = PriorityLevel::LOW->value;
    }

    public function created(Project $project)
    {
        if ($project->manager !== null) {
            $this->notifyManager($project);
        }
    }
    
    public function updated(Project $project)
    {
        if ($project->isDirty('manager') && $project->manager) {
            $this->notifyManager($project);
        }
    }

    public function notifyManager(Project $project)
    {
        $manager = $project->projectManager;
        if ($manager) {
            $manager->notify(new ManagerAssignedProjectNotification($project));
        }
    }
}
