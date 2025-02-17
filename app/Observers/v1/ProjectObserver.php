<?php

namespace App\Observers\v1;

use App\Enums\Enums\PriorityLevel;
use App\Enums\Enums\Statuses;
use App\Models\Project;
use App\Services\V1\ProjectAssignmentService;

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
            (new ProjectAssignmentService($project))->notifyAssignedManager();
        }
    }
    
    public function updated(Project $project)
    {
        if ($project->isDirty('manager') && $project->manager) {
            (new ProjectAssignmentService($project))->notifyAssignedManager();
        }
    }

    
}
