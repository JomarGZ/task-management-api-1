<?php

namespace App\Observers\v1;

use App\Enums\Enums\PriorityLevel;
use App\Enums\Enums\Statuses;
use App\Models\Project;

class ProjectObserver
{
    public function creating(Project $project): void
    {
       $project->status = Statuses::NOT_STARTED->value;
       $project->priority = PriorityLevel::LOW->value;
    }
  
}
