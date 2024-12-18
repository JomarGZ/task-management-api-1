<?php

namespace App\Models;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'project_id',
        'title',
        'description',
        'status',
        'deadline_at',
        'started_at',
        'completed_at',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
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
}
