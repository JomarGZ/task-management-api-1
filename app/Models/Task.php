<?php

namespace App\Models;

use App\Enums\Enums\Statuses;
use App\Traits\BelongsToTenant;
use App\Traits\HasComment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'assigned_dev_id',
        'assigned_qa_id',
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

   public function assignments()
   {
        return $this->morphMany(Assignment::class, 'assignable');
   }

   public function assignedUsers()
   {
        return $this->morphToMany(User::class, 'assignable', 'assignments')
            ->withTimestamps();
   }

    public function assignedDev()
    {
        return $this->belongsTo(User::class, 'assigned_dev_id');
    }
    public function assignedQA()
    {
        return $this->belongsTo(User::class, 'assigned_qa_id');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumbnail-150')
            ->width(150)
            ->height(150);
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
