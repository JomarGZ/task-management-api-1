<?php

namespace App\Models;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'team_id',
        'name',
        'description'
    ];

    public function assignedTeamMembers()
    {
        return $this->belongsToMany(User::class, 'project_team', 'project_id', 'user_id')
            ->withPivot('position')
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


}
