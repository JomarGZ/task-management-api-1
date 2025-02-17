<?php

namespace App\Models;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'tenant_id',
        'manager',
        'client_name',
        'started_at',
        'ended_at',
        'budget',
        'priority',
        'status',
        'team_id',
        'name',
        'description'
    ];

    protected function casts () {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function setBudgetAttribute($value)
    {
        $this->attributes['budget'] = (int) $value * 100;
    }
    public function getBudgetAttribute($value)
    {
        return (int) $value / 100;
    }

    public function projectManager()
    {
        return $this->belongsTo(User::class, 'manager');
    }

    public function assignedTeamMembers()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id')
            ->withTimestamps();
    }

    public function teamAssignee()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function scopeSearch($query, $search)
    {
        return $query->when($search, function(Builder $query, $search) {
            $query->whereAny([
                'name',
                'description'
            ], 'like', "%{$search}%");
        });
    }

    public function scopeFilterByStatus(Builder $query, $status)
    {
        return $query->when($status, fn($query) => $query->where('status', $status));
    }

    public function scopeFilterByPriority(Builder $query, $priority)
    {
        return $query->when($priority, fn($query) => $query->where('priority', $priority));
    }


}
