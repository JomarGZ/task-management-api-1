<?php

namespace App\Models;

use App\Enums\Enums\Statuses;
use App\Traits\BelongsToTenant;
use App\Traits\HasComment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;
    use BelongsToTenant, HasComment;

    protected $fillable = [
        'tenant_id',
        'project_id',
        'assigned_id',
        'title',
        'description',
        'status',
        'deadline_at',
        'started_at',
        'completed_at',
        'priority_level'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_id');
    }

    public function scopeSearch($query, $search) {
        return $query->when($search, function (Builder $query, $search) {
            $query->whereAny([
                'title',
                'description'
            ], 'like', "%$search%");
        });
    }

    public function scopeFilterByStatus($query, $status)
    {
        return $query->when($status, function (Builder $query, $status) {
            $query->where('status', $status);
        });
    }

    public function scopeFilterByPriorityLevel($query, $priorityLevel)
    {
        return $query->when($priorityLevel, function (Builder $query, $priorityLevel) {
            $query->where('priority_level', $priorityLevel);
        });
    }

    public function isInProgress()
    {
        return $this->status === Statuses::IN_PROGRESS->value;
    }
    public function isCompleted()
    {
        return $this->status === Statuses::COMPLETED->value;
    }

    public function markAsInProgress()
    {
        if (is_null($this->started_at)) {
            $this->update(['started_at' => now()]);
        }
    }
    public function markAsCompleted()
    {
        if (is_null($this->completed_at)) {
            $this->update(['completed_at' => now()]);
        }
    }

    public function updatePreviousDeadlineIfChanged()
    {
        if ($this->isDirty('deadline_at')) {
            $this->previous_deadline_at = $this->getOriginal('deadline_at');
        }
    }

    public function updateTimeStampsBaseOnStatus()
    {
        if ($this->isInProgress()) {
            $this->markAsInProgress();
        }
        if ($this->isCompleted()) {
            $this->markAsCompleted();
        }
    }
}
