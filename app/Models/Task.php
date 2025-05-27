<?php

namespace App\Models;

use App\Enums\Enums\Statuses;
use App\Traits\BelongsToTenant;
use App\Traits\HasComment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Task extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;
    use BelongsToTenant, HasComment, InteractsWithMedia;

    protected $fillable = [
        'tenant_id',
        'project_id',
        'title',
        'description',
        'category',
        'status',
        'deadline_at',
        'started_at',
        'completed_at',
        'priority_level',
    ];
    // protected $casts = [
    //     'deadline_at' => 'datetime',
    // ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function links()
    {
        return $this->hasMany(Link::class);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumbnail-150')
            ->width(150)
            ->height(150);
    }

    public function scopeUpcomingDeadlines(Builder $query, $startDate = null, $endDate = null)
    {
        $startDate = $startDate instanceof Carbon ? $startDate->toDateString() : now()->toDateString();
        $endDate = $endDate instanceof Carbon  ? $endDate->toDateString() : now()->addDays(3)->toDateString();

        return $query
            ->whereNotNull('deadline_at')
            ->whereBetween('deadline_at', [$startDate, $endDate]);
    }

    public function scopeFilterByStatus($query, $status)
    {
        return $query->when($status, fn ($query) => $query->where('status', $status));
    }

    public function scopeFilterByPriorityLevel($query, $priorityLevel)
    {
        return $query->when($priorityLevel, fn ($query) => $query->where('priority_level', $priorityLevel));
    }

    public function scopeFilterByProjectId($query, $projectId)
    {
        return $query->when($projectId, fn ($query) => $query->where('project_id', $projectId));
    }

    public function scopeFilterByAssigneeId($query, $assigneeId)
    {
        return $query->when($assigneeId, fn ($query) => $query->whereHas('users', fn ($query) => $query->where('users.id', $assigneeId)));
    }

    public function scopeFilterBySearch($query, $search)
    {
        return $query->when($search, fn ($query) => $query->whereAny(['title', 'description'], 'like', "%$search%"));
    }

    public function scopeAssigneeIds($query)
    {
        return $query->users()->pluck('users.id');
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
